<?php
namespace WilokeListingTools\Controllers;

use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;
use WilokeListingTools\Models\UserModel;

class UserPlanController extends Controller
{
    public function __construct()
    {
//		$aPostTypes = General::getPostTypeKeys(false, false);
//		foreach ($aPostTypes as $planKey){
//			add_action('wiloke-listing-tools/payment-succeeded/'.$planKey.'_plan', array($this, 'setUserPlan'));
//		}
        add_action('wiloke-listing-tools/payment-succeeded/listing_plan', [$this, 'setUserPlan']);
        add_action('wiloke-listing-tools/payment-succeeded/event_plan', [$this, 'setUserPlan']);

        add_action('wiloke-listing-tools/payment-renewed', [$this, 'updateNextBillingDateGMT']);
        add_action('wiloke-listing-tools/changed-payment-status', [$this, 'deletePlanIfIsCancelled']);
        add_action('wiloke-listing-tools/changed-payment-status', [$this, 'updateUserPlanIfSucceededOrActivate']);
        add_action('wiloke-listing-tools/woocommerce/after-order-succeeded',
            [$this, 'setUserPlanOfUserBoughtViaWooCommerce']);
        add_action('wiloke-listing-tools/on-changed-user-plan', [$this, 'changeUserPlan']);
        add_action('woocommerce_subscription_renewal_payment_complete',
            [$this, 'updateUserPlanAfterWooCommerceRenewedSuccessfully']);
        add_filter('get_avatar', [$this, 'wilcityAvatar'], 1, 5);

        // Firebase
        add_action('wp_ajax_wilcity_get_user_short_info', [$this, 'getUserShortInfo']);

        add_action('init', [$this, 'fixUserPlanIssue']);
    }

    public function fixUserPlanIssue()
    {
        if (!is_user_logged_in()) {
            return false;
        }

        $userID = get_current_user_id();
        $fixKey = 'fixed_user_plan_'.$userID;

        if (GetSettings::getOptions($fixKey)) {
            return false;
        }

        $aAllPlans = UserModel::getAllPlans($userID);
        if (empty($aAllPlans)) {
            SetSettings::setOptions($fixKey, true);

            return false;
        }

        SetSettings::setUserMeta($userID, 'backup_userplan', $aAllPlans);

        $aRebuildPlans = [];

        foreach ($aAllPlans as $planType => $aPlans) {
            foreach ($aPlans as $planID => $aPlanInfo) {
                if (!isset($aPlanInfo['postType']) || empty($aPlanInfo['postType'])) {
                    continue;
                }
                $aRebuildPlans[$aPlanInfo['postType'].'_plan'][$planID] = $aPlanInfo;
            }
        }

        if (!empty($aRebuildPlans)) {
            SetSettings::setUserMeta($userID, wilokeListingToolsRepository()->get('user:userPlans'), $aRebuildPlans);
        }

        SetSettings::setOptions($fixKey, true);
    }

    public function getUserShortInfo()
    {
        $oUser = get_user_by('id', $_POST['userID']);

        wp_send_json_success([
            'displayName' => $oUser->display_name,
            'avatar'      => User::getAvatar($oUser->ID)
        ]);
    }

    public function wilcityAvatar($avatar, $id_or_email, $size, $default, $alt)
    {
        if (is_object($id_or_email)) {
            if (!empty($id_or_email->user_id)) {
                $id = (int)$id_or_email->user_id;
            }
        } else if (!is_numeric($id_or_email)) {
            $user = get_user_by('email', $id_or_email);
            if (!empty($user)) {
                $id = $user->user_id;
            }
        } else {
            $id = $id_or_email;
        }

        if (isset($id)) {
            $url = GetSettings::getUserMeta($id, 'avatar');

            if (!empty($url)) {
                $avatar = "<img alt='{$alt}' src='{$url}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
            }
        }

        return $avatar;
    }

    /*
     * @aInfo: $planID, $orderID, $productID, $status
     */
    public function setUserPlanOfUserBoughtViaWooCommerce($aResponse)
    {
        if (!isset($aResponse['planID']) || empty($aResponse['planID'])) {
            return false;
        }
        $aPaymentIDs = PaymentModel::getPaymentIDsByWooOrderID($aResponse['orderID']);

        if (empty($aPaymentIDs)) {
            return false;
        }

        $isTrial = isset($aResponse['isTrial']) ? $aResponse['isTrial'] : false;
        foreach ($aPaymentIDs as $aPaymentID) {
            $aData = $aResponse;

            $aData['userID']      = PaymentModel::getField('userID', $aPaymentID['ID']);
            $aData['billingType'] = PaymentModel::getField('billingType', $aPaymentID['ID']);
            $aData['gateway']     = $aResponse['gateway'];
            $aData['paymentID']   = $aPaymentID['ID'];
            $aData['planID']      = $aResponse['planID'];
            $aData['postID']      = $aResponse['postID'];
            $aData['isTrial']     = $isTrial;

            $this->setUserPlan($aData);
        }
    }

    /*
     * Update User Plan After Next Billing was proceeded
     *
     * @refer https://docs.woocommerce.com/document/subscriptions/develop/action-reference/
     * @since 1.2.0
     */
    public function updateUserPlanAfterWooCommerceRenewedSuccessfully(\WC_Subscription $that)
    {
        $aPaymentIDs = PaymentModel::getPaymentIDsByWooOrderID($that->get_parent_id());
        if (empty($aPaymentIDs)) {
            return false;
        }

        $oOrder = wc_get_order($that->get_parent_id());
        $userID = $oOrder->get_user_id();

        foreach ($aPaymentIDs as $aPayment) {
            $planID        = PaymentModel::getField('planID', $aPayment['ID']);
            $instUserModel = new UserModel();
            $instUserModel->updateNextBillingDateGMT(strtotime($that->get_date('next_payment', 'gmt')), $planID,
                $userID, $aPayment['ID']);
        }
    }

    public function changeUserPlan($aInfo)
    {
        $userID = isset($aInfo['userID']) ? $aInfo['userID'] : User::getCurrentUserID();

        $instUserModel = new UserModel();
        $instUserModel->setUserID($userID)
                      ->setGateway($aInfo['gateway'])
                      ->setPaymentID($aInfo['paymentID'])
                      ->setPlanID($aInfo['planID'])
                      ->setOldPlanID($aInfo['oldPlanID'])
                      ->setPostType($aInfo['listingType'])
                      ->setBillingType($aInfo['billingType'])
        ;

        if (!GetWilokeSubmission::isNonRecurringPayment($aInfo['billingType'])) {
            if (!isset($aInfo['nextBillingDateGMT']) || empty($aInfo['nextBillingDateGMT'])) {
                $aPlanSettings = GetSettings::getPlanSettings($aInfo['planID']);
                $periodDays    = '+1 day';
                if (!empty($aPlanSettings['trial_period'])) {
                    $periodDays = '+'.$aPlanSettings['trial_period'].' day';
                } else if (!empty($aPlanSettings['regular_period'])) {
                    $periodDays = '+'.$aPlanSettings['regular_period'].' day';
                }
                PaymentMetaModel::set($aInfo['paymentID'],
                    wilokeListingToolsRepository()->get('addlisting:nextBillingDateGMT'),
                    Time::timestampUTC($periodDays));
                $instUserModel->setNextBillingDateGMT(Time::timestampUTCNow($periodDays));
            } else {
                $instUserModel->setNextBillingDateGMT($aInfo['nextBillingDateGMT']);
            }
        }

        $this->middleware(['validateBeforeSetUserPlan'], [
            'instUserModel' => $instUserModel,
            'billingType'   => $aInfo['billingType']
        ]);

        $instUserModel->updateUserPlan();
    }

    /*
     * Set user plan
     * @aInfo: $status, $gateway, $billingType, $paymentID, $planID
     */
    public function setUserPlan($aInfo)
    {
        $instUserModel = new UserModel();
        if (isset($aInfo['category']) && $aInfo['category'] == 'promotion') {
            return true;
        }

        if (!isset($aInfo['onChangedPlan']) || ($aInfo['onChangedPlan'] != 'yes')) {
            $userID = isset($aInfo['userID']) ? $aInfo['userID'] : get_current_user_id();
            if (isset($aInfo['listingType']) && !empty($aInfo['listingType'])) {
                $postType = $aInfo['listingType'];
            } else if (isset($aInfo['postID'])) {
                $postType = get_post_type($aInfo['postID']);
            } else {
                $postType = '';
            }

            $instUserModel->setUserID($userID)
                          ->setBillingType($aInfo['billingType'])
                          ->setGateway($aInfo['gateway'])
                          ->setPaymentID($aInfo['paymentID'])
                          ->setPlanID($aInfo['planID'])
                          ->setPostType($postType)
            ;

            if (isset($aInfo['isTrial']) && $aInfo['isTrial']) {
                UserModel::setUsedTrialPlans($aInfo['planID'], $aInfo['userID']);
                $instUserModel->setIsTrial(true);
            }

            if (!GetWilokeSubmission::isNonRecurringPayment($aInfo['billingType'])) {
                if (!isset($aInfo['nextBillingDateGMT']) || empty($aInfo['nextBillingDateGMT'])) {
                    PaymentMetaModel::set($aInfo['paymentID'],
                        wilokeListingToolsRepository()->get('addlisting:nextBillingDateGMT'),
                        Time::timestampUTC('+1 day'));
                    $instUserModel->setNextBillingDateGMT(Time::timestampUTCNow('+1 day'));
                } else {
                    if (is_string($aInfo['nextBillingDateGMT'])) {
                        $nextBillingDate = strtotime($aInfo['nextBillingDateGMT']);
                    } else {
                        $nextBillingDate = $aInfo['nextBillingDateGMT'];
                    }
                    PaymentMetaModel::set($aInfo['paymentID'],
                        wilokeListingToolsRepository()->get('addlisting:nextBillingDateGMT'), $nextBillingDate);
                    $instUserModel->setNextBillingDateGMT($aInfo['nextBillingDateGMT']);
                }
            }

            $this->middleware(['validateBeforeSetUserPlan'], [
                'instUserModel' => $instUserModel,
                'billingType'   => $aInfo['billingType']
            ]);

            $instUserModel->setUserPlan();
        }
    }

    public function updateUserPlanIfSucceededOrActivate($aInfo)
    {
        if ($aInfo['newStatus'] != 'succeeded' && $aInfo['newStatus'] != 'active') {
            return false;
        }

        $aInfo['billingType']        = PaymentModel::getField('billingType', $aInfo['paymentID']);
        $aInfo['userID']             = PaymentModel::getField('userID', $aInfo['paymentID']);
        $aInfo['planID']             = PaymentModel::getField('planID', $aInfo['paymentID']);
        $aInfo['gateway']            = PaymentModel::getField('gateway', $aInfo['paymentID']);
        $aInfo['nextBillingDateGMT'] = PaymentMetaModel::getNextBillingDateGMT($aInfo['paymentID']);

        $this->setUserPlan($aInfo);
    }

    public function deletePlanIfIsCancelled($aInfo)
    {
        if ($aInfo['newStatus'] != 'cancelled' && $aInfo['newStatus'] !== 'cancelled_and_unpublish_listing') {
            return false;
        }

        $planID = PaymentModel::getField('planID', $aInfo['paymentID']);
        $userID = PaymentModel::getField('userID', $aInfo['paymentID']);

        $instUserModel = new UserModel();
        $instUserModel->setUserID($userID);
        $instUserModel->deleteUserPlan($planID);
    }

    /*
     * @aInfo: $nextBillingPayment (timestamp UTC), paymentID
     */
    public function updateNextBillingDateGMT($aInfo)
    {
        $userID = PaymentModel::getField('userID', $aInfo['paymentID']);
        $planID = PaymentModel::getField('planID', $aInfo['paymentID']);

        $instUserModel = new UserModel();
        $instUserModel->updateNextBillingDateGMT($aInfo['nextBillingDateGMT'], $planID, $userID);
    }
}

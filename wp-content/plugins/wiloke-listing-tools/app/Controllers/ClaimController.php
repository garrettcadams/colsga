<?php

namespace WilokeListingTools\Controllers;


use Stripe\Util\Set;
use WilcityPaidClaim\Register\RegisterClaimSubMenu;
use WilokeListingTools\Framework\Helpers\DebugStatus;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Helpers\Submission;
use WilokeListingTools\Framework\Payment\FreePlan\FreePlan;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\UserModel;

class ClaimController extends Controller {
    use SetPlanRelationship;

    protected $planID;
    protected $listingID;
    protected $claimerID;
    protected $claimID;
    protected $aClaimerInfo;
    private static $isPaidClaim = false;

	public function __construct() {
		add_action('wilcity/footer/vue-popup-wrapper', array($this, 'printFooter'));
		add_action('wp_ajax_wilcity_claim_request', array($this, 'handleClaimRequest'));
		add_action('wp_ajax_nopriv_wilcity_claim_request', array($this, 'handleClaimRequest'));
		add_action('updated_post_meta', array($this, 'approvedClaimListing'), 10, 4);
		add_action('updated_post_meta', array($this, 'rejectClaimListing'), 10, 4);

		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX.'/v2', '/listings/(?P<postID>\d+)/fields/claims', array(
				'methods' => 'GET',
				'callback' => array($this, 'getClaimFields')
			));
		});
	}

    public function rejectClaimListing($metaID, $objectID, $metaKey, $metaValue){
	    if ( $metaKey !== 'wilcity_claim_status' || ($metaValue != 'cancelled' && $metaValue != 'pending') ){
		    return false;
	    }

	    if ( !isset($_POST['attribute_post_author']) || empty($_POST['attribute_post_author']) ){
	        wp_die('"Attribute this listing to"" setting is required');
        }
        $author = abs($_POST['attribute_post_author']);
	    $listingID =  GetSettings::getPostMeta($objectID, 'claimed_listing_id');
	    if ( empty($listingID) || empty($author) ){
	        return false;
        }
	    SetSettings::setPostMeta($listingID, 'claim_status', 'not_claim');
	    global $wpdb;
	    $wpdb->update(
            $wpdb->posts,
            array(
                'post_author' => $author
            ),
            array(
                'ID' => $listingID
            ),
            array(
                '%d'
            ),
            array(
                '%d'
            )
        );
	    $claimerID = GetSettings::getPostMeta($objectID, 'claimer_id');
	    do_action('wiloke/claim/'.$metaValue, $claimerID, $listingID);
    }

	/*
	 * When a request has been approved, we will cancelled all other request and switch the post author of this listing
	 */
	public function approvedClaimListing($metaID, $objectID, $metaKey, $metaValue){
		if ( $metaKey !== 'wilcity_claim_status' || $metaValue != 'approved' ){
            return false;
        }
        $this->listingID = GetSettings::getPostMeta($objectID, 'claimed_listing_id');
        $claimerID = GetSettings::getPostMeta($objectID, 'claimer_id');

        $formerlyAuthorID = get_post_field('post_author', $this->listingID);
        SetSettings::setPostMeta($objectID, 'formerly_post_author', $formerlyAuthorID);

        do_action('wiloke/claim/approved', $claimerID, $this->listingID, $objectID);

        wp_update_post(
            array(
                'ID'            => $this->listingID,
                'post_author'   => $claimerID
            )
        );

        SetSettings::setPostMeta($this->listingID, 'claim_status', 'claimed');
    }

    public static function isClaimerExisting($listingID, $claimerID){
	    global $wpdb;
	    $tbl = $wpdb->postmeta;

	    $aAllClaimedPostsByAuthor = $wpdb->get_results(
            $wpdb->prepare(
                    "SELECT post_id FROM $tbl WHERE meta_key=%s AND meta_value=%d",
                'wilcity_claimer_id', $claimerID
            ),
            ARRAY_A
        );

	    if ( empty($aAllClaimedPostsByAuthor) ){
	        return false;
        }

        foreach ($aAllClaimedPostsByAuthor as $aData){
	        $claimedID = GetSettings::getPostMeta($aData['post_id'], 'claimed_listing_id');
	        if ( $claimedID == $listingID ){
	            return $aData['post_id'];
            }
        }
    }

    protected function insertClaim(){
        $oUserInfo = get_user_by('id', $this->claimerID);
        $this->claimID = wp_insert_post(array(
            'post_type'     => 'claim_listing',
            'post_status'   => self::isPaidClaim() ? 'draft' : 'publish',
            'post_title'    => $oUserInfo->user_login . ' '  . esc_html__( 'wants to claim ', 'wiloke-listing-tools') . ' ' . get_the_title($this->listingID)
        ));

        $this->updateClaimSettings();
    }

	protected function updateClaim(){
        $this->claimID = self::isClaimerExisting($this->listingID, $this->claimerID);

        if ( empty($this->claimID) ){
            $this->insertClaim();
        }else{
            $this->updateClaimSettings();
        }
    }

    protected function updateClaimSettings(){
        SetSettings::setPostMeta($this->claimID, 'claimer_id', $this->claimerID);
        SetSettings::setPostMeta($this->claimID, 'claimed_listing_id', $this->listingID);
        SetSettings::setPostMeta($this->claimID, 'claim_status', 'pending');
        SetSettings::setPostMeta($this->claimID, 'claimer_info', $this->aClaimerInfo);
    }

    public static function isPaidClaim(){
        if ( DebugStatus::status('WILCITY_DISABLE_PAID_CLAIM') || !class_exists('WilcityPaidClaim\Register\RegisterClaimSubMenu') ){
	        self::$isPaidClaim = false;
            return false;
        }

        $aOptions = GetSettings::getOptions(RegisterClaimSubMenu::$optionKey);
        if ( empty($aOptions) || !isset($aOptions['toggle']) || $aOptions['toggle'] == 'disable' ){
	        self::$isPaidClaim = false;
            return false;
        }

	    self::$isPaidClaim = true;
        return true;
    }

	public function handleClaimRequest(){
		$aData = $_POST['data'];
		$this->middleware([ 'isUserLoggedIn', 'isListingPostType', 'isClaimAvailable'], array(
			'postID' => $aData['postID']
		));

		if ( get_post_field('post_author', $aData['postID']) == get_current_user_id() ){
		    wp_send_json_error(array(
                'msg' => esc_html__('You are the author of this post already.', 'wiloke-listing-tools')
            ));
        }

		do_action('wiloke-listing-tools/before-handling-claim-request', $aData);

		$aClaimFields = GetSettings::getOptions('claim_settings');
		foreach ($aClaimFields as $key => $aField){
			if ( $aField['key'] == 'claimPackage' ){
				continue;
			}

			if ( $aField['isRequired'] == 'yes' ){
				if ( !isset($aData[$aField['key']]) || empty($aData[$aField['key']]) ){
					wp_send_json_error(
						array(
							'msg' => sprintf(esc_html__('We need your %s.', 'wiloke-listing-tools'), $aField['label'])
						)
					);
				}
			}

			if ( $aField['type'] == 'checkbox' ){
			    $values = $aData[$aField['key']];
				$aValues = array_map('sanitize_text_field', $values);
				$aData['value'] = $aValues;
            }else{
				$aData['value'] = sanitize_text_field($aData[$aField['key']]);
            }
		}

		$this->listingID = $aData['postID'];
		$this->planID = isset($aData['claimPackage']) ? $aData['claimPackage'] : '';
        $this->claimerID = User::getCurrentUserID();
        $this->aClaimerInfo = $aData;
        $this->updateClaim();

        do_action('wilcity/handle-claim-request', array(
            'planID'    => $this->planID,
            'postID'    => $this->listingID,
            'claimerID' => $this->claimerID,
            'claimID'   => $this->claimID,
            'isPaidClaim' => self::isPaidClaim() ? 'yes' : 'no'
        ));

        if ( self::isPaidClaim() ){
	        Session::setSession(wilokeListingToolsRepository()->get('payment:storePlanID'), $this->planID);
	        Session::setSession(wilokeListingToolsRepository()->get('payment:sessionObjectStore'), $this->listingID);
	        Session::setSession(wilokeListingToolsRepository()->get('payment:category'), 'paidClaim');

	        $aUserPlan = UserModel::getSpecifyUserPlanID($this->planID, get_current_user_id(), true);
	        $aInfo = array(
		        'planID'    => $this->planID,
		        'objectID'  => $this->listingID,
		        'userID'    => $this->claimerID
	        );
	        $relationshipID = $this->setPlanRelationship($aUserPlan, $aInfo);

	        if ( !empty($aUserPlan) ) {
		        $this->middleware(['isExceededMaximumListing'], array('aUserPlan'=>$aUserPlan));

	            if ( !empty($aUserPlan['remainingItems']) && (absint($aUserPlan['remainingItems']) > 0) ){

	                $instUserModel = new UserModel();
		            $instUserModel->updateRemainingItemsUserPlan( $this->planID );

		            if ( GetWilokeSubmission::isNonRecurringPayment($aUserPlan['billingType']) ){
			            $aPlanSettings = GetSettings::getPlanSettings($this->planID);
			            $duration = $aPlanSettings['regular_period'];
			            PostController::setExpiration($this->listingID, $duration, false);
		            }else{
			            PostController::setExpiration($this->listingID, $aUserPlan['nextBillingDate'], false);
		            }

		            SetSettings::setPostMeta($this->claimID, 'belongs_to', $this->planID);
		            do_action('wilcity/claim-listing/approved', array(
                        'userID' => get_current_user_id(),
                        'postID' => $this->listingID,
                        'status' => 'succeeded',
                        'claimID'=> $this->claimID
                    ));

		            wp_send_json_success(array(
			            'msg' => esc_html__('Congratulations! Your claim has been approved.', 'wiloke-listing-tools')
		            ));
                }
	        }

            if ( empty($relationshipID) ){
                wp_send_json_error(esc_html__('We could not create a relationship between User Plan and Claim Plan.', 'wiloke-listing-tools'));
            }

            Session::setSession(wilokeListingToolsRepository()->get('payment:storePlanID'), $this->planID);
            Session::setSession(wilokeListingToolsRepository()->get('claim:sessionClaimID'), $this->claimID);

	        $aPlanSettings = GetSettings::getPlanSettings($this->planID);

	        if ( empty($aPlanSettings['regular_price']) ){
		        $oFreePlan = new FreePlan($this->planID);
		        $aStatus = $oFreePlan->proceedPayment();
		        if ( $aStatus['status'] != 'success' ){
			        wp_send_json_error(array(
				        'msg' => esc_html__('ERROR: We could not create Free Plan', 'wiloke-listing-tools')
			        ));
		        }else{
			        do_action('wiloke/free-claim/submitted', $this->claimerID, $this->listingID, $this->planID);
			        wp_send_json_success(array(
				        'msg' => esc_html__('Thanks for your claiming! Our staff will review your request and contact you shortly', 'wiloke-listing-tools')
			        ));
                }
	        }else{
		        $redirectTo = GetWilokeSubmission::getField('checkout', true);
		        $productID  = GetSettings::getPostMeta($this->planID, 'woocommerce_association');
		        if ( !empty($productID) ){
			        $wooCommerceCartUrl = GetSettings::getCartUrl($this->planID);
			        /*
					* @hooked WooCommerceController:removeProductFromCart
					*/
			        do_action('wiloke-listing-tools/before-redirecting-to-cart', $productID);
			        $redirectTo = $wooCommerceCartUrl;
			        Session::setSession(wilokeListingToolsRepository()->get('payment:associateProductID'), $productID);
		        }

		        SetSettings::setPostMeta($this->listingID, 'belongs_to', $this->planID);

		        wp_send_json_success(array(
			        'redirectTo' => add_query_arg(
				        array(
					        'planID' => $this->planID
				        ),
				        $redirectTo
			        )
		        ));
            }
        }else{
            do_action('wiloke/free-claim/submitted', $this->claimerID, $this->listingID, $this->claimID);
            SetSettings::setPostMeta($this->claimID, 'claim_plan_id', GetWilokeSubmission::getFreeClaimPlanID($this->listingID));
	        wp_send_json_success(array(
                'msg' => esc_html__('Thanks for your claiming! Our staff will review your request and contact you shortly', 'wiloke-listing-tools')
            ));
        }
	}

	public function getClaimFields($oData){
	    $postID = $oData->get_param('postID');
	    $postType = get_post_type($postID);
		$aSupportedPostTypes = GetSettings::getFrontendPostTypes(true);

		if ( !in_array($postType, $aSupportedPostTypes) ){
			return array(
                'error' => array(
                    'userMessage' => esc_html__('Oops! There are no claim fields.', 'wiloke-listing-tools'),
                    'code' => 404
                )
            );
		}

		if ( !self::isPaidClaim() ){
		    if ( empty(GetWilokeSubmission::getFreeClaimPlanID($postID)) ){
			    return array(
				    'error' => array(
					    'userMessage' => esc_html__('Please go to Wiloke Submission -> Set a Free Claim Plan of this post type' , 'wiloke-listing-tools'),
					    'code' => 401
				    )
			    );
            }
        }

		$post = get_post($postID);

		$aClaimSettings = GetSettings::getOptions('claim_settings');

		if ( !empty($aClaimSettings) ){
			foreach ($aClaimSettings as $order => $aClaimSetting){
				$aClaimSettings[$order]['label'] = stripslashes($aClaimSetting['label']);
				if ( $aClaimSetting['type'] !== 'radio' && $aClaimSetting['type'] !== 'checkbox' ){
					continue;
				}

				$aOptions = array();
				$aParseOptions = explode(',', $aClaimSetting['options']);
				foreach ($aParseOptions as $key => $val){
					$val = trim($val);
					$aOptions[$key]['label'] = $val;
					$aOptions[$key]['value'] = $val;
				}

				unset($aClaimSettings[$order]['options']);
				$aClaimSettings[$order]['options'] = $aOptions;
			}
		}

		$aClaimSettings = apply_filters('wilcity/claim-field-settings', $aClaimSettings, $post);

		return array('data'=>$aClaimSettings);
    }

	public function printFooter(){
	    if ( !is_singular(General::getPostTypeKeys(false, true)) ){
	        return '';
        }
		?>
		<claim-popup ref="oClaimPopup"></claim-popup>
		<?php
	}
}

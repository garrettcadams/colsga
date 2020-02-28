<?php

namespace WilokeListingTools\Controllers;

use WilokeListingTools\Framework\Helpers\FileSystem;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Register\WilokeSubmissionConfiguration;

trait PrintAddListingSettings
{
    protected function addAdditionalToggle()
    {
        if ( !isset($this->aPlanSettings['toggle_coupon']) || $this->aPlanSettings['toggle_coupon'] == 'enable') {
            return true;
        }

        $aCouponFields = array(
            'coupon_title',
            'coupon_highlight',
            'coupon_popup_image',
            'coupon_description',
            'coupon_description',
            'coupon_code',
            'coupon_popup_description',
            'coupon_redirect_to'
        );

        foreach ($aCouponFields as $key) {
            $this->aPlanSettings[ 'toggle_'.$key ] = 'disable';
        }
    }

    protected function isDisableOnPlan($key)
    {
        if ($key == 'video') {
            $key = 'videos';
        }

        return isset($this->aPlanSettings[ 'toggle_'.$key ]) && $this->aPlanSettings[ 'toggle_'.$key ] == 'disable';
    }

    protected function removeAllUncheckedSection($aSections)
    {
        global $wiloke;
        $isRemoveField = isset($wiloke->aThemeOptions['addlisting_unchecked_features_type']) && $wiloke->aThemeOptions['addlisting_unchecked_features_type'] == 'hidden';
        foreach ($aSections as $sectionKey => $aSection) {
            $key = $aSection['key'];

            if ($key == 'video') {
                $key = $aSection['key'].'s';
            }

            if ($this->isDisableOnPlan($key)) {
                if ($isRemoveField) {
                    unset($aSections[ $sectionKey ]);
                    continue;
                }
            }

            if (isset($aSection['fields'])) {
                foreach ($aSection['fields'] as $fieldKey => $aField) {
                    if ((isset($aField['toggle']) && $aField['toggle'] == 'disable') || ($isRemoveField && $isRemoveField && $this->isDisableOnPlan($fieldKey))) {
                        unset($aSections[ $sectionKey ]['fields'][ $fieldKey ]);
                    } else if (isset($aField['isRequired']) && $aField['isRequired'] == 'no') {
                        unset($aSections[ $sectionKey ]['fields'][ $fieldKey ]['isRequired']);
                    }
                }
            }
        }

        return $aSections;
    }

    protected function excludeSocialNetworks()
    {
        if (isset($_GET['listing_type']) && !empty($_GET['listing_type'])) {
//			$aListingSettings = GetSettings::getOptions();
            $aListingSettings = GetSettings::getOptions(General::getUsedSectionKey($_GET['listing_type']));
        }
    }

    private function buildAddListingCache($aInfo, $postType)
    {
        $cacheKey = 'addlisting-cache-at-'.$postType;
        $fileName = 'addlisting-'.$postType.'.js';
        if (FileSystem::isFileExists($fileName)) {
            $currentVersion = GetSettings::getOptions('addlisting-global-version');
            if (version_compare($currentVersion, WILOKE_LISTING_TOOL_VERSION, '>=')) {
                $cachedAt = GetSettings::getOptions($cacheKey);
                $savedAt  = GetSettings::getOptions(General::getUsedSectionSavedAt($postType, true));

                if ($cachedAt > $savedAt) {
                    wp_enqueue_script('addlisting-'.$postType, FileSystem::getFileURI($fileName), array(), WILOKE_LISTING_TOOL_VERSION.$savedAt, false);

                    return true;
                }
            }
        }

        $status = FileSystem::filePutContents($fileName, '/* <![CDATA[ */ window.WILCITY_ADDLISTING='.json_encode($aInfo, JSON_UNESCAPED_UNICODE).'; /* ]]> */');
        if ($status) {
            SetSettings::setOptions('addlisting-global-version', WILOKE_LISTING_TOOL_VERSION);
            SetSettings::setOptions($cacheKey, current_time('timestamp', 1));
        }
        wp_localize_script('jquery-migrate', 'WILCITY_ADDLISTING', $aInfo);
    }

    public function printAddListingSettings()
    {
        global $post;
        if ($post != null && GetWilokeSubmission::getField('addlisting') != $post->ID && empty(Session::getSession(wilokeListingToolsRepository()->get('payment:storePlanID')))) {
            return false;
        }

        $this->listingID = isset($_REQUEST['postID']) && !empty($_REQUEST['postID']) ? absint($_REQUEST['postID']) : '';
        if ( !empty($this->listingID)) {
            $listingType = get_post_type($this->listingID);
        } else {
            $listingType = isset($_REQUEST['listing_type']) ? esc_js($_REQUEST['listing_type']) : 'listing';
        }
        $aInfo = array('listingType' => $listingType);

        if (isset($_REQUEST['planID']) && !empty($_REQUEST['planID'])) {
            $planID              = $_REQUEST['planID'];
            $this->aPlanSettings = GetSettings::getPostMeta($planID, 'add_listing_plan');
        } else {
            $planID              = '';
            $this->aPlanSettings = array();

            if (GetWilokeSubmission::isFreeAddListing()) {
                $aPlans = GetWilokeSubmission::getAddListingPlans($listingType.'_plans');
                if (is_array($aPlans)) {
                    $planID              = end($aPlans);
                    $this->aPlanSettings = GetSettings::getPostMeta($planID, 'add_listing_plan');
                }
            }
        }

        if (isset($post->ID) && !General::isElementorPreview()) {
            $this->aSections = $this->getAvailableFields();
            $this->addAdditionalToggle();
            $this->aSections = $this->removeAllUncheckedSection($this->aSections);
            $this->mergeSettingValues();
            $aInfo = array_merge($aInfo, array(
                'oSocialNetworks' => class_exists('\WilokeSocialNetworks') ? \WilokeSocialNetworks::$aSocialNetworks : array(),
                'aAllSections'    => wilokeListingToolsRepository()->get('settings:allSections'),
                'aPriceRange'     => wilokeListingToolsRepository()->get('addlisting:aPriceRange'),
                'oDayOfWeek'      => wilokeListingToolsRepository()->get('general:aDayOfWeek'),
                'oTimeRange'      => wilokeListingToolsRepository()->get('addlisting:aTimeRange'),
                'oTimeFormats'    => array(
                    array(
                        'value' => 12,
                        'name'  => esc_html__('12-Hour Format', 'wiloke-listing-tools'),
                    ),
                    array(
                        'value' => 24,
                        'name'  => esc_html__('24-Hour Format', 'wiloke-listing-tools'),
                    )
                ),
                'oBusinessHours'  => General::generateBusinessHours(),
            ));
        }
        wp_localize_script('jquery-migrate', strtoupper(WILCITY_WHITE_LABEL).'_ADDLISTING_INLINE', array(
            'planID'                => $planID,
            'listingID'             => $this->listingID,
            'wilcityAddListingCsrf' => esc_js(wp_create_nonce('wilcity-submit-listing')),
            'aPlanSettings'         => $this->aPlanSettings,
            'listingType'           => $aInfo['listingType'],
            'aUsedSections'         => $this->aSections
        ));
        $this->buildAddListingCache($aInfo, $listingType);
    }
}

<?php

namespace WilokeListingTools\Controllers;

use WilokeListingTools\Framework\Helpers\FileSystem;
use WilokeListingTools\Framework\Helpers\Firebase;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Frontend\SingleListing;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Models\UserModel;
use WilokeListingTools\Register\WilokeSubmission;

class OptimizeScripts extends Controller
{
    private $rawTranslation;
    private $rawGlobal;

    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'singleEnqueueScripts']);
        add_action('wp_enqueue_scripts', [$this, 'addCustomScripts'], 998);
        add_action('updated_option', [$this, 'rebuildCacheFiles'], 10);
        add_action('wp_head', [$this, 'inlineScripts'], 1);
        add_action('wp_print_styles', [$this, 'removeScripts'], 999);
    }

    public function removeScripts()
    {
        // Removing jquery smooth on Search Page to resolve conflict Event Calendar style issue
        if ((function_exists('wilcityIsSearchPage') && wilcityIsSearchPage()) || is_front_page()) {
            wp_dequeue_style('jquery-ui-style');
        }
    }

    private function restrictAdminOnlyItem($aItems)
    {
        if (!empty($aItems)) {
            if (!current_user_can('administrator')) {
                $aItems = array_filter($aItems, function ($aItem) {
                    if (in_array($aItem['key'],
                            SingleListing::getAdminOnly()) || (isset($aItem['adminOnly']) && $aItem['adminOnly'] == 'yes')
                    ) {
                        return false;
                    }

                    return true;
                });
            }
        }

        return $aItems;
    }

    public function inlineScripts()
    {
           global $wiloke;
           $mapTheme = isset($wiloke->aThemeOptions['map_theme']) ? esc_js($wiloke->aThemeOptions['map_theme']) : 'blurWater';
           if ( $mapTheme == 'custom' ):
               $theme = isset($wiloke->aThemeOptions['map_custom_theme']) && !empty($wiloke->aThemeOptions['map_custom_theme']) ? $wiloke->aThemeOptions['map_custom_theme'] : '[]';
           ?>
               <script style="text/javascript">
                   window.WILCITY_CUSTOM_MAP = <?php echo $theme; ?>;
               </script>
           <?php
           endif;
           ?>
           <script>
               window.webpack_public_path__ = "<?php echo trailingslashit(get_template_directory_uri() . '/assets/production/js'); ?>"
               window.WHITE_LABEL = "<?php echo esc_attr(WILCITY_WHITE_LABEL); ?>"
           </script>
           <?php
       }

    public function singleEnqueueScripts()
    {
        global $post;
        $aSupportedPostTypes = GetSettings::getAllDirectoryTypes(true);

        if (!is_singular($aSupportedPostTypes)) {
            return false;
        }

        $isUsedDefaultSidebar     = GetSettings::getPostMeta($post->ID,
            wilokeListingToolsRepository()->get('listing-settings:keys', true)->sub('isUsedDefaultSidebar'));
        $isUsedDefaultNav         = GetSettings::getPostMeta($post->ID,
            wilokeListingToolsRepository()->get('listing-settings:keys', true)->sub('isUsedDefaultNav'));
        $aNavigation['draggable'] = SingleListing::getNavOrder();
        $aNavigation['draggable'] = $this->restrictAdminOnlyItem($aNavigation['draggable']);
        $aNavigation['fixed']     = wilokeListingToolsRepository()->get('listing-settings:navigation',
            true)->sub('fixed')
        ;
        $aSidebarItem             = SingleListing::getSidebarOrder();

        if (!empty($aSidebarItem)) {
            if (!current_user_can('administrator')) {
                $aSidebarItem = array_filter($aSidebarItem, function ($aSidebarItem) {
                    if (in_array($aSidebarItem['key'],
                            SingleListing::getAdminOnly()) || (isset($aSidebarItem['adminOnly']) && $aSidebarItem['adminOnly'] == 'yes')
                    ) {
                        return false;
                    }

                    return true;
                });
            }
        }

        $isExternalSingleListing = false;
        if (FileSystem::isFileExists(WILCITY_WHITE_LABEL.'-single-listing-settings.js')) {
            $cacheVersion = GetSettings::getOptions('single_listing_settings_ver');
            if (version_compare($cacheVersion, WILOKE_LISTING_TOOL_VERSION, '>=')) {
                wp_enqueue_script(WILCITY_WHITE_LABEL.'-single-listing-settings.js',
                    FileSystem::getFileURI(WILCITY_WHITE_LABEL.'-single-listing-settings.js'), ['jquery-migrate'],
                    $cacheVersion, false);
                $isExternalSingleListing = true;
            }
        }

        if (!$isExternalSingleListing) {
            $internalSingleListingSettings = FileSystem::filePutContents(WILCITY_WHITE_LABEL.'-single-listing-settings.js',
                '/* <![CDATA[ */ window.'.$this->getDefineName('WILCITY_',
                    'SINGLE_LISTING').'='.json_encode(wilokeListingToolsRepository()->get('listing-settings')).'; /* ]]> */');
            if ($internalSingleListingSettings) {
                SetSettings::setOptions('single_listing_settings_ver', WILOKE_LISTING_TOOL_VERSION);
                wp_enqueue_script(WILCITY_WHITE_LABEL.'-single-listing-settings.js',
                    FileSystem::getFileURI(WILCITY_WHITE_LABEL.'-single-listing-settings.js'), ['jquery-migrate'],
                    WILOKE_LISTING_TOOL_VERSION, false);
                $isExternalSingleListing = true;
            }
        }

        if (!$isExternalSingleListing) {
            wp_localize_script('jquery-migrate', $this->getDefineName('WILCITY_', 'SINGLE_LISTING'),
                wilokeListingToolsRepository()->get('listing-settings'));
        }

        wp_localize_script('jquery-migrate', $this->getDefineName('WILCITY_', 'SINGLE_LISTING_SETTINGS'), [
            'navigation'      => [
                'isUsedDefaultNav' => empty($isUsedDefaultNav) ? 'yes' : $isUsedDefaultNav,
                'items'            => $aNavigation
            ],
            'general'         => GetSettings::getPostMeta($post->ID,
                wilokeListingToolsRepository()->get('listing-settings:keys', true)->sub('general')),
            'sidebar'         => [
                'settings'             => $aSidebarItem,
                'isUsedDefaultSidebar' => empty($isUsedDefaultSidebar) ? 'yes' : $isUsedDefaultSidebar,
            ],
            'aDefaultNavKeys' => SingleListing::getDefaultNavKeys()
        ]);
    }

    private function getDefineName($prefix, $name)
    {
        $prefixToLower = strtolower($prefix);

        if ($prefixToLower == 'wiloke_') {
            return strtoupper(WILOKE_WHITE_LABEL).'_'.$name;
        } else if ($prefixToLower == 'wilcity_') {
            return strtoupper(WILCITY_WHITE_LABEL).'_'.$name;
        }

        return $prefix.$name;
    }

    private function buildScriptCode($isFocus = false)
    {
        global $wiloke, $post;
        if ($isFocus) {
            $aThemeOptions = \Wiloke::getThemeOptions(true);
        } else {
            $aThemeOptions = $wiloke->aThemeOptions;
        }

        $mapTheme = isset($aThemeOptions['map_theme']) ? esc_js($aThemeOptions['map_theme']) : 'blurWater';

        $role                        = '';
        $defaultPostType             = 'listing';
        $postTypeDefaultExcerptEvent = 'listing';
        if (class_exists('\WilokeListingTools\Framework\Helpers\GetSettings')) {
            $aDefaultPostTypes           = GetSettings::getFrontendPostTypes(true);
            $defaultPostType             = $aDefaultPostTypes[0];
            $postTypeDefaultExcerptEvent = $defaultPostType == 'event' ? $aDefaultPostTypes[1] : $defaultPostType;
        }

        $mapboxStyle = \WilokeThemeOptions::getOptionDetail('mapbox_style');
        $mapboxStyle = empty($mapboxStyle) ? 'mapbox://styles/mapbox/streets-v9' : $mapboxStyle;

        $aGlobal = [
            'homeURL'                     => home_url('/'),
            'restAPI'                     => rest_url(WILOKE_PREFIX.'/v2/'),
            'dateFormat'                  => get_option('date_format'),
            'uploadType'                  => isset($aThemeOptions['addlisting_upload_img_via']) ? $aThemeOptions['addlisting_upload_img_via'] : '',
            'maxUpload'                   => (int)(ini_get('upload_max_filesize')) * 1024,
            'ajaxurl'                     => esc_url(admin_url('admin-ajax.php')),
            'isUseMapBound'               => 'no',
            'hasGoogleAPI'                => isset($aThemeOptions['general_google_api']) && !empty($aThemeOptions['general_google_api']) ? 'yes' : 'no',
            'mapCenter'                   => isset($aThemeOptions['map_center']) ? $aThemeOptions['map_center'] : '',
            'mapMaxZoom'                  => isset($aThemeOptions['map_max_zoom']) ? abs($aThemeOptions['map_max_zoom']) : 7,
            'mapMinZoom'                  => isset($aThemeOptions['map_minimum_zoom']) ? abs($aThemeOptions['map_minimum_zoom']) : 1,
            'mapDefaultZoom'              => isset($aThemeOptions['map_default_zoom']) ? abs($aThemeOptions['map_default_zoom']) : 4,
            'mapTheme'                    => esc_js($mapTheme),
            'mapLanguage'                 => isset($aThemeOptions['general_google_language']) ? trim($aThemeOptions['general_google_language']) : '',
            'mapboxStyle'                 => esc_js($mapboxStyle),
            'isAddingListing'             => !class_exists('\WilokeListingTools\Framework\Store\Session') || empty(Session::getSession(wilokeListingToolsRepository()->get('payment:storePlanID'))) ? 'no' : 'yes',
            'aUsedSocialNetworks'         => \WilokeSocialNetworks::getUsedSocialNetworks(),
            'isPaidClaim'                 => !class_exists('\WilokeListingTools\Controllers\ClaimController') || !\WilokeListingTools\Controllers\ClaimController::isPaidClaim() ? 'no' : 'yes',
            'datePickerFormat'            => apply_filters('wilcity_date_picker_format', 'mm/dd/yy'),
            'defaultPostType'             => $defaultPostType,
            'defaultPostTypeExcerptEvent' => $postTypeDefaultExcerptEvent,
            'isUploadImgViaAjax'          => defined('WILCITY_BETA_UPLOAD_IMG_VIA_AJAX') || $aThemeOptions['addlisting_upload_img_via'] == 'ajax' ? 'yes' : 'no',
            'oFirebaseConfiguration'      => class_exists('WilokeListingTools\Framework\Helpers\Firebase') && Firebase::isFirebaseEnable() ? Firebase::getFirebaseChatConfiguration() : '',
            'localeCode'                  => isset($aThemeOptions['general_locale_code']) && !empty($aThemeOptions['general_locale_code']) ? esc_attr($aThemeOptions['general_locale_code']) : 'en-US'
        ];

        // Map
        $aGlobal['mapType'] = \WilokeThemeOptions::getOptionDetail('map_type');
        if ($aGlobal['mapType'] == 'mapbox') {
            $aGlobal['mapAPI']      = $aThemeOptions['mapbox_api'];
            $aGlobal['mapSizeIcon'] = isset($aThemeOptions['mapbox_iconsize']) ? $aThemeOptions['mapbox_iconsize'] : '';
        }

        $_SESSION['fbCSRF'] = wp_create_nonce('fbCSRF');

        $aInlineGlobal = [
            'security'        => wp_create_nonce('wilSecurity'),
            'language'        => General::getCurrentLanguage(),
            'postID'          => is_single() ? esc_js($post->ID) : '',
            'productionURL'   => trailingslashit(get_template_directory_uri().'/assets/production/js'),
            'unit'            => \WilokeThemeOptions::getOptionDetail('unit_of_distance'),
            'fbState'         => $_SESSION['fbCSRF'],
            'homeURL'         => home_url('/'),

            'isUsingFirebase' => class_exists('WilokeListingTools\Framework\Helpers\Firebase') && Firebase::isFirebaseEnable() ? 'yes' : 'no'
        ];

        if (is_user_logged_in()) {
            $oUserMeta = get_userdata(get_current_user_id());
            $role      = isset($oUserMeta->roles[0]) ? $oUserMeta->roles[0] : '';
            if (method_exists('\WilokeListingTools\Register\WilokeSubmission',
                    'isDashboard') && WilokeSubmission::isDashboard()
            ) {
                $aExternalLinks = DashboardController::getNavigation();

                if (!empty($aExternalLinks)) {
                    $aExternalLinks = array_filter($aExternalLinks, function ($aNav) {
                        return isset($aNav['redirect']) && !empty($aNav['redirect']);
                    });

                    $aInlineGlobal['oDashboardExternalLinks'] = $aExternalLinks;
                }

                $toggleDeleteAccount = isset($aThemeOptions['toggle_allow_customer_delete_account']) && $aThemeOptions['toggle_allow_customer_delete_account'] == 'enable';

                if ($toggleDeleteAccount) {
                    $aGlobal['oDashboardDeleteAccount'] = [
                        'warning' => $aThemeOptions['customer_delete_account_warning']
                    ];
                }

                $aGlobal['chartColor'] = esc_attr(apply_filters('wilcity/filter/chart-color', '#f06292'));
            }
        }

        $aInlineGlobal['userRole'] = $role;

        if (\WilokeThemeOptions::isEnable('map_bound_toggle')) {
            $aGlobal['isUseMapBound'] = 'yes';
            $aGlobal['mapBoundStart'] = $aThemeOptions['map_bound_start'];
            $aGlobal['mapBoundEnd']   = $aThemeOptions['map_bound_end'];
        }

        if (class_exists('\WilokeListingTools\Framework\Helpers\General')) {
            $aGlobal['oSingleMap']['maxZoom']     = isset($aThemeOptions['single_map_max_zoom']) && !empty($aThemeOptions['single_map_max_zoom']) ? $aThemeOptions['single_map_max_zoom'] : 21;
            $aGlobal['oSingleMap']['minZoom']     = isset($aThemeOptions['single_map_minimum_zoom']) && !empty($aThemeOptions['single_map_minimum_zoom']) ? $aThemeOptions['single_map_minimum_zoom'] : 21;
            $aGlobal['oSingleMap']['defaultZoom'] = isset($aThemeOptions['single_map_default_zoom']) && !empty($aThemeOptions['single_map_default_zoom']) ? $aThemeOptions['single_map_default_zoom'] : 21;
        }

        $aInlineGlobal['datePickerFormat'] = apply_filters('wilcity_date_picker_format', 'mm/dd/yy');

        if (class_exists('WilokeListingTools\Frontend\User')) {
            if (is_user_logged_in()) {
                $userID                          = get_current_user_id();
                $aUser['displayName']            = User::getField('display_name', $userID);
                $aUser['avatar']                 = User::getAvatar($userID);
                $aUser['position']               = User::getPosition($userID);
                $aInlineGlobal['user']           = $aUser;
                $aInlineGlobal['isUserLoggedIn'] = 'yes';
                $aInlineGlobal['userID']         = abs($userID);
            } else {
                $aInlineGlobal['isUserLoggedIn'] = 'no';
                $aInlineGlobal['canRegister']    = RegisterLoginController::canRegister() ? 'yes' : 'no';
            }
        }
        if (!is_user_logged_in() || $isFocus) {
            if (isset($aThemeOptions['toggle_google_recaptcha']) && $aThemeOptions['toggle_google_recaptcha'] == 'enable') {
                $aGlobal['oGoogleReCaptcha']['siteKey'] = $aThemeOptions['recaptcha_site_key'];
                $aGlobal['oGoogleReCaptcha']['on']      = $aThemeOptions['using_google_recaptcha_on'];
            }

            $aGlobal['oFacebook'] = [
                'API'    => $aThemeOptions['fb_api_id'],
                'toggle' => \WilokeThemeOptions::isEnable('fb_toggle_login') ? 'yes' : 'no'
            ];
        }

        if (class_exists('WilokeSocialNetworks') && class_exists('WilokeListingTools\Frontend\User') && User::canSubmitListing()) {
            $aGlobal['oSocialNetworks'] = \WilokeSocialNetworks::$aSocialNetworks;
        }

        if (isset($aThemeOptions['search_country_restriction']) && !empty($aThemeOptions['search_country_restriction'])) {
            $aGlobal['countryRestriction'] = $aThemeOptions['search_country_restriction'];
        }

        if (isset($aThemeOptions['general_search_restriction']) && !empty($aThemeOptions['general_search_restriction'])) {
            $aGlobal['searchCountryRestriction'] = $aThemeOptions['general_search_restriction'];
        }

        $aTranslation  = GetSettings::getTranslation();
        $promotionDesc = GetSettings::getOptions('promotion_description');
        if (!empty($promotionDesc)) {
            $aTranslation['selectAdsDesc'] = stripslashes($promotionDesc);
        }

        return [$aTranslation, $aGlobal, $aInlineGlobal];
    }

    private function encodeData($aData)
    {
        list($aTranslation, $aGlobal, $aInlineGlobal) = $aData;
        $this->rawGlobal      = $aGlobal;
        $this->rawTranslation = $aTranslation;

        $globalJson      = json_encode($aGlobal, JSON_UNESCAPED_UNICODE);
        $translationJson = json_encode($aTranslation, JSON_UNESCAPED_UNICODE);

        return [
            'global'      => !empty($globalJson) ? $globalJson : '',
            'translation' => !empty($translationJson) ? $translationJson : '',
            'inline'      => $aInlineGlobal
        ];
    }

    public function addCustomScripts()
    {
        if (!defined('WILOKE_THEME_URI')) {
            return false;
        }

        $globalVersion = GetSettings::getOptions('global_js_version');
        $jsURL         = WILOKE_THEME_URI.'assets/production/js/';
        $aBuiltScripts = $this->rebuildCacheFiles('wiloke_themeoptions', false);

        if (empty($globalVersion) || $globalVersion !== WILOKE_THEMEVERSION || (defined('WILCITY_FOCUS_REBUILD_FILE') && WILCITY_FOCUS_REBUILD_FILE)) {
            $this->rebuildCacheFiles('wiloke_themeoptions', true);
            if (FileSystem::isFileExists(WILCITY_WHITE_LABEL.'-global.js')) {
                SetSettings::setOptions('global_js_version', WILOKE_THEMEVERSION);
            }
        }

        $individualVersion = GetSettings::getOptions('individual_scripts_version');
        $version           = !empty($individualVersion) ? WILOKE_THEMEVERSION.'_'.$individualVersion : WILOKE_THEMEVERSION;

        wp_register_script(WILCITY_WHITE_LABEL.'-empty', $jsURL.'activeListItem.min.js', ['jquery'],
            WILOKE_THEMEVERSION, false);
        wp_enqueue_script(WILCITY_WHITE_LABEL.'-empty');

        if (defined('WILCITY_FOCUS_INLINE') || GetSettings::getOptions('wilcity_global_error') == 'yes' || General::isElementorPreview()) {
            wp_localize_script(WILCITY_WHITE_LABEL.'-empty', $this->getDefineName('WILOKE_', 'GLOBAL'),
                $this->rawGlobal);
        } else {
            wp_enqueue_script(WILCITY_WHITE_LABEL.'-global', FileSystem::getFileURI(WILCITY_WHITE_LABEL.'-global.js'),
                [], $version);
        }
        
        if (defined('WILCITY_FOCUS_INLINE') || GetSettings::getOptions('wilcity_i18_error') == 'yes' || General::isElementorPreview()) {
            wp_localize_script(WILCITY_WHITE_LABEL.'-empty', $this->getDefineName('WILCITY_', 'I18'),
                $this->rawTranslation);
        } else {
            wp_enqueue_script(WILCITY_WHITE_LABEL.'-i18', FileSystem::getFileURI(WILCITY_WHITE_LABEL.'-i18.js'),
                [], $version);
        }

        wp_localize_script(WILCITY_WHITE_LABEL.'-empty', $this->getDefineName('WILOKE_', 'INLINE_GLOBAL'),
            $aBuiltScripts['inline']);

        wp_localize_script('jquery-migrate', $this->getDefineName('WILCITY_', 'GLOBAL'), [
            'oStripe'  => [
                'publishableKey' => GetWilokeSubmission::getField('stripe_publishable_key'),
                'hasCustomerID'  => UserModel::getStripeID() ? 'yes' : 'no'
            ],
            'oGeneral' => [
                'brandName' => GetWilokeSubmission::getField('brandname')
            ]
        ]);

        do_action('wilcity/wiloke-listing-tools/after-added-addCustomScripts');
    }

    public function rebuildCacheFiles($options, $isRebuildCacheFile = true)
    {
        if ($options != 'wiloke_themeoptions' && $options != 'wiloke_themeoptions-transients' && $options != 'firebase_chat_configuration') {
            return false;
        }

        $aBuiltScripts = $this->buildScriptCode(true);
        $aEncodeJson   = $this->encodeData($aBuiltScripts);

        if ($isRebuildCacheFile) {
            if (empty($aEncodeJson['global'])) {
                SetSettings::setOptions('wilcity_global_error', 'yes');
            } else {
                $globalFile = FileSystem::filePutContents(WILCITY_WHITE_LABEL.'-global.js',
                    '/* <![CDATA[ */ window.'.$this->getDefineName('WILOKE_',
                        'GLOBAL').'='.$aEncodeJson['global'].'; /* ]]> */');
                if ($globalFile) {
                    SetSettings::setOptions('individual_scripts_version', time());
                    SetSettings::deleteOption('wilcity_global_error');
                } else {
                    SetSettings::setOptions('wilcity_global_error', 'yes');
                }
            }

            if (empty($aEncodeJson['translation'])) {
                SetSettings::setOptions('wilcity_i18_error', 'yes');
            } else {
                $translationFile = FileSystem::filePutContents(WILCITY_WHITE_LABEL.'-i18.js',
                    '/* <![CDATA[ */ window.'.$this->getDefineName('WILCITY_',
                        'I18').'='.$aEncodeJson['translation'].'; /* ]]> */');

                if ($translationFile) {
                    SetSettings::setOptions('individual_scripts_version', time());
                    SetSettings::deleteOption('wilcity_i18_error');
                } else {
                    SetSettings::setOptions('wilcity_i18_error', 'yes');
                }
            }
        }

        return $aEncodeJson;
    }
}

<?php
namespace WILCITY_APP\Controllers;

use Stripe\Util\Set;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\SetSettings;

class HomeController
{
    use JsonSkeleton;
    private $isBuildingApp = false;
    
    public function __construct()
    {
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', '/homepage-sections', [
                'methods'  => 'GET',
                'callback' => [$this, 'homepageSections'],
            ]);
        });
        
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', '/homepage-section-detail/(?P<id>\w+)', [
                'methods'  => 'GET',
                'callback' => [$this, 'homepageSectionDetails'],
            ]);
        });
        
        add_action('admin_enqueue_scripts', [$this, 'pageEnqueueScripts']);
        add_action('trashed_post', [$this, 'updateHomePageCache'], 10, 1);
        add_action('after_delete_post', [$this, 'updateHomePageCache'], 10, 1);
        add_filter('wilcity/mobile/render_slider_sc', [$this, 'getSliderSC'], 10, 2);
        add_filter('wilcity/mobile/render_listings_on_mobile', [$this, 'getListingsOnMobile'], 10, 2);
        add_action('wilcity/mobile/update-cache', [$this, 'reUpdateAppCache']);
        add_action('update_option', [$this, 'updateHomePageAfterSavingThemeOptions']);
        add_action('updated_postmeta', [$this, 'updateHomePageAfterUpdatingMetaData'], 10, 3);
        add_action('edited_term', [$this, 'flushCacheAfterUpdatingSite']);
        add_action('post_updated', [$this, 'rebuildHomePageAfterPostUpdated'], 10, 2);
        add_action('wp_insert_post', [$this, 'rebuildHomePageAfterPostAdded'], 10, 3);
    }
    
    private function verifyRebuildHomePage($oPost)
    {
        $aListingPostTypes   = General::getPostTypeKeys(true, false);
        $aListingPostTypes[] = 'review';
        $aListingPostTypes[] = 'discussion';
        
        $aListingPostTypes =
            apply_filters('wilcity/wilcity-mobile-app/filter/rebuildHomePageAfterPostUpdated/allowedPostTypes',
                $aListingPostTypes);
        if ($oPost->post_status != 'publish' || !in_array($oPost->post_type, $aListingPostTypes)) {
            return false;
        }
        
        return true;
    }
    
    private function rebuildHomePageApp($postID)
    {
        $mobilePageID = $this->getOptionField('mobile_app_page');
        $isFocus      = false;
        $content      = '';
        if ($postID == $mobilePageID) {
            $isFocus = true;
            $content = get_post_field('post_content', $postID);
        }
        
        $this->proceedSaveAppCache($mobilePageID, $isFocus, $content);
    }
    
    public function rebuildHomePageAfterPostAdded($postID, $post, $update)
    {
        if (!$this->verifyRebuildHomePage($post)) {
            return false;
        }
        
        $this->rebuildHomePageApp($postID);
    }
    
    public function rebuildHomePageAfterPostUpdated($postID, $oPostAfter)
    {
        if (!$this->verifyRebuildHomePage($oPostAfter)) {
            return false;
        }
        
        $this->rebuildHomePageApp($postID);
    }
    
    public function updateHomePageAfterUpdatingMetaData($metaID, $objectID, $metaKey)
    {
        if (!current_user_can('administrator')) {
            return false;
        }
        if (strpos($metaKey, 'wilcity_') === false) {
            return false;
        }
        $mobilePageID = $this->getOptionField('mobile_app_page');
        if (empty($mobilePageID)) {
            return false;
        }
        $this->proceedSaveAppCache($mobilePageID);
    }
    
    public function getListingsOnMobile($atts, $post)
    {
        $aListing = $this->listingSkeleton($post, ['oGallery', 'oSocialNetworks', 'oVideos', 'oNavigation'], $atts);
        return $aListing;
    }
    
    public function getSliderSC($atts, \WP_Query $query)
    {
        $aResponse = [];
        while ($query->have_posts()) {
            $query->the_post();
            $aPost       = $this->listingSkeleton($query->post);
            $aNavAndHome = $this->getNavigationAndHome($query->post);
            $aResponse[] = $aPost + $aNavAndHome;
        }
        
        return $aResponse;
    }
    
    private function proceedSaveAppCache($postID, $isFocus = false, $content = null)
    {
        if ($this->isBuildingApp && !$isFocus) {
            return false;
        }
        
        $rawContent = empty($content) ? get_post_field('post_content_filtered', $postID) : $content;
        if (empty($rawContent)) {
            return false;
        }
        
        $this->isBuildingApp = true;
        $compliedSC          = do_shortcode($rawContent);
        $aParseContent       = explode('%SC%', $compliedSC);
        $aSectionsSettings   = [];
        $aSectionIDs         = [];
        foreach ($aParseContent as $sc) {
            $id = uniqid('section_');
            $sc = trim($sc);
            $sc = wp_kses($sc, []);
            
            if (!empty($sc)) {
                $aParseSC               = json_decode($sc, true);
                $aSectionIDs[$id]       = $aParseSC['TYPE'];
                $aSectionsSettings[$id] = base64_encode($sc);
            }
        }
        
        $aSettings = apply_filters('wilcity/wilcity-mobile-app/before-save-homepage-sections', [
            'aSectionKeys'      => $aSectionIDs,
            'aSectionsSettings' => $aSectionsSettings
        ]);
        SetSettings::setOptions('app_homepage', json_encode($aSettings['aSectionsSettings']));
        SetSettings::setOptions('app_homepage_section', $aSettings['aSectionKeys']);
        SetSettings::setOptions('app_homepage_id', $postID);
        SetSettings::setOptions('app_homepage_last_cache', current_time('timestamp', 1));
    }
    
    public function flushCacheAfterUpdatingSite()
    {
        $mobilePageID = $this->getOptionField('mobile_app_page');
        if (empty($mobilePageID)) {
            return false;
        }
        $this->proceedSaveAppCache($mobilePageID);
    }
    
    public function updateHomePageCache($postID)
    {
        $mobilePageID = $this->getOptionField('mobile_app_page');
        if (empty($mobilePageID) || $mobilePageID == $postID) {
            return false;
        }
        $this->proceedSaveAppCache($mobilePageID);
    }
    
    public function updateHomePageAfterSavingThemeOptions($option)
    {
        if ($option != 'wiloke_themeoptions' && $option != 'wiloke_themeoptions-transients') {
            return false;
        }
        
        if (!\WilokeThemeOptions::isEnable('app_google_admob_homepage', false)) {
            return false;
        }
        $mobilePageID = $this->getOptionField('mobile_app_page');
        if (empty($mobilePageID)) {
            return false;
        }
        
        $this->proceedSaveAppCache($mobilePageID);
    }
    
    public function saveHomepageSections($postID, $oPost)
    {
        if (!in_array($oPost->post_status, ['publish', 'inherit'])) {
            return false;
        }
        
        $mobilePageID = $this->getOptionField('mobile_app_page');
        
        if (empty($mobilePageID)) {
            if (get_page_template_slug($postID) != 'templates/mobile-app-homepage.php') {
                return false;
            }
        } else {
            $postID = $mobilePageID;
        }
        
        $this->proceedSaveAppCache($postID);
    }
    
    public function reUpdateAppCache()
    {
        $lastCache = GetSettings::getOptions('app_homepage_last_cache');
        $now       = current_time('timestamp', 1);
        if (empty($lastCache) || ((($now - $lastCache) / 60) > 10)) {
            $postID = GetSettings::getOptions('app_homepage_id');
            $this->proceedSaveAppCache($postID);
        }
    }
    
    public function pageEnqueueScripts()
    {
        if (!isset($_GET['post']) || !is_numeric($_GET['post'])) {
            return false;
        }
        
        if (get_page_template_slug($_GET['post']) != 'templates/mobile-app-homepage.php') {
            return false;
        }
        
        wp_enqueue_script('wilcity-mobile-app', plugin_dir_url(__FILE__).'../../assets/js/script.js', ['jquery'], null,
            true);
    }
    
    public function compilerBox()
    {
        if (!isset($_GET['post'])) {
            return false;
        }
        
        $pageID = abs($_GET['post']);
        
        $status = $this->isMobileAppTemplate($pageID);
        if (!$status) {
            return false;
        }
        ?>
        <button id="wilcity-compiler-code" class="button button-primary">Compiler code</button>
        <?php
    }
    
    public function homePageOptions()
    {
        $rawHomeData = GetSettings::getOptions('app_homepage');
        if (empty($rawHomeData)) {
            return ['error' => 'Error'];
        }
        
        return json_decode($rawHomeData, true);
    }
    
    function homepageAllSections()
    {
        $aParseHomeData = $this->homePageOptions();
        $aResponse      = [];
        
        foreach ($aParseHomeData as $key => $rawSection) {
            $aSection        = json_decode(base64_decode($rawSection), true);
            $aResponse[$key] = $aSection;
        }
        
        return [
            'status' => 'success',
            'oData'  => apply_filters('wilcity/wilcity-mobile-app/homepage-sections', $aResponse)
        ];
    }
    
    public function homepageSections()
    {
        $aSections = GetSettings::getOptions('app_homepage_section');
        if (empty($aSections)) {
            return [
                'status' => 'error',
                'msg'    => esc_html__('There are sections', WILCITY_MOBILE_APP)
            ];
        }
        
        return [
            'status' => 'success',
            'oData'  => $aSections
        ];
    }
    
    public function homepageSectionDetails($aData)
    {
        $msg = esc_html__('This section does not exists', WILCITY_MOBILE_APP);
        if (!isset($aData['id']) || empty($aData['id'])) {
            return [
                'status' => 'error',
                'msg'    => $msg
            ];
        }
        $aSections = $this->homePageOptions();
        
        if (!isset($aSections[$aData['id']]) || empty($aSections[$aData['id']])) {
            return [
                'status' => 'error',
                'msg'    => $msg
            ];
        }
        
        return [
            'status' => 'success',
            'oData'  => json_decode(base64_decode($aSections[$aData['id']]), true)
        ];
    }
    
    /**
     * @param $pageID
     *
     * @return bool
     */
    public function isMobileAppTemplate($pageID)
    {
        $pageTemplate = get_page_template_slug($pageID);
        if ($pageTemplate !== 'templates/mobile-app-homepage.php') {
            return false;
        }
        
        return true;
    }
}

<?php

namespace WILCITY_APP\Controllers;

use WilokeListingTools\Controllers\ReviewController;
use WilokeListingTools\Controllers\ShareController;
use WilokeListingTools\Controllers\SharesStatisticController;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Frontend\SingleListing;
use WilokeListingTools\Models\ReviewMetaModel;
use WilokeListingTools\Models\ReviewModel;

class Listing
{
    use BuildQuery;
    use JsonSkeleton;
    
    public function __construct()
    {
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', '/listing-detail/(?P<target>\w+)', [
                'methods'  => 'GET',
                'callback' => [$this, 'getListing'],
            ]);
        });
        
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', '/listing-detail/(?P<target>\d+)', [
                'methods'  => 'GET',
                'callback' => [$this, 'getListing'],
            ]);
        });
        
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', '/listing-meta/(?P<target>\d+)/(?P<metaKey>\w+)', [
                'methods'  => 'GET',
                'callback' => [$this, 'getListingMeta'],
            ]);
        });
        
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', '/listing-meta/(?P<target>\w+)/(?P<metaKey>\w+)', [
                'methods'  => 'GET',
                'callback' => [$this, 'getListingMeta'],
            ]);
        });
        
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', '/listing/sidebar/(?P<id>\d+)', [
                'methods'  => 'GET',
                'callback' => [$this, 'getSidebar']
            ]);
        });
        
        add_filter('wilcity/nav-order', [$this, 'addTypeToSections']);
        add_filter('wilcity/wilcity-mobile-app/filter/wilcity_app_listing_blocks',
            [$this, 'getListingSkeletonOnHomepage'], 10, 2);
        add_filter('wilcity/mobile/render_listings_on_mobile', [$this, 'getListingSkeletonOnHomepage'], 10, 2);
        add_action('save_post', [$this, 'deleteCacheFile']);
        add_action('before_delete_post', [$this, 'deleteCacheFile']);
    }
    
    public function deleteCacheFile($postID)
    {
        $aPostTypes = General::getPostTypeKeys(false, false);
        if (!in_array(get_post_type($postID), $aPostTypes)) {
            return false;
        }
        $postSlug = get_post_field('post_name', $postID);
        $this->deleteCaching('listing-detail-'.$postSlug);
        $this->deleteCaching('json-skeleton-'.$postSlug);
    }
    
    public function getListingSkeletonOnHomepage($atts, $post)
    {
        $cache = $this->getCaching('json-skeleton-'.$post->post_name);
        if (!empty($cache)) {
            return $cache;
        }
        
        $aListing = $this->listingSkeleton($post, ['oGallery', 'oSocialNetworks', 'oVideos', 'oNavigation'], $atts);
        $this->writeCaching($aListing, 'json-skeleton-'.$post->post_name);
        return $aListing;
    }
    
    public function getListingCustomSection($aData)
    {
        $this->getCustomSection($aData['target'], $aData['metaKey']);
    }
    
    public function addTypeToSidebar($aSections)
    {
        foreach ($aSections as $key => $aVal) {
            if (isset($aVal['isCustomSection']) && $aVal['isCustomSection'] == 'yes') {
                $category = $this->detectShortcodeType($aVal['content']);
                
                if (!empty($category)) {
                    $sc = $this->parseCustomShortcode($aVal['content']);
                    if (!empty($sc)) {
                        $aSections[$key]['oContent'] = do_shortcode($sc);
                    }
                }
            }
        }
        
        return $aSections;
    }
    
    public function addTypeToSections($aSections)
    {
        if (empty($aSections)) {
            return [];
        }
        
        foreach ($aSections as $key => $aVal) {
            if (isset($aVal['isCustomSection']) && $aVal['isCustomSection'] == 'yes') {
                $aSections[$key]['category'] = $this->detectShortcodeType($aVal['content']);
            } else {
                $aSections[$key]['category'] = $aVal['key'];
            }
        }
        
        return $aSections;
    }
    
    public function getListingMeta($aData)
    {
        $aResult = $this->getPostMeta($aData);
        
        if (empty($aResult)) {
            return [
                'status' => 'error',
                'msg'    => 'noDataFound'
            ];
        } else {
            return [
                'status'   => 'success',
                'oResults' => $aResult
            ];
        }
    }
    
    public function getSidebar($aData)
    {
        global $post;
        $post             = get_post($aData['id']);
        $aSidebarSettings = SingleListing::getSidebarOrder($post);
        if (empty($aSidebarSettings)) {
            return [
                'status' => 'error',
                'msg'    => esc_html__('There are no sidebar item', WILCITY_MOBILE_APP)
            ];
        }
        $aSidebarItems = [];
        foreach ($aSidebarSettings as $aSidebarSetting) {
            if (!isset($aSidebarSetting['key']) || (isset($aSidebarSetting['status']) && $aSidebarSetting['status'] == 'no')) {
                continue;
            }
            $aSidebarSetting['isMobile'] = true;
            
            $val = $this->getSCContent($aSidebarSetting);
            
            if (!empty($val)) {
                $aSidebarItems[] = [
                    'aSettings' => $aSidebarSetting,
                    'oContent'  => $val
                ];
            }
        }
        
        if (empty($aSidebarItems)) {
            return [
                'status' => 'error'
            ];
        } else {
            return [
                'status'   => 'success',
                'oResults' => $aSidebarItems
            ];
        }
    }
    
    public function getListing($aData)
    {
        $aArgs = $this->buildSingleQuery($aData);
        
        $query = new \WP_Query($aArgs);
        
        if ($query->have_posts()) {
            $aPost = [];
            while ($query->have_posts()) {
                $query->the_post();
                $aPost       = $this->listingSkeleton($query->post);
                $aNavAndHome = $this->getNavigationAndHome($query->post);
                $aButton     = $this->getListingDetailExternalButton($query->post->ID);
                $aPost       = $aPost + $aNavAndHome + $aButton;
                $postID      = $query->post->ID;
            }
            
            return apply_filters('wilcity/wilcity-mobile-app/filter/listing-detail', [
                'status'   => 'success',
                'oResults' => $aPost
            ], $aData, $postID);
        } else {
            return [
                'status' => 'error',
                'msg'    => esc_html__('No Post found', WILCITY_MOBILE_APP)
            ];
        }
    }
}

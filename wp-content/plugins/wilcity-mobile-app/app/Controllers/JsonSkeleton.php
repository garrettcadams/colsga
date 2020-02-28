<?php

namespace WILCITY_APP\Controllers;

use WILCITY_SC\SCHelpers;
use WilokeListingTools\Controllers\SharesStatisticController;
use WilokeListingTools\Framework\Helpers\FileSystem;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\Frontend\BusinessHours;
use WilokeListingTools\Frontend\SingleListing;
use WilokeListingTools\Frontend\User as WilcityUser;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Models\Coupon;
use WilokeListingTools\Models\EventModel;
use WilokeListingTools\Models\FavoriteStatistic;
use WilokeListingTools\Models\ReviewMetaModel;
use WilokeListingTools\Models\ReviewModel;
use WilokeListingTools\Models\UserModel;
use WilokeListingTools\MetaBoxes\Listing as ListingMetaBoxes;
use WilokeListingTools\Controllers\ReviewController;

trait JsonSkeleton
{
    private $aIDs = [];
    private $listingID = '';
    private $aThemeOptions;
    public $aExcludeImgSizes = [];
    public $videoThumbnail = null;
    private $aCustomSections = [
        'select_field'    => 'boxIcon',
        'checkbox_field'  => 'boxIcon',
        'checkbox2_field' => 'boxIcon',
        'textarea_field'  => 'text',
        'date_time_field' => 'text',
        'text_field'      => 'text',
        'image_field'     => 'image'
    ];
    
    public function buildGallery($aGalleryIDs, $maximumItems = null)
    {
        $this->aExcludeImgSizes = ['wilcity_500x275', 'thumbnail', 'wilcity_290x165', 'wilcity_360x200'];
        if (empty($aGalleryIDs)) {
            $aGallery = false;
        } else {
            $aGallery = [];
            if (!empty($maximumItems)) {
                $aGalleryIDs = array_splice($aGalleryIDs, 0, $maximumItems);
            }
            $aImgSizes = $this->imgSizes();
            foreach ($aGalleryIDs as $imgID) {
                foreach ($aImgSizes as $imgSize) {
                    $rawImg = wp_get_attachment_image_url($imgID, $imgSize);
                    if (!empty($rawImg)) {
                        $aGallery[$imgSize][] = [
                            'url' => $rawImg,
                            'id'  => $imgID
                        ];
                    }
                }
            }
        }
        $this->aExcludeImgSizes = [];
        
        return $aGallery;
    }
    
    protected function buildCachingFile($fileName)
    {
        return $fileName.'.json';
    }
    
    protected function getCaching($fileName)
    {
        $fileName = $this->buildCachingFile($fileName);
        if (FileSystem::isFileExists($fileName, 'wilcity-mobile-app')) {
            $content = FileSystem::fileGetContents($fileName, 'wilcity-mobile-app');
            if (!empty($content)) {
                return json_decode($content, true);
            }
        }
        
        return '';
    }
    
    protected function writeCaching($content, $fileName)
    {
        $fileName = $this->buildCachingFile($fileName);
        $content  = is_array($content) ? json_encode($content) : $content;
        FileSystem::filePutContents($fileName, $content, 'wilcity-mobile-app');
    }
    
    protected function deleteCaching($fileName)
    {
        $fileName = $this->buildCachingFile($fileName);
        FileSystem::deleteFile($fileName, 'wilcity-mobile-app');
    }
    
    private function isPostDoesNotExist($postID)
    {
        return get_the_title($postID) === false;
    }
    
    public function getOptionField($key = '')
    {
        if (!empty($this->aThemeOptions)) {
            return isset($this->aThemeOptions[$key]) ? $this->aThemeOptions[$key] : '';
        }
        
        $this->aThemeOptions = \Wiloke::getThemeOptions(true);
        
        return isset($this->aThemeOptions[$key]) ? $this->aThemeOptions[$key] : '';
    }
    
    public function buildSelectOptions($aOptions, $default = '')
    {
        $aFinalOptions = [];
        
        foreach ($aOptions as $key) {
            $aFinalOptions[] = [
                'name'     => ucfirst(str_replace(['-', '_'], [' ', ' '], $key)),
                'id'       => $key,
                'selected' => empty($default) || $default != $key ? false : true
            ];
        }
        
        return $aFinalOptions;
    }
    
    private function getPostIDBySlug($slug)
    {
        global $wpdb;
        if (isset($this->aIDs[$slug])) {
            return $this->aIDs[$slug];
        }
        
        $id                = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT ID FROM {$wpdb->posts} WHERE name=%s",
                $slug
            )
        );
        $this->aIDs[$slug] = $id;
        
        return empty($id) ? false : $id;
    }
    
    private function getGeneralReviewInfo($postID, $postType)
    {
        $average         = GetSettings::getPostMeta($postID, 'average_reviews');
        $aAverageReviews = ReviewMetaModel::getAverageCategoriesReview($postID);
        
        $aContent['average']               = empty($average) ? 0 : floatval($average);
        $aContent['oAverageDetailsReview'] = $aAverageReviews;
        $aContent['quality']               = ReviewMetaModel::getReviewQualityString($average, $postType);
        
        return $aContent;
    }
    
    public function getReviewItem($post, $postParent, $aDetails)
    {
        $aGallery                    = GetSettings::getPostMeta($post->ID, 'gallery');
        $aReview['ID']               = abs($post->ID);
        $aReview['userID']           = abs($post->post_author);
        $aReview['postTitle']        = get_the_title($post->ID);
        $aReview['permalink']        = get_permalink($post->ID);
        $aReview['shareURL']         = GetSettings::getShareReviewURL(get_permalink($postParent), $post->ID);
        $aReview['postContent']      = get_post_field('post_content', $post->ID);
        $aReview['postDate']         = Time::getPostDate($post->post_date);
        $aReview['countLiked']       = abs(ReviewMetaModel::countLiked($post->ID));
        $aReview['countShared']      = abs(SharesStatisticController::renderShared($post->ID, false));
        $aReview['countDiscussions'] = abs(ReviewMetaModel::countDiscussion($post->ID));
        $aReview['hasDiscussion']    = ReviewModel::hasDiscussion($post->ID);
        $aReview['oGallery']         = empty($aGallery) ? false : $this->buildGallery(array_keys($aGallery));
        $aReview['isLiked']          = ReviewMetaModel::isLiked($post->ID);
        $aReviewDetails              = ReviewMetaModel::getReviewDetailsScore($post->ID, $aDetails, true);
        $aReview['oReviews']         = [
            'oDetails' => $aReviewDetails['oDetails'],
            'quality'  => ReviewMetaModel::getReviewQualityString($aReviewDetails['average'],
                get_post_type($postParent)),
            'average'  => $aReviewDetails['average']
        ];
        $aReview['oUserInfo']        = $this->getUserInfo($post->post_author);
        
        return $aReview;
        
    }
    
    public function getPostMeta($aData, $isTestMode = false)
    {
        if (!is_numeric($aData['target'])) {
            $postID   = $this->getPostIDBySlug($aData['target']);
            $postSlug = $aData['target'];
        } else {
            $postID   = $aData['target'];
            $postSlug = get_post_field('post_name', $postID);
        }
        
        $cacheFileName = 'post-meta-'.$postSlug.'-'.$aData['metaKey'];
        
        if ($content = $this->getCaching($cacheFileName)) {
            return $content;
        }
        
        $aContent         = null;
        $aData['metaKey'] = trim($aData['metaKey']);
        
        switch ($aData['metaKey']) {
            case 'coupon':
                return '';
            case 'posts':
                return '';
            case 'my_products':
                $aContent = apply_filters('wilcity/wilcity-mobile-app/filter/product-on-single-listing', '', $aData,
                    $isTestMode);
                break;
                break;
            case 'photos':
            case 'gallery':
                if ($isTestMode) {
                    return GetSettings::getPostMeta($postID, 'gallery');
                }
                $aGallery = GetSettings::getPostMeta($postID, 'gallery');
                $aContent = empty($aGallery) ? false : $this->buildGallery(array_keys($aGallery));
                break;
            case 'videos':
                $maxItems = isset($aData['maximumItemsOnHome']) ? abs($aData['maximumItemsOnHome']) : '';
                if ($isTestMode) {
                    return GetSettings::getPostMeta($postID, 'video_srcs');
                }
                $aContent = $this->getVideos($postID, $maxItems);
                break;
            case 'content':
            case 'listing_content':
                if ($isTestMode) {
                    return get_post_field('post_content', $postID);
                }
                $aContent = get_post_field('post_content', $postID);
                
                break;
            case 'tags':
                $aRawTags = wp_get_post_terms($postID, 'listing_tag');
                if ($isTestMode) {
                    return empty($aRawTags) || is_wp_error($aRawTags) ? '' : 'work';
                }
                if (empty($aRawTags) || is_wp_error($aRawTags)) {
                    $aContent = false;
                } else {
                    foreach ($aRawTags as $oTag) {
                        $aTag          = get_object_vars($oTag);
                        $aTag['oIcon'] = \WilokeHelpers::getTermOriginalIcon($oTag);
                        $aContent[]    = $aTag;
                    }
                }
                break;
            case 'events':
                $this->aExcludeImgSizes = ['wilcity_500x275', 'thumbnail', 'wilcity_290x165', 'wilcity_360x200'];
                if (isset($aData['maximumItemsOnHome'])) {
                    $postsPerPage = $aData['maximumItemsOnHome'];
                } else if (isset($aData['postsPerPage'])) {
                    $postsPerPage = $aData['postsPerPage'];
                } else {
                    $postsPerPage = 10;
                }
                $aArgs = [
                    'post_type'      => 'event',
                    'posts_per_page' => $postsPerPage,
                    'post_status'    => 'publish',
                    'post_parent'    => $postID
                ];
                
                if ($isTestMode) {
                    $aArgs['posts_per_page'] = 1;
                }
                
                $oEventQuery = new \WP_Query(
                    [
                        'post_type'      => 'event',
                        'posts_per_page' => $postsPerPage,
                        'post_status'    => 'publish',
                        'post_parent'    => $postID
                    ]
                );
                if ($oEventQuery->have_posts()) {
                    if ($isTestMode) {
                        wp_reset_postdata();
                        
                        return 'have_events';
                    }
                    while ($oEventQuery->have_posts()) {
                        $oEventQuery->the_post();
                        $aContent[] = $this->listingSkeleton($oEventQuery->post);
                    }
                } else {
                    $aContent = false;
                }
                wp_reset_postdata();
                $this->aExcludeImgSizes = [];
                break;
            case 'reviews':
                $this->aExcludeImgSizes = ['wilcity_500x275', 'thumbnail', 'wilcity_290x165', 'wilcity_360x200'];
                if (isset($aData['maximumItemsOnHome'])) {
                    $postsPerPage = $aData['maximumItemsOnHome'];
                } else if (isset($aData['postsPerPage'])) {
                    $postsPerPage = $aData['postsPerPage'];
                } else {
                    $postsPerPage = 10;
                }
                
                if ($isTestMode) {
                    $postsPerPage = 1;
                }
                
                $page  = isset($aData['page']) ? abs($aData['page']) : 1;
                $query = new \WP_Query(
                    [
                        'post_type'      => 'review',
                        'post_status'    => 'publish',
                        'posts_per_page' => $postsPerPage,
                        'post_parent'    => abs($postID),
                        'paged'          => $page
                    ]
                );
                if ($isTestMode) {
                    if ($query->have_posts()) {
                        wp_reset_postdata();
                        
                        return 'has_reviews';
                    } else {
                        return '';
                    }
                }
                
                $aContent['mode'] = GetSettings::getOptions(General::getReviewKey('mode', get_post_type($postID)));
                $aContent['mode'] = empty($aContent['mode']) ? 5 : abs($aContent['mode']);
                $postType         = get_post_type($postID);
                
                $aGeneralReviewsInfo = $this->getGeneralReviewInfo($postID, $postType);
                $aContent            = array_merge($aContent, $aGeneralReviewsInfo);
                
                if ($query->have_posts()) {
                    $aContent['total']    = abs($query->found_posts);
                    $aContent['maxPages'] = abs($query->max_num_pages);
                    
                    if ($page == $query->max_num_pages) {
                        $aContent['next'] = false;
                    } else {
                        $aContent['next'] = $page + 1;
                    }
                    
                    $aDetails = GetSettings::getOptions(General::getReviewKey('details', $postType));
                    global $post;
                    while ($query->have_posts()) {
                        $query->the_post();
                        $aContent['aReviews'][] = $this->getReviewItem($post, $postID, $aDetails);
                    }
                    wp_reset_postdata();
                } else {
                    $aContent = false;
                }
                $this->aExcludeImgSizes = [];
                break;
            case 'restaurant_menu':
                if (!GetSettings::isPlanAvailableInListing($aData['target'], 'toggle_restaurant_menu')) {
                    return '';
                }
                $aContent = SingleListing::getRestaurantMenu($aData['target']);
                break;
            default:
                $aContent = $this->getCustomSection($aData['target'], $aData['metaKey']);
                break;
        }
        
        $this->writeCaching($aContent, $cacheFileName);
        
        return $aContent;
    }
    
    public function getOrderBy()
    {
        return apply_filters('wilcity/app/orderby', [
            'post_date'   => esc_html__('Latest Listings', WILCITY_MOBILE_APP),
            'post_title'  => esc_html__('Title', WILCITY_MOBILE_APP),
            'best_viewed' => esc_html__('View', WILCITY_MOBILE_APP),
            'best_rated'  => esc_html__('Rating', WILCITY_MOBILE_APP),
            'best_shared' => esc_html__('Sharing', WILCITY_MOBILE_APP),
            'menu_order'  => esc_html__('Our Suggestion', WILCITY_MOBILE_APP)
        ]);
    }
    
    protected function getUserProfile($userID, $excludeFollowContact = false)
    {
        $oUser     = new \WP_User($userID);
        $aUserInfo = [
            'oBasicInfo' => [
                'userID'       => $userID,
                'user_name'    => $oUser->user_login,
                'first_name'   => $oUser->user_firstname,
                'last_name'    => $oUser->user_lastname,
                'display_name' => $oUser->display_name,
                'avatar'       => User::getAvatar($oUser->ID),
                'cover_image'  => User::getCoverImage($oUser->ID),
                'position'     => User::getPosition($oUser->ID),
                'description'  => $oUser->description,
                'email'        => $oUser->user_email
            ]
        ];
        
        if (!$excludeFollowContact) {
            $aUserInfo['oFollowAndContact'] = [
                'address'         => User::getAddress($oUser->ID),
                'website'         => User::getWebsite($oUser->ID),
                'phone'           => User::getPhone($oUser->ID),
                'social_networks' => ''
            ];
            
            $aRawSocialNetworks = User::getSocialNetworks($oUser->ID);
            if (!empty($aRawSocialNetworks)) {
                $aSocialNetworks = [];
                foreach ($aRawSocialNetworks as $icon => $url) {
                    $aSocialNetworks[] = [
                        'id'  => $icon,
                        'url' => $url
                    ];
                }
                
                $aUserInfo['oFollowAndContact']['social_networks'] = $aSocialNetworks;
            }
        }
        
        return $aUserInfo;
    }
    
    protected function getListingDetailExternalButton($postID)
    {
        $buttonLink = GetSettings::getPostMeta($postID, 'button_link');
        $aResponse  = [];
        if (!empty($buttonLink)) {
            $aResponse['oButton']['name'] = GetSettings::getPostMeta($postID, 'button_name');
            $aResponse['oButton']['link'] = $buttonLink;
            $aResponse['oButton']['icon'] = GetSettings::getPostMeta($postID, 'button_icon');
        } else {
            $aResponse['oButton'] = '';
        }
        
        return $aResponse;
    }
    
    protected function parseCustomShortcode($shortcode, $postID = '')
    {
        if (empty($shortcode)) {
            return '';
        }
        $this->listingID = $postID;
        $shortcode       = str_replace(['{{', '}}'], ['"', '"'], $shortcode);
        
        return trim(preg_replace_callback('/\s+/', function ($matched) {
            if (!empty($this->listingID)) {
                return ' is_mobile="yes" post_id="'.$this->listingID.'" ';
            }
            
            return ' is_mobile="yes" ';
        }, $shortcode, 1));
    }
    
    public function getCustomSection($postID, $metaKey)
    {
        $post      = get_post($postID);
        $aSettings = SingleListing::getNavOrder($post);
        
        if (!isset($aSettings[$metaKey])) {
            return false;
        } else {
            if (!isset($aSettings[$metaKey]['content'])) {
                return '';
            }
            
            $rawContent = trim($aSettings[$metaKey]['content']);
            
            if (empty($rawContent)) {
                return '';
            }
            
            $customShortcode = $this->parseCustomShortcode($rawContent, $postID);
            
            if (empty($customShortcode)) {
                return $aSettings[$metaKey]['content'];
            }
            $rawParsedSC = do_shortcode($customShortcode);
            
            if (!is_array($rawParsedSC)) {
                $testJSON = json_decode($rawParsedSC, true);
                if ($testJSON) {
                    $content = $testJSON;
                } else {
                    $content = $rawParsedSC;
                }
            } else {
                $content = $rawParsedSC;
            }
            
            return wilcityAppStripTags($content);
        }
    }
    
    protected function eventCommentItem($post)
    {
        return [
            'ID'               => abs($post->ID),
            'postTitle'        => get_the_title($post->ID),
            'postContent'      => get_post_field('post_content', $post->ID),
            'postDate'         => date_i18n(get_option('date_format'), strtotime($post->post_date)),
            'oAuthor'          => [
                'userID'      => abs($post->post_author),
                'avatar'      => User::getAvatar($post->post_author),
                'displayName' => User::getField('display_name', $post->post_author)
            ],
            'countDiscussions' => abs(GetSettings::countNumberOfChildrenReviews($post->ID)),
            'countLiked'       => abs(ReviewMetaModel::countLiked($post->ID)),
            'countShared'      => abs(SharesStatisticController::renderShared($post->ID, false)),
            'isLiked'          => ReviewController::isLikedReview($post->ID, true) == 'yes'
        ];
    }
    
    /**
     * @param $post
     *
     * @return array
     */
    public function getFeaturedImg($postID)
    {
        $aSizes       = $this->imgSizes();
        $aFeaturedImg = [];
        
        foreach ($aSizes as $size) {
            if (strpos($size, 'wilcity_') !== false) {
                continue;
            }
            
            if (is_array($size)) {
                $sizeName = 'wilcity_'.$size[0].'x'.$size[1];
            } else {
                $sizeName = $size;
            }
            $aFeaturedImg[$sizeName] = GetSettings::getFeaturedImg($postID, $size);
            if (empty($aFeaturedImg[$sizeName])) {
                $aFeaturedImg[$sizeName] = WILCITY_APP_IMG_PLACEHOLDER;
            }
        }
        
        return $aFeaturedImg;
    }
    
    protected function getListingData($key, $post, $aAtts = [])
    {
        $content = '';
        switch ($key) {
            case 'listing_content':
                $content = get_post_field('post_content', $post->ID);
                break;
            case 'featured_image':
                $content = $this->getFeaturedImg($post);
                break;
            case 'category':
            case 'listing_cat':
            case 'location':
            case 'listing_location':
            case 'tag':
            case 'listing_tag':
                $key      = $key == 'category' ? 'cat' : 'category';
                $taxonomy = strpos($key, 'listing_') === 0 ? $key : 'listing_'.$key;
                $RawTerms = \WilokeHelpers::getTermByPostID($post->ID, $taxonomy, false);
                $aTerms   = [];
                if ($RawTerms) {
                    foreach ($RawTerms as $oRawTerm) {
                        $aTerm              = get_object_vars($oRawTerm);
                        $aTerm['oGradient'] = \WilokeHelpers::getTermGradient($oRawTerm);
                        $aTerms[]           = (object)$aTerm;
                    }
                }
                $content = $aTerms;
                break;
            case 'gallery':
            case 'photos':
                $content = $this->getGallery($post->ID);
                break;
        }
        
        return $content;
    }
    
    protected function detectShortcodeType($content)
    {
        foreach ($this->aCustomSections as $fieldType => $category) {
            if (strpos($content, $fieldType) === false) {
                continue;
            }
            
            return $category;
        }
        
        return '';
    }
    
    protected function getSCContent($aSetting)
    {
        $aRenderMachine =
            wilokeListingToolsRepository()->get('listing-settings:sidebar_settings', true)->sub('renderMachine');
        if (!isset($aRenderMachine[$aSetting['key']])) {
            if (!isset($aSetting['content'])) {
                return false;
            }
            if (isset($aSetting['isCustomSection']) && $aSetting['isCustomSection'] == 'yes') {
                $category = $this->detectShortcodeType($aSetting['content']);
                if (!empty($category)) {
                    $sc = $this->parseCustomShortcode($aSetting['content']);
                    if (!empty($sc)) {
                        $val = do_shortcode($sc);
                        if ($category == 'boxIcon') {
                            $aSetting['key'] = 'tags';
                            $val             = json_decode($val, true);
                        }
                    }
                } else {
                    unset($aSetting);
                }
            } else {
                $val = $aSetting['content'];
            }
        } else {
            $val = do_shortcode("[".$aRenderMachine[$aSetting['key']]." atts='".stripslashes(json_encode($aSetting)).
                                "'/]");
            if (!empty($val)) {
                $parseVal = json_decode($val, true);
                $val      = is_array($parseVal) ? $parseVal : $val;
            }
        }
        
        return empty($val) ? false : $val;
    }
    
    public function getNavigationAndHome($post)
    {
        $aSettings       = SingleListing::getNavOrder($post);
        $this->listingID = abs($post->ID);
        $aHomeSections   = [];
        foreach ($aSettings as $key => $aSection) {
            if ($aSection['isShowOnHome'] == 'yes') {
                if ($key !== 'reviews' && strpos('google_adsense', $key) === false && empty($this->getPostMeta([
                        'target'  => $post->ID,
                        'metaKey' => $aSection['key']
                    ], true))
                ) {
                    unset($aSettings[$key]);
                    continue;
                };
                $aSection['key'] = trim($aSection['key']);
                $val             = $this->getSCContent($aSection);
                if (!empty($val)) {
                    $aSection['content'] = $val;
                }
                $aHomeSections[trim($key)] = $aSection;
            }
        }
        
        $aNavigation = array_filter($aSettings, function ($aSection) {
            return ($aSection['status'] == 'yes' || $aSection['key'] == 'review');
        });
        
        $aPost['oNavigation']   = $aNavigation;
        $aPost['oHomeSections'] = $aHomeSections;
        
        $aPost = apply_filters('wilcity/wilcity-mobile-app/listing/navigation-and-home', $aPost, $this->listingID);
        
        return $aPost;
    }
    
    public function getUserInfo($userID, $aSpecifyInfo = [])
    {
        if (empty($aSpecifyInfo)) {
            $aSpecifyInfo = [
                'userID',
                'avatar',
                'displayName',
                'position',
                'phone',
                'address',
                'oSocialNetworks',
                'coverImage',
                'website',
                'email'
            ];
        }
        $aData = [];
        
        if (in_array('userID', $aSpecifyInfo)) {
            $aData['userID'] = abs($userID);
        }
        
        if (in_array('avatar', $aSpecifyInfo)) {
            $aData['avatar'] = User::getAvatar($userID);
        }
        
        if (in_array('displayName', $aSpecifyInfo)) {
            $aData['displayName'] = User::getField('display_name', $userID);
        }
        
        if (in_array('position', $aSpecifyInfo)) {
            $aData['position'] = User::getPosition($userID);
        }
        
        if (in_array('phone', $aSpecifyInfo)) {
            $aData['phone'] = User::getPhone($userID);
        }
        
        if (in_array('address', $aSpecifyInfo)) {
            $aData['address'] = User::getAddress($userID);
        }
        
        if (in_array('oSocialNetworks', $aSpecifyInfo)) {
            $aData['oSocialNetworks'] = User::getSocialNetworks($userID);
        }
        
        if (in_array('coverImage', $aSpecifyInfo)) {
            $aData['coverImage'] = User::getCoverImage($userID);
        }
        
        if (in_array('website', $aSpecifyInfo)) {
            $aData['website'] = User::getField('url', $userID);
        }
        
        if (in_array('email', $aSpecifyInfo)) {
            $aData['email'] = User::getField('email', $userID);
        }
        
        return $aData;
    }
    
    public function imgSizes()
    {
        $aSizes = apply_filters('wilcity/mobile/featured-image/sizes', [
            'large',
            'medium',
            'thumbnail',
            'wilcity_500x275',
            'wilcity_290x165',
            'wilcity_360x200'
        ]);
        
        if (empty($this->aExcludeImgSizes)) {
            return $aSizes;
        }
        
        return array_diff($aSizes, $this->aExcludeImgSizes);
    }
    
    public function getFirstCat($post)
    {
        $oFirstCat = GetSettings::getLastPostTerm($post->ID, 'listing_cat');
        
        if (!empty($oFirstCat)) {
            $aCat          = get_object_vars($oFirstCat);
            $aCat['oIcon'] = \WilokeHelpers::getTermOriginalIcon($oFirstCat);
        }
        
        return false;
    }
    
    public function eventItem($post)
    {
        global $wiloke;
        $aEvent['ID']              = abs($post->ID);
        $aEvent['postTitle']       = get_the_title($post->ID);
        $aEvent['postContent']     = get_post_field('post_content', $post->ID);
        $aEvent['postExcerpt']     =
            strip_tags(\Wiloke::contentLimit($wiloke->aThemeOptions['listing_excerpt_length'], $post, false,
                $post->post_content, true));
        $aEvent['oAuthorInfo']     = $this->getUserInfo($post->post_author);
        $aEvent['isMyInterested']  = UserModel::isMyFavorite($post->ID) ? 'yes' : 'no';
        $aEvent['totalInterested'] = FavoriteStatistic::countFavorites($post->ID);
        $aEvent['oMapInfo']        = GetSettings::getListingMapInfo($post->ID);
        $aEvent['oFeaturedImg']    = $this->getFeaturedImg($post);
        $aEvent['website']         = GetSettings::getPostMeta($post->ID, 'website');
        $aEvent['phone']           = GetSettings::getPostMeta($post->ID, 'phone');
        $aEvent['email']           = GetSettings::getPostMeta($post->ID, 'email');
        $aEvent['oFirstCat']       = $this->getFirstCat($post);
        
        $aEventData = EventModel::getEventData($post->ID);
        $frequency  = $aEventData['frequency'];
        $timezone   = GetSettings::getPostMeta($post->ID, 'timezone');
        
        $aTimeInformation = [
            'oStarts'  => [
                'on' => Time::toDateFormat($aEventData['startsOn']),
                'at' => Time::toTimeFormat($aEventData['startsOn'])
            ],
            'oEnds'    => [
                'on' => Time::toDateFormat($aEventData['endsOn']),
                'at' => Time::toTimeFormat($aEventData['endsOn'])
            ],
            'timezone' => Time::findUTCOffsetByTimezoneID($timezone)
        ];
        
        switch ($frequency) {
            case 'occurs_once':
                $aTimeInformation['type'] = esc_html__('Occurs Once', WILOKE_LISTING_DOMAIN);
                break;
            case 'daily':
                $aTimeInformation['type'] = esc_html__('Daily', WILOKE_LISTING_DOMAIN);
                break;
            case 'weekly':
                $specifyDay               = $aEventData['specifyDays'];
                $dayName                  =
                    wilokeListingToolsRepository()->get('general:aDayOfWeek', true)->sub($specifyDay);
                $aTimeInformation['type'] = sprintf(esc_html__('Every %s', WILOKE_LISTING_DOMAIN), $dayName);
                break;
        }
        $aEvent['oSchedule'] = $aTimeInformation;
        
        return $aEvent;
    }
    
    public function getReviewDetails($postID)
    {
        return ReviewMetaModel::getGeneralReviewData($postID);
    }
    
    private function getVideoThumbnail()
    {
        if ($this->videoThumbnail == null) {
            $aThumb = \WilokeThemeOptions::getOptionDetail('listing_video_thumbnail');
            if (isset($aThumb['id']) && !empty($aThumb['id'])) {
                $this->videoThumbnail = wp_get_attachment_image_url($aThumb['id'], 'medium');
            } else {
                $this->videoThumbnail = $aThumb['url'];
            }
        }
        
        return $this->videoThumbnail;
    }
    
    public function getVideos($postID, $maximumItems = null)
    {
        $aVideos = GetSettings::getPostMeta($postID, 'video_srcs');
        
        if (empty($aVideos)) {
            $aVideos = false;
        } else {
            if (!empty($maximumItems)) {
                $aVideos = array_splice($aVideos, 0, $maximumItems);
            }
            foreach ($aVideos as $id => $aVideo) {
                if (!isset($aVideo['thumbnail']) || $aVideo['thumbnail'] == false) {
                    $aVideos[$id]['thumbnail'] = $this->getVideoThumbnail();
                }
            }
        }
        
        return $aVideos;
    }
    
    public function getGallery($postID, $maximumItems = null)
    {
        $this->aExcludeImgSizes = ['wilcity_500x275', 'thumbnail', 'wilcity_290x165', 'wilcity_360x200'];
        $aRawGallery            = GetSettings::getPostMeta($postID, 'gallery');
        if (empty($aRawGallery)) {
            $aGallery = false;
        } else {
            $aGallery    = [];
            $aGalleryIDs = array_keys($aRawGallery);
            if (!empty($maximumItems)) {
                $aGalleryIDs = array_splice($aGalleryIDs, 0, $maximumItems);
            }
            
            $aImgSizes = $this->imgSizes();
            foreach ($aGalleryIDs as $imgID) {
                foreach ($aImgSizes as $imgSize) {
                    $rawImg = wp_get_attachment_image_url($imgID, $imgSize);
                    if (!empty($rawImg)) {
                        $aGallery[$imgSize][] = [
                            'url' => $rawImg,
                            'id'  => $imgID
                        ];
                    }
                }
            }
        }
        $this->aExcludeImgSizes = [];
        
        return $aGallery;
    }
    
    private function getFavoritesData($post)
    {
        $isMyFavorite   = UserModel::isMyFavorite($post->ID, true);
        $totalFavorites = FavoriteStatistic::countFavorites($post->ID);
        
        if ($post->post_type == 'event') {
            $text = $totalFavorites > 1 ? esc_html__('people interested', 'wilcity-mobile-app') :
                esc_html__('people interested', 'wilcity-mobile-app');
        } else {
            $text = $totalFavorites > 1 ? esc_html__('Favorites', 'wilcity-mobile-app') :
                esc_html__('Favorite', 'wilcity-mobile-app');
        }
        
        return [
            'isMyFavorite'   => $isMyFavorite,
            'totalFavorites' => $totalFavorites,
            'text'           => $text
        ];
    }
    
    public function listingSkeleton($post, $aExcludes = [], $aAtts = [])
    {
        $aFeaturedImg = $this->getFeaturedImg($post->ID);
        
        $averageReviews = GetSettings::getPostMeta($post->ID, 'average_reviews');
        $logo           = GetSettings::getLogo($post->ID, 'large');
        $toggleReport   = GetSettings::getOptions('toggle_report');
        $toggleReview   = GetSettings::getOptions(General::getReviewKey('toggle', $post->post_type));
        
        $aResponse = [
            'ID'              => abs($post->ID),
            'postTitle'       => get_the_title($post->ID),
            'postLink'        => get_permalink($post->ID),
            'tagLine'         => strip_tags(GetSettings::getTagLine($post, true, true)),
            'phone'           => GetSettings::getPostMeta($post->ID, 'phone'),
            'logo'            => empty($logo) ? '' : $logo,
            'oVideos'         => GetSettings::getPostMeta($post->ID, 'video_srcs'),
            'timezone'        => GetSettings::getTimezone($post->ID),
            'coverImg'        => GetSettings::getCoverImage($post->ID),
            'oAddress'        => ListingMetaBoxes::getListingAddress($post->ID),
            'oFeaturedImg'    => $aFeaturedImg,
            'businessStatus'  => BusinessHours::isEnableBusinessHour($post) ?
                BusinessHours::getCurrentBusinessHourStatus($post) : '',
            'oPriceRange'     => GetSettings::getPriceRange($post->ID, true),
            'claimStatus'     => GetSettings::getPostMeta($post->ID, 'claim_status'),
            'oSocialNetworks' => GetSettings::getSocialNetworks($post->ID),
            'oGallery'        => $this->getGallery($post->ID),
            'oCustomSettings' => GetSettings::getPostMeta($post->ID, 'custom_settings'),
            'oReview'         => [
                'mode'    => abs(GetSettings::getOptions(General::getReviewKey('mode', $post->post_type))),
                'average' => floatval($averageReviews),
                'quality' => ReviewMetaModel::getReviewQualityString($averageReviews, $post->post_type)
            ],
            'oFavorite'       => $this->getFavoritesData($post),
            'oAuthor'         => [
                'ID'          => $post->post_author,
                'displayName' => WilcityUser::getField('display_name', $post->post_author),
                'avatar'      => WilcityUser::getAvatar($post->post_author)
            ],
            'isReport'        => !empty($toggleReport) && $toggleReport == 'enable',
            'isReview'        => !empty($toggleReview) && $toggleReview == 'enable'
        ];
        
        if (isset($aAtts['TYPE'])) {
            if ($aAtts['TYPE'] == 'LISTINGS') {
                if ($aAtts['style'] == 'grid') {
                    $adsType = 'GRID';
                } else {
                    $adsType = 'LISTINGS_SLIDER';
                }
            } else {
                $adsType = 'GRID';
            }
            
            $aResponse['isAds'] = SCHelpers::renderAds($post, $adsType, true);
        }
        
        $aCoupon = GetSettings::getPostMeta($post->ID, 'coupon');
        if (!empty($aCoupon) && !empty($aCoupon['code'])) {
            if (empty($aCoupon['expiry_date']) ||
                (Time::timestampUTC($aCoupon['expiry_date']) > current_time('timestamp', 1))
            ) {
                $aResponse['oCoupon'] = [
                    'title'     => $aCoupon['title'],
                    'highlight' => $aCoupon['highlight'],
                    'icon'      => 'la la-flash'
                ];
            }
        }
        
        $aResponse = apply_filters('wilcity/app/single-skeletons/'.$post->post_type, $aResponse, $post);
        $oFirstCat = GetSettings::getLastPostTerm($post->ID, 'listing_cat');
        
        if (!empty($oFirstCat)) {
            $aResponse['oTerm'] = $oFirstCat;
            $aResponse['oIcon'] = \WilokeHelpers::getTermOriginalIcon($oFirstCat);
        }
   
        if (!empty($aExcludes)) {
            foreach ($aExcludes as $key) {
                if (isset($aResponse[$key])) {
                    unset($aResponse[$key]);
                }
            }
        }
   
        return $aResponse;
    }
}

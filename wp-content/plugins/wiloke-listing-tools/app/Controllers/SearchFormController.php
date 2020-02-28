<?php

namespace WilokeListingTools\Controllers;

use Stripe\Util\Set;
use WILCITY_SC\SCHelpers;
use WilokeListingTools\AlterTable\AlterTableBusinessHourMeta;
use WilokeListingTools\AlterTable\AlterTableBusinessHours;
use WilokeListingTools\AlterTable\AlterTableEventsData;
use WilokeListingTools\AlterTable\AlterTableLatLng;
use WilokeListingTools\Framework\Helpers\FileSystem;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\MapHelpers;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Helpers\TermSetting;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Frontend\BusinessHours;
use WilokeListingTools\Frontend\PriceRange;
use WilokeListingTools\Frontend\SingleListing;
use WilokeListingTools\Models\EventModel;
use WilokeListingTools\Models\ReviewMetaModel;
use WilokeListingTools\Models\UserModel;

class SearchFormController extends Controller
{
    public static $aSearchFormSettings;
    private $searchFormVersionKey = 'hero_search_form_version';
    private $searchFormsKey = 'hero_search_form_settings';
    private $isJoinedPostMeta = false;
    private $isUsingDefaultOrderBy = false;
    private $aCacheObjectTypes = [];
    protected static $aTermsPrinted = [];
    private $isImprovedSearchByTitle = false;
    private $aDefaultOrderBy = ['post_title', 'post_date', 'post_name', 'slug', 'menu_order post_date'];
    private $aSpecialOrderBy = ['meta_value_num'];
    protected $aGotTags = [];
    protected $aTagsBelongToCat = [];
    
    public function __construct()
    {
        add_filter('query_vars', [$this, 'customQuery']);
        add_action('wp_ajax_wilcity_get_search_fields', [$this, 'getSearchFields']);
        add_action('wp_ajax_nopriv_wilcity_get_search_fields', [$this, 'getSearchFields']);
        add_action('wilcity/render-search', [$this, 'renderSearchResults']);
        
        add_action('wp_ajax_'.apply_filters('wilcity/wiloke-listing-tools/ajax-prefix', 'wilcity_search_listings'), [
            $this,
            'searchListings'
        ]);
        add_action('wp_ajax_nopriv_'.apply_filters('wilcity/wiloke-listing-tools/ajax-prefix',
                'wilcity_search_listings'), [
            $this,
            'searchListings'
        ]);
        
        add_action('wp_ajax_wilcity_get_json_listings', [$this, 'searchFormJson']);
        add_action('wp_ajax_nopriv_wilcity_get_json_listings', [$this, 'searchFormJson']);
        
        // Event Query
        //		add_filter('posts_join', array($this, 'maybeJoinEventData'), 10, 2);
        
        add_filter('posts_search', [$this, 'improveSearchTitle'], 10, 2);
        add_filter('posts_distinct', [$this, 'addUniQueryToSelectID'], 10, 2);
        add_filter('posts_join', [$this, 'maybeJoinPostMeta'], 10, 2);
        
        add_filter('posts_join', [$this, 'preventListingsThatDoesNotHaveLatLng'], 10, 2);
        add_filter('posts_where', [$this, 'addEventWhen'], 10, 2);
        add_filter('posts_where', [$this, 'addPreventListingsThatDoesNotHaveLatLng'], 10, 2);
        
        add_filter('posts_join', [$this, 'joinEvents'], 10, 2);
        add_filter('posts_where', [$this, 'addEventBetweenDateRange'], 10, 2);
        add_filter('posts_fields', [$this, 'addEventSelection'], 10, 2);
        add_filter('posts_orderby', [$this, 'orderByEventDate'], 10, 2);
        
        /* Latlng and map bound query  */
        add_filter('posts_join', [$this, 'joinLatLng'], 10, 2);
        add_filter('posts_pre_query', [$this, 'addHavingDistance'], 10, 2);
        add_filter('posts_orderby', [$this, 'orderByDistance'], 10, 2);
        
        add_filter('posts_fields', [$this, 'addLatLngSelectionToQuery'], 10, 2);
        add_filter('posts_where', [$this, 'addMapBoundsToQuery'], 10, 2);
        /* End */
        
        add_filter('posts_join', [$this, 'joinOpenNow'], 50, 2);
        add_filter('posts_where', [$this, 'addOpenNowToPostsWhere'], 50, 2);
        
        add_action('wp_ajax_wilcity_search_by_ajax', [$this, 'ajaxSearch']);
        add_action('wp_ajax_nopriv_wilcity_search_by_ajax', [$this, 'ajaxSearch']);
        
        add_action('wp_ajax_wilcity_fetch_individual_cat_tags', [$this, 'fetchIndividualCatTags']);
        add_action('wp_ajax_nopriv_wilcity_fetch_individual_cat_tags', [$this, 'fetchIndividualCatTags']);
        
        add_action('wp_ajax_wilcity_get_listings_nearbyme', [$this, 'fetchListingsNearByMe']);
        add_action('wp_ajax_nopriv_wilcity_get_listings_nearbyme', [$this, 'fetchListingsNearByMe']);
        
        add_action('wp_ajax_wilcity_fetch_terms_suggestions', [$this, 'fetchTermsSuggestions']);
        add_action('wp_ajax_nopriv_wilcity_fetch_terms_suggestions', [$this, 'fetchTermsSuggestions']);
        
        add_action('wilcity/footer/vue-popup-wrapper', [$this, 'mapSearchFormPopup']);
        
        add_action('wp_ajax_wilcity_fetch_hero_fields', [$this, 'fetchHeroSearchFields']);
        add_action('wp_ajax_nopriv_wilcity_fetch_hero_fields', [$this, 'fetchHeroSearchFields']);
        
        add_action('wp_ajax_wilcity_fetch_terms_options', [$this, 'fetchTermOptions']);
        add_action('wp_ajax_nopriv_wilcity_fetch_terms_options', [$this, 'fetchTermOptions']);
        add_action('wilcity/footer/vue-popup-wrapper', [$this, 'printQuickSearchForm']);
        add_action('wilcity/saved-hero-search-form', [$this, 'saveSearchFormVersion'], 10, 2);
        
        add_filter('terms_clauses', [$this, 'modifyTermsClauses'], 99999, 3);
        
        add_action('wp_ajax_wilcity_fetch_hero_fields', [$this, 'getHeroSearchFields']);
        add_action('wp_ajax_nopriv_wilcity_fetch_hero_fields', [$this, 'getHeroSearchFields']);
        
        add_action('wp_ajax_wilcity_quick_search_form_suggestion', [$this, 'fetchHeroSearchFormSuggestion']);
        add_action('wp_ajax_nopriv_wilcity_quick_search_form_suggestion', [
            $this,
            'fetchHeroSearchFormSuggestion'
        ]);
        
        // Show up Query
        add_action('rest_api_init', function () {
            register_rest_route('wiloke/v2', 'terms', [
                'methods'  => 'POST',
                'callback' => [$this, 'getListingsInTerm']
            ]);
        });
    }
    
    public function getListingsInTerm()
    {
        $args = file_get_contents("php://input");
        if (empty($args)) {
            return [
                'status' => 'error',
                'msg'    => esc_html__('There is no listing yet', 'wiloke-listing-tools')
            ];
        }
        
        $aRequest           = json_decode($args, true);
        $aArgs              = json_decode(base64_decode($aRequest['args']), true);
        $aSCSettings        = json_decode(base64_decode($aRequest['scSettings']), true);
        $aArgs['post_type'] = $aRequest['postType'];
        
        if ($aArgs['orderby'] == 'nearbyme') {
            $aArgs['geocode'] = $aRequest['oAddress'];
        }
        
        $query = new \WP_Query($aArgs);
        
        if (!$query->have_posts()) {
            wp_reset_postdata();
            
            return [
                'status' => 'error',
                'msg'    => esc_html__('There is no listing yet', 'wiloke-listing-tools')
            ];
        }
        
        $aListings = [];
        $aAtts     = [
            'maximum_posts_on_lg_screen' => 'col-lg-3',
            'maximum_posts_on_md_screen' => 'col-md-4',
            'maximum_posts_on_sm_screen' => 'col-sm-6',
            'img_size'                   => 'wilcity_360x200'
        ];
        
        $aAtts = wp_parse_args($aSCSettings, $aAtts);
        while ($query->have_posts()) {
            $query->the_post();
            $aListings[] = self::jsonSkeleton($query->post, $aAtts);
        }
        wp_reset_postdata();
        
        return [
            'status'   => 'success',
            'listings' => $aListings
        ];
    }
    
    public function debugQuery($null, $that)
    {
        if (strpos($that->request, 'wiloke_distance') !== false) {
            var_export($that->query_vars['orderby']);
            die;
        }
    }
    
    protected function isAdminQuery()
    {
        if (is_admin() && !wp_doing_ajax()) {
            return true;
        }
        
        if (wp_doing_ajax()) {
            if (isset($_POST['action'])) {
                $action = $_POST['action'];
            } else if (isset($_GET['action'])) {
                $action = $_GET['action'];
            }
            
            if (!isset($action) || strpos($action, 'wilcity') === false) {
                return true;
            }
        }
        
        return false;
    }
    
    protected function isIgnoreModify($that, $postType)
    {
        if (isset($that->query_vars['isIgnoreAllQueries']) && $that->query_vars['isIgnoreAllQueries']) {
            return true;
        }
        
        if (isset($that->query_vars['aIgnoreModifyPostTypes']) &&
            is_array($that->query_vars['aIgnoreModifyPostTypes']) && in_array($postType,
                $that->query_vars['aIgnoreModifyPostTypes'])
        ) {
            return true;
        }
        
        return false;
    }
    
    protected function isFocusExcludeEventExpired($that)
    {
        if (!isset($that->query_vars['post_type']) || $that->query_vars['post_type'] != 'event') {
            return false;
        }
        
        if (isset($that->query_vars['isFocusExcludeEventExpired']) && $that->query_vars['isFocusExcludeEventExpired']) {
            return true;
        }
        
        return false;
    }
    
    public static function flushSearchCache()
    {
        $aPostTypeKeys = General::getPostTypeKeys(false, false);
        
        if (empty($aPostTypeKeys)) {
            return false;
        }
        
        foreach ($aPostTypeKeys as $postType) {
            SetSettings::deleteOption(General::mainSearchFormSavedAtKey($postType));
        }
    }
    
    public function modifyTermsClauses($clauses, $taxonomy, $aArgs)
    {
        if ($this->isAdminQuery()) {
            return $clauses;
        }
        global $wpdb;
        if (isset($aArgs['postTypes'])) {
            $postTypes = $aArgs['postTypes'];
        } else if (isset($aArgs['post_types'])) {
            $postTypes = $aArgs['post_types'];
        }
        
        if (!isset($postTypes) || empty($postTypes)) {
            return $clauses;
        }
        
        // allow for arrays
        if (is_array($postTypes)) {
            $postTypes = array_map(function ($type) {
                global $wpdb;
                
                return $wpdb->_real_escape($type);
            }, $postTypes);
            $postTypes = implode("','", $postTypes);
        } else {
            $postTypes = $wpdb->_real_escape($postTypes);
        }
        
        $clauses['join']  .= " INNER JOIN $wpdb->term_relationships AS r ON r.term_taxonomy_id = tt.term_taxonomy_id INNER JOIN $wpdb->posts AS p ON p.ID = r.object_id";
        $clauses['where'] .= " AND p.post_type IN ('".$postTypes."') GROUP BY t.term_id";
        
        return $clauses;
    }
    
    public function saveSearchFormVersion($postType, $aFields)
    {
        SetSettings::setOptions($this->searchFormVersionKey, current_time('timestamp'));
    }
    
    public function printQuickSearchForm()
    {
        $aQuickSearchForm = GetSettings::getOptions('quick_search_form_settings');
        if (!isset($aQuickSearchForm['toggle_quick_search_form']) ||
            $aQuickSearchForm['toggle_quick_search_form'] == 'no'
        ) {
            return '';
        }
        ?>
        <quick-search-form-popup popup-id="quick-search-form-popup"
                                 raw-settings='<?php echo json_encode($aQuickSearchForm); ?>'></quick-search-form-popup>
        <?php
    }
    
    /*
     * @since 1.2.1
     */
    public function fetchHeroSearchFormSuggestion()
    {
        $aQuickSearchForm = GetSettings::getOptions('quick_search_form_settings');
        if (!isset($aQuickSearchForm['toggle_quick_search_form']) ||
            $aQuickSearchForm['toggle_quick_search_form'] == 'no'
        ) {
            return wp_send_json_error();
        }
        
        if ($aQuickSearchForm['suggestion_order_by'] == 'rand') {
            $aListOrderBy = [
                'count' => 'Count',
                'id'    => 'ID',
                'slug'  => 'Slug',
                'name'  => 'Name',
                'none'  => 'None'
            ];
            $orderby      = array_rand($aListOrderBy);
        } else {
            $orderby = $aQuickSearchForm['suggestion_order_by'];
        }
        
        $args = [
            'taxonomy' => $aQuickSearchForm['taxonomy_suggestion'],
            'number'   => !empty($aQuickSearchForm['number_of_term_suggestions']) ?
                $aQuickSearchForm['number_of_term_suggestions'] : 6,
            'orderby'  => $orderby,
            'order'    => $aQuickSearchForm['suggestion_order']
        ];
        
        if (isset($aQuickSearchForm['isShowParentOnly']) && $aQuickSearchForm['isShowParentOnly'] == 'yes') {
            $args['parent'] = 0;
        }
        
        $aTerms = GetSettings::getTerms($args);
        
        if (empty($aTerms) || is_wp_error($aTerms)) {
            wp_send_json_error();
        }
        
        foreach ($aTerms as $order => $oTerm) {
            $aTerms[$order]->link        = get_term_link($oTerm);
            $aGradientSettings           = GetSettings::getTermGradients($oTerm);
            $aTerms[$order]->oGradient   = $aGradientSettings;
            $aTerms[$order]->featuredImg = \WilokeHelpers::getTermFeaturedImage($oTerm, [700, 350]);
        }
        
        wp_send_json_success(['aResults' => $aTerms]);
    }
    
    public static function parseRequestFromUrl()
    {
        $aRequest = [];
        if (isset($_REQUEST['date_range']) && !empty($_REQUEST['date_range'])) {
            $dateRange  = urldecode($_REQUEST['date_range']);
            $aDateRange = explode(',', $dateRange);
            if (!empty($aDateRange[0]) && !empty($aDateRange[1])) {
                $aRequest['date_range'] = [
                    'from' => $aDateRange[0],
                    'to'   => $aDateRange[1]
                ];
            }
        }
        
        if (isset($_REQUEST['title']) && !empty($_REQUEST['title'])) {
            $aRequest['title'] = urldecode($_REQUEST['title']);
        }
        
        if (isset($_REQUEST['type']) && !empty($_REQUEST['type'])) {
            $aRequest['type'] = urldecode($_REQUEST['type']);
        } else {
            $aRequest['type'] = General::getDefaultPostTypeKey();
        }
        
        if (isset($_REQUEST['order'])) {
            $aRequest['order'] = sanitize_text_field($_REQUEST['order']);
        }
        
        if (isset($_REQUEST['orderby'])) {
            $aRequest['orderby'] = sanitize_text_field($_REQUEST['orderby']);
        }
        
        if (isset($_REQUEST['latLng']) && !empty($_REQUEST['latLng'])) {
            $latLng       = urldecode($_REQUEST['latLng']);
            $aParseLatLng = explode(',', $latLng);
            if (!empty($aParseLatLng[0]) && !empty($aParseLatLng[1])) {
                $aRequest['oAddress']['lat']     = $aParseLatLng[0];
                $aRequest['oAddress']['lng']     = $aParseLatLng[1];
                $aRequest['oAddress']['address'] = urldecode($_REQUEST['address']);
                $aRequest['oAddress']['radius']  = isset($_REQUEST['radius']) ? $_REQUEST['radius'] :
                    GetSettings::getSearchFormField
                ($_REQUEST['type'], 'defaultRadius');
                $aRequest['oAddress']['unit']    = isset($_REQUEST['unit']) ? $_REQUEST['unit'] :
                    GetSettings::getSearchFormField($_REQUEST['type'], 'unit');
            }
        }
        
        if (isset($_REQUEST['listing_cat']) && !empty($_REQUEST['listing_cat'])) {
            $listingCats             = urldecode($_REQUEST['listing_cat']);
            $aRequest['listing_cat'] = explode(',', $listingCats);
        }
        
        if (isset($_REQUEST['listing_location']) && !empty($_REQUEST['listing_location'])) {
            $aRequest['listing_location'] = urldecode($_REQUEST['listing_location']);
        }
        
        if (isset($_REQUEST['listing_tag']) && !empty($_REQUEST['listing_tag'])) {
            $aRequest['listing_tag'] = urldecode($_REQUEST['listing_tag']);
        }
        
        return $aRequest;
    }
    
    public static function isValidTerm($postType, $oTerm)
    {
        $aTermBelongsTo = GetSettings::getTermMeta($oTerm->term_id, 'belongs_to');
        if (in_array($oTerm->term_id, self::$aTermsPrinted)) {
            return false;
        }
        
        if (empty($aTermBelongsTo)) {
            return true;
        }
        
        return in_array($postType, $aTermBelongsTo);
    }
    
    public function buildTermItemInfo($oTerm)
    {
        $aTerm['value']  = $oTerm->slug;
        $aTerm['name']   = $oTerm->name;
        $aTerm['parent'] = $oTerm->parent;
        
        $aIcon = \WilokeHelpers::getTermOriginalIcon($oTerm);
        if ($aIcon) {
            $aTerm['oIcon'] = $aIcon;
        } else {
            $featuredImgID  = GetSettings::getTermMeta($oTerm->term_id, 'featured_image_id');
            $featuredImg    = wp_get_attachment_image_url($featuredImgID, [32, 32]);
            $aTerm['oIcon'] = [
                'type' => 'image',
                'url'  => $featuredImg
            ];
        }
        
        return $aTerm;
    }
    
    public function fetchTermOptions()
    {
        $at      = abs($_POST['at']);
        $savedAt = GetSettings::getOptions('get_taxonomy_saved_at');
        
        if (empty($savedAt)) {
            $savedAt = current_time('timestamp', 1);
            SetSettings::setOptions('get_taxonomy_saved_at', $savedAt);
        }
        
        if ($at == $savedAt) {
            wp_send_json_success([
                'action' => 'used_cache'
            ]);
        }
        
        if (isset($_POST['orderBy']) && !empty($_POST['orderBy'])) {
            $orderBy = $_POST['orderBy'];
        } else {
            $orderBy = 'count';
        }
        
        if (isset($_POST['order']) && !empty($_POST['order'])) {
            $order = $_POST['order'];
        } else {
            $order = 'DESC';
        }
        
        $isShowParentOnly = isset($_POST['isShowParentOnly']) && $_POST['isShowParentOnly'] == 'yes';
        $aRawTerms        = GetSettings::getTaxonomyHierarchy([
            'taxonomy'   => $_POST['taxonomy'],
            'orderby'    => $orderBy,
            'order'      => $order,
            'hide_empty' => isset($_POST['isHideEmpty']) ? $_POST['isHideEmpty'] : 0,
            'parent'     => 0
        ], $_POST['postType'], $isShowParentOnly, true);
        
        if (!$aRawTerms) {
            $aTerms = [
                [
                    -1 => esc_html__('There are no terms', 'wiloke-listing-tools')
                ]
            ];
        } else {
            $aTerms = [];
            foreach ($aRawTerms as $oTerm) {
                if (isset($_POST['postType']) && !self::isValidTerm($_POST['postType'], $oTerm)) {
                    continue;
                }
                
                $aTerms[] = $this->buildTermItemInfo($oTerm);
            }
        }
        wp_send_json_success([
            'terms'  => $aTerms,
            'action' => 'update_new_terms',
            'at'     => $savedAt
        ]);
    }
    
    public function getHeroSearchFields()
    {
        $postType = $_GET['postType'];
        $at       = isset($_GET['at']) ? abs($_GET['at']) : 0;
        if (empty($postType)) {
            wp_send_json_error(['msg' => esc_html__('The Directory Type is required.', 'wiloke-listing-tools')]);
        }
        
        $at      = abs($at);
        $savedAt = GetSettings::getOptions(General::heroSearchFormSavedAt($postType));
        
        if (empty($savedAt)) {
            $savedAt = current_time('timestamp', 1);
            SetSettings::setOptions(General::heroSearchFormSavedAt('heroSearchFormSavedAt'), $savedAt);
        } else {
            $savedAt = abs($savedAt);
        }
        
        if ($at == $savedAt) {
            wp_send_json_success([
                'action'  => 'use_cache',
                'success' => 'success'
            ]);
        }
        
        $aFields = GetSettings::getOptions(General::getHeroSearchFieldsKey($postType));
        $aFields = apply_filters('wilcity/filter/hero-search-form/fields', $aFields);
        
        if (empty($aFields)) {
            wp_send_json_error([
                'msg'    => esc_html__('Please go to Wiloke Tools -> Your Directory Type settings -> Add some fields to Hero Search Form',
                    'wiloke-listing-tools'),
                'status' => 'error'
            ]);
        }
        
        return wp_send_json_success([
            'oSettings' => $aFields,
            'at'        => $savedAt,
            'action'    => 'update_field',
            'status'    => 'success'
        ]);
    }
    
    public function fetchHeroSearchFields()
    {
        if (empty($_GET['postType'])) {
            wp_send_json_error(['msg' => esc_html__('The Directory Type is required.', 'wiloke-listing-tools')]);
        }
        
        $at      = abs($_GET['at']);
        $savedAt = GetSettings::getOptions(General::heroSearchFormSavedAt($_GET['postType']));
        
        if (empty($savedAt)) {
            $savedAt = current_time('timestamp', 1);
            SetSettings::setOptions(General::heroSearchFormSavedAt('heroSearchFormSavedAt'), $savedAt);
        } else {
            $savedAt = abs($savedAt);
        }
        
        if ($at == $savedAt) {
            wp_send_json_success([
                'action' => 'use_cache'
            ]);
        }
        
        $aFields = GetSettings::getOptions(General::getHeroSearchFieldsKey($_GET['postType']));
        $aFields = apply_filters('wilcity/filter/hero-search-form/fields', $aFields);
        
        if (empty($aFields)) {
            wp_send_json_error([
                'msg' => esc_html__('Please go to Wiloke Tools -> Your Directory Type settings -> Add some fields to Hero Search Form',
                    'wiloke-listing-tools')
            ]);
        }
        
        wp_send_json_success([
            'oSettings' => $aFields,
            'at'        => $savedAt,
            'action'    => 'update_field'
        ]);
    }
    
    public function mapSearchFormPopup()
    {
        
        if (GetSettings::isTaxonomyUsingCustomPage()) {
            return '';
        }
        
        if (wilcityIsMapPage() || is_tax('listing_cat') || is_tax('listing_location') || is_tax('listing_tag') ||
            wilcityIsNoMapTemplate()
        ) {
            
            global $wiloke, $post;
            $latLng      = $address = $taxonomy = $taxID = '';
            $type        = 'listing';
            $aTaxonomies = [];
            $aDateRange  = [];
            $aRequest    = SearchFormController::parseRequestFromUrl();
            
            if (is_tax()) {
                $taxSlug  = get_query_var('term');
                $taxonomy = get_query_var('taxonomy');
                
                $taxID = get_queried_object_id();
                if ($taxonomy == 'listing_cat' || $taxonomy == 'listing_tag') {
                    $aRequest[$taxonomy]    = [$taxSlug];
                    $aTaxonomies[$taxonomy] = [$taxSlug];
                } else {
                    $aRequest[$taxonomy]    = $taxSlug;
                    $aTaxonomies[$taxonomy] = $taxSlug;
                }
                
                if (isset($_REQUEST['type'])) {
                    $type                 = $_REQUEST['type'];
                    $aRequest['postType'] = $type;
                } else {
                    if (isset($taxID)) {
                        $aBelongsTo = GetSettings::getTermMeta($taxID, 'belongs_to');
                        if (!empty($aBelongsTo)) {
                            $type             = $aBelongsTo[0];
                            $aRequest['type'] = $type;
                        }
                    }
                }
                if (isset($wiloke->aThemeOptions['taxonomy_image_size']) &&
                    !empty($wiloke->aThemeOptions['taxonomy_image_size'])
                ) {
                    $aRequest['img_size'] = $wiloke->aThemeOptions['taxonomy_image_size'];
                }
            } else {
                if (isset($aRequest['listing_cat'])) {
                    $aTaxonomies['listing_cat'] = $aRequest['listing_cat'];
                }
                
                if (isset($aRequest['listing_location'])) {
                    $aTaxonomies['listing_location'] = $aRequest['listing_location'];
                }
                
                $imgSize = GetSettings::getPostMeta($post->ID, 'search_img_size');
                if (!empty($imgSize)) {
                    $aRequest['img_size'] = $imgSize;
                }
            }
            
            $aTaxonomiesOption = [];
            
            if (!empty($aTaxonomies)) {
                foreach ($aTaxonomies as $tax => $rawSlug) {
                    $slug      = is_array($rawSlug) ? $rawSlug[0] : $rawSlug;
                    $oTermInfo = get_term_by('slug', $slug, $tax);
                    if (!empty($oTermInfo) && !is_wp_error($oTermInfo)) {
                        $aTaxonomiesOption[$tax] = [
                            [
                                'name'  => $oTermInfo->name,
                                'value' => $slug
                            ]
                        ];
                    }
                }
            }
            if (isset($aRequest['oAddress'])) {
                $address = $aRequest['oAddress']['address'];
                $latLng  = $aRequest['oAddress']['lat'].','.$aRequest['oAddress']['lng'];
            }
            
            if (isset($aRequest['type'])) {
                $type                 = $aRequest['type'];
                $aRequest['postType'] = $type;
            }
            
            if (!empty($aRequest['date_range'])) {
                $aDateRange = $aRequest['date_range'];
            }
            
            $search = isset($aRequest['title']) ? $aRequest['title'] : '';
            
            if (isset($aRequest['title'])) {
                $aRequest['s'] = $aRequest['title'];
            }
            
            if (!isset($aRequest['image_size'])) {
                $aRequest['image_size'] = '';
            }
            
            $aRequest = wp_parse_args(
                $aRequest,
                [
                    'postType'   => 'listing',
                    'image_size' => '',
                    'order'      => '',
                    'orderby'    => ''
                ]
            );
            
            $isMap = wilcityIsMapPage() ? 'yes' : 'no';
            
            ?>
            <search-form-popup type="<?php echo esc_attr($type); ?>" is-map="<?php echo esc_attr($isMap); ?>"
                               posts-per-page="<?php echo esc_attr($wiloke->aThemeOptions['listing_posts_per_page']); ?>"
                               raw-taxonomies='<?php echo esc_attr(json_encode($aTaxonomies)); ?>'
                               s="<?php echo esc_attr($search); ?>" address="<?php echo esc_attr($address); ?>"
                               lat-lng="<?php echo esc_attr($latLng); ?>"
                               raw-date-range='<?php echo esc_attr(json_encode($aDateRange)); ?>'
                               form-item-class="col-md-6 col-lg-6"
                               popup-title="<?php esc_html_e('Search', 'wiloke-listing-tools'); ?>"
                               image-size="<?php echo esc_attr($aRequest['image_size']); ?>"
                               raw-taxonomies-options="<?php echo esc_attr(json_encode($aTaxonomiesOption)); ?>"
                               order-by="<?php echo esc_attr($aRequest['orderby']); ?>"
                               order="<?php echo esc_attr($aRequest['order']); ?>" template-id=""
                               style="grid"></search-form-popup>
            <?php
        }
    }
    
    protected function getTag($oTerm, $aQuery = [])
    {
        $aTagSlugs = GetSettings::getTermMeta($oTerm->term_id, 'tags_belong_to');
        if (empty($aTagSlugs)) {
            return false;
        }
        
        $aTagIDs = [];
        foreach ($aTagSlugs as $tag) {
            $oTag = get_term_by('slug', $tag, 'listing_tag');
            if ($oTag) {
                $aTagIDs[] = $oTag->term_id;
            }
        }
        
        $aArgs = [
            'taxonomy' => 'listing_tag',
            'include'  => $aTagIDs
        ];
        
        if (isset($aQuery['order']) && !empty($aQuery['order'])) {
            $aArgs['order'] = $aQuery['order'];
        }
        
        if (isset($aQuery['orderBy']) && !empty($aQuery['orderBy'])) {
            $aArgs['orderby'] = $aQuery['orderBy'];
        }
        
        if (isset($aQuery['hide_empty'])) {
            $aArgs['hide_empty'] = filter_var($aQuery['hide_empty'], FILTER_VALIDATE_BOOLEAN);
        } else {
            $aArgs['hide_empty'] = false;
        }
        
        $aTerms = get_terms($aArgs);
        if (empty($aTerms) || is_wp_error($aTerms)) {
            return false;
        }
        
        foreach ($aTerms as $oTag) {
            if (in_array($oTag->slug, $this->aGotTags)) {
                continue;
            }
            
            $this->aTagsBelongToCat[] = [
                'value' => $oTag->slug,
                'name'  => $oTag->name,
                'label' => $oTag->name
            ];
            $this->aGotTags[]         = $oTag->slug;
        }
    }
    
    public function fetchIndividualCatTags()
    {
        if (!isset($_POST['termSlug']) || empty($_POST['termSlug'])) {
            wp_send_json_error();
        } else {
            if (is_array($_POST['termSlug'])) {
                foreach ($_POST['termSlug'] as $termID) {
                    $oTerm = get_term_by('slug', $termID, 'listing_cat');
                    $this->getTag($oTerm, $_POST);
                }
            } else {
                $oTerm = get_term_by('slug', $_POST['termSlug'], 'listing_cat');
                $this->getTag($oTerm, $_POST);
            }
            
            if (empty($this->aTagsBelongToCat)) {
                wp_send_json_error();
            }
            
            wp_send_json_success($this->aTagsBelongToCat);
        }
    }
    
    public static function getSearchFormSettings()
    {
        if (empty(self::$aSearchFormSettings)) {
            self::$aSearchFormSettings = GetSettings::getOptions('quick_search_form_settings');
        }
        
        return self::$aSearchFormSettings;
    }
    
    public function fetchTermsSuggestions()
    {
        self::getSearchFormSettings();
        $isShowParentOnly = isset(self::$aSearchFormSettings['isShowParentOnly']) &&
                            self::$aSearchFormSettings['isShowParentOnly'] == 'yes' ? 1 : '';
        $aRawCategories   = GetSettings::getTerms([
            'taxonomy'   => self::$aSearchFormSettings['taxonomy_suggestion'],
            'number'     => self::$aSearchFormSettings['number_of_term_suggestions'],
            'orderby'    => self::$aSearchFormSettings['suggestion_order_by'],
            'hide_empty' => false,
            'parent'     => $isShowParentOnly
        ]);
        
        if (!empty($aRawCategories) && !is_wp_error($aRawCategories)) {
            $aCategories = [];
            foreach ($aRawCategories as $oRawCategory) {
                $aCategories[] = [
                    'name'  => $oRawCategory->name,
                    'slug'  => $oRawCategory->slug,
                    'id'    => $oRawCategory->term_id,
                    'link'  => get_term_link($oRawCategory),
                    'oIcon' => \WilokeHelpers::getTermOriginalIcon($oRawCategory)
                ];
            }
            
            wp_send_json_success([
                'aResults' => $aCategories
            ]);
        }
        
        wp_send_json_error();
    }
    
    private function setupListingQuickSearchFromSkeleton($oPost, $oPostTypeObject, $defaultFeaturedImg)
    {
        $postThumbnail = get_the_post_thumbnail_url($oPost->ID, 'thumbnail');
        
        $aSinglePost = [
            'postTitle'      => get_the_title($oPost->ID),
            'postType'       => $oPost->post_type,
            'postLink'       => get_post_permalink($oPost->ID),
            'thumbnail'      => empty($postThumbnail) ? $defaultFeaturedImg : $postThumbnail,
            'thumbnailLarge' => get_the_post_thumbnail_url($oPost->ID, 'large'),
            'logo'           => GetSettings::getLogo($oPost->ID, 'thumbnail'),
            'name'           => $oPostTypeObject->labels->name,
            'singularName'   => $oPostTypeObject->labels->singular_name,
            'tagLine'        => GetSettings::getTagLine($oPost->ID)
        ];
        
        $oTerm = GetSettings::getLastPostTerm($oPost->ID, 'listing_cat');
        if ($oTerm) {
            $aSinglePost['oIcon'] = \WilokeHelpers::getTermOriginalIcon($oTerm);
        } else {
            $aSinglePost['oIcon'] = false;
        }
        
        return $aSinglePost;
    }
    
    public function excludePostTypeIfThereIsNoPost($aPostTypes, $aPosts)
    {
        foreach ($aPostTypes as $postType) {
            if (!isset($aPosts[$postType]['posts'])) {
                unset($aPosts[$postType]);
            }
        }
        
        return $aPosts;
    }
    
    /**
     * Search posts by keyword
     */
    public function ajaxSearch()
    {
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        
        $aPostTypes       = General::getPostTypeKeys(false);
        $aQuickSearchForm = GetSettings::getOptions('quick_search_form_settings');
        if (isset($aQuickSearchForm['exclude_post_types']) && !empty($aQuickSearchForm['exclude_post_types'])) {
            $aPostTypes = array_diff($aPostTypes, $aQuickSearchForm['exclude_post_types']);
        }
        $postTypes = implode("','", $aPostTypes);
        
        global $wpdb;
        $keyword = esc_sql($keyword);
        
        $aResults = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID, post_type, post_title, post_excerpt FROM $wpdb->posts WHERE post_title LIKE %s AND post_status='publish' AND post_type IN ('".
                $postTypes."') ORDER BY IF(menu_order!=0, menu_order, post_date) LIMIT 20 ",
                '%'.$keyword.'%'
            )
        );
        
        $total  = 0;
        $aPosts = [];
        
        foreach ($aPostTypes as $postType) {
            $aPosts[$postType] = [];
        }
        
        $aOptions           = \Wiloke::getThemeOptions(true);
        $defaultFeaturedImg = '';
        if (isset($aOptions['listing_featured_image']) && isset($aOptions['listing_featured_image']['id']) &&
            !empty($aOptions['listing_featured_image']['id'])
        ) {
            $defaultFeaturedImg = wp_get_attachment_image_url($aOptions['listing_featured_image']['id'], 'thumbnail');
        }
        
        $aExcludes = [];
        if (!empty($aResults) && !is_wp_error($aResults)) {
            $total = count($aResults);
            foreach ($aResults as $oResult) {
                if (isset($this->aCacheObjectTypes[$oResult->post_type]) &&
                    !empty($this->aCacheObjectTypes[$oResult->post_type])
                ) {
                    $oPostTypeObject = $this->aCacheObjectTypes[$oResult->post_type];
                } else {
                    $oPostTypeObject                              = get_post_type_object($oResult->post_type);
                    $this->aCacheObjectTypes[$oResult->post_type] = $oPostTypeObject;
                }
                
                if (!isset($aPosts[$oResult->post_type]['posts'])) {
                    $aPosts[$oResult->post_type]['groupTitle'] = $oPostTypeObject->labels->name;
                    $aPosts[$oResult->post_type]['posts']      = [];
                }
                
                $aPosts[$oResult->post_type]['posts'][] = $this->setupListingQuickSearchFromSkeleton($oResult,
                    $oPostTypeObject, $defaultFeaturedImg);
                $aExcludes[]                            = $oResult->ID;
            }
        }
        
        if ($total == 20) {
            $aPosts = $this->excludePostTypeIfThereIsNoPost($aPostTypes, $aPosts);
            wp_send_json_success([
                'type'     => 'postTitle',
                'aResults' => $aPosts
            ]);
        }
        
        if ($total < 20) {
            $aArgs = [
                'post_type'      => $aPostTypes,
                'post_status'    => 'publish',
                'posts_per_page' => 20 - $total,
                'orderby'        => 'menu_order post_date'
            ];
            if (!empty($aExcludes)) {
                $aArgs['post__not_in'] = $aExcludes;
            }
            
            $aRawCategories = GetSettings::getTerms(
                [
                    'taxonomy'   => 'listing_tag',
                    'hide_empty' => true,
                    'name__like' => $keyword,
                    'number'     => 20
                ]
            );
            
            if (!empty($aRawCategories) && !is_wp_error($aRawCategories)) {
                $aCategories = [];
                foreach ($aRawCategories as $oRawCategory) {
                    $aCategories[] = $oRawCategory->term_id;
                }
                $aArgs['tax_query'] = [
                    [
                        'taxonomy' => 'listing_tag',
                        'field'    => 'term_id',
                        'terms'    => $aCategories
                    ]
                ];
                $query              = new \WP_Query($aArgs);
                
                if ($query->have_posts()) {
                    global $post;
                    while ($query->have_posts()) {
                        $query->the_post();
                        if (isset($this->aCacheObjectTypes[$post->post_type]) &&
                            !empty($this->aCacheObjectTypes[$post->post_type])
                        ) {
                            $oPostTypeObject = $this->aCacheObjectTypes[$post->post_type];
                        } else {
                            $oPostTypeObject                           = get_post_type_object($post->post_type);
                            $this->aCacheObjectTypes[$post->post_type] = $oPostTypeObject;
                        }
                        
                        if (!isset($aPosts[$post->post_type]['posts'])) {
                            $aPosts[$post->post_type]['groupTitle'] = $oPostTypeObject->labels->name;
                            $aPosts[$post->post_type]['posts']      = [];
                        }
                        
                        $aPosts[$post->post_type]['posts'][] = $this->setupListingQuickSearchFromSkeleton($post,
                            $oPostTypeObject, $defaultFeaturedImg);
                    }
                }
            }
        }
        
        if (empty($aPosts)) {
            wp_send_json_error();
        }
        $aPosts = $this->excludePostTypeIfThereIsNoPost($aPostTypes, $aPosts);
        wp_send_json_success([
            'type'     => 'postTitle',
            'aResults' => $aPosts
        ]);
    }
    
    private function isQueryEvent($that, $isFocusDate = true)
    {
        
        if ($that->query_vars['post_type'] == 'event' && isset($that->query_vars['isAppEventQuery'])) {
            return true;
        }
        
        if ((is_admin() && !wp_doing_ajax())) {
            return false;
        }
        
        if (is_singular() && !is_page_template()) {
            if ($that->query_vars['post_type'] == 'event') {
                return false;
            }
        }
        
        if ($this->isIgnoreModify($that, 'event')) {
            return false;
        }
        
        if ($isFocusDate) {
            return $that->query_vars['post_type'] == 'event' && isset($that->query_vars['date_range']) &&
                   !empty($that->query_vars['date_range']);
        }
        
        return ($that->query_vars['post_type'] == 'event') ? true : false;
    }
    
    private function orderByEventConditional($that)
    {
        return !isset($that->query_vars['orderby']) || empty($that->query_vars['orderby']) ||
               (strpos($that->query_vars['orderby'],
                       '_event') === false) ? false : true;
    }
    
    public function customQuery($aVars)
    {
        $aVars[] = 'geocode';
        $aVars[] = 'map_bounds';
        $aVars[] = 'latLng';
        $aVars[] = 'unit';
        $aVars[] = 'open_now';
        $aVars[] = 'date_range';
        $aVars[] = 'is_map';
        
        return $aVars;
    }
    
    public function checkEventQuery($x, $that)
    {
        if (current_user_can('administrator') && wp_doing_ajax()) {
            var_export($that->request);
            die();
        }
        
        return $x;
    }
    
    public function addHavingDistance($nothing, $that)
    {
        if ($this->isAdminQuery()) {
            return $nothing;
        }
        if (isset($that->query_vars['geocode']) && !empty($that->query_vars['geocode'])) {
            global $wpdb;
            $radius        = $wpdb->_real_escape($that->query_vars['geocode']['radius']);
            $that->request = str_replace('ORDER BY', 'HAVING wiloke_distance < '.$radius.' ORDER BY', $that->request);
        }
        
        return $nothing;
    }
    
    private function isUsingDefaultOrderBy($that)
    {
        if ($this->isUsingDefaultOrderBy) {
            return true;
        }
        
        foreach ($this->aSpecialOrderBy as $special) {
            if (strpos($that->query_vars['orderby'], $special) === 0) {
                $this->isUsingDefaultOrderBy = true;
            }
        }
        
        return $this->isUsingDefaultOrderBy;
    }
    
    public function orderByDistance($orderBy, $that)
    {
        if ($this->isAdminQuery()) {
            return $orderBy;
        }
        
        if (isset($that->query_vars['geocode']) && !empty($that->query_vars['geocode'])) {
            if (!$this->isUsingDefaultOrderBy($that)) {
                return 'wiloke_distance';
            }
        }
        
        return $orderBy;
    }
    
    public function addEventSelection($field, $that)
    {
        
        if (!$this->isQueryEvent($that, false)) {
            return $field;
        }
        
        if ($this->orderByEventConditional($that) || $this->isQueryEvent($that, false)) {
            global $wpdb;
            $eventDataTbl = $wpdb->prefix.AlterTableEventsData::$tblName;
            $field        .= ", $eventDataTbl.startsOn as wilcity_event_starts_on, $eventDataTbl.endsOn, $eventDataTbl.frequency";
        }
        
        return $field;
    }
    
    public function addMapBoundsToQuery($where, $that)
    {
        if ($this->isAdminQuery()) {
            return $where;
        }
        if (isset($that->query_vars['map_bounds']) && !empty($that->query_vars['map_bounds'])) {
            global $wpdb;
            $latLngTbl  = $wpdb->prefix.AlterTableLatLng::$tblName;
            $additional =
                " AND ( ($latLngTbl.lat >= ".$wpdb->_real_escape($that->query_vars['map_bounds']['aFLatLng']['lat']).
                " AND $latLngTbl.lat <= ".$wpdb->_real_escape($that->query_vars['map_bounds']['aSLatLng']['lat']).
                ") AND ( $latLngTbl.lng >= ".$wpdb->_real_escape($that->query_vars['map_bounds']['aFLatLng']['lng']).
                " AND  $latLngTbl.lng <= ".$wpdb->_real_escape($that->query_vars['map_bounds']['aSLatLng']['lng']).
                " ) )";
            $where      .= $additional;
        }
        
        return $where;
    }
    
    public function addLatLngSelectionToQuery($field, $that)
    {
        if ($this->isAdminQuery()) {
            return $field;
        }
        if (isset($that->query_vars['geocode']) && !empty($that->query_vars['geocode'])) {
            global $wpdb;
            $latLngTbl = $wpdb->prefix.AlterTableLatLng::$tblName;
            
            $unit         = $wpdb->_real_escape($that->query_vars['geocode']['unit']);
            $aParseLatLng = explode(',', $that->query_vars['geocode']['latLng']);
            $unit         = $unit == 'km' ? 6371 : 3959;
            $lat          = $wpdb->_real_escape(trim($aParseLatLng[0]));
            $lng          = $wpdb->_real_escape(trim($aParseLatLng[1]));
            
            $field .= ",( $unit * acos( cos( radians('".$lat.
                      "') ) * cos( radians( $latLngTbl.lat ) ) * cos( radians( $latLngTbl.lng ) - radians('".$lng.
                      "') ) + sin( radians('".$lat."') ) * sin( radians( $latLngTbl.lat ) ) ) ) as wiloke_distance";
        }
        
        return $field;
    }
    
    public function addEventBetweenDateRange($where, $that)
    {
        if (!$this->isQueryEvent($that) || !isset($that->query_vars['date_range'])) {
            return $where;
        }
        global $wpdb;
        
        $eventTbl = $wpdb->prefix.AlterTableEventsData::$tblName;
        
        $aDataRange = $that->query_vars['date_range'];
        
        $dateFormat = apply_filters('wilcity_date_picker_format', 'mm/dd/yy');
        $dateFormat = Time::convertJSDateFormatToPHPDateFormat($dateFormat);
        
        $from = Time::mysqlDateTime(Time::toTimestamp($dateFormat, $wpdb->_real_escape($aDataRange['from'])));
        $to   = Time::mysqlDateTime(Time::toTimestamp($dateFormat, $wpdb->_real_escape($aDataRange['to'])));
        
        $where .= " AND ($eventTbl.startsOn <= $eventTbl.endsOn) AND (
            ($eventTbl.startsOn >= '".$from."' AND $eventTbl.startsOn <= '".$to."')
            OR (
                $eventTbl.startsOn < '".$from."'
                AND
                ($eventTbl.endsOn >= '".$from."' OR $eventTbl.endsOn >= '".$to."')
            )
        )";
        
        return $where;
    }
    
    private function isPassedDateRange($aArgs)
    {
        
        if (isset($aArgs['date_range'])) {
            
            if (empty($aArgs['date_range']['from']) || empty($aArgs['date_range']['to']) || $from > $to) {
                return false;
            }
            
            $dateFormat = apply_filters('wilcity_date_picker_format', 'mm/dd/yy');
            $dateFormat = Time::convertJSDateFormatToPHPDateFormat($dateFormat);
            
            $from = Time::toTimestamp($dateFormat, $aArgs['date_range']['from']);
            $to   = Time::toTimestamp($dateFormat, $aArgs['date_range']['to']);
            
            if ($from > $to) {
                return false;
            }
        }
        
        return true;
    }
    
    public function addEventWhen($where, $that)
    {
        if (!$this->isQueryEvent($that, false) && !$this->isFocusExcludeEventExpired($that)) {
            return $where;
        }
        
        global $wpdb;
        $eventTbl = $wpdb->prefix.AlterTableEventsData::$tblName;
        $now      = Time::mysqlDateTime(current_time('timestamp', true));
        
        if (!$this->orderByEventConditional($that)) {
            
            if (empty($that->query_vars['orderby']) || in_array($that->query_vars['orderby'], $this->aDefaultOrderBy)) {
                $where .= " AND ($eventTbl.endsOnUTC >= '".$now."' OR $eventTbl.endsOnUTC IS NULL)";
            }
            
            return $where;
        }
        
        switch ($that->query_vars['orderby']) {
            case 'upcoming_event':
                $where .= " AND $eventTbl.startsOnUTC > '".$now."'";
                break;
            case 'ongoing_event':
            case 'happening_event':
                $where .= " AND $eventTbl.startsOnUTC <= '".$now."' AND $eventTbl.endsOnUTC >= '".$now."'";
                break;
            case 'expired_event':
                $where .= " AND (($eventTbl.endsOnUTC <= '".$now.
                          "' OR $eventTbl.endsOnUTC <= $eventTbl.startsOnUTC) || ($eventTbl.endsOnUTC IS NULL))";
                break;
            case 'starts_from_ongoing_event':
                $where .= " AND ( ( $eventTbl.startsOnUTC <= '".$now."' AND $eventTbl.endsOnUTC >= '".$now.
                          "') OR $eventTbl.startsOnUTC > '".$now."' )";
                break;
            default:
                $originalWhere = $where;
                if (!isset($that->query_vars['isDashboard'])) {
                    $where .= " AND $eventTbl.endsOnUTC >= '".$now."'";
                }
                
                $where = apply_filters('wilcity/wiloke-listing-tools/filter/event-orderby', $where, $originalWhere,
                    $that->query_vars['orderby'], $now);
                break;
        }
        
        return $where;
    }
    
    public function addPreventListingsThatDoesNotHaveLatLng($where, $that)
    {
        if ($this->isAdminQuery()) {
            return $where;
        }
        global $wpdb;
        $latLngTbl = $wpdb->prefix.AlterTableLatLng::$tblName;
        
        if (isset($that->query_vars['is_map']) && $that->query_vars['is_map'] == 'yes') {
            $where .= " AND ($latLngTbl.lat != '' AND $latLngTbl.lng != '' AND $latLngTbl.lat != $latLngTbl.lng) ";
        }
        
        return $where;
    }
    
    public function orderByEventDate($orderBy, $that)
    {
        if (!$this->isQueryEvent($that, false)) {
            return $orderBy;
        }
        
        if (isset($that->query_vars['orderby']) && (strpos($that->query_vars['orderby'],
                    'upcoming_event') !== false || strpos($that->query_vars['orderby'],
                    'happening_event') !== false || strpos($that->query_vars['orderby'],
                    'starts_from_ongoing_event') !== false)
        ) {
            return "wilcity_event_starts_on ".$that->query_vars['order'];
        }
        
        return isset($that->query_vars['order']) && !empty($that->query_vars['order']) ?
            'wilcity_event_starts_on '.$that->query_vars['order'] : 'wilcity_event_starts_on ASC';
    }
    
    public function joinEvents($join, $that)
    {
        global $wpdb;
        if (!$this->isQueryEvent($that, false) && !$this->isFocusExcludeEventExpired($that)) {
            return $join;
        }
        
        $eventsDataTbl = $wpdb->prefix.AlterTableEventsData::$tblName;
        
        if (strpos($join, $eventsDataTbl) !== false) {
            return $join;
        }
        
        $join .= " LEFT JOIN $eventsDataTbl ON ($eventsDataTbl.objectID = $wpdb->posts.ID)";
        
        return $join;
    }
    
    public function improveSearchTitle($search, $wpQuery)
    {
        if ($this->isAdminQuery()) {
            return $search;
        }
        
        if (!empty($search) && !empty($wpQuery->query_vars['search_terms'])) {
            global $wpdb;
            $aQuery          = $wpQuery->query_vars;
            $n               = !empty($q['exact']) ? '' : '%';
            $aSearch         = [];
            $aSpecialSearch  = [];
            $fQuery          = '';
            $allSearchString = implode(' ', $aQuery['search_terms']);
            
            foreach (( array )$aQuery['search_terms'] as $term) {
                $target           = $n.$wpdb->_real_escape($term).$n;
                $aSpecialSearch[] = $wpdb->prepare(
                    "($wpdb->posts.post_title LIKE %s)",
                    $target
                );
                
                $aSpecialSearch[] = $wpdb->prepare(
                    "($wpdb->posts.post_content LIKE %s)",
                    $target
                );
                
                $fQuery         .= (empty($fQuery) ? " " : " AND ")." (".implode(' OR ', $aSpecialSearch).")";
                $aSpecialSearch = [];
            }
            
            $aSearch[] = $wpdb->prepare(
                "($wpdb->postmeta.meta_key IN ('wilcity_phone', 'wilcity_website', 'wilcity_tagline') AND $wpdb->postmeta.meta_value LIKE %s)",
                $n.$allSearchString.$n
            );
            
            $latLngTbl = $wpdb->prefix.AlterTableLatLng::$tblName;
            
            $aSearch[]                     = ' ('.$wpdb->prepare(
                    "($latLngTbl.address LIKE %s OR $latLngTbl.lat = %s OR $latLngTbl.lng=%s)",
                    $wpdb->_real_escape($n.$allSearchString.$n), $wpdb->_real_escape($allSearchString),
                    $wpdb->_real_escape($allSearchString)
                ).') ';
            $search                        = ' AND ('.$fQuery.' OR '.' ('.implode(' OR ', $aSearch).') )';
            $this->isImprovedSearchByTitle = true;
        }
        
        return $search;
    }
    
    public function addUniQueryToSelectID($district, $that)
    {
        if ($this->isAdminQuery()) {
            return $district;
        }
        
        if (!isset($that->query_vars['search_terms']) || empty($that->query_vars['search_terms'])) {
            return $district;
        }
        
        if (!$this->isImprovedSearchByTitle) {
            return $district;
        }
        
        $this->isImprovedSearchByTitle = true;
        
        return 'DISTINCT';
    }
    
    public function maybeJoinEventData($join, $that)
    {
        if (!isset($that->query_vars['post_type']) || $that->query_vars['post_type'] !== 'event') {
            return $join;
        }
        
        if ($this->isAdminQuery()) {
            return $join;
        }
        
        global $wpdb;
        $eventDataTbl = $wpdb->prefix.AlterTableEventsData::$tblName;
        if (strpos($join, $eventDataTbl) !== false) {
            return $join;
        }
        
        $join .= " LEFT JOIN $eventDataTbl ON ($eventDataTbl.objectID = $wpdb->posts.ID)";
        
        return $join;
    }
    
    public function maybeJoinPostMeta($join, $that)
    {
        if ($this->isAdminQuery()) {
            return $join;
        }
        
        if ((!isset($that->query_vars['search_terms']) || empty($that->query_vars['search_terms']))) {
            return $join;
        }
        
        global $wpdb;
        $postMetaTbl = $wpdb->postmeta;
        if (strpos($join, $postMetaTbl) === false) {
            $join .= " LEFT JOIN $postMetaTbl ON ($postMetaTbl.post_id = $wpdb->posts.ID)";
        }
        
        $latLngTbl = $wpdb->prefix.AlterTableLatLng::$tblName;
        if (strpos($join, $latLngTbl) !== false) {
            return $join;
        }
        
        $join .= " LEFT JOIN $latLngTbl ON ($latLngTbl.objectID = $wpdb->posts.ID)";
        
        return $join;
    }
    
    public function preventListingsThatDoesNotHaveLatLng($join, $that)
    {
        if ($this->isAdminQuery()) {
            return $join;
        }
        global $wpdb;
        $latLngTbl = $wpdb->prefix.AlterTableLatLng::$tblName;
        if (isset($that->query_vars['is_map']) && $that->query_vars['is_map'] == 'yes') {
            $joinLatLng = " LEFT JOIN $latLngTbl ON ($latLngTbl.objectID = $wpdb->posts.ID)";
            if (strpos($join, $joinLatLng) === false) {
                $join .= " ".$joinLatLng;
            }
        }
        
        return $join;
    }
    
    public function addSelectEventStartUTC()
    {
    
    }
    
    public function joinLatLng($join, $that)
    {
        if ($this->isAdminQuery()) {
            return $join;
        }
        global $wpdb;
        if (((isset($that->query_vars['geocode']) && !empty($that->query_vars['geocode'])) ||
             (isset($that->query_vars['map_bounds']) && !empty($that->query_vars['map_bounds'])))
        ) {
            $latLngTbl  = $wpdb->prefix.AlterTableLatLng::$tblName;
            $joinLatLng = " LEFT JOIN $latLngTbl ON ($latLngTbl.objectID = $wpdb->posts.ID)";
            if (strpos($join, $joinLatLng) === false) {
                $join .= $joinLatLng;
            }
            
        }
        
        return $join;
    }
    
    public function joinOpenNow($join, $that)
    {
        if ($this->isAdminQuery()) {
            return $join;
        }
        global $wpdb;
        if (!isset($that->query_vars['open_now']) || empty($that->query_vars['open_now']) ||
            $that->query_vars['open_now'] == 'no'
        ) {
            return $join;
        }
        
        $businessHourTbl = $wpdb->prefix.AlterTableBusinessHours::$tblName;
        $bhMeta          = $wpdb->prefix.AlterTableBusinessHourMeta::$tblName;
        
        $join .= " LEFT JOIN $businessHourTbl ON ($businessHourTbl.objectID=$wpdb->posts.ID) LEFT JOIN $bhMeta ON ($bhMeta.objectID=$wpdb->posts.ID) ";
        
        return $join;
    }
    
    public function addOpenNowToPostsWhere($where, $that)
    {
        if ($this->isAdminQuery()) {
            return $where;
        }
        if (!isset($that->query_vars['open_now']) || empty($that->query_vars['open_now']) ||
            $that->query_vars['open_now'] == 'no'
        ) {
            return $where;
        }
        global $wpdb;
        
        $businessHourTbl  = $wpdb->prefix.AlterTableBusinessHours::$tblName;
        $businessHourMeta = $wpdb->prefix.AlterTableBusinessHourMeta::$tblName;
        
        date_default_timezone_set('UTC');
        $utcTimestampNow = \time();
        $todayIndex      = date('N', $utcTimestampNow);
        $dayKey          = Time::getDayKey($todayIndex - 1);
        $utcHourNow      = date('H:i:s', $utcTimestampNow);
        $where           .= " AND ( ($businessHourMeta.meta_key = 'wilcity_hourMode') AND ( ($businessHourMeta.meta_value = 'always_open')  OR ( ($businessHourMeta.meta_value = 'open_for_selected_hours') AND ($businessHourTbl.dayOfWeek='".
                            $wpdb->_real_escape($dayKey)."' AND $businessHourTbl.isOpen='yes' AND
		(
		    ($businessHourTbl.firstOpenHourUTC <= '".$utcHourNow."' AND '".$utcHourNow."' <= $businessHourTbl.firstCloseHourUTC)
		    OR
		    ($businessHourTbl.firstOpenHourUTC <= '".$utcHourNow."' AND '".$utcHourNow."' >= $businessHourTbl.firstCloseHourUTC AND $businessHourTbl.firstCloseHourUTC < $businessHourTbl.firstOpenHourUTC)
		    OR
		    (
		        ($businessHourTbl.secondOpenHourUTC IS NOT NULL AND $businessHourTbl.secondCloseHourUTC IS NOT NULL)
		        AND
		        (
		            ($businessHourTbl.secondOpenHourUTC <= '".$utcHourNow.
                            "' AND $businessHourTbl.secondCloseHourUTC >= '".$utcHourNow."')
		            OR
		            ($businessHourTbl.secondOpenHourUTC <= '".$utcHourNow."' AND '".$utcHourNow."' >= $businessHourTbl.secondCloseHourUTC AND $businessHourTbl.secondCloseHourUTC < $businessHourTbl.secondOpenHourUTC)
		        )
            )
		))) )) ";
        
        return $where;
    }
    
    public static function buildQueryArgs($aRequest)
    {
        if (empty($aRequest['postType'])) {
            $aRequest['postType'] = General::getFirstPostTypeKey(false, false);
        }
        $aArgs = [
            'post_type'   => $aRequest['postType'],
            'post_status' => 'publish',
            'is_map'      => isset($aRequest['is_map']) ? $aRequest['is_map'] : 'no'
        ];
        
        if (!isset($aRequest['orderby']) || empty($aRequest['orderby'])) {
            if ($aRequest['postType'] != 'event') {
                $orderBy = \WilokeThemeOptions::getOptionDetail('listing_search_page_order_by');
                if (empty($orderBy)) {
                    $orderBy = 'menu_order post_date';
                    $order   = 'DESC';
                } else {
                    $orderBy = $orderBy == 'menu_order' ?
                        $orderBy.' '.\WilokeThemeOptions::getOptionDetail('listing_search_page_order_by_fallback') :
                        $orderBy;
                    $order   = \WilokeThemeOptions::getOptionDetail('listing_search_page_order');
                }
            } else {
                $orderBy = \WilokeThemeOptions::getOptionDetail('event_search_page_order_by');
                if (empty($orderBy)) {
                    $orderBy = 'menu_order post_date';
                    $order   = 'DESC';
                } else {
                    $orderBy = $orderBy == 'menu_order' ?
                        $orderBy.' '.\WilokeThemeOptions::getOptionDetail('event_search_page_order_by_fallback') :
                        $orderBy;
                    $order   = \WilokeThemeOptions::getOptionDetail('event_search_page_order');
                }
            }
            $aArgs['order']   = $order;
            $aArgs['orderby'] = $orderBy;
        } else {
            $aArgs['order']   = $aRequest['order'];
            $aArgs['orderby'] = $aRequest['orderby'];
        }
        
        if ($aArgs['orderby'] == 'rand' && isset($aRequest['postsNotIn']) && is_array($aRequest['postsNotIn'])) {
            $aArgs['post__not_in'] = $aRequest['postsNotIn'];
        }
        
        if (isset($aRequest['aBounds']) && !empty($aRequest['aBounds'])) {
            unset($aRequest['oAddress']);
            $aArgs['map_bounds'] = $aRequest['aBounds'];
        }
        
        if (isset($aRequest['oAddress']) && !empty($aRequest['oAddress']) && !empty($aRequest['oAddress']['lat'])) {
            $aArgs['order'] = 'ASC';
            
            $aArgs['geocode'] = [
                'latLng' => $aRequest['oAddress']['lat'].','.$aRequest['oAddress']['lng'],
                'radius' => isset($aRequest['oAddress']['radius']) ? $aRequest['oAddress']['radius'] :
                    GetSettings::getSearchFormField($aRequest['postType'],
                        'defaultRadius'),
                'unit'   => isset($aRequest['oAddress']['unit']) ? $aRequest['oAddress']['unit'] :
                    GetSettings::getSearchFormField($aRequest['postType'],
                        'unit')
            ];
        }
        
        if (isset($aRequest['open_now']) && !empty($aRequest['open_now']) && $aRequest['open_now'] !== 'no') {
            $aArgs['open_now'] = $aRequest['open_now'];
        }
        
        if (isset($aRequest['date_range'])) {
            // $aArgs['date_range'] = $aRequest['date_range'];
            $aArgs['date_range'] = [
                'from' => $aRequest['date_range']['from'],
                'to'   => $aRequest['date_range']['to']
            ];
        }
        
        $taxLogic = apply_filters('wilcity/wiloke-listing-tools/filter/multi-tax-logic', 'AND');
        if (isset($aRequest['listing_location']) && !empty($aRequest['listing_location']) &&
            $aRequest['listing_location'] != -1
        ) {
            if (is_array($aRequest['listing_location'])) {
                if ($taxLogic == 'AND') {
                    foreach ($aRequest['listing_location'] as $term) {
                        $aArgs['tax_query'][] = [
                            'taxonomy' => 'listing_location',
                            'field'    => is_numeric($term) ? 'term_id' : 'slug',
                            'terms'    => $term
                        ];
                    }
                } else {
                    $aArgs['tax_query'][]           = [
                        'taxonomy' => 'listing_location',
                        'field'    => is_numeric($aRequest['listing_location'][0]) ? 'term_id' : 'slug',
                        'terms'    => $aRequest['listing_location']
                    ];
                    $aArgs['tax_query']['relation'] = 'OR';
                }
            } else {
                $aArgs['tax_query'][] = [
                    'taxonomy' => 'listing_location',
                    'field'    => is_numeric($aRequest['listing_location']) ? 'term_id' : 'slug',
                    'terms'    => $aRequest['listing_location']
                ];
            }
        }
        
        if (isset($aRequest['listing_cat']) && !empty($aRequest['listing_cat']) && $aRequest['listing_cat'] != -1) {
            if (is_array($aRequest['listing_cat'])) {
                if ($taxLogic == 'AND') {
                    foreach ($aRequest['listing_cat'] as $term) {
                        $aArgs['tax_query'][] = [
                            'taxonomy' => 'listing_cat',
                            'field'    => is_numeric($term) ? 'term_id' : 'slug',
                            'terms'    => $term
                        ];
                    }
                } else {
                    $aArgs['tax_query'][]           = [
                        'taxonomy' => 'listing_cat',
                        'field'    => is_numeric($aRequest['listing_cat'][0]) ? 'term_id' : 'slug',
                        'terms'    => $aRequest['listing_cat']
                    ];
                    $aArgs['tax_query']['relation'] = 'OR';
                }
            } else {
                $aArgs['tax_query'][] = [
                    'taxonomy' => 'listing_cat',
                    'field'    => is_numeric($aRequest['listing_cat']) ? 'term_id' : 'slug',
                    'terms'    => $aRequest['listing_cat']
                ];
            }
        }
        
        if (isset($aRequest['listing_tag']) && !empty($aRequest['listing_tag'])) {
            if (is_array($aRequest['listing_tag'])) {
                if ($taxLogic == 'AND') {
                    foreach ($aRequest['listing_tag'] as $term) {
                        $aArgs['tax_query'][] = [
                            'taxonomy' => 'listing_tag',
                            'field'    => is_numeric($term) ? 'term_id' : 'slug',
                            'terms'    => $term
                        ];
                    }
                } else {
                    $aArgs['tax_query'][]           = [
                        'taxonomy' => 'listing_tag',
                        'field'    => is_numeric($aRequest['listing_tag'][0]) ? 'term_id' : 'slug',
                        'terms'    => $aRequest['listing_tag']
                    ];
                    $aArgs['tax_query']['relation'] = 'OR';
                }
            } else {
                $aArgs['tax_query'][] = [
                    'taxonomy' => 'listing_tag',
                    'field'    => is_numeric($aRequest['listing_tag']) ? 'term_id' : 'slug',
                    'terms'    => $aRequest['listing_tag']
                ];
            }
        }
        
        if (isset($aArgs['tax_query']) && count($aArgs['tax_query']) > 1) {
            $aArgs['tax_query']['relation'] = 'AND';
        }
        
        if ((isset($aRequest['best_rated']) && $aRequest['best_rated'] == 'yes') || (strpos($aArgs['orderby'],
                    'best_rated') !== false && strpos($aArgs['orderby'], 'menu_order') === false)
        ) {
            $aArgs['orderby']  = 'meta_value_num';
            $aArgs['meta_key'] = 'wilcity_average_reviews';
            $aArgs['order']    = 'DESC';
            
            if (isset($aRequest['orderByFallback'])) {
                $aArgs['orderby'] = $aArgs['orderby'].' '.$aRequest['orderByFallback'];
            }
            
        } else if ((isset($aRequest['best_viewed']) && $aRequest['best_viewed'] == 'yes') || (strpos($aArgs['orderby'],
                    'best_viewed') !== false && strpos($aArgs['orderby'], 'menu_order') === false)
        ) {
            $aArgs['orderby']  = 'meta_value_num';
            $aArgs['meta_key'] = 'wilcity_count_viewed';
            $aArgs['order']    = 'DESC';
            
            if (isset($aRequest['orderByFallback'])) {
                $aArgs['orderby'] = $aArgs['orderby'].' '.$aRequest['orderByFallback'];
            } else {
                $aArgs['orderby'] = $aArgs['orderby'].' post_date';
            }
            
        } else if ((isset($aRequest['recommended']) && $aRequest['recommended'] == 'yes') || (strpos($aArgs['orderby'],
                    'recommended') !== false && strpos($aArgs['orderby'], 'menu_order') === false)
        ) {
            if (isset($aRequest['orderByFallback'])) {
                $aArgs['orderby'] = 'menu_order '.$aRequest['orderByFallback'];
            } else {
                $aArgs['orderby'] = 'menu_order post_date';
            }
            $aArgs['order'] = 'DESC';
        }
        
        if (isset($aRequest['price_range']) && !empty($aRequest['price_range']) &&
            $aRequest['price_range'] !== 'nottosay'
        ) {
            $aArgs['meta_query'][] = [
                [
                    'key'     => 'wilcity_price_range',
                    'value'   => $aRequest['price_range'],
                    'compare' => '='
                ]
            ];
        }
        
        if (isset($aArgs['meta_query']) && count($aArgs['meta_query'])) {
            $aArgs['meta_query']['relation'] = 'AND';
        }
        
        if (isset($aRequest['s']) && !empty($aRequest['s'])) {
            $aArgs['s'] = $aRequest['s'];
        }
        
        $aArgs['posts_per_page'] =
            isset($aRequest['postsPerPage']) && !empty($aRequest['postsPerPage']) ? absint($aRequest['postsPerPage']) :
                get_option('posts_per_page');
        $aArgs['posts_per_page'] = $aArgs['posts_per_page'] > 200 ? 200 : $aArgs['posts_per_page'];
        
        if (isset($aRequest['page']) && !empty($aRequest['page'])) {
            $aArgs['paged'] = abs($aRequest['page']);
        }
        
        return $aArgs;
    }
    
    public static function jsonSkeleton($post, $aAtts)
    {
        global $wiloke;
        $aThemeOptions = \Wiloke::getThemeOptions();
        
        $aListing = [];
        
        $aListing['postID']    = $post->ID;
        $aListing['postTitle'] = get_the_title($post->ID);
        $aListing['permalink'] = get_permalink($post->ID);
        $aListing['postType']  = $post->post_type;
        
        $aListing['logo'] = GetSettings::getLogo($post->ID);
        $tagLine          = GetSettings::getPostMeta($post->ID, 'tagline');
        
        if (empty($tagLine)) {
            $aListing['excerpt'] = \Wiloke::contentLimit($aThemeOptions['listing_excerpt_length'], $post, true,
                $post->post_content, true);
            $aListing['excerpt'] = strip_shortcodes($aListing['excerpt']);
        } else {
            $aListing['excerpt'] = $tagLine;
        }
        $aListing['featuredImage'] = GetSettings::getFeaturedImg($post->ID, $aAtts['img_size']);
        
        if (ReviewController::isEnableRating()) {
            $averageReview = GetSettings::getPostMeta($post->ID, 'average_reviews');
            if (empty($averageReview)) {
                $aListing['oReviews'] = false;
            } else {
                $aListing['oReviews']            = [];
                $aListing['oReviews']['average'] = $averageReview;
                $aListing['oReviews']['mode']    = ReviewController::getMode($post->post_type);
                $aListing['oReviews']['quality'] = ReviewMetaModel::getReviewQualityString($averageReview,
                    $post->post_type);
            }
        } else {
            $aListing['oReviews'] = false;
        }
        $aListingAddress = GetSettings::getListingMapInfo($post->ID);
        
        if (!empty($aListingAddress) && !empty($aListingAddress['lat'])) {
            $mapPageUrl                             = add_query_arg(
                [
                    'title' => $post->post_title,
                    'lat'   => $aListingAddress['lat'],
                    'lng'   => $aListingAddress['lng']
                ],
                $wiloke->aThemeOptions['map_page']
            );
            $aListing['oAddress']['mapPageUrl']     = $mapPageUrl;
            $aListing['oAddress']['address']        = stripslashes($aListingAddress['address']);
            $aListing['oAddress']['addressOnGGMap'] = GetSettings::getAddress($post->ID, true);
            $aListing['oAddress']['lat']            = $aListingAddress['lat'];
            $aListing['oAddress']['lng']            = $aListingAddress['lng'];
            $aListing['oAddress']['marker']         = SingleListing::getMapIcon($post);
        } else {
            $aListing['oAddress'] = false;
        }
        
        $aListing['phone'] = GetSettings::getPostMeta($post->ID, 'phone');
        
        $aFooterSettings = GetSettings::getOptions(General::getSingleListingSettingKey('footer_card',
            $post->post_type));
        $taxonomy        = isset($aFooterSettings['taxonomy']) ? $aFooterSettings['taxonomy'] : 'listing_cat';
        
        $oTermFooter = \WilokeHelpers::getTermByPostID($post->ID, $taxonomy);
        if (!$oTermFooter) {
            $aListing['oTermFooter'] = false;
        } else {
            $aListing['oTermFooter']['name']  = $oTermFooter->name;
            $aListing['oTermFooter']['link']  = get_term_link($oTermFooter->term_id);
            $aListing['oTermFooter']['oIcon'] = \WilokeHelpers::getTermOriginalIcon($oTermFooter);
        }
        if (BusinessHours::isEnableBusinessHour($post)) {
            $aBusinessHours = BusinessHours::getCurrentBusinessHourStatus($post);
            if ($aBusinessHours['status'] == 'day_off') {
                $aBusinessHours['class'] = ' color-quaternary';
            }
            $aListing['oBusinessHours'] = $aBusinessHours;
        } else {
            $aListing['oBusinessHours'] = false;
        }
        
        $aImagesID = GetSettings::getPostMeta($post->ID, 'gallery');
        if (empty($aImagesID)) {
            $aListing['gallery'] = false;
        } else {
            $aImagesSrc   = [];
            $gallery_size = apply_filters('wiloke-listing-tools/listing-card/gallery-size', 'large');
            foreach ($aImagesID as $id => $src) {
                $imgSrc = wp_get_attachment_image_url($id, $gallery_size);
                if ($imgSrc) {
                    $aImagesSrc[] = $imgSrc;
                } else {
                    $aImagesSrc[] = $src;
                }
            }
            $aListing['gallery'] = implode(',', $aImagesSrc);
        }
        
        $aListing['isMyFavorite'] = UserModel::isMyFavorite($post->ID) ? 'yes' : 'no';
        
        $aListing['price'] = '';
        $aPriceRange       = GetSettings::getPriceRange($post->ID, true);
        if (!$aPriceRange) {
            $aListing['priceRange'] = '';
            $price                  = GetSettings::getPostMeta($post->ID, 'single_price');
            if (!empty($price)) {
                $aListing['price'] = GetWilokeSubmission::renderPrice($price);
            }
        } else {
            $aListing['priceRange'] = GetWilokeSubmission::renderPrice($aPriceRange['minimumPrice']).' - '.
                                      GetWilokeSubmission::renderPrice($aPriceRange['maximumPrice']);
        }
        
        $aHeader = GetSettings::getOptions(General::getSingleListingSettingKey('header_card', $post->post_type));
        $type    = isset($aHeader['btnAction']) ? $aHeader['btnAction'] : 'total_views';
        
        $aListing['headerCardType'] = $type;
        switch ($type):
            case 'call_us':
                $aListing['headerCardAction'] = GetSettings::getListingPhone($post->ID);
                break;
            case 'email_us':
                $aListing['headerCardAction'] = GetSettings::getListingEmail($post->ID);
                break;
            default:
                $aListing['headerCardAction'] = GetSettings::getListingTotalViews($post->ID);
                break;
        endswitch;
        $aListing['isClaimed'] = SingleListing::isClaimedListing($post->ID, true) ? 'yes' : 'no';
        $adsType               = '';
        
        if (isset($aAtts['style'])) {
            if ($aAtts['style'] == 'grid') {
                $adsType = 'GRID';
            }
            if (!empty($adsType)) {
                $aListing['isAds'] = SCHelpers::renderAds($post, $adsType, true) ? 'yes' : 'no';
            }
        }
        
        $aListing    = apply_filters('wilcity/filter-listing-slider/meta-data', $aListing, $post);
        $aListings[] = (object)$aListing;
        
        return $aListing;
    }
    
    public function searchFormJson()
    {
        $this->middleware(['isWilokeShortcodeActivated'], []);
        $aRequest                 = $_POST['oArgs'];
        $aRequest['postType']     = isset($_POST['postType']) ? $_POST['postType'] : General::getPostTypeKeys(false,
            false);
        $aRequest['page']         = $_POST['page'];
        $aRequest['postsPerPage'] = $_POST['postsPerPage'];
        $aRequest['is_map']       = isset($_POST['isMap']) ? $_POST['isMap'] : 'no';
        
        $aRequest = apply_filters('wiloke-listing-tools/search-form-controller/search-orderby', $aRequest);
        $aArgs    = self::buildQueryArgs($aRequest);
        $aArgs    = apply_filters('wiloke-listing-tools/search-form-controller/query-args', $aArgs);
        
        $hasPostsNotIn = false;
        if (isset($aArgs['orderby']) && $aArgs['orderby'] == 'rand') {
            if (isset($aArgs['post__not_in']) && !empty($aArgs['post__not_in'])) {
                $hasPostsNotIn = true;
                unset($aArgs['paged']);
            }
        }
        
        $errorMsg = '<div class="col-md-12">'.\WilokeMessage::message([
                'status'     => 'danger',
                'msgIcon'    => 'la la-frown-o',
                'hasMsgIcon' => true,
                'msg'        => esc_html__('Sorry, We found no posts matched what are you looking for ...',
                    'wiloke-listing-tools')
            ], true).'</div>';
        
        if (!$this->isPassedDateRange($aArgs)) {
            wp_send_json_error([
                'msg'      => $errorMsg,
                'maxPosts' => 0,
                'maxPages' => 0
            ]);
        }
        
        $query = new \WP_Query($aArgs);
        if (!$query->have_posts()) {
            wp_reset_postdata();
            wp_send_json_error([
                'msg'      => $errorMsg,
                'maxPosts' => 0,
                'maxPages' => 0
            ]);
        }
        
        $aListings = [];
        
        $aAtts = [
            'maximum_posts_on_lg_screen' => 'col-lg-3',
            'maximum_posts_on_md_screen' => 'col-md-4',
            'maximum_posts_on_sm_screen' => 'col-sm-6',
            'get_listings_by'            => 'latest',
            'img_size'                   => isset($_POST['img_size']) && !empty($_POST['img_size']) ?
                $_POST['img_size'] : apply_filters('wilcity/filter/search-without-map/default-img-size',
                    'wilcity_360x200'),
            'posts_per_page'             => 6,
            'listing_cats'               => '',
            'listing_locations'          => '',
            'listing_tags'               => '',
            'extra_class'                => ''
        ];
        
        $aPostsNotIn = [];
        while ($query->have_posts()) {
            $query->the_post();
            $aPostsNotIn[] = $query->post->ID;
            $aListings[]   = self::jsonSkeleton($query->post, $aAtts);
        }
        wp_reset_postdata();
        
        $aResponse = [
            'listings' => $aListings,
            'maxPosts' => $query->found_posts,
            'maxPages' => $query->max_num_pages
        ];
        
        if ($hasPostsNotIn) {
            $aResponse['maxPosts']   = abs($aResponse['maxPosts']) + count($aArgs['post__not_in']);
            $aResponse['maxPages']   = abs($aResponse['maxPages']) + 1;
            $aResponse['postsNotIn'] = $aPostsNotIn;
        }
        
        wp_send_json_success($aResponse);
    }
    
    public function changeOrderByTermToOrConditional()
    {
        return 'OR';
    }
    
    public function fetchListingsNearByMe()
    {
        if (empty($_POST['oAddress'])) {
            wp_send_json_error([
                'msg' => esc_html__('Sorry, We could not detect your location.', WILOKE_LISTING_DOMAIN)
            ]);
        }
        
        $postsPerPage = isset($_POST['postsPerPage']) ? abs($_POST['postsPerPage']) : 10;
        $postsPerPage = $postsPerPage > 100 ? 10 : $postsPerPage;
        
        $aData['oAddress']     = $_POST['oAddress'];
        $aData['postsPerPage'] = $postsPerPage;
        $aData['postType']     = $_POST['postType'];
        
        $imgSize = apply_filters('wilcity/filter/search-without-map/default-img-size', 'wilcity_360x200');
        if (!empty($_POST['data'])) {
            $aParseData = unserialize(base64_decode($_POST['data']));
            $imgSize    = $aParseData['img_size'];
            
            $aTaxonomies = ['listing_cat', 'listing_location', 'listing_tag'];
            foreach ($aTaxonomies as $taxonomy) {
                $aTerms           = SCHelpers::getAutoCompleteVal($aParseData[$taxonomy.'s']);
                $aData[$taxonomy] = $aTerms;
            }
            
            if (!empty($aParseData['custom_taxonomy_key']) && !empty($aParseData['custom_taxonomies_id'])) {
                $aParseTaxonomyIds                         = explode(',', $aParseData['custom_taxonomies_id']);
                $aParseTaxonomyIds                         = array_map(function ($taxonomyID) {
                    return trim($taxonomyID);
                }, $aParseTaxonomyIds);
                $aData[$aParseData['custom_taxonomy_key']] = $aParseTaxonomyIds;
            }
        }
        
        add_filter('wilcity/wiloke-listing-tools/filter/multi-tax-logic', [$this, 'changeOrderByTermToOrConditional']);
        $aArgs = self::buildQueryArgs($aData);
        remove_filter('wilcity/wiloke-listing-tools/filter/multi-tax-logic',
            [$this, 'changeOrderByTermToOrConditional']);
        
        $query = new \WP_Query($aArgs);
        $aAtts = [
            'img_size' => $imgSize
        ];
        
        if ($query->have_posts()) {
            $aListings = [];
            while ($query->have_posts()) {
                $query->the_post();
                $aListings[] = self::jsonSkeleton($query->post, $aAtts);
            }
            wp_reset_postdata();
            
            wp_send_json_success([
                'aResults' => $aListings
            ]);
        } else {
            wp_send_json_error([
                'msg' => esc_html__('Sorry, We found no posts near by you.', 'wiloke-listing-tools')
            ]);
        }
    }
    
    public function searchListings()
    {
        $this->middleware(['isWilokeShortcodeActivated'], []);
        $aRequest = $_POST['oArgs'];
        
        $aRequest['postType']     = $_POST['postType'];
        $aRequest['postsPerPage'] = isset($_POST['postsPerPage']) ? abs($_POST['postsPerPage']) : '';
        $aRequest['page']         = isset($_POST['page']) ? abs($_POST['page']) : '';
        
        $aArgs = self::buildQueryArgs($aRequest);
        $aArgs = apply_filters('wiloke-listing-tools/search-form-controller/query-args', $aArgs);
        
        $errorMsg = '<div class="col-md-12">'.\WilokeMessage::message([
                'status'     => 'danger',
                'msgIcon'    => 'la la-frown-o',
                'hasMsgIcon' => true,
                'msg'        => esc_html__('Sorry, We found no posts matched what are you looking for ...',
                    'wiloke-listing-tools')
            ], true).'</div>';
        
        if (!$this->isPassedDateRange($aArgs)) {
            
            wp_send_json_error([
                'msg'      => $errorMsg,
                'maxPosts' => 0,
                'maxPages' => 0
            ]);
        }
        
        $hasPostsNotIn = false;
        if (isset($aArgs['orderby']) && $aArgs['orderby'] == 'rand') {
            if (isset($aArgs['post__not_in']) && !empty($aArgs['post__not_in'])) {
                $hasPostsNotIn = true;
                unset($aArgs['paged']);
            }
        }
        
        $query = new \WP_Query($aArgs);
        
        if (!$query->have_posts()) {
            wp_reset_postdata();
            wp_send_json_error([
                'msg'      => $errorMsg,
                'maxPosts' => 0,
                'maxPages' => 0
            ]);
        }
        
        $maxLgScreen = $maxMdScreen = '';
        if (isset($aRequest['templateId']) && !empty($aRequest['templateId'])) {
            $maxLgScreen = GetSettings::getPostMeta($aRequest['templateId'], 'maximum_posts_on_lg_screen');
            $maxMdScreen = GetSettings::getPostMeta($aRequest['templateId'], 'maximum_posts_on_md_screen');
            $maxSmScreen = GetSettings::getPostMeta($aRequest['templateId'], 'maximum_posts_on_sm_screen');
            $imgSize     = GetSettings::getPostMeta($aRequest['templateId'], 'search_img_size');
        }
        
        if (empty($maxSmScreen)) {
            $maxSmScreen = 'col-sm-12';
        }
        
        if (empty($maxMdScreen)) {
            $maxSmScreen = 'col-md-6';
        }
        
        if (empty($maxLgScreen)) {
            $container = \WilokeThemeOptions::getOptionDetail('search_page_layout');
            if ($container == 'container-fullwidth') {
                $maxLgScreen = 'col-lg-4';
            } else {
                $maxLgScreen = 'col-lg-6';
            }
        }
        
        if (empty($imgSize)) {
            $imgSize = 'medium';
        }
        
        $style       = isset($_POST['style']) && !empty($_POST['style']) ? $_POST['style'] : 'grid';
        $aPostsNotIn = [];
        
        ob_start();
        while ($query->have_posts()) :
            $query->the_post();
            $aPostsNotIn[] = $query->post->ID;
            
            if ($aArgs['post_type'] == 'event') {
                wilcity_render_event_item($query->post, [
                    'maximum_posts_on_lg_screen' => $maxLgScreen,
                    'maximum_posts_on_md_screen' => $maxMdScreen,
                    'maximum_posts_on_sm_screen' => $maxSmScreen,
                    'maximum_posts_on_xs_screen' => 'col-xs-12',
                    'img_size'                   => $imgSize,
                    'style'                      => $style,
                    'isSearchNearByMe'           => isset($aArgs['geocode']) && !empty($aArgs['geocode'])
                ]);
            } else {
                wilcity_render_grid_item($query->post, [
                    'maximum_posts_on_lg_screen' => $maxLgScreen,
                    'maximum_posts_on_md_screen' => $maxMdScreen,
                    'maximum_posts_on_sm_screen' => $maxSmScreen,
                    'maximum_posts_on_xs_screen' => 'col-xs-12',
                    'img_size'                   => $imgSize,
                    'style'                      => $style,
                    'isSearchNearByMe'           => isset($aArgs['geocode']) && !empty($aArgs['geocode'])
                ]);
            }
        endwhile;
        wp_reset_postdata();
        $content = ob_get_contents();
        ob_end_clean();
        
        $aResponse = [
            'msg'      => $content,
            'maxPosts' => $query->found_posts,
            'maxPages' => $query->max_num_pages
        ];
        
        if ($hasPostsNotIn) {
            $aResponse['maxPosts']   = abs($aResponse['maxPosts']) + count($aArgs['post__not_in']);
            $aResponse['maxPages']   = abs($aResponse['maxPages']) + 1;
            $aResponse['postsNotIn'] = $aPostsNotIn;
        }
        
        wp_send_json_success($aResponse);
    }
    
    public function renderSearchResults($aAtts)
    {
        global $post;
        $this->middleware(['isWilokeShortcodeActivated'], []);
        global $wiloke;
        $postsPerPage =
            isset($wiloke->aThemeOptions['listing_posts_per_page']) ? $wiloke->aThemeOptions['listing_posts_per_page'] :
                get_option('posts_per_page');
        
        if (isset($aAtts['postType'])) {
            $postType = $aAtts['postType'];
        } else if (isset($aAtts['type'])) {
            $postType = $aAtts['type'];
        } else {
            $postType = 'listing';
        }
        
        $aAtts['postType'] = $postType;
        
        $aArgs = self::buildQueryArgs($aAtts);
        
        $aArgs                   = apply_filters('wiloke-listing-tools/search-form-controller/query-args', $aArgs);
        $aArgs['posts_per_page'] = $postsPerPage;
        $query                   = new \WP_Query($aArgs);
        $gridID                  = apply_filters('wilcity/filter/id-prefix', 'wilcity-search-results');
        $aListingIDs             = [];
        
        if (!isset($aAtts['aItemsPerRow']) || empty($aAtts['aItemsPerRow'])) {
            $container = \WilokeThemeOptions::getOptionDetail('search_page_layout');
            if ($container == 'container-fullwidth') {
                $aAtts['aItemsPerRow'] = [
                    'lg' => 'col-lg-4',
                    'md' => 'col-md-6',
                    'sm' => 'col-sm-6'
                ];
            } else {
                $aAtts['aItemsPerRow'] = [
                    'lg' => 'col-lg-6',
                    'md' => 'col-md-6',
                    'sm' => 'col-sm-6'
                ];
            }
        }
        
        $aAtts['style'] = isset($aAtts['style']) ? $aAtts['style'] : 'grid';
        
        if (!isset($aAtts['img_size']) || empty($aAtts['img_size'])) {
            $aAtts['img_size'] = apply_filters('wilcity/filter/search-without-map/default-img-size', 'wilcity_360x200');
        }
        ?>
        <div id="<?php echo esc_attr($gridID); ?>"
             class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix',
                 ' wilcity-grid')); ?> row js-listing-grid mb-30"
             data-col-xs-gap="15" data-col-sm-gap="15" data-col-md-gap="15">
            <?php
            if ($query->have_posts()) :
                while ($query->have_posts()) :
                    $query->the_post();
                    if ($postType == 'event') {
                        wilcity_render_event_item($query->post, [
                            'maximum_posts_on_lg_screen' => $aAtts['aItemsPerRow']['lg'],
                            'maximum_posts_on_md_screen' => $aAtts['aItemsPerRow']['md'],
                            'maximum_posts_on_sm_screen' => $aAtts['aItemsPerRow']['sm'],
                            'maximum_posts_on_xs_screen' => 'col-xs-12',
                            'img_size'                   => $aAtts['img_size'],
                            'TYPE'                       => 'TOP_SEARCH',
                            'isSearchNearByMe'           => isset($aArgs['geocode']) && !empty($aArgs['geocode'])
                        ]);
                    } else {
                        wilcity_render_grid_item($query->post, [
                            'maximum_posts_on_lg_screen' => $aAtts['aItemsPerRow']['lg'],
                            'maximum_posts_on_md_screen' => $aAtts['aItemsPerRow']['md'],
                            'maximum_posts_on_sm_screen' => $aAtts['aItemsPerRow']['sm'],
                            'maximum_posts_on_xs_screen' => 'col-xs-12',
                            'img_size'                   => $aAtts['img_size'],
                            'style'                      => $aAtts['style'],
                            'TYPE'                       => 'TOP_SEARCH',
                            'isSearchNearByMe'           => isset($aArgs['geocode']) && !empty($aArgs['geocode'])
                        ]);
                    }
                    $aListingIDs[] = $query->post->ID;
                endwhile;
            else:
                \WilokeMessage::message([
                    'status'     => 'danger',
                    'hasMsgIcon' => true,
                    'msgIcon'    => 'la la-frown-o',
                    'msg'        => esc_html__('No results found', 'wiloke-listing-tools')
                ]);
            endif;
            wp_reset_postdata();
            ?>
        </div>
        <nav class="mt-20 mb-20">
            <ul id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-search-pagination')); ?>"
                class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix',
                    'wilcity-pagination pagination_module__1NBfW')); ?>"
                data-action="<?php echo esc_attr(apply_filters('wilcity/wiloke-listing-tools/ajax-prefix',
                    'wilcity_search_listings')); ?>"
                data-gridid="<?php echo esc_attr($gridID); ?>" data-post-type="<?php echo esc_attr($postType); ?>"
                data-totals="<?php echo esc_attr($query->found_posts); ?>"
                data-max-pages="<?php echo esc_attr($query->max_num_pages); ?>"
                data-posts-per-page="<?php echo esc_attr($postsPerPage); ?>" data-current-page="1"
                data-img_size="<?php echo esc_attr($aAtts['img_size']); ?>"
                data-style="<?php echo esc_attr($aAtts['style']); ?>"></ul>
        </nav>
        <?php
    }
    
    public function getSearchFields()
    {
        $at       = abs($_POST['at']);
        $postType = !isset($_POST['postType']) || empty($_POST['postType']) ? 'listing' :
            sanitize_text_field($_POST['postType']);
        $savedAt  = GetSettings::getOptions(General::mainSearchFormSavedAtKey($postType));
        
        if (empty($savedAt)) {
            $savedAt = current_time('timestamp', 1);
            SetSettings::setOptions(General::mainSearchFormSavedAtKey($postType), $savedAt);
        }
        
        if ($at == $savedAt) {
            wp_send_json_success([
                'action' => 'use_cache'
            ]);
        }
        
        $aSearchFields = GetSettings::getOptions(General::getSearchFieldsKey($postType));
        
        if (empty($aSearchFields)) {
            wp_send_json_error([
                'msg' => esc_html__('Oops! You have not configured the search fields', WILOKE_LISTING_DOMAIN)
            ]);
        } else {
            foreach ($aSearchFields as $key => $aSearchField) {
                switch ($aSearchField['key']) {
                    case 'price_range':
                        $aOptions = [];
                        foreach (wilokeListingToolsRepository()->get('general:priceRange') as $rangeKey => $rangeName) {
                            $aOptions['name']                 = $rangeName;
                            $aOptions['value']                = $rangeKey;
                            $aSearchFields[$key]['options'][] = $aOptions;
                        }
                        break;
                    case 'google_place':
                        $aSearchFields[$key]['address'] =
                            isset($_REQUEST['address']) ? stripslashes($_REQUEST['address']) : '';
                        break;
                    case 'post_type':
                        $aTypes = General::getPostTypes(false);
                        foreach ($aTypes as $directoryType => $aType) {
                            $aOption['name']                  = $aType['name'];
                            $aOption['value']                 = $directoryType;
                            $aSearchFields[$key]['options'][] = $aOption;
                        }
                        break;
                    default:
                        if (isset($aSearchField['group']) && $aSearchField['group'] == 'term' &&
                            $aSearchField['isAjax'] == 'no'
                        ) {
                            $termOrderBy = isset($aSearchField['orderBy']) ? $aSearchField['orderBy'] : 'count';
                            if ($aSearchField['key'] == 'listing_tag' && !empty($_POST['catId'])) {
                                $aTagSlugs = GetSettings::getTermMeta($_POST['catId'], 'tags_belong_to');
                                if (!empty($aTagSlugs)) {
                                    foreach ($aTagSlugs as $order => $slug) {
                                        $oTerm                                    = get_term_by('slug', $slug,
                                            'listing_tag');
                                        $aSearchField['options'][$order]['label'] = $oTerm->name;
                                        $aSearchField['options'][$order]['name']  = $oTerm->name;
                                        $aSearchField['options'][$order]['value'] = $oTerm->slug;
                                    }
                                    
                                    $aSearchFields[$key] = $aSearchField;
                                } else {
                                    $aTagsQuery = [
                                        'taxonomy'   => 'listing_tag',
                                        'hide_empty' => isset($aSearchField['isHideEmpty']) ?
                                            $aSearchField['isHideEmpty'] : false,
                                        'orderby'    => $termOrderBy,
                                        'order'      => isset($aSearchField['order']) ? $aSearchField['order'] : 'DESC'
                                    ];
                                    if (class_exists('Yikes_Custom_Taxonomy_Order')) {
                                        $aTagsQuery['meta_key'] = 'tax_position';
                                        $aTagsQuery['orderby']  = 'meta_value_num';
                                    }
                                    
                                    $aTags = GetSettings::getTerms($aTagsQuery);
                                    if (empty($aTags) || is_wp_error($aTags)) {
                                        $aSearchFields[$key] = [];
                                    } else {
                                        foreach ($aTags as $order => $oTerm) {
                                            $aTagsBelongsTo = GetSettings::getTermMeta($oTerm->term_id, 'belongs_to');
                                            if (empty($aTagsBelongsTo) || in_array($postType, $aTagsBelongsTo)) {
                                                $aSearchField['options'][$order]['label'] = $oTerm->name;
                                                $aSearchField['options'][$order]['name']  = $oTerm->name;
                                                $aSearchField['options'][$order]['value'] = $oTerm->slug;
                                            }
                                        }
                                        $aSearchFields[$key] = $aSearchField;
                                    }
                                }
                            } else {
                                $aTerms = GetSettings::getTaxonomyHierarchy([
                                    'taxonomy'   => $aSearchField['key'],
                                    'orderby'    => $termOrderBy,
                                    'order'      => isset($aSearchField['order']) ? $aSearchField['order'] : 'DESC',
                                    'parent'     => 0,
                                    'hide_empty' => isset($aSearchField['isHideEmpty']) ? $aSearchField['isHideEmpty'] :
                                        false
                                ], $postType, $aSearchField['isShowParentOnly'] == 'yes', false);
                                
                                if (empty($aTerms)) {
                                    unset($aSearchFields[$key]);
                                } else {
                                    foreach ($aTerms as $order => $oTerm) {
                                        if (is_wp_error($oTerm) || empty($oTerm)) {
                                            continue;
                                        }
                                        
                                        $aTagsBelongsTo = GetSettings::getTermMeta($oTerm->term_id, 'belongs_to');
                                        if (!empty($aTagsBelongsTo) && !in_array($postType, $aTagsBelongsTo)) {
                                            continue;
                                        }
                                        
                                        if ($aSearchField['key'] == 'listing_cat') {
                                            $aSearchField['options'][$order] = $this->buildTermItemInfo($oTerm);
                                        } else {
                                            $aSearchField['options'][$order]['label'] = $oTerm->name;
                                            $aSearchField['options'][$order]['name']  = $oTerm->name;
                                            $aSearchField['options'][$order]['value'] = $oTerm->slug;
                                        }
                                    }
                                    $aSearchField['test_data'] = [
                                        'taxonomy'   => $aSearchField['key'],
                                        'orderby'    => $termOrderBy,
                                        'order'      => isset($aSearchField['order']) ? $aSearchField['order'] : 'DESC',
                                        'parent'     => 0,
                                        'hide_empty' => isset($aSearchField['isHideEmpty']) ?
                                            $aSearchField['isHideEmpty'] : false
                                    ];
                                    $aSearchFields[$key]       = $aSearchField;
                                }
                            }
                        }
                        break;
                }
            }
        }
        
        wp_send_json_success(
            [
                'fields' => $aSearchFields,
                'at'     => $savedAt,
                'action' => 'update_search_fields'
            ]
        );
    }
}

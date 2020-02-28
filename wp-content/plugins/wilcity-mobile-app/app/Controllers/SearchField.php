<?php

namespace WILCITY_APP\Controllers;

use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;

class SearchField
{
    protected $aTaxonomyAndParamsRelationship = [
        'listing_location' => 'listingLocation',
        'listing_cat'      => 'listingCat',
        'listing_tag'      => 'listingTag'
    ];
    
    public function __construct()
    {
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', '/search-fields/listing', [
                'methods'  => 'GET',
                'callback' => [$this, 'getFields'],
            ]);
            
            register_rest_route(WILOKE_PREFIX.'/v2', '/get-tags/(?P<categoryID>\d+)', [
                'methods'  => 'GET',
                'callback' => [$this, 'getTagsByCatID'],
            ]);
        });
    }

    public function getTagsByCatID($aData)
    {
        $oTerm = get_term($aData['categoryID'], 'listing_cat');
        
        $aTagSlugs = GetSettings::getTermMeta($oTerm->term_id, 'tags_belong_to');
        if (empty($aTagSlugs)) {
            return [
                'status' => 'error'
            ];
        }
        
        $aTags = [];
        foreach ($aTagSlugs as $slug) {
            $oTerm   = get_term_by('slug', $slug, 'listing_tag');
            $aTags[] = [
                'name'     => $oTerm->name,
                'id'       => $oTerm->term_id,
                'slug'     => $oTerm->slug,
                'selected' => false
            ];
        }
        
        return [
            'status'   => 'success',
            'aOptions' => $aTags
        ];
    }
    
    public function getFields(\WP_REST_Request $oRequest)
    {
        $aData    = $oRequest->get_params();
        $postType = !isset($aData['postType']) ? 'listing' : sanitize_text_field($aData['postType']);
        
        if (empty($postType)) {
            $postType = General::getDefaultPostTypeKey(false);
        }
        
        $aRawSearchFields = GetSettings::getOptions(General::getSearchFieldsKey($postType));
        
        if (empty($aRawSearchFields)) {
            return [
                'status' => 'error'
            ];
        }
        
        $aSearchFields = [];
        foreach ($aRawSearchFields as $key => $aField) {
            $aSearchField          = $aField;
            $aSearchField['key']   = $aField['key'];
            $aSearchField['name']  = $aField['label'];
            $aSearchField['value'] = '';
            
            if (isset($aField['isDefault'])) {
                if ($aField['isDefault'] == 'true') {
                    $aSearchField['isDefault'] = true;
                } else if ($aField['isDefault'] == 'false') {
                    $aSearchField['isDefault'] = false;
                }
            }
            
            switch ($aField['type']) {
                case 'select2':
                case 'select':
                case 'checkbox2':
                    $aSearchField['type'] = 'select';
                    if ($aField['type'] == 'checkbox2') {
                        $aSearchField['isMultiple'] = 'yes';
                    } else {
                        if (isset($aField['isMultiple']) && ($aField['isMultiple'] == 'yes')) {
                            $aSearchField['isMultiple'] = 'yes';
                        } else {
                            $aSearchField['isMultiple'] = 'no';
                        }
                    }
                    if (!isset($aField['isAjax']) || ($aField['isAjax'] == 'no')) {
                        if (in_array($aField['key'], ['listing_location', 'listing_cat', 'listing_tag'])) {
                            $isParentOnly = isset($aField['isShowParentOnly']) && $aField['isShowParentOnly'] == 'yes';
                            $isHideEmpty  = isset($aField['isHideEmpty']) ? $aField['isHideEmpty'] : false;
                            $aRawTerms    = GetSettings::getTaxonomyHierarchy([
                                'taxonomy'   => $aField['key'],
                                'orderby'    => isset($aField['orderBy']) ? $aField['orderBy'] : 'count',
                                'parent'     => 0,
                                'hide_empty' => $isHideEmpty
                            ], $postType, $isParentOnly, false);
                            
                            if (empty($aRawTerms) || is_wp_error($aRawTerms)) {
                                $aSearchField['options'] = [
                                    [
                                        'name' => esc_html__('No categories', 'wiloke-mobile-app'),
                                        'id'   => -1
                                    ]
                                ];
                            } else {
                                $aTerms   = [];
                                $paramKey = $this->aTaxonomyAndParamsRelationship[$key];
                                foreach ($aRawTerms as $oTerm) {
                                    $aTerms[] = [
                                        'name'     => $oTerm->name,
                                        'id'       => $oTerm->term_id,
                                        'slug'     => $oTerm->slug,
                                        'selected' => isset($aData[$paramKey]) && ($aData[$paramKey] == $oTerm->slug
                                                                                   || $aData[$paramKey] ==
                                                                                      $oTerm->term_id),
                                        'count'    => GetSettings::getTermCountInPostType($postType, $aField['key'])
                                    ];
                                }
                                $aSearchField['options'] = $aTerms;
                            }
                        }
                        $aSearchField['isAjax'] = 'no';
                    } else {
                        $aSearchField['isAjax']     = 'yes';
                        $aSearchField['ajaxAction'] = $aField['ajaxAction'];
                    }
                    switch ($aField['key']) {
                        case 'price_range':
                            $aRawPriceRange = wilokeListingToolsRepository()->get('general:priceRange');
                            $aPriceRange    = [];
                            foreach ($aRawPriceRange as $priceKey => $priceDesc) {
                                $aPriceRange[] = [
                                    'name'     => $priceDesc,
                                    'id'       => $priceKey,
                                    'selected' => $priceKey == 'nottosay' ? true : false
                                ];
                            }
                            
                            $aSearchField['options'] = $aPriceRange;
                            break;
                        case 'post_type':
                            $aSearchField['key'] = 'postType';
                            $aRawPostTypes       = General::getPostTypes(false, false);
                            $aPostTypes          = [];
                            $order               = 1;
                            foreach ($aRawPostTypes as $type => $aSettings) {
                                $aPostTypes[] = [
                                    'name'     => $aSettings['name'],
                                    'id'       => $type,
                                    'selected' => $order === 1 ? true : false
                                ];
                                $order++;
                            }
                            $aSearchField['options'] = $aPostTypes;
                            break;
                    }
                    break;
                case 'autocomplete':
                    $aSearchField['type']          = 'google_auto_complete';
                    $aSearchField['maxRadius']     = abs($aField['maxRadius']);
                    $aSearchField['defaultRadius'] = abs($aField['defaultRadius']);
                    break;
                case 'checkbox':
                    $aSearchField['type'] = 'checkbox';
                    break;
                case 'wp_search':
                    $aSearchField['type'] = 'input';
                    $aSearchField['key']  = 's';
                    break;
                case 'date_range':
                    $aSearchField['type'] = 'date_range';
                    break;
            }
            
            $aSearchFields[] = $aSearchField;
        }
        
        return [
            'status'   => 'success',
            'oResults' => $aSearchFields
        ];
    }
}

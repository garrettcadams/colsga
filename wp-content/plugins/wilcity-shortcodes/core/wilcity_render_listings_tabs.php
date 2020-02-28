<?php
use \WilokeListingTools\Framework\Helpers\GetSettings;
use \WilokeListingTools\Framework\Helpers\TermSetting;

function wilcityRenderListingsTabsSC($aAtts)
{
    $aParentTermIDs = \WILCITY_SC\SCHelpers::getAutoCompleteVal($aAtts[$aAtts['taxonomy'].'s']);
    if (empty($aParentTermIDs)) {
        return '';
    }
    $aTermChildren = [];
    $prefix        = 'term_tab';
    $aQueryArgs    = [
        'posts_per_page' => $aAtts['posts_per_page'],
        'post_status'    => 'publish',
        'orderby'        => $aAtts['orderby'],
        'order'          => $aAtts['order']
    ];
    
    $selected = '';
    
    if ($aAtts['taxonomy'] == 'custom') {
        if (empty($aAtts['custom_taxonomies_id']) || empty($aAtts['custom_taxonomy_key'])) {
            return '';
        }
        
        $aAtts['taxonomy'] = $aAtts['custom_taxonomy_key'];
        $aParentTermIDs    = explode(',', $aAtts['custom_taxonomies_id']);
    }
    
    if ($aAtts['get_term_type'] == 'term_children') {
        $aRawTermIDs = get_terms(
            [
                'hide_empty' => false,
                'parent'     => $aParentTermIDs[0],
                'taxonomy'   => $aAtts['taxonomy'],
                'count'      => $aAtts['number_of_term_children']
            ]
        );
        
        if (empty($aRawTermIDs) || is_wp_error($aRawTermIDs)) {
            return '';
        }
        $oParentTerm = get_term($aParentTermIDs[0], $aAtts['taxonomy']);
        
        $aQueryArgs['tax_query'] = [
            [
                'taxonomy' => $aAtts['taxonomy'],
                'terms'    => $oParentTerm->term_id,
                'field'    => 'term_id'
            ]
        ];
        
        $aTabs[$prefix.$oParentTerm->slug] = [
            'slug'     => $oParentTerm->slug,
            'query'    => $aQueryArgs,
            'name'     => esc_html__('All', 'wilcity-mobile-app'),
            'endpoint' => 'terms/'.$oParentTerm->slug
        ];
        $selected                          = $prefix.$oParentTerm->slug;
        $parentLink                        = get_term_link($oParentTerm->term_id);
        foreach ($aRawTermIDs as $oTerm) {
            $aTermChildren[] = $oTerm->term_id;
        }
    } else {
        $aTermChildren = $aParentTermIDs;
        
        if (empty($aTermChildren)) {
            return '';
        }
    }
    
    $aQueryArgs['tax_query'] = [
        [
            'taxonomy' => $aAtts['taxonomy'],
            'terms'    => $aTermChildren,
            'field'    => 'term_id'
        ]
    ];
    
    $defaultPostType = '';
    
    foreach ($aTermChildren as $termID) {
        $oTerm = get_term($termID);
        if (is_wp_error($oTerm)) {
            continue;
        }
        
        if (empty($defaultPostType)) {
            $defaultPostType = TermSetting::getDefaultPostType($oTerm->term_id, $oTerm->taxonomy);
        }
        
        if (empty($selected)) {
            $selected = $prefix.$oTerm->slug;
        }
        
        $aQueryArgs['tax_query'] = [
            [
                'taxonomy' => $aAtts['taxonomy'],
                'terms'    => [$oTerm->term_id],
                'field'    => 'term_id'
            ]
        ];
        
        $aTabs[$prefix.$oTerm->slug] = [
            'slug'     => $oTerm->slug,
            'query'    => $aQueryArgs,
            'name'     => $oTerm->name,
            'endpoint' => 'terms/'.$oTerm->slug
        ];
    }
    
    $aSCSettings = array_diff_assoc([
        'maximum_posts_on_lg_screen' => $aAtts['maximum_posts_on_lg_screen'],
        'maximum_posts_on_md_screen' => $aAtts['maximum_posts_on_md_screen'],
        'maximum_posts_on_sm_screen' => $aAtts['maximum_posts_on_sm_screen'],
        'img_size'                   => $aAtts['img_size']
    ], $aQueryArgs);
    
    unset($aSCSettings['heading_color']);
    unset($aSCSettings['description_color']);
    unset($aSCSettings['taxonomy']);
    unset($aSCSettings['get_term_type']);
    unset($aSCSettings['listing_cats']);
    unset($aSCSettings['terms_tab_id']);
    unset($aSCSettings['maximum_posts_on_lg_screen']);
    unset($aSCSettings['maximum_posts_on_md_screen']);
    unset($aSCSettings['maximum_posts_on_sm_screen']);
    $itemWrapperClass = $aAtts['maximum_posts_on_lg_screen'].' '.$aAtts['maximum_posts_on_md_screen'].' '.
                        $aAtts['maximum_posts_on_sm_screen'];
    
    $aAtts['radius'] =
        empty($aAtts['radius']) ? WilokeThemeOptions::getOptionDetail('default_radius') : $aAtts['radius'];
    $aAtts['unit']   = WilokeThemeOptions::getOptionDetail('unit_of_distance');
    
    $searchURL = add_query_arg(
                     [
                         'orderby' => $aAtts['orderby'],
                         'order'   => $aAtts['order']
                     ],
                     GetSettings::getSearchPage()
    );
    ?>
    <div id="<?php echo esc_attr($aAtts['terms_tab_id']); ?>" class="wilcity-terms-tabs wilcity-listings-tabs"
         data-posttype="<?php echo
         esc_attr($defaultPostType);
         ?>" data-searchurl="<?php echo esc_url($searchURL); ?>" data-taxonomy="<?php echo esc_attr
    ($aAtts['taxonomy']); ?>">
        <tabs selected="<?php echo esc_attr($selected); ?>"
              tab-alignment="<?php echo esc_attr($aAtts['tab_alignment']); ?>" v-on:changed-selected="selectedTermID">
            <?php if (!empty($aAtts['post_types_filter']) || !empty($aAtts['heading'])) : ?>
                <template v-slot:specialnavitembefore>
                    <?php if (!empty($aAtts['heading'])) : ?>
                        <li class="term-grid-title float-left">
                            <?php if (isset($parentLink)) : ?>
                                <a style="padding-left: 0; color: <?php echo esc_attr($aAtts['heading_color']); ?>"
                                   class="ignore-lava"
                                   href="<?php echo esc_url($parentLink); ?>"><?php echo esc_html($aAtts['heading']); ?></a>
                            <?php else: ?>
                                <a style="padding-left: 0; color: <?php echo esc_attr($aAtts['heading_color']); ?>"
                                   class="ignore-lava"
                                   href="#"><?php echo esc_html($aAtts['heading']); ?></a>
                            <?php endif; ?>
                        </li>
                    <?php endif; ?>
                    <?php
                    if (!empty($aAtts['post_types_filter'])) :
                        if (is_array($aAtts['post_types_filter'])) {
                            $aPostKeys = $aAtts['post_types_filter'];
                        } else {
                            $aPostKeys = explode(',', $aAtts['post_types_filter']);
                        }
                        $aOptions = [];
                        foreach ($aPostKeys as $postType) {
                            $oPostType  = get_post_type_object($postType);
                            $aOptions[] = [
                                'name'  => $oPostType->labels->singular_name,
                                'value' => $postType
                            ];
                        }
                        
                        $aSelectSettings = [
                            'isAjax'     => 'no',
                            'isMultiple' => 'no',
                            'value'      => $defaultPostType
                        ];
                        ?>
                        <li class="term-tab-post-type-filter">
                            <select-2 v-on:select-two-changed="changePostType"
                                      :a-raw-options='<?php echo json_encode($aOptions); ?>'
                                      :settings='<?php echo json_encode($aSelectSettings); ?>'></select-2>
                        </li>
                    <?php endif; ?>
                </template>
            <?php endif; ?>
            
            <?php if ($aAtts['toggle_viewmore'] == 'enable') : ?>
            <template v-slot:specialnavitemafter>
                <li class="term-view-more-wrapper">
                    <a class="ignore-lava wilcity-view-all"
                       :href="viewMoreURL"><?php esc_html_e('View more',
                            'wilcity-shortcodes'); ?>
                    </a>
                </li>
            </template>
            <?php endif; ?>
            <template slot-scope="{selected}">
                <?php foreach ($aTabs as $tabID => $aTermInfo) : ?>
                    <listing-tab-item :post-type="postType" :selected="selected"
                                      tab-id="<?php echo esc_attr($tabID); ?>"
                                      name="<?php echo esc_attr($aTermInfo['name']); ?>"
                                      query-args="<?php echo esc_attr(base64_encode(json_encode($aTermInfo['query']))); ?>"
                                      sc-settings="<?php echo esc_attr(base64_encode(json_encode($aSCSettings))); ?>"
                                      endpoint="<?php echo esc_attr($aTermInfo['endpoint']); ?>"
                                      taxonomy="<?php echo esc_attr($aAtts['taxonomy']); ?>"
                                      item-wrapper-class="<?php echo esc_attr($itemWrapperClass); ?>"
                                      orderby="<?php echo esc_attr($aAtts['orderby']) ?>"
                                      :radius="<?php echo abs($aAtts['radius']); ?>"
                                      unit="<?php echo esc_attr($aAtts['unit']); ?>"
                                      v-on:my-address="myAddress"
                    >
                    </listing-tab-item>
                <?php endforeach; ?>
            </template>
        </tabs>
    </div>
    <?php
}

<?php
/*
 * Template Name: Wilcity Map
 */

use WilokeListingTools\Controllers\SearchFormController;
use \WilokeListingTools\Framework\Helpers\GetSettings;
use \WilokeListingTools\Framework\Helpers\General;

get_header();
    global $wiloke;
    $search = $latLng = $address = $taxonomy = $taxID = '';

    $aTaxonomies = array();
    $aDateRange = array();
    $aRequest = SearchFormController::parseRequestFromUrl();
    $aRequest['postType'] = General::getDefaultPostTypeKey();

    if ( is_tax() ){
        $taxSlug = get_query_var('term');
        $taxonomy = get_query_var('taxonomy');

        $taxID = get_queried_object_id();
        if ( $taxonomy == 'listing_cat' || $taxonomy == 'listing_tag' ){
            $aRequest[$taxonomy] = array($taxSlug);
            $aTaxonomies[$taxonomy] = array($taxSlug);
        }else{
            $aRequest[$taxonomy] = $taxSlug;
            $aTaxonomies[$taxonomy] = $taxSlug;
        }

        if ( !isset($_REQUEST['type']) && isset($taxID) ){
	        $aBelongsTo = GetSettings::getTermMeta($taxID, 'belongs_to');
	        if ( !empty($aBelongsTo) ){
		        $aRequest['postType'] = $aBelongsTo[0];
	        }
        }
        if ( isset($wiloke->aThemeOptions['taxonomy_image_size']) && !empty($wiloke->aThemeOptions['taxonomy_image_size']) ){
            $aRequest['img_size'] = $wiloke->aThemeOptions['taxonomy_image_size'];
        }

        $taxTitle = get_queried_object()->name;

	    $aTaxonomiesKeys = array('listing_cat', 'listing_location', 'listing_tag');
	    foreach ($aTaxonomiesKeys as $order => $taxKey){
	        if ( $taxKey == $taxonomy ){
	            continue;
            }
            if ( isset($_GET[$taxKey]) ){
	            if ( $taxonomy == 'listing_cat' || $taxonomy == 'listing_tag' ){
		            $aRequest[$taxKey] = array($_GET[$taxKey]);
	            }else{
		            $aRequest[$taxKey] = $_GET[$taxKey];
                }
            }
        }

	    $mapPageID = $wiloke->aThemeOptions['search_page'];
    }else{
        if ( isset($aRequest['listing_cat']) ){
            $aTaxonomies['listing_cat'] = $aRequest['listing_cat'];
        }

        if ( isset($aRequest['listing_location']) ){
            $aTaxonomies['listing_location'] = $aRequest['listing_location'];
        }

        $imgSize = GetSettings::getPostMeta($post->ID, 'search_img_size');
	    if ( !empty($imgSize) ){
		    $aRequest['img_size'] = $imgSize;
	    }else{
		    $aRequest['img_size'] = apply_filters('wilcity/filter/map/default-img-size', 'wilcity_360x200');
	    }

	    $aRequest['templateID'] = $post->ID;
	    $mapPageID = $post->ID;
    }

    $aTaxonomiesOption = array();

    if ( !empty($aTaxonomies) ){
        foreach ($aTaxonomies as $tax => $rawSlug){
            $slug = is_array($rawSlug) ? $rawSlug[0] : $rawSlug;
            $oTermInfo = get_term_by('slug', $slug, $tax);
            if ( !empty($oTermInfo) && !is_wp_error($oTermInfo) ){
                $aTaxonomiesOption[$tax] = array(
                    array(
                        'name' => $oTermInfo->name,
                        'value'=> $slug
                    )
                );

                $taxID = $oTermInfo->term_id;
            }
        }
    }

    if ( isset($_REQUEST['order_by']) ){
        $aRequest['orderby'] = $_REQUEST['order_by'];
    }

    if ( isset($aRequest['oAddress']) ){
        $address = $aRequest['oAddress']['address'];
        $latLng  = $aRequest['oAddress']['lat'] . ','.$aRequest['oAddress']['lng'];
    }

    if ( isset($_REQUEST['type']) && !empty($_REQUEST['type']) ){
        $aRequest['postType'] = $_REQUEST['type'];
    }


    if ( !empty($aRequest['date_range']) ){
        $aDateRange = $aRequest['date_range'];
    }

    $search = isset($aRequest['title']) ? $aRequest['title'] : '';

    if ( isset($aRequest['title']) ){
        $aRequest['s'] = $aRequest['title'];
    }

    if ( !isset($aRequest['image_size']) ){
        $aRequest['image_size'] = '';
    }

    $aRequest['style'] = GetSettings::getPostMeta($mapPageID, 'style');
    $aRequest['style'] = empty($aRequest['style']) ? 'grid' : $aRequest['style'];

    $aRequest = wp_parse_args(
        $aRequest,
        array(
            'postType' => 'listing',
            'image_size' => '',
            'order' => '',
            'orderby' => '',
            'templateID' => ''
        )
    );

    $listSwitchClass = 'listing-bar_item__266Xo js-list-button';
    $gridSwitchClass = 'listing-bar_item__266Xo js-grid-button';
    if ( $aRequest['style'] == 'list' ){
        $listSwitchClass .= ' color-primary';
    }else{
        $gridSwitchClass .= ' color-primary';
    }
?>
	<div class="wil-content">
		<section id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-map-wrapper')); ?>" style="min-height: 500px;" class="wil-section bg-color-gray-1 pd-0">
            <div v-show="!isInitialized" class="full-load"><div class="pill-loading_module__3LZ6v pos-a-center"><div class="pill-loading_loader__3LOnT"></div></div></div>
			<div class="listing-map_left__1d9nh js-listing-map-content">
                <div class="listing-bar_module__2BCsi js-listing-bar-sticky">
                    <div class="container">
                        <div class="listing-bar_resuilt__R8pwY">
                            <span v-show="foundPosts!=0"><?php esc_html_e('Showing', 'wilcity'); ?> <span v-html="showingListingDesc"></span></span>
                            <a class="wil-btn wil-btn--border wil-btn--round wil-btn--xs" @click.prevent="resetSearchForm" href="#"><i class="color-primary la la-share"></i> <?php esc_html_e('Reset', 'wilcity'); ?>
                            </a>
                        </div>
                        <div class="listing-bar_layout__TK3vH">
                            <a class="<?php echo esc_attr($gridSwitchClass); ?>" href="#" data-tooltip="<?php echo esc_attr__('Grid Layout', 'wilcity'); ?>" @click.prevent="switchLayoutTo('<?php echo $aRequest['style'] == 'grid2' ? 'grid2' : 'grid'; ?>')" data-tooltip-placement="bottom"><i class="la la-th-large"></i></a>
                            <a class="<?php echo esc_attr($listSwitchClass); ?>" href="#" @click.prevent="switchLayoutTo('list')" data-tooltip="<?php echo esc_attr__('List Layout', 'wilcity'); ?>" data-tooltip-placement="bottom"><i class="la la-list"></i></a><a class="listing-bar_item__266Xo js-map-button" href="#"><i class="la la-map-marker"></i><i class="la la-close"></i></a>
                            <a class="wil-btn js-listing-search-button wil-btn--primary wil-btn--round wil-btn--xs " href="#"><i class="la la-search"></i> <?php esc_html_e('Search', 'wilcity'); ?>
                            </a>
                            <a class="wil-btn js-listing-search-button-mobile wil-btn--primary wil-btn--round wil-btn--xs " href="#" @click.prevent="toggleSearchFormPopup"><i class="la la-search"></i> <?php esc_html_e('Search', 'wilcity'); ?>
                            </a>
                        </div>
                    </div>
                </div><!-- End / listing-bar_module__2BCsi -->
				<div v-if="!isMobile" class="content-box_module__333d9 content-box_lg__3v3a- listing-map_box__3QnVm mb-0 js-listing-search">
                    <search-form v-on:searching="togglePreloader" type="<?php echo esc_attr($aRequest['postType']); ?>" is-map="yes" posts-per-page="<?php echo esc_attr($wiloke->aThemeOptions['listing_posts_per_page']); ?>" raw-taxonomies='<?php echo esc_attr(json_encode($aTaxonomies)); ?>' s="<?php echo esc_attr($search); ?>" address="<?php echo esc_attr($address); ?>" raw-date-range='<?php echo esc_attr(json_encode($aDateRange)); ?>' lat-lng="<?php echo esc_attr($latLng); ?>" form-item-class="col-md-6 col-lg-6" is-popup="no" is-mobile="no" v-on:fetch-listings="triggerFetchListing" taxonomy="<?php echo esc_attr($taxonomy); ?>" image-size="<?php echo esc_attr($aRequest['image_size']); ?>" raw-taxonomies-options="<?php echo esc_attr(json_encode($aTaxonomiesOption)); ?>"  order-by="<?php echo esc_attr($aRequest['orderby']); ?>" order="<?php echo esc_attr($aRequest['order']); ?>" template-id="<?php echo esc_attr($aRequest['templateID']); ?>" template-style="<?php echo esc_attr($aRequest['style']); ?>"></search-form>
				</div>

				<div class="content-box_module__333d9 content-box_lg__3v3a- listing-map_box__3QnVm bg-color-gray-2">
					<div class="content-box_body__3tSRB">
						<listings :posts-per-page="<?php echo abs($wiloke->aThemeOptions['listing_posts_per_page']); ?>" img-size="<?php echo esc_attr($aRequest['img_size']); ?>"></listings>
					</div>
				</div>
			</div>
            <?php if ( wilcityIsMapbox() ) : ?>
                <mapbox default-zoom="" max-zoom="" min-zoom="" mode="multiple" map-id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-map')); ?>" is-using-mapcluster="yes" grid-size="<?php echo esc_attr($aRequest['image_size']); ?>" marker-svg="<?php echo esc_url(get_template_directory_uri() . "/assets/img/marker.svg"); ?>"></mapbox>
            <?php else: ?>
                <google-map default-zoom="" max-zoom="" min-zoom="" mode="multiple" map-id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-map')); ?>" is-using-mapcluster="yes" grid-size="<?php echo esc_attr($aRequest['image_size']); ?>" marker-svg="<?php echo esc_url(get_template_directory_uri() . "/assets/img/marker.svg"); ?>"></google-map>
            <?php endif; ?>
		</section>
	</div>
<?php
get_footer();

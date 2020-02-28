<?php
function wilcityRenderMapSC($aAtts){
	global $wiloke;
	$mapType = WilokeThemeOptions::getOptionDetail('map_type');
	if ( $mapType == 'mapbox' ){
		wp_enqueue_style('mapbox-gl', 'https://api.tiles.mapbox.com/mapbox-gl-js/v0.53.1/mapbox-gl.css');
		wp_enqueue_script('jquery-ui-autocomplete');
		wp_enqueue_script('mapbox', get_template_directory_uri() . '/assets/production/js/mapbox.min.js', array('jquery'), WILCITY_SC_VERSION, true);
    }else{
		wp_enqueue_script('markerclusterer', get_template_directory_uri() . '/assets/vendors/googlemap/markerclusterer.js', array('jquery'), WILCITY_SC_VERSION, true);
		wp_enqueue_script('snazzy-info-window', get_template_directory_uri() . '/assets/vendors/googlemap/snazzy-info-window.min.js', array('jquery'), WILCITY_SC_VERSION, true);
		wp_enqueue_script(WILCITY_WHITE_LABEL.'-map', get_template_directory_uri() . '/assets/production/js/map.min.js', array('jquery'), WILCITY_SC_VERSION, true);
    }
	?>
	<section id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-map-wrapper')); ?>" style="min-height: 500px;" class="wilcity-map-shortcode wil-section bg-color-gray-1 pd-0">
		<div v-show="!isInitialized" class="full-load"><div class="pill-loading_module__3LZ6v pos-a-center"><div class="pill-loading_loader__3LOnT"></div></div></div>
		<div class="listing-map_left__1d9nh js-listing-map-content">
			<div class="listing-bar_module__2BCsi js-listing-bar-sticky">
				<div class="container">
					<div class="listing-bar_resuilt__R8pwY">
						<span v-show="foundPosts!=0"><?php esc_html_e('Showing', 'wilcity-shortcodes'); ?> <span v-html="showingListingDesc"></span></span>
						<a class="wil-btn wil-btn--border wil-btn--round wil-btn--xs" @click.prevent="resetSearchForm" href="#"><i class="color-primary la la-share"></i> <?php esc_html_e('Reset', 'wilcity-shortcodes'); ?>
						</a>
					</div>
					<div class="listing-bar_layout__TK3vH">
						<a class="listing-bar_item__266Xo js-grid-button color-primary" href="#" data-tooltip="<?php echo esc_attr__('Grid Layout', 'wilcity-shortcodes'); ?>" @click.prevent="switchLayoutTo('grid')" data-tooltip-placement="bottom"><i class="la la-th-large"></i></a>
						<a class="listing-bar_item__266Xo js-list-button" href="#" @click.prevent="switchLayoutTo('list')" data-tooltip="<?php echo esc_attr__('List Layout', 'wilcity-shortcodes'); ?>" data-tooltip-placement="bottom"><i class="la la-list"></i></a><a class="listing-bar_item__266Xo js-map-button" href="#"><i class="la la-map-marker"></i><i class="la la-close"></i></a>
						<a class="wil-btn js-listing-search-button wil-btn--primary wil-btn--round wil-btn--xs " href="#"><i class="la la-search"></i> <?php esc_html_e('Search', 'wilcity-shortcodes'); ?>
						</a>
						<a class="wil-btn js-listing-search-button-mobile wil-btn--primary wil-btn--round wil-btn--xs " href="#" @click.prevent="toggleSearchFormPopup"><i class="la la-search"></i> <?php esc_html_e('Search', 'wilcity-shortcodes'); ?>
						</a>
					</div>
				</div>
			</div><!-- End / listing-bar_module__2BCsi -->
			<div v-if="!isMobile" class="content-box_module__333d9 content-box_lg__3v3a- listing-map_box__3QnVm mb-0 js-listing-search">
				<search-form v-on:searching="togglePreloader" type="<?php echo esc_attr($aAtts['type']); ?>" raw-taxonomies="" raw-taxonomies-options is-map="yes" posts-per-page="<?php echo esc_attr($wiloke->aThemeOptions['listing_posts_per_page']); ?>" lat-lng="" form-item-class="col-md-6 col-lg-6" is-popup="no" is-mobile="no" v-on:fetch-listings="triggerFetchListing" image-size="<?php echo esc_attr($aAtts['img_size']); ?>" order-by="<?php echo esc_attr($aAtts['orderby']); ?>" order="<?php echo esc_attr($aAtts['order']); ?>" lat-lng="<?php echo esc_attr(trim($aAtts['latlng'])); ?>" template-style="<?php echo esc_attr($aAtts['style']); ?>"></search-form>
			</div>

			<div class="content-box_module__333d9 content-box_lg__3v3a- listing-map_box__3QnVm bg-color-gray-2">
				<div class="content-box_body__3tSRB">
					<listings posts-per-page="<?php echo abs($wiloke->aThemeOptions['listing_posts_per_page']); ?>" img-size="<?php echo esc_attr($aAtts['img_size']); ?>"></listings>
				</div>
			</div>
		</div>
		<map default-zoom="<?php echo esc_attr($aAtts['default_zoom']); ?>" max-zoom="<?php echo esc_attr($aAtts['max_zoom']); ?>" min-zoom="<?php echo esc_attr($aAtts['min_zoom']); ?>"  mode="multiple" map-id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-map')); ?>" is-using-mapcluster="yes" grid-size="<?php echo esc_attr($aAtts['image_size']); ?>" marker-svg="<?php echo esc_url(get_template_directory_uri() . "/assets/img/marker.svg"); ?>"></map>
	</section>
	<?php
}

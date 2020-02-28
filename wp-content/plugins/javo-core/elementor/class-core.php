<?php
/**
 * Load the class loader.
 */

if( ! function_exists( 'jvbpd_core_elementor_init' ) ) {
	add_action( 'init', 'jvbpd_core_elementor_init' );
	function jvbpd_core_elementor_init() {

		require_once( jvbpdCore()->elementor_path . '/class-main.php' );
		require_once( jvbpdCore()->elementor_path . '/class-elements-tool.php' );
		require_once( jvbpdCore()->elementor_path . '/class-replace.php' );
		require_once( jvbpdCore()->elementor_path . '/class-bp-replace.php' );

		if(class_exists('WPML_Elementor_Module_With_Items')) {
			require_once( jvbpdCore()->elementor_path . '/class-wpml.php' );
			require_once( jvbpdCore()->elementor_path . '/function-wpml.php' );
		}

		require_once( jvbpdCore()->widget_path . '/search-form-section.php' );

		require_once( jvbpdCore()->elementor_path . '/modules/class-module-base.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/class-widget-base.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/class-module-manager.php' );

		//require_once( jvbpdCore()->widget_path . '/base-custom-block.php' );

		require_once( jvbpdCore()->elementor_path . '/modules/carousel/module.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/carousel/widgets/class-base.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/carousel/widgets/media-carousel.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/carousel/widgets/carousel-single-listing.php' );

		require_once( jvbpdCore()->elementor_path . '/modules/block/module.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/block/widgets/base.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/block/widgets/page-block.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/block/widgets/map-block.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/block/widgets/map-list-block.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/block/widgets/archive-block.php' );

		require_once( jvbpdCore()->elementor_path . '/modules/meta/module.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/meta/widgets/base.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/meta/manager-extends.php' );
		// require_once( jvbpdCore()->elementor_path . '/modules/meta/widgets/acf-meta.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/meta/widgets/post-base-meta.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/meta/widgets/ticket-base-meta.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/meta/widgets/listing-base-meta.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/meta/widgets/listing-base-field.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/meta/widgets/module-meta.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/meta/widgets/module-media.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/meta/widgets/module-card.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/meta/widgets/block-card.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/meta/widgets/archive-meta.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/meta/widgets/module-repeater-meta.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/review/module.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/review/widgets/base.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/review/widgets/single.review.php' );/*
		require_once( jvbpdCore()->elementor_path . '/modules/review/widgets/review-progress.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/review/widgets/review-notice.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/review/widgets/review-form.php' ); */

		require_once( jvbpdCore()->elementor_path . '/modules/button/module.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/button/widgets/base.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/button/widgets/favorite.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/button/widgets/login-signup.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/button/widgets/add-form-submit.php' );

		require_once( jvbpdCore()->elementor_path . '/modules/testimonial/module.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/testimonial/widgets/base.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/testimonial/widgets/testimonial.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/testimonial/widgets/testimonial-wide.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/testimonial/widgets/featured-block.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/testimonial/widgets/members.php' );

		require_once( jvbpdCore()->elementor_path . '/modules/userforms/module.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/userforms/widgets/base.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/userforms/widgets/login.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/userforms/widgets/signup.php' );

		require_once( jvbpdCore()->elementor_path . '/modules/slider/module.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/slider/widgets/base.php' );
		require_once( jvbpdCore()->elementor_path . '/modules/slider/widgets/jv-page-slider.php' );

		Jvbpd_Listing_Elementor::getInstance();
		new \jvbpdelement\Manager();

		if( function_exists( 'jvbpd_add_group_control' ) ) {
			add_action( 'elementor/controls/controls_registered', 'jvbpd_add_group_control' );
		}
	}
}

require_once( dirname( __FILE__ ) . '/class-custom-page-settings.php' );
add_action( 'elementor/init', 'jvbpd_el_page_settings' );

if( ! function_exists( 'jvbpd_add_group_control' ) ) {
	function jvbpd_add_group_control( $controls_manager ) {
		$grouped = array(
			'jv-box-style' => 'JV_Group_Control_Box_Style',
			'jvbpd-group-block-style' => 'jvbpd_group_block_style',
			'jvbpd-group-nav-menu' => 'jvbpd_group_nav_menu',
			'jvbpd-group-button-style' => 'jvbpd_group_button_style',
		);

		require_once( jvbpdCore()->elementor_path . '/helper-jv-group-control-box.php' );
		require_once( jvbpdCore()->widget_path . '/group/block-style.php' );
		require_once( jvbpdCore()->widget_path . '/group/nav-style.php' );
		require_once( jvbpdCore()->widget_path . '/group/button-style.php' );

		foreach ( $grouped as $control_id => $class_name ) {
			$controls_manager->add_group_control( $control_id, new $class_name() );
		}

		/*
		$fonts = $controls_manager->get_control( 'font' )->get_settings( 'fonts' );
		$new_fonts = array_merge( [ 'tahoma' => 'system' ], $fonts );
		$controls_manager->get_control( 'font' )->set_settings( 'fonts', $new_fonts ); */
	}
}

if( ! function_exists( 'jvbpd_custom_nav_menu_item' ) ) {
	add_action( 'pre_get_posts', 'jvbpd_custom_nav_menu_define' );
	function jvbpd_custom_nav_menu_define( $q ) {
		if( $q->is_main_query() ) {
			add_filter( 'jvbpd_custom_nav_menu_item', 'jvbpd_custom_nav_menu_item' );
		}
	}
	function jvbpd_custom_nav_menu_item( $is_admin=false ) {
		return $is_admin || ( is_admin() && $GLOBALS[ 'post' ]->post_type == 'jvbpd-listing-elmt' );
	}
}

if( ! function_exists( 'jvbpd_hide_show_widgets' ) ) {
	// add_action('elementor/widgets/widgets_registered', 'jvbpd_hide_show_widgets');
	function jvbpd_hide_show_widgets( $widgets=Array() ){
		global $post;

		$filter_category = false;
		$filter_categories = Array(
			'map' => 'jvbpd-map-page',
			'single' => 'jvbpd-single-listing',
		);
		$is_template_type = get_post_meta( $post->ID, 'jvbpd_template_type', true );
		$wp_template = get_post_meta( $post->ID, '_wp_page_template', true );

		switch( $is_template_type ) {
			case 'single_listing_page' :
				$filter_category = $filter_categories[ 'single' ];
				break;
			case 'type_listing_archive' :
				$filter_category = $filter_categories[ 'map' ];
				break;
		}

		if( $wp_template == 'lava_lv_listing_map' ) {
			$filter_category = $filter_categories[ 'map' ];
		}

		foreach( $widgets->get_widget_types() as $widgetID => $widgetMeta ) {
			if( $widgetMeta->get_unique_name() == 'common' ) {
				continue;
			}
			if( $filter_category ) {
				if( $filter_category == $filter_categories[ 'map' ] && $widgetID == 'jvbpd-search-from-listing' ) {
					continue;
				}
				if( ! in_array( $filter_category, $widgetMeta->get_categories() ) ) {
					$widgets->unregister_widget_type( $widgetID );
				}
			}else{
				foreach( $filter_categories as $filter_category_key ) {
					if( in_array( $filter_category_key, $widgetMeta->get_categories() ) ) {
						$widgets->unregister_widget_type( $widgetID );
					}
				}
			}
		}

	}
}

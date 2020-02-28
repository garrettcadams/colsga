<?php
namespace jvbpdelement;  //main namespace

global $jvbpd_cf7;
//$jvbpd_cf7= array_map('basename', glob(dirname( __FILE__ ) . '/widgets/*.php'));
//$jvbpd_cf7= array_map('basename', '/opt/bitnami/apps/wordpress/htdocs/wp-content/plugins/core/widgets/*.php');
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Plugin {

	public function __construct() {
		add_action( 'elementor/init', Array( $this, 'jvbpd_categories_register' ) );
		add_action( 'elementor/widgets/widgets_registered', Array( $this, 'jvbpd_widgets_register' ) );
	}

	public function jvbpd_categories_register() {
		$categories = Array(
			'jvbpd-elements' => Array(
				'title' => esc_html__( 'JAVO', 'jvfrmtd' ),
				'icon' => 'fa fa-header',
			),
			'jvbpd-single-post' => Array(
				'title' => esc_html__( '[Javo] Single Post', 'jvfrmtd' ),
				'icon' => 'fa fa-header',
			),
			'jvbpd-single-listing' => Array(
				'title' => esc_html__( '[Javo] Single Listing', 'jvfrmtd' ),
				'icon' => 'fa fa-header',
			),
			'jvbpd-map-page' => Array(
				'title' => esc_html__( '[Javo] Map Page', 'jvfrmtd' ),
				'icon' => 'fa fa-header',
			),
			'jvbpd-core-add-form' => Array(
				'title' => esc_html__( '[Javo] Add New Form', 'jvfrmtd' ),
				'icon' => 'fa fa-header',
			),
			'jvbpd-elements-bp' => Array(
				'title' => esc_html__( '[Javo] BuddyPress', 'jvfrmtd' ),
				'icon' => 'fa fa-header',
			),
			'jvbpd-elements-canvas' => Array(
				'title' => esc_html__( '[Javo] Canvas', 'jvfrmtd' ),
				'icon' => 'fa fa-header',
			),
			'jvbpd-page-builder-login' => Array(
				'title' => esc_html__( '[javo] Login', 'jvfrmtd' ),
				'icon' => 'fa fa-header',
			),
			'jvbpd-page-builder-search' => Array(
				'title' => esc_html__( '[javo] Search Form', 'jvfrmtd' ),
				'icon' => 'fa fa-header',
			),
			'jvbpd-page-builder-module' => Array(
				'title' => esc_html__( '[javo] Module', 'jvfrmtd' ),
				'icon' => 'fa fa-header',
			),
		);

		foreach($categories as $category => $catMeta ) {
			\Elementor\Plugin::$instance->elements_manager->add_category( $category, $catMeta, 1 );
		}
	}

	public function jvbpd_widgets_register() {

		//include the widgets here
		$incWidgets = Array(
			'common' => Array(
				Array( 'helper.php' => '' ),
				//Array( 'jvbpd-section-cf7.php' => 'jvbpd_cf7' ),
				Array( 'category-block.php' => 'jvbpd_categoryBlock' ),
				//Array( 'post-box-block.php' => 'jvbpd_post_box_Block' ),
				Array( 'mail-chimp.php' => 'jvbpd_mailchimp' ),
				Array( 'grid-block.php' => 'jvbpd_gridblock' ),
				Array(
					'search-form-listing.php' => Array(
						'cond' => 'lava_directory',
						'inst' => 'jvbpd_search_form_listing',
					),
				),
				// Array( 'block2.php' => 'jvbpd_block2' ),

				//Array( 'custom-block-post.php' => 'custom_block_post' ),
				Array(
					'custom-block-listing.php' => Array(
						'cond' => 'lava_directory',
						'inst' => 'custom_block_listing',
					),
				),

				//Array( 'slider.php' => 'jvbpd_slider' ),
				Array( 'price-table.php' => 'jvbpd_Pricing_Table' ),
				// Array( 'lava-grid.php' => 'jvbpd_lava_grid' ),
				// Array( 'lava-post-grid.php' => 'jvbpd_lava_post_grid' ),
				//Array( 'team-members.php' => 'jvbpd_team_members' ), undefined _id issue
				//Array( 'carousel-category.php' => 'jvbpd_carousel_category' ),
				Array( 'nav-menu.php' => 'jvbpd_nav_menu' ),
				Array(
					'search-form-button.php' => Array(
						'cond' => 'lava_directory',
						'inst' => 'jvbpd_search_form_button',
					),
				),
				Array( 'jv-button.php' => 'jvbpd_button' ),
				Array( 'icons.php' => 'jvbpd_icons' ),
				Array( 'heading.php' => 'jvbpd_heading' ),
				Array( 'heading-animation.php' => 'jvbpd_animation_headline' ),
				//Array( 'image-block.php' => 'jvbpd_Image_block' ),
				Array( 'tabs.php' => 'jvbpd_tabs' ),
				Array( 'canvas-opener.php' => 'Jvbpd_Canvas_Opener' ),
				Array( 'canvas-closer.php' => 'Jvbpd_Canvas_Closer' ),
				Array( 'image-marker.php'	=> 'Jvbpd_Image_Marker'),
				Array( 'absolute-images.php'	=> 'Jvbpd_Absolute_Images'),
				Array( 'scrollspy.php'	=> 'Jvbpd_Scrollspy'),
				Array( 'site-logo.php' => 'Jvbpd_Site_Logo' ),
				Array(
					'bp-meta.php' => Array(
						'cond' => 'buddypress',
						'inst' => 'Jvbpd_Bp_Meta',
					),
				),
				Array(
					'bp-list-meta.php' => Array(
						'cond' => 'buddypress',
						'inst' => 'Jvbpd_Bp_List_Meta',
					),
				),
				Array(
					'bp-activity-meta.php' => Array(
						'cond' => 'buddypress',
						'inst' => 'Jvbpd_Bp_Activity_Meta',
					),
				),
				Array(
					'bp-active-list.php' => Array(
						'cond' => 'buddypress',
						'inst' => 'Jvbpd_Bp_Active_list',
					),
				),
				Array(
					'bp-activity.php' => Array(
						'cond' => 'buddypress',
						'inst' => 'Jvbpd_Bp_Activity',
					),
				),
				Array(
					'bp-buddypress-list.php' => Array(
						'cond' => 'buddypress',
						'inst' => 'Jvbpd_Bp_BuddyPress_list',
					),
				),
				Array(
					'bp-members.php' => Array(
						'cond' => 'buddypress',
						'inst' => 'Jvbpd_Bp_Members',
					),
				),
				Array(
					'review-list.php' => Array(
						'cond' => 'lv_directoryReview',
						'inst' => 'Jvbpd_ReviewList',
					),
				),
				Array(
					'header-search.php' => Array(
						'cond' => 'lv_directory_header_search',
						'inst' => 'Jvbpd_Header_Search',
					),
				),
				//Array( 'float-buttons.php' => 'Jvbpd_Float_buttons' ),
				Array( 'header-user-menu.php' => 'Jvbpd_Header_User_Menu_Widget' ),
				
				Array( 'jv-video.php' => 'Jvbpd_video' ),
				Array( 'jv-modal-popup.php' => 'Jvbpd_modal_popup' ),
				
			),

			'themes' => Array(

				Array( 'single-header.php' => 'jvbpd_single_header' ),
				Array( 'single-spyscroll.php' => 'jvbpd_single_spyscroll' ),

				Array(
					'single-listing-route-timeline.php' => Array(
						'cond' => 'lava_RouteTimeline',
						'inst' => 'jvbpd_single_route_timeline',
					),
				),
				Array(
					'single-listing-service.php' => Array(
						'cond' => 'lava_directory',
						'inst' => 'jvbpd_single_service',
					),
				),
				Array(
					'single-author-reviews.php' => Array(
						'cond' => 'lava_directory',
						'inst' => 'jvbpd_single_author_reviews',
					),
				),

				/*
				Disabled
				Array( 'single-buttons.php' => 'jvbpd_single_buttons' ),
				Array( 'custom-widgets.php' => 'jvbpd_pracf' ),
				*/

				/** Single **/
				Array( 'single-post-header.php' => 'Jvbpd_Single_Post_Header' ),
				Array( 'single-author-box.php' => 'Jvbpd_Single_Author_Box' ),
				Array( 'single-post-comment-list.php' => 'Jvbpd_Single_Post_Comment_List' ),
				Array( 'single-post-navigation.php' => 'Jvbpd_Single_Post_Navigation' ),

				Array(
					'single-listing-badge.php' => Array(
						'cond' => 'lava_directory',
						'inst' => 'Jvbpd_Single_Badge',
					),
				),

				Array( 'single-title-line.php' => 'jvbpd_single_title_line' ),
				Array( 'single-btn-meta.php' => 'jvbpd_single_btn_meta' ),
				Array( 'single-profile.php' => 'jvbpd_single_profile' ),

				Array(
					'single-listing-description.php' => Array(
						'cond' => 'lava_directory',
						'inst' => 'jvbpd_single_description',
					),
				),
				Array(
					'single-listing-amenities.php' => Array(
						'cond' => 'lava_directory',
						'inst' => 'jvbpd_single_amenities',
					),
				),
				Array(
					'single-listing-faq.php' => Array(
						'cond' => 'lava_FaQ',
						'inst' => 'jvbpd_single_faq',
					),
				),
				Array(
					'single-listing-get-direction.php' => Array(
						'cond' => 'lava_directory_direction',
						'inst' => 'Jvbpd_Single_Get_Direction',
					),
				),
				Array(
					'single-listing-custom-field.php' => Array(
						'cond' => 'lv_directory_customfield',
						'inst' => 'Jvbpd_Single_Custom_Field',
					),
				),
				Array(
					'single-review.php' => Array(
						'cond' => 'lv_directoryReview',
						'inst' => 'jvbpd_single_review',
					),
				),
				Array(
					'single-vendor.php' => Array(
						'cond' => 'lava_directory_vendor',
						'inst' => 'jvbpd_single_vender',
					),
				),
				Array(
					'single-booking.php' => Array(
						'cond' => 'lava_directory_booking',
						'inst' => 'jvbpd_single_booking',
					),
				),
				Array(
					'single-events.php' => Array(
						'cond' => 'Lava_EventConnector',
						'inst' => 'jvbpd_single_events',
					),
				),

				Array( 'single-contact-info.php' => 'jvbpd_single_contact_info' ),

				Array(
					'single-small-map.php' => Array(
						'cond' => 'lava_directory',
						'inst' => 'jvbpd_single_small_map',
					),
				),
				Array(
					'single-gallery.php' => Array(
						'cond' => 'lava_directory',
						'inst' => 'jvbpd_single_gallery',
					),
				),
				Array(
					'single-working-hours.php' => Array(
						'cond' => 'lava_open_hours_manager',
						'inst' => 'jvbpd_single_working_hours',
					),
				),
				Array(
					'single-listing-more-taxo.php' => Array(
						'cond' => 'javo_moreTax',
						'inst' => 'jvbpd_single_more_taxo',
					),
				),

				Array( 'single-listing-cf7.php' => 'jvbpd_single_cf7' ),

				Array(
					'single-listing-title.php' => Array(
						'cond' => 'lava_directory',
						'inst' => 'jvbpd_single_title',
					),
				),
				Array(
					'single-listing-address.php' => Array(
						'cond' => 'lava_directory',
						'inst' => 'jvbpd_single_address',
					),
				),
				Array(
					'single-listing-tel1.php' => Array(
						'cond' => 'lava_directory',
						'inst' => 'jvbpd_single_tel1',
					),
				),
				Array(
					'single-listing-review-average.php' => Array(
						'cond' => 'lv_directoryReview',
						'inst' => 'jvbpd_single_review_average',
					),
				),
				Array(
					'single-listing-breadcrumb.php' => Array(
						'cond' => 'lava_directory',
						'inst' => 'jvbpd_single_breadcrumb',
					),
				),
				Array(
					'single-listing-video.php' => Array(
						'cond' => 'lava_directory_video',
						'inst' => 'jvbpd_single_video',
					),
				),
				Array(
					'single-listing-3dviewer.php' => Array(
						'cond' => 'lava_directory_3DViewer',
						'inst' => 'jvbpd_single_3dviewer',
					),
				),
				Array(
					'single-listing-favorite.php' => Array(
						'cond' => 'lv_directory_favorite',
						'inst' => 'Jvbpd_Single_Favorite',
					),
				),

				Array( 'single-social-share.php' => 'jvbpd_social_share' ),

				Array(
					'single-lava-booking.php' => Array(
						'cond' => 'lava_bookings',
						'inst' => 'Jvbpd_Lava_Booking',
					),
				),
				Array(
					'single-lava-tour-timeline.php' => Array(
						'cond' => 'lava_TourTimeline',
						'inst' => 'Jvbpd_Lava_Tour_Timeline',
					),
				),

				Array(
					'map-list-reset-filter.php' => Array(
						'cond' => 'lava_directory',
						'inst' => 'jvbpd_map_list_reset_filter',
					),
				),
				Array(
					'map-list-listing-filter-total-count.php' => Array(
						'cond' => 'lava_directory',
						'inst' => 'jvbpd_map_list_filter_total_count',
					),
				),
				Array(
					'map-list-grid-toggle.php' => Array(
						'cond' => 'lava_directory',
						'inst' => 'jvbpd_map_list_grid_toggle',
					),
				),
				Array(
					'map-list-toggle.php' => Array(
						'cond' => 'lava_directory',
						'inst' => 'jvbpd_map_list_toggle',
					),
				),
				Array(
					'map-list-sort-dropdown.php' => Array(
						'cond' => 'lava_directory',
						'inst' => 'jvbpd_map_list_sort_dropdown',
					),
				),
				Array(
					'map-list-category-banner.php' => Array(
						'cond' => 'lava_directory',
						'inst' => 'jvbpd_map_list_category_banner',
					),
				),
				Array(
					'archive-title.php' => Array(
						'cond' => 'lava_directory',
						'inst' => 'Jvbpd_Archive_Title',
					),
				),
				Array(
					'acf-detail.php' => Array(
						'cond' => 'lava_directory',
						'inst' => 'jvbpd_acf_detail',
					),
				),
				/* homa */
				Array( 'single-header-buttons.php' => 'jvbpd_single_header_buttons' ),

				/** Maps **/

				Array(
					'map-filter-btns.php' => Array(
						'cond' => 'lava_directory',
						'inst' => 'jvbpd_map_filter_btns',
					),
				),
				Array(
					'map-maps.php' => Array(
						'cond' => 'lava_directory',
						'inst' => 'jvbpd_map_maps',
					),
				),
				Array(
					'map-amenities.php' => Array(
						'cond' => 'lava_directory',
						'inst' => 'jvbpd_map_amenities',
					),
				),
				Array(
					'map-more-tax.php' => Array(
						'cond' => 'javo_moreTax',
						'inst' => 'jvbpd_map_more_tax',
					),
				),
				Array(
					'map-list-filters.php' => Array(
						'cond' => 'lava_directory',
						'inst' => 'jvbpd_map_list_filters',
					),
				),
				Array(
					'map-acf-filter.php' => Array(
						'cond' => 'lava_directory',
						'inst' => 'jvbpd_map_acf_filter',
					),
				),
			),
		);

		if( is_array( $incWidgets ) ) {
			foreach( $incWidgets as $widgetType => $widgets ) {
				$path = Array( jvbpdCore()->widget_path );
				if( in_array( $widgetType, Array( 'themes', 'form' ) ) ) {
					// $path[] = jvbpdCore()->getTemplateName();
					$path[] = 'listing';
				}
				if( $widgetType == 'form' ) {
					$path[] = 'form';
				}
				if( is_array( $widgets ) ) {
					foreach( $widgets as $widget ) {
						foreach( $widget as $file => $instanceName ) {
							if(isset($instanceName['cond'])){
								if(!function_exists($instanceName['cond'])){
									continue;
								}
								$instanceName = $instanceName['inst'];
							}
							$filePath = trailingslashit( join( '/', $path ) ) . $file;
							if( file_exists( $filePath ) ) {
								require_once( $filePath );
								if( '' !==  $instanceName ) {
									$instance = '\jvbpdelement\Widgets\\' . $instanceName;
									if( class_exists( $instance ) ) {
										\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new $instance() );
									}
								}
							}
						}
					}
				}
			}
		}

	}

}
new Plugin;

<?php
return array(
	'timezone' => array(
		'id'            => 'listing_timezone',
		'title'         => 'Timezone',
		'context'       => 'normal',
		'priority'      => 'low',
		'show_names'    => true, // Show field names on the left
		'fields'        => array(
			array(
				'name'          => 'Timezone',
				'id'            => 'wilcity_timezone',
				'type'          => 'text'
			)
		)
	),
	'myProducts' => array(
		'id'            => 'my_products',
		'title'         => 'My Products',
		'object_types'  => \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false, true),
		'context'       => 'normal',
		'priority'      => 'low',
		'save_fields'   => false,
		'show_names'    => true, // Show field names on the left
		'fields'        => array(
			array(
				'type'      => 'select2_posts',
				'description'      => 'Showing WooCommerce Products on this Listing page',
				'post_types'=> array('product'),
				'attributes' => array(
					'ajax_action' => 'wilcity_fetch_dokan_products',
					'post_types'  => 'product'
				),
				'id'        => 'wilcity_my_products',
				'multiple'  => true,
				'name'      => 'My Products',
				'default_cb'=> array('WilokeListingTools\MetaBoxes\Listing', 'getMyProducts')
			)
		)
	),
	'myNumberRestaurantMenus' => array(
		'id'            => 'wilcity_number_restaurant_menus_wrapper',
		'title'         => 'Restaurant Menu Control',
		'object_types'  => \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false, true),
		'context'       => 'normal',
		'priority'      => 'low',
		'save_fields'   => true,
		'show_names'    => true, // Show field names on the left
		'fields'        => array(
			array(
				'type'          => 'text',
				'id'            => 'wilcity_number_restaurant_menus',
				'name'          => '',
				'after'         => '<button id="wilcity-add-menu-restaurant" class="button button-primary">'.esc_html__('Add New Menu', 'wiloke-listing-tools').'</button>',
				'default'       => 1
			),
			array(
				'type'          => 'hidden',
				'description'   => '',
				'id'            => 'wilcity_changed_menu_restaurant',
				'name'          => 'Has Changed',
				'save_field'    => false,
				'default'       => ''
			),
			array(
				'type'          => 'hidden',
				'description'   => '',
				'id'            => 'wilcity_menu_restaurant_keys',
				'name'          => 'Keys',
				'save_field'    => false,
				'default'       => ''
			)
		)
	),
	'myRestaurantMenu' => array(
		'id'            => 'wilcity_restaurant_menu_group',
		'title'         => 'Restaurant Menu Settings',
		'object_types'  => \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false, true),
		'context'       => 'normal',
		'priority'      => 'low',
		'save_fields'   => true,
		'show_names'    => true, // Show field names on the left
		'general_settings' => array(
			'general_settings' => array(
				array(
					'name' => 'Menu Title',
					'id'   => 'wilcity_group_title',
					'type' => 'text'
				),
				array(
					'name' => 'Menu Description',
					'id'   => 'wilcity_group_description',
					'type' => 'text'
				),
				array(
					'name' => 'Menu Icon',
					'description' => 'You can use <a href="https://fontawesome.com/v4.7.0/" target="_blank">FontAwesome 4</a> or <a href="https://icons8.com/line-awesome" target="_blank">Line Awesome</a>',
					'id'   => 'wilcity_group_icon',
					'type' => 'text'
				)
			),
			'group_settings' => array(
				'id'          => 'wilcity_restaurant_menu_group',
				'type'        => 'group',
				'description' => 'Setting up Menu',
				'repeatable'  => true, // use false if you want non-repeatable group
				'after_group' => '<a class="wilcity-delete-menu-restaurant" style="position: absolute; color: red; bottom: 10px; right: 10px; padding: 10px;" href="#">Delete Menu</a>',
				'options'     => array(
					'group_title'      => __( 'Menu', 'cmb2' ),
					'add_button'       => __( 'Add new Item', 'wiloke-listing-tools' ),
					'remove_button'    => __( 'Remove Item', 'wiloke-listing-tools' ),
					'sortable'         => true,
					'closed'           => true,
					'remove_confirm'   => esc_html__( 'Are you sure you want to remove?', 'wiloke-listing-tools' )
				)
			)
		),
		'group_fields' => array(
			array(
				'name' => 'Gallery',
				'id'   => 'gallery',
				'type' => 'file_list'
			),
			array(
				'name' => 'Title',
				'id'   => 'title',
				'type' => 'text'
			),
			array(
				'name' => 'Description',
				'id'   => 'description',
				'type' => 'textarea'
			),
			array(
				'name' => 'Price',
				'id'   => 'price',
				'type' => 'text'
			),
			array(
				'name' => 'Link To',
				'id'   => 'link_to',
				'type' => 'text'
			),
			array(
				'name' => 'Is open new window?',
				'id'   => 'is_open_new_window',
				'type' => 'checkbox'
			)
		)
	),
	'myRoom' => array(
		'id'            => 'wilcity_my_room',
		'title'         => 'My Room',
		'object_types'  => \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false, true),
		'context'       => 'normal',
		'priority'      => 'low',
		'show_names'    => true, // Show field names on the left
		'fields'        => array(
			array(
				'type'      => 'select2_posts',
				'description'      => 'Showing Rooms on this Listing page',
				'post_types'=> array('product'),
				'attributes' => array(
					'ajax_action' => 'wilcity_fetch_my_room',
					'post_types'  => 'product'
				),
				'id'        => 'wilcity_my_room',
				'multiple'  => false,
				'default_cb'=> array('WilokeListingTools\MetaBoxes\Listing', 'getMyRoom')
			)
		)
	),
	'myPosts' => array(
		'id'            => 'wilcity_posts',
		'save_fields'   => false,
		'title'         => 'My Posts',
		'object_types'  => \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false, true),
		'context'       => 'normal',
		'priority'      => 'low',
		'show_names'    => true, // Show field names on the left
		'fields'        => array(
			array(
				'type'      => 'select2_posts',
				'description'      => 'Showing Posts on this Listing page',
				'post_types'=> array('post'),
				'attributes' => array(
					'ajax_action' => 'wilcity_fetch_my_posts',
					'post_types'  => 'post'
				),
				'id'        => 'wilcity_my_posts',
				'multiple'  => true,
				'default_cb'=> array('WilokeListingTools\MetaBoxes\Listing', 'getMyPosts')
			)
		)
	),
	'sidebars' => array(
		array(
			'id'    => 'general',
			'icon'  => 'la la-check-square',
			'name'  => esc_html__('General', 'wiloke-listing-tools'),
			'component' => 'WilokeSingleGeneral'
		),
		array(
			'id'    => 'edit-navigation',
			'icon'  => 'la la-check-square',
			'name'  => esc_html__('Edit Navigation', 'wiloke-listing-tools'),
			'component' => 'WilokeSingleEditNavigation'
		),
		array(
			'id'    => 'edit-sidebar',
			'icon'  => 'la la-database',
			'name'  => esc_html__('Edit Sidebar', 'wiloke-listing-tools'),
			'component' => 'WilokeSingleEditSidebar'
		)
	),
	'searchFields' => array(
		array(
			'type'          => 'date_range',
			'label'         => 'Event Date',
			'fromLabel'     => 'From',
			'toLabel'       => 'To',
			'key'           => 'date_range',
			'inPostTypes'   => array('event'),
			'isDefault'     => true
		),
		array(
			'type'      => 'wp_search',
			'label'     => 'What are you looking for?',
			'key'       => 'wp_search',
			'isDefault' => true
		),
		array(
			'type'          => 'autocomplete',
			'label'         => 'Where to look?',
			'radiusLabel'   => 'Radius',
			'key'           => 'google_place',
			'maxRadius'     => 500,
			'defaultRadius' => 200,
			'unit'          => 'km',
			'isDefault'     => true
		),
		array(
			'type'  => 'select2',
			'label' => 'Region',
			'group' => 'term',
			'key'   => 'listing_location',
			'ajaxAction' => 'wilcity_select2_fetch_term',
			'isAjax' => 'no',
			'isShowParentOnly' => 'no',
			'orderBy' => 'count',
			'order' => 'DESC',
			'isHideEmpty' => 0,
			'isDefault' => true
		),
		array(
			'type'              => 'select2',
			'label'             => 'Category',
			'group'             => 'term',
			'isAjax'            => 'no',
			'isMultiple'        => 'no',
			'ajaxAction'        => 'wilcity_select2_fetch_term',
			'key'               => 'listing_cat',
			'orderBy' => 'count',
			'order' => 'DESC',
			'isShowParentOnly'  => 'no',
			'isHideEmpty' => 0,
			'isDefault'         => true
		),
		array(
			'type'       => 'checkbox2',
			'label'      => 'Tags',
			'group'      => 'term',
			'ajaxAction' => 'wilcity_select2_fetch_term',
			'isAjax'     => 'no',
			'key'        => 'listing_tag',
			'isHideEmpty' => 0,
			'orderBy' => 'count',
			'order' => 'DESC',
			'isShowParentOnly' => 'no',
			'isDefault' => true
		),
		array(
			'type'      => 'select2',
			'label'     => 'Price range',
			'notInPostTypes'=> array('event'),
			'key'       => 'price_range',
			'isDefault' => true
		),
		array(
			'type'      => 'checkbox',
			'label'     => 'Open Now',
			'key'       => 'open_now',
			'notInPostTypes'=> array('event'),
			'isDefault' => true
		),
		array(
			'type'      => 'checkbox',
			'label'     => 'Most Viewed',
			'key'       => 'best_viewed',
			'isDefault' => true
		),
		array(
			'type'      => 'checkbox',
			'label'     => 'Recommended',
			'key'       => 'recommended',
			'isDefault' => true
		),
		array(
			'type'      => 'checkbox',
			'label'     => 'Rating',
			'key'       => 'best_rated',
			'notInPostTypes'=> array('event'),
			'isDefault' => true
		),
		array(
			'type'      => 'select2',
			'label'     => 'Type',
			'desc'      => 'The visitors can search other post types on the search form',
			'key'       => 'post_type',
			'isDefault' => true
		),
//		array(
//			'type'      => 'select2',
//			'label'     => 'Event Order By',
//			'desc'      => 'Set Event Order By',
//			'key'       => 'event_orderby',
//			'isDefault' => true
//		)
	),
	'navigation' => array(
		'fixed' => array(
			'home' => array(
				'name' => esc_html__('Home', 'wiloke-listing-tools'),
				'key'  => 'home',
				'isDraggable'   => 'no',
				'icon'          => 'la la-home',
				'status'        => 'yes'
			),
			'insights' => array(
				'name'          => esc_html__('Insights', 'wiloke-listing-tools'),
				'key'           => 'insights',
				'isDraggable'   => 'no',
				'icon'          => 'la la-bar-chart',
				'status'        => 'yes'
			),
			'settings' => array(
				'name'          => esc_html__('Settings', 'wiloke-listing-tools'),
				'key'           => 'settings',
				'isDraggable'   => 'no',
				'icon'          => 'la la-cog',
				'status'        => 'yes'
			)
		),
		'draggable' => array(
			'restaurant_menu' => array(
				'name'          => 'Restaurant Menu',
				'key'           => 'restaurant_menu',
				'isDraggable'   => 'yes',
				'icon'          => 'la la-cutlery',
				'isShowOnHome'  => 'yes',
				'status'        => 'no'
			),
			'coupon' => array(
				'name'          => 'Coupon',
				'key'           => 'coupon',
				'isDraggable'   => 'yes',
				'icon'          => 'la la-tag',
				'isShowOnHome'  => 'yes',
				'status'        => 'no'
			),
			'photos' => array(
				'name'          => 'Photos',
				'key'           => 'photos',
				'isDraggable'   => 'yes',
				'icon'          => 'la la-image',
				'isShowOnHome'  => 'yes',
				'maximumItemsOnHome' => 3,
				'status'        => 'yes'
			),
			'content' => array(
				'name'          => 'Description',
				'key'           => 'content',
				'isDraggable'   => 'yes',
				'icon'          => 'la la-file-text',
				'isShowOnHome'  => 'yes',
				'status'        => 'yes'
			),
			'videos' => array(
				'name'          => 'Videos',
				'key'           => 'videos',
				'isDraggable'   => 'yes',
				'icon'          => 'la la-video-camera',
				'isShowOnHome'  => 'yes',
				'maximumItemsOnHome' => 3,
				'status'        => 'yes'
			),
			'tags' => array(
				'name'          => 'Listing Features',
				'key'           => 'tags',
				'isDraggable'   => 'yes',
				'icon'          => 'la la-list-alt',
				'isShowOnHome'  => 'yes',
				'maximumItemsOnHome' => 3,
				'status'        => 'yes'
			),
			'my_products' => array(
				'name'          => 'My Products',
				'key'           => 'my_products',
				'isDraggable'   => 'yes',
				'icon'          => 'la la-video-camera',
				'isShowOnHome'  => 'no',
				'maximumItemsOnHome' => 3,
				'status'        => 'no'
			),
			'events' => array(
				'name'          => 'Events',
				'key'           => 'events',
				'icon'          => 'la la-bookmark',
				'isDraggable'   => 'yes',
				'isShowOnHome'  => 'yes',
				'maximumItemsOnHome' => 3,
				'status'        => 'yes'
			),
			'posts' => array(
				'name'          => 'Posts',
				'key'           => 'posts',
				'icon'          => 'la la-pencil',
				'isDraggable'   => 'yes',
				'isShowOnHome'  => 'yes',
				'maximumItemsOnHome' => 3,
				'status'        => 'yes'
			),
			'reviews' => array(
				'name'          => 'Reviews',
				'key'           => 'reviews',
				'icon'          => 'la la-star-o',
				'isDraggable'   => 'yes',
				'isShowOnHome'  => 'yes',
				'maximumItemsOnHome' => 3,
				'status'        => 'yes'
			),
			'google_adsense_1' => array(
				'name'          => 'Google AdSense 1',
				'key'           => 'google_adsense_1',
				'icon'          => 'la la-bullhorn',
				'isDraggable'   => 'yes',
				'isShowOnHome'  => 'no',
				'isShowBoxTitle'=> 'no',
				'status'        => 'no'
			),
			'google_adsense_2' => array(
				'name'          => 'Google AdSense 2',
				'key'           => 'google_adsense_2',
				'icon'          => 'la la-bullhorn',
				'isDraggable'   => 'yes',
				'isShowOnHome'  => 'no',
				'isShowBoxTitle'=> 'no',
				'status'        => 'no'
			),
			'taxonomy' => array(
				'name'          => 'Listing Taxonomy',
				'key'           => 'taxonomy',
				'taxonomy'      => '',
				'isDraggable'   => 'yes',
				'icon'          => 'la la-bookmark',
				'isShowOnHome'  => 'no',
				'maximumItemsOnHome' => 3,
				'status'        => 'no'
			)
		)
	),
	'sidebar_settings' => array(
		'aStyles' => array(
			'list'      => 'List',
			'slider'    => 'Slider',
			'grid'      => 'Grid'
		),
		'aRelatedBy' => array(
			'listing_location' => 'In the same Listing Locations',
			'listing_category' => 'In the same Listing Categories',
			'listing_tag'      => 'In the same Listing Tags',
			'google_address'   => 'In the same Google Address',
		),
		'toggleUseDefaults' => array(
			'label'     => esc_html__('Use default tabs', 'wiloke-listing-tools'),
			'value'     => 'yes'
		),
		'renderMachine' => array(
			'singlePrice'   => 'wilcity_sidebar_single_price',
			'priceRange'    => 'wilcity_sidebar_price_range',
			'businessInfo'  => 'wilcity_sidebar_business_info',
			'businessHours' => 'wilcity_sidebar_business_hours',
			'claim'         => 'wilcity_sidebar_claim',
			'categories'    => 'wilcity_sidebar_categories',
			'taxonomy'      => 'wilcity_sidebar_taxonomy',
			'tags'          => 'wilcity_sidebar_tags',
			'map'           => 'wilcity_sidebar_googlemap',
			'statistic'     => 'wilcity_sidebar_statistics',
			'bookingcombannercreator' => 'wilcity_sidebar_bookingcombannercreator',
			'myProducts'    => 'wilcity_sidebar_my_products',
			'woocommerceBooking' => 'wilcity_sidebar_woocommerce_booking',
			'author'        => 'wilcity_author_profile',
			'coupon'        => 'wilcity_sidebar_coupon',
			'relatedListings'=> 'wilcity_sidebar_related_listings',
		),
		'items' => array(
			'businessHours' => array(
				'name'      => esc_html__('Business Hours', 'wiloke-listing-tools'),
				'key'       => 'businessHours',
				'icon'      => 'la la-bookmark',
				'status'    => 'yes'
			),
			'priceRange' => array(
				'name'      => esc_html__('Price Range', 'wiloke-listing-tools'),
				'key'       => 'priceRange',
				'icon'      => 'la la-bookmark',
				'status'    => 'yes'
			),
			'singlePrice' => array(
				'name'      => esc_html__('Single Price', 'wiloke-listing-tools'),
				'key'       => 'singlePrice',
				'icon'      => 'la la-bookmark',
				'status'    => 'no'
			),
			'businessInfo' => array(
				'name'      => esc_html__('Business Info', 'wiloke-listing-tools'),
				'key'       => 'businessInfo',
				'icon'      => 'la la-bookmark',
				'status'    => 'yes'
			),
			'statistic' => array(
				'name'      => esc_html__('Statistic', 'wiloke-listing-tools'),
				'key'       => 'statistic',
				'icon'      => 'la la-bookmark',
				'status'    => 'yes'
			),
			'categories' => array(
				'name'      => esc_html__('Categories', 'wiloke-listing-tools'),
				'key'       => 'categories',
				'icon'      => 'la la-bookmark',
				'status'    => 'yes'
			),
			'taxonomy' => array(
				'name'      => 'Taxonomy',
				'key'       => 'taxonomy',
				'icon'      => 'la la-bookmark',
				'taxonomy'  => '',
				'status'    => 'no'
			),
			'coupon' => array(
				'name'      => 'Coupon',
				'key'       => 'coupon',
				'icon'      => 'la la-bookmark',
				'status'    => 'no'
			),
			'tags' => array(
				'name'      => esc_html__('Tags', 'wiloke-listing-tools'),
				'key'       => 'tags',
				'icon'      => 'la la-bookmark',
				'status'    => 'yes'
			),
			'map' => array(
				'name'      => esc_html__('Map', 'wiloke-listing-tools'),
				'key'       => 'map',
				'icon'      => 'la la-bookmark',
				'status'    => 'yes'
			),
			'author' => array(
				'name'      => esc_html__('Author', 'wiloke-listing-tools'),
				'key'       => 'author',
				'icon'      => 'la la-user',
				'status'    => 'yes'
			),
			'claim' => array(
				'name'      => esc_html__('Claim Listing', 'wiloke-listing-tools'),
				'key'       => 'claim',
				'icon'      => 'la la-bookmark',
				'status'    => 'yes'
			),
			'googleads' => array(
				'name'      => 'Google AdSense',
				'key'       => 'google_adsense',
				'icon'      => 'la la-bullhorn',
				'adminOnly' => 'yes', // Only admin can disable it on the single listing setting
				'status'    => 'yes'
			),
			'promotion' => array(
				'promotionID'=> '',
				'name'       => 'Promotion',
				'key'        => 'promotion',
				'style'      => 'slider',
				'icon'       => 'la la-bullhorn',
				'adminOnly'  => 'yes', // Only admin can disable it on the single listing setting
				'status'     => 'yes',
				'postsPerPage'     => 3,
				'isMultipleSections'=> 'yes'
			),
			'relatedListings' => array(
				'name'       => 'Related Listings',
				'key'        => 'relatedListings',
				'conditional'=> 'listing_category',
				'order'      => 'DESC',
				'style'      => 'slider',
				'orderby'    => 'menu_order',
				'oOrderFallbackBy'=> 'post_date',
				'icon'       => 'la la-bullhorn',
				'adminOnly'  => 'yes', // Only admin can disable it on the single listing setting
				'status'     => 'yes',
				'postsPerPage' => 3,
				'radius'     => 5,
				'isMultipleSections'=> 'yes'
			),
			'bookingcombannercreator' => array(
				'name'      => 'Booking.com Banner Creator',
				'key'       => 'bookingcombannercreator',
				'icon'      => 'la la-hotel',
				'status'    => 'yes'
			),
			'myProducts' => array(
				'name'      => 'My Products',
				'key'       => 'myProducts',
				'icon'      => 'la la-shopping-cart',
				'status'    => 'no'
			),
			'woocommerceBooking' => array(
				'name'      => 'My Room',
				'key'       => 'woocommerceBooking',
				'icon'      => 'la la-shopping-cart',
				'status'    => 'no'
			)
		),
		'updates' => array(
			'businessInfo' => array(
				'name'      => esc_html__('Business Info', 'wiloke-listing-tools'),
				'key'       => 'businessInfo',
				'icon'      => 'la la-bookmark',
				'status'    => 'yes'
			)
		),
		'deprecated' => array(
			'addressInfo'
		)
	),
	'defines' => array(
		'layout'        => esc_html__('Layout', 'wiloke-listing-tools'),
		'layoutDesc'    => esc_html__('Customize your page layout', 'wiloke-listing-tools'),
		'addButton'    => esc_html__('Add a Button', 'wiloke-listing-tools'),
		'addButtonDesc' => esc_html__('The button at the top of your Page helps people take an action.', 'wiloke-listing-tools'),
		'websiteLink' => esc_html__('Add a website link', 'wiloke-listing-tools'),
		'icon' => esc_html__('Icon', 'wiloke-listing-tools'),
		'buttonName' => esc_html__('Button Name', 'wiloke-listing-tools'),
		'rightSidebar'  => esc_html__('Right Sidebar', 'wiloke-listing-tools'),
		'leftSidebar'   => esc_html__('Left Sidebar', 'wiloke-listing-tools'),
		'navigation'    => esc_html__('Edit Navigation', 'wiloke-listing-tools'),
		'navigationDesc'=> esc_html__('Click and drag a tab name to rearrange the order of the navigation.', 'wiloke-listing-tools'),
		'sidebar'       => esc_html__('Sidebar', 'wiloke-listing-tools'),
		'sidebarDesc'   => esc_html__('Click and drag a sidebar item to rearrange the order', 'wiloke-listing-tools'),
		'isUseDefaultLabel' => esc_html__('Use Default?', 'wiloke-listing-tools')
	),
	'keys' => array(
		'navigation'            => 'navigation_settings',
		'sidebar'               => 'sidebar_settings',
		'isUsedDefaultSidebar'  => 'yes',
		'isUsedDefaultNav'      => 'yes',
		'general'               => 'general_settings',
		'highlightBoxes'        => 'highlight_boxes',
		'card'                  => 'listing_card',
		'footer_card'           => 'listing_footer_card',
		'header_card'           => 'listing_header_card'
	),
	'listingCard' => array(
		'aButtonInfoOptions' => array(
			array(
				'name' => 'Call Us',
				'value'=> 'call_us',
				'key'  => 'call_us'
			),
			array(
				'name' => 'Email Us',
				'value'=> 'email_us',
				'key'  => 'email_us'
			),
			array(
				'name' => 'Total Views',
				'value'=> 'total_views',
				'key'  => 'total_views'
			)
		),
		'aBodyTypeFields' => array(
			array(
				'name' => 'Google Address',
				'value'=> 'google_address',
				'key'  => 'google_address'
			),
			array(
				'name' => 'Phone',
				'value'=> 'phone',
				'key'  => 'phone'
			),
			array(
				'name' => 'Email',
				'value'=> 'email',
				'key'  => 'email'
			),
			array(
				'name' => 'Website',
				'value'=> 'website',
				'key'  => 'website'
			),
			array(
				'name' => 'Price Range',
				'value'=> 'price_range',
				'key'  => 'price_range'
			),
			array(
				'name' => 'Single Price',
				'value'=> 'single_price',
				'key'  => 'single_price'
			),
			array(
				'name' => 'Listing Category',
				'value'=> 'listing_cat',
				'key'  => 'listing_cat'
			),
			array(
				'name' => 'Listing Location',
				'value'=> 'listing_location',
				'key'  => 'listing_location'
			),
			array(
				'name' => 'Listing Tag',
				'value'=> 'listing_tag',
				'key'  => 'listing_tag'
			),
			array(
				'name' => 'Custom Taxonomy',
				'value'=> 'custom_taxonomy',
				'key'  => 'custom_taxonomy'
			),
//			array(
//				'name' => 'Social Networks',
//				'value'=> 'social_networks',
//				'key'  => 'social_networks'
//			),
			array(
				'name' => 'Custom Field',
				'value'=> 'custom_field',
				'key'  => ''
			)
		),
		'aBodyItems' => array(
			array(
				'type'      => 'google_address',
				'icon'      => 'la la-map-marker',
				'key'       => 'google_address'
			),
			array(
				'type'      => 'phone',
				'icon'      => 'la la-phone',
				'key'       => 'phone'
			),
			array(
				'type'      => 'email',
				'icon'      => 'la la-envelope',
				'key'       => 'email'
			),
			array(
				'type'      => 'website',
				'icon'      => 'la la-link',
				'key'       => 'website'
			),
			array(
				'type'      => 'price_range',
				'icon'      => '',
				'key'       => 'price_range'
			),
			array(
				'type'      => 'listing_tag',
				'icon'      => '',
				'key'       => 'listing_tag'
			),
			array(
				'type'      => 'listing_location',
				'icon'      => '',
				'key'       => 'listing_location'
			),
			array(
				'type'      => 'listing_cat',
				'icon'      => '',
				'key'       => 'listing_cat'
			),
			array(
				'type' => 'custom_taxonomy',
				'icon' => '',
				'key'  => ''
			),
			array(
				'type'      => 'custom_field',
				'icon'      => '',
				'key'       => ''
			),
		),
		'aFooter' => array(
			'taxonomy' => 'listing_cat'
		)
	)
);
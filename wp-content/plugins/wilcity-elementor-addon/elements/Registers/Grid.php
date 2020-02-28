<?php

namespace WILCITY_ELEMENTOR\Registers;


use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use WILCITY_SC\SCHelpers;

class Grid extends Widget_Base {
	use Helpers;

	public function get_name() {
		return apply_filters('wilcity/filter/id-prefix', 'wilcity-grid');
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return WILCITY_EL_PREFIX. 'Grid Layout';
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'fa fa-picture-o';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'theme-elements' ];
	}



	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.1.0
	 *
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'grid_general_section',
			[
				'label' => 'General Settings',
			]
		);

		$this->add_control(
			'heading',
			[
				'label'         => 'Heading',
				'type'          => Controls_Manager::TEXT,
				'default'       => 'The Latest Listings'
			]
		);

		$this->add_control(
			'heading_color',
			[
				'label'         => 'Heading Color',
				'type'          => Controls_Manager::COLOR,
				'default'       => ''
			]
		);

		$this->add_control(
			'desc',
			[
				'label' => 'Description',
				'type' => Controls_Manager::TEXTAREA,
				'default' => ''
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label'         => 'Description Color',
				'type'          => Controls_Manager::COLOR,
				'default'       => ''
			]
		);

		$this->add_control(
			'header_desc_text_align',
			[
				'label'         => 'Heading and Description Text Alignment',
				'type'          => Controls_Manager::SELECT,
				'default'       => 'wil-text-center',
				'admin_label'   => true,
				'options'		=> array(
					'wil-text-center' => 'Center',
					'wil-text-left'   => 'Left',
					'wil-text-right'  => 'Right'
				)
			]
		);

		$this->add_control(
			'style',
			[
				'label'         => 'Style',
				'type'          => Controls_Manager::SELECT,
				'default'       => 'grid',
				'admin_label'   => true,
				'options'		=> array(
					'grid'   => 'Grid 1 (Default)',
					'grid2'  => 'Grid 2',
					'list'   => 'List'
				)
			]
		);

		$this->add_control(
			'border',
			[
				'label'         => 'Toggle Border',
				'description'   => 'Adding a border around listing grid',
				'type'          => Controls_Manager::SELECT,
				'default'       => 'border-gray-0',
				'admin_label'   => true,
				'options'		=> array(
					'border-gray-1'   => 'Enable',
					'border-gray-0'   => 'Disable'
				)
			]
		);

		$this->add_control(
			'toggle_viewmore',
			[
				'label'         => 'Toggle Viewmore',
				'type'          => Controls_Manager::SELECT,
				'default'       => 'disable',
				'admin_label'   => true,
				'options'		=> array(
					'disable'   => 'Disable',
					'enable'    => 'Enable'
				)
			]
		);

		$this->add_control(
			'viewmore_btn_name',
			[
				'label'         => 'Button Name',
				'type'          => Controls_Manager::TEXT,
				'default'       => 'View More',
				'admin_label'   => true,
				'relation'    => array(
					'parent'    => 'toggle_viewmore',
					'show_when' => array('toggle_viewmore', '=', 'enable')
				)
			]
		);

		$this->add_control(
			'post_type',
			[
				'label'         => 'Post Type',
				'type'          => Controls_Manager::SELECT,
				'default'       => 'listing',
				'admin_label'   => true,
				'options'		=> SCHelpers::getPostTypeOptions()
			]
		);

		if ( $this->getTerms('listing_cat') !== 'toomany' ){
			$this->add_control(
				'listing_cats',
				[
					'label' => 'Select Categories',
					'type' => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple' => true,
					'options' => $this->getTerms('listing_cat')
				]
			);
		}else{
			$this->add_control(
				'listing_cats',
				[
					'label' => 'Enter in Category IDs',
					'description' => 'Each category is separated by a comma. For example: 1,2,3',
					'type' => Controls_Manager::TEXT,
					'label_block' => true
				]
			);
		}

		if ( $this->getTerms('listing_location') !== 'toomany' ){
			$this->add_control(
				'listing_locations',
				[
					'label' => 'Select Locations',
					'type' => Controls_Manager::SELECT2,
					'multiple' => true,
					'label_block' => true,
					'options' => $this->getTerms('listing_location')
				]
			);
		}else{
			$this->add_control(
				'listing_locations',
				[
					'label' => 'Enter in Location IDs',
					'description' => 'Each location is separated by a comma. For example: 1,2,3',
					'type' => Controls_Manager::TEXT,
					'label_block' => true
				]
			);
		}

		if ( $this->getTerms('listing_tag') !== 'toomany' ){
			$this->add_control(
				'listing_tags',
				[
					'label' => 'Select Tags',
					'type' => Controls_Manager::SELECT2,
					'multiple' => true,
					'label_block' => true,
					'options' => $this->getTerms('listing_tag')
				]
			);
		}else{
			$this->add_control(
				'listing_tags',
				[
					'label' => 'Enter in Tag IDs',
					'description' => 'Each tag is separated by a comma. For example: 1,2,3',
					'type' => Controls_Manager::TEXT,
					'label_block' => true
				]
			);
		}

		$this->add_control(
			'custom_taxonomy_key',
			[
				'label' => 'Taxonomy Key',
				'type' => Controls_Manager::TEXT,
				'description' => 'This feature is useful if you want to use show up your custom taxonomy',
				'default'=> ''
			]
		);

		$this->add_control(
			'custom_taxonomies_id',
			[
				'label' => 'Your Custom Taxonomies IDs',
				'type' => Controls_Manager::TEXT,
				'description' => 'Each taxonomy should separated by a comma, Eg: 1,2,3,4. Leave empty if you are working on Taxonomy Template',
				'default'=> ''
			]
		);

		$this->add_control(
			'listing_ids',
			[
				'label' => 'Specify Listing IDs',
				'description' => 'Each Listing ID is separated by a comma. For example: 1,2,3',
				'type' => Controls_Manager::TEXT,
				'default'=> ''
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label' => 'Maximum Items',
				'type' => Controls_Manager::TEXT,
				'default'=> 6
			]
		);

		$this->add_control(
			'img_size',
			[
				'label' => 'Image Size',
				'description' => 'For example: 200x300. 200: Image width. 300: Image height',
				'type' => Controls_Manager::TEXT,
				'default'=> 'wilcity_360x200'
			]
		);

		$this->add_control(
			'orderby',
			[
				'label' => 'Order By',
				'type' => Controls_Manager::SELECT,
				'default'=> 'post_date',
				'options'     => array(
					'post_date'     => 'Listing Date',
					'post_title'    => 'Listing Title',
					'menu_order'    => 'Listing Order',
					'best_viewed'   => 'Popular Viewed',
					'best_rated'    => 'Popular Rated',
					'best_shared'   => 'Popular Shared',
					'rand'          => 'Random',
					'premium_listings' => 'Premium Listings',
					'post__in'      => 'Like Specify Listing IDs field',
					'nearbyme'      => 'Near By Me',
					'open_now'      => 'Open now'
				)
			]
		);

		$this->add_control(
			'radius',
			[
				'label'         => 'Radius',
				'description'   => 'Fetching all listings within x radius',
				'value'         => 10,
				'condition'     => array(
					'orderby'   => 'nearbyme'
				),
				'type'          => Controls_Manager::TEXT
			]
		);

		$this->add_control(
			'unit',
			[
				'type'  => Controls_Manager::SELECT,
				'label'         => 'Unit',
				'condition'     => array(
					'orderby'   => 'nearbyme'
				),
				'options'       => array(
					'km'    => 'KM',
					'm'     => 'Miles'
				),
				'default' => 'km'
			]
		);

		$this->add_control(
			'tabname',
			[
				'type'  => Controls_Manager::TEXT,
				'label'       => 'Tab Name',
				'description' => 'If the grid layout is inside of a tab, we recommend putting the Tab ID to this field. If the tab is emptied, the listings will be shown after the browser is loaded. Otherwise, it will be shown after someone clicks on the Tab Name.',
				'relation'    => array(
					'parent'    => 'orderby',
					'show_when' => array('orderby', '=', 'nearbyme')
				)
			]
		);

		$this->add_control(
			'extra_class',
			[
				'label'     => 'Extra Class',
				'type'      => Controls_Manager::TEXT
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'grid_devices_settings',
			[
				'label' => 'Devices Settings',
			]
		);

		$this->add_control(
			'maximum_posts_on_lg_screen',
			[
				'label' => 'Items / row on >=1200px',
				'description'   => 'Set number of listings will be displayed when the screen is larger or equal to 1400px ',
				'type' => Controls_Manager::SELECT,
				'default'=> 'col-lg-4',
				'options'     => array(
					'col-lg-2'  => '6 Items / row',
					'col-lg-3'  => '4 Items / row',
					'col-lg-4'  => '3 Items / row',
					'col-lg-6'  => '2 Items / row',
					'col-lg-12' => '1 Item / row'
				)
			]
		);

		$this->add_control(
			'maximum_posts_on_md_screen',
			[
				'label'         => 'Items / row on >=960px',
				'description'   => 'Set number of listings will be displayed when the screen is larger or equal to 1200px ',
				'type' => Controls_Manager::SELECT,
				'default'=> 'col-md-3',
				'options'     => array(
					'col-md-2'  => '6 Items / row',
					'col-md-3'  => '4 Items / row',
					'col-md-4'  => '3 Items / row',
					'col-md-6'  => '2 Items / row',
					'col-md-12' => '1 Item / row'
				)
			]
		);

		$this->add_control(
			'maximum_posts_on_sm_screen',
			[
				'label'         => 'Items / row on >=720px',
				'description'   => 'Set number of listings will be displayed when the screen is larger or equal to 640px ',
				'type' => Controls_Manager::SELECT,
				'default'=> 'col-sm-12',
				'options'     => array(
					'col-sm-2'  => '6 Items / row',
					'col-sm-3'  => '4 Items / row',
					'col-sm-4'  => '3 Items / row',
					'col-sm-6'  => '2 Items / row',
					'col-sm-12' => '1 Item / row'
				)
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.1.0
	 *
	 * @access protected
	 */
	protected function render() {
		$aSettings = $this->get_settings();
		$aSettings = wp_parse_args(
			$aSettings,
			array(
				'TYPE'      => 'GRID',
				'heading'         => '',
				'border'         => 'border-gray-0',
				'heading_color'   => '',
				'desc'   => '',
				'desc_color'   => '',
				'header_desc_text_align'   => '',
				'post_type' => 'listing',
				'from'  => 'all',
				'maximum_posts_on_lg_screen'    => 'col-lg-3',
				'maximum_posts_on_md_screen'    => 'col-md-4',
				'maximum_posts_on_sm_screen'    => 'col-sm-6',
				'img_size'          => 'wilcity_img_360x200',
				'listing_ids'          => '',
				'orderby'    => '',
				'posts_per_page'    => 6,
				'listing_cats'      => '',
				'toggle_viewmore'   => 'enable',
				'viewmore_btn_name'         => 'View more',
				'style'   => 'style1',
				'custom_taxonomy_key'       => '',
				'custom_taxonomies_id'      => '',
				'listing_locations' => '',
				'listing_tags'      => '',
				'extra_class'       => ''
			)
		);

		wilcity_sc_render_grid($aSettings);
	}
}
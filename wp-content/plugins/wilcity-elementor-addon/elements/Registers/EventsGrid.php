<?php

namespace WILCITY_ELEMENTOR\Registers;



use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use WILCITY_SC\SCHelpers;

class EventsGrid extends Widget_Base {
	use Helpers;

	public function get_name() {
		return WILCITY_WHITE_LABEL.'-event-grid';
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
		return WILCITY_EL_PREFIX. 'Event Layout';
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
			'from',
			[
				'label'         => 'Get Listings By',
				'type'          => Controls_Manager::SELECT,
				'default'       => 'all',
				'options'		=> array(
					'listing_tag'       => 'Listing Tags',
					'listing_cat'       => 'Listing Categories',
					'listing_location'  => 'Listing Locations',
					'all'               => 'Anywhere'
				)
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
					'options' => $this->getTerms('listing_cat'),
					'condition' => [
						'from' => 'listing_cat'
					]
				]
			);
		}else{
			$this->add_control(
				'listing_cats',
				[
					'label' => 'Enter in Category IDs',
					'description' => 'Each category is separated by a comma. For example: 1,2,3',
					'type' => Controls_Manager::TEXT,
					'label_block' => true,
					'options' => $this->getTerms('listing_cat'),
					'condition' => [
						'from' => 'listing_cat'
					]
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
					'options' => $this->getTerms('listing_location'),
					'condition' => [
						'from' => 'listing_location'
					]
				]
			);
		}else{
			$this->add_control(
				'listing_locations',
				[
					'label' => 'Enter in Location IDs',
					'description' => 'Each location is separated by a comma. For example: 1,2,3',
					'type' => Controls_Manager::TEXT,
					'label_block' => true,
					'options' => $this->getTerms('listing_location'),
					'condition' => [
						'from' => 'listing_location'
					]
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
					'options' => $this->getTerms('listing_tag'),
					'condition' => [
						'from' => 'listing_tag'
					]
				]
			);
		}else{
			$this->add_control(
				'listing_tags',
				[
					'label' => 'Enter in Tag IDs',
					'description' => 'Each tag is separated by a comma. For example: 1,2,3',
					'type' => Controls_Manager::TEXT,
					'label_block' => true,
					'options' => $this->getTerms('listing_tag'),
					'condition' => [
						'from' => 'listing_tag'
					]
				]
			);
		}

		$this->add_control(
			'posts_per_page',
			[
				'label' => 'Maximum Items',
				'type' => Controls_Manager::TEXT,
				'default'=> 6
			]
		);

		$this->add_control(
			'orderby',
			[
				'label' => 'Order By',
				'type' => Controls_Manager::SELECT,
				'default'=> 'post_date',
				'options'     => array(
					'post_date'       => 'Listing Date',
					'post_title'      => 'Listing Title',
					'menu_order'      => 'Listing Order',
					'upcoming_event'  => 'Upcoming Events',
					'happening_event' => 'Happening Events',
					'starts_from_ongoing_event' => 'Upcoming + Happening'
				)
			]
		);

		$this->add_control(
			'order',
			[
				'label' => 'Order',
				'type' => Controls_Manager::SELECT,
				'default'	=> 'DESC',
				'options'     => array(
					'DESC'       => 'DESC',
					'ASC'      	=> 'ASC'
				)
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
				'post_type' => 'event',
				'heading'         => '',
				'heading_color'   => '',
				'desc'   => '',
				'desc_color'   => '',
				'toggle_viewmore'   => 'disable',
				'viewmore_btn_name'         => 'View more',
				'header_desc_text_align'   => '',
				'maximum_posts_on_lg_screen'    => 'col-lg-3',
				'maximum_posts_on_md_screen'    => 'col-md-4',
				'maximum_posts_on_sm_screen'    => 'col-sm-6',
				'from'      => 'all',
				'orderby'   => 'post_date',
				'img_size'          => 'wilcity_img_360x200',
				'posts_per_page'    => 6,
				'extra_class'       => ''
			)
		);
		wilcity_sc_render_events_grid($aSettings);
	}
}
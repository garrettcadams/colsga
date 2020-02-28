<?php

namespace WILCITY_ELEMENTOR\Registers;


use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use WILCITY_SC\SCHelpers;

class ListingsSlider extends Widget_Base {
	use Helpers;

	public function get_name() {
		return WILCITY_WHITE_LABEL.'-listings-slider';
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
		return WILCITY_EL_PREFIX. 'Listings Slider';
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
			'post_type',
			[
				'label'         => 'Post Type',
				'type'          => Controls_Manager::SELECT,
				'default'       => 'listing',
				'admin_label'   => true,
				'options'		=> SCHelpers::getPostTypeOptions()
			]
		);

		$this->add_control(
			'from',
			[
				'label'         => 'Get Listings By',
				'type'          => Controls_Manager::SELECT,
				'default'       => 'all',
				'options'		=> array(
					'all'     		    => 'Any Term',
					'listing_cat'    	=> 'Listing Categories',
					'listing_location'  => 'Listing Locations',
					'listing_tag'    	=> 'Listing Tags'
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
			'maximum_posts',
			[
				'label' => 'Maximum Items',
				'type' => Controls_Manager::TEXT,
				'default'=> 8
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
					'post__in'      => 'Like Specify Listing IDs field',
					'premium_listings'   => 'Premium Listings'
				)
			]
		);

		$this->add_control(
			'toggle_gradient',
			[
				'label' => 'Toggle Gradient',
				'type' => Controls_Manager::SELECT,
				'default'=> 'enable',
				'options'     => array(
					'enable'     => 'Enable',
					'disable'    => 'Disable'
				)
			]
		);

		$this->add_control(
			'left_gradient',
			[
				'label'         => 'Left Gradient',
				'type'          => Controls_Manager::COLOR,
				'default'       => '#006bf7',
				'condition' => [
					'toggle_gradient' => 'enable'
				]
			]
		);

		$this->add_control(
			'right_gradient',
			[
				'label'         => 'Right Gradient',
				'type'          => Controls_Manager::COLOR,
				'default'       => '#ed6392',
				'condition' => [
					'toggle_gradient' => 'enable'
				]
			]
		);

		$this->add_control(
			'gradient_opacity',
			[
				'label'         => 'Opacity',
				'type'          => Controls_Manager::TEXT,
				'description'   => 'The value must equal to or smaller than 1',
				'default'       => '0.3',
				'condition' => [
					'toggle_gradient' => 'enable'
				]
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
			'listings_on_screens',
			[
				'label' => 'Devices Settings',
			]
		);

		$this->add_control(
			'desktop_image_size',
			[
				'label'         => 'Desktop Image Size',
				'description'   => 'You can use the defined image sizes like: full, large, medium, wilcity_560x300 or 400,300 to specify the image width and height.',
				'type' => Controls_Manager::TEXT,
				'default'=> ''
			]
		);

		$this->add_control(
			'maximum_posts_on_extra_lg_screen',
			[
				'label'         => 'Items / row on >=1600px',
				'description'   => 'Set number of listings will be displayed when the screen is larger or equal to 1600px ',
				'type' => Controls_Manager::TEXT,
				'default'=> 6
			]
		);

		$this->add_control(
			'maximum_posts_on_lg_screen',
			[
				'label'         => 'Items / row on >=1400px',
				'description'   => 'Set number of listings will be displayed when the screen is larger or equal to 1400px ',
				'type' => Controls_Manager::TEXT,
				'default'=> 5
			]
		);

		$this->add_control(
			'maximum_posts_on_md_screen',
			[
				'label'         => 'Items / row on >=1200px',
				'description'   => 'Set number of listings will be displayed when the screen is larger or equal to 1200px ',
				'type' => Controls_Manager::TEXT,
				'default'=> 5
			]
		);

		$this->add_control(
			'maximum_posts_on_sm_screen',
			[
				'label'         => 'Items on >=992px',
				'description'   => 'Set number of listings will be displayed when the screen is larger or equal to 992px ',
				'type' => Controls_Manager::TEXT,
				'default'=> 2
			]
		);

		$this->add_control(
			'maximum_posts_on_extra_sm_screen',
			[
				'label'         => 'Items on >=640px',
				'description'   => 'Set number of listings will be displayed when the screen is larger or equal to 640px ',
				'type' => Controls_Manager::TEXT,
				'default'=> 1
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'slider_configurations',
			[
				'label' => 'Slider configuration',
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => 'Auto Play',
				'type' => Controls_Manager::TEXT,
				'default'       => 3000
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
				'TYPE'      => 'LISTINGS_SLIDER',
				'maximum_posts'             => 8,
				'maximum_posts_on_extra_lg_screen'=> 6,
				'maximum_posts_on_lg_screen'=> 6,
				'maximum_posts_on_md_screen'=> 6,
				'maximum_posts_on_sm_screen'=> 2,
				'listing_ids'=> '',
				'toggle_gradient'=> 'enable',
				'left_gradient' => '#006bf7',
				'right_gradient' => '#ed6392',
				'gradient_opacity' => '0.3',
				'maximum_posts_on_extra_sm_screen'=> 2,
				'heading'         => '',
				'heading_color'   => '',
				'desc'   => '',
				'desc_color'   => '',
				'header_desc_text_align'   => '',
				'post_type'                 => 'listing',
				'toggle_viewmore'           => 'disable',
				'viewmore_btn_name'         => 'View more',
				'from'                      => 'all',
				'listing_tags'              => '',
				'listing_cats'              => '',
				'listing_locations'         => '',
				'orderby'                   => '',
				'desktop_image_size'        => '',
				'custom_taxonomy_key'       => '',
				'custom_taxonomies_id'      => '',
				'autoplay'              	=> 3000,
				'extra_class'               => '',
				'css_custom'                => ''
			)
		);

		wilcity_render_slider($aSettings);
	}
}
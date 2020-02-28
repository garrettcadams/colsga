<?php

namespace WILCITY_ELEMENTOR\Registers;


use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use WILCITY_ELEMENTOR\Registers\Helpers;
use WilokeListingTools\Framework\Helpers\General;

class Pricing extends Widget_Base {
	use Helpers;

	public function get_name() {
		return WILCITY_WHITE_LABEL.'-pricing-table';
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
		return WILCITY_EL_PREFIX. 'Pricing Table';
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
			'items_per_row',
			[
				'label'         => 'Items / Row',
				'type'          => Controls_Manager::SELECT,
				'default'       => 'col-md-4 col-lg-4',
				'options'		=> array(
					'col-md-4 col-lg-4' => '3 Items / Row',
					'col-md-3 col-lg-3' => '4 Items / Row',
					'col-md-6 col-lg-6' => '2 Items / Row',
					'col-md-12 col-lg-12' => '1 Item / Row'
				)
			]
		);

		$this->add_control(
			'toggle_nofollow',
			[
				'label'         => 'Add rel="nofollow" to Plan URL',
				'type'          => Controls_Manager::SELECT,
				'default'       => 'disable',
				'options'		=> array(
					'disable' => 'Disable',
					'enable'  => 'Enable'
				)
			]
		);

		$aPostTypes = General::getPostTypeKeys(false, false);
		$aPricingOptions = array('flexible' => 'Depends on Listing Type Request');
		$aPricingOptions = $aPricingOptions + array_combine($aPostTypes, $aPostTypes);

		$this->add_control(
			'listing_type',
			[
				'label'         => 'Post Type',
				'type'          => Controls_Manager::SELECT,
				'default'       => 'flexible',
				'options'		=> $aPricingOptions
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
				'include'       => '',
				'items_per_row' => 'col-md-4',
				'extra_class'   => '',
				'listing_type'  => 'flexible',
				'toggle_nofollow'  => 'disable',
				'css'           => ''
			)
		);
		wilcityPricing($aSettings);
	}
}
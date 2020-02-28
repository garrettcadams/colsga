<?php

namespace WILCITY_ELEMENTOR\Registers;


use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use WilokeListingTools\Framework\Helpers\General;

class SearchForm extends Widget_Base {
	use Helpers;

	public function get_name() {
		return WILCITY_WHITE_LABEL.'-searchform';
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
		return WILCITY_EL_PREFIX. 'Search Form';
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
			'section_hero',
			[
				'label' => 'Settings',
			]
		);

		$this->add_control(
			'items',
			[
				'label'   => 'Search Form',
				'type'    => Controls_Manager::REPEATER,
				'fields' => [
					array(
						'type' 	=> Controls_Manager::TEXT,
						'label' => 'Tab Name',
						'name' 	=> 'name'
					),
					array(
						'type' 	=> Controls_Manager::SELECT,
						'label' => 'Directory Type',
						'name' 	=> 'post_type',
						'options' => General::getPostTypeOptions(false, false)
					)
				],
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
				'TYPE'        => 'SEARCH FORM',
				'items'       => array(),
				'search_form_bg_color' => '',
				'tab_activation_bg_color' => '',
				'tab_text_color' => '',
				'extra_class' => '',
			)
		);
		wilcity_sc_render_hero_search_form($aSettings);
	}
}
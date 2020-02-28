<?php

namespace WILCITY_ELEMENTOR\Registers;


use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use WILCITY_ELEMENTOR\Registers\Helpers;
use WilokeListingTools\Framework\Helpers\General;

class Heading extends Widget_Base {
	use Helpers;

	public function get_name() {
		return WILCITY_WHITE_LABEL.'-heading';
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
		return WILCITY_EL_PREFIX. 'Heading';
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
			'heading_section',
			[
				'label' => 'General Settings',
			]
		);

		$this->add_control(
			'blur_mark',
			[
				'label'     => 'Blur Mark',
				'type'      => Controls_Manager::TEXT
			]
		);

		$this->add_control(
			'heading',
			[
				'label'     => 'Heading',
				'type'      => Controls_Manager::TEXT
			]
		);

		$this->add_control(
			'heading_color',
			[
				'label'     => 'Heading Color',
				'type'      => Controls_Manager::COLOR
			]
		);

		$this->add_control(
			'description',
			[
				'label'     => 'Description',
				'type'      => Controls_Manager::TEXTAREA
			]
		);

		$this->add_control(
			'description_color',
			[
				'label'     => 'Description Color',
				'type'      => Controls_Manager::COLOR
			]
		);


		$this->add_control(
			'alignment',
			[
				'label'     => 'Alignment',
				'type'      => Controls_Manager::SELECT,
				'default'   => 'wil-text-center',
				'options'   => array(
					'wil-text-center' => 'Center',
					'wil-text-right'  => 'Right',
					'wil-text-left'	  => 'Left'
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
				'TYPE'      => 'HEADING',
				'blur_mark'         => '',
				'blur_mark_color'   => '',
				'heading'           => '',
				'heading_color'     => '#252c41',
				'description'       => '',
				'description_color' => '#70778b',
				'alignment'         => 'wil-text-center',
				'extra_class'       => ''
			)
		);
		wilcity_render_heading($aSettings);
	}
}
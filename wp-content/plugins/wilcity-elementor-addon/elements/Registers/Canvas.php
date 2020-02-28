<?php

namespace WILCITY_ELEMENTOR\Registers;


use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use WILCITY_ELEMENTOR\Registers\Helpers;
use WilokeListingTools\Framework\Helpers\General;

class Canvas extends Widget_Base {
	use Helpers;

	public function get_name() {
		return WILCITY_WHITE_LABEL.'-canvas';
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
		return WILCITY_EL_PREFIX. 'Wiloke Waves';
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
				'type'          => Controls_Manager::TEXT
			]
		);

		$this->add_control(
			'description',
			[
				'label'         => 'Description',
				'type'          => Controls_Manager::TEXTAREA
			]
		);

		$this->add_control(
			'left_gradient_color',
			[
				'label'         => 'Left Gradient',
				'type'          => Controls_Manager::COLOR,
				'default'       => '#f06292'
			]
		);

		$this->add_control(
			'right_gradient_color',
			[
				'label'         => 'Right Gradient',
				'type'          => Controls_Manager::COLOR,
				'default'       => '#f97f5f'
			]
		);

		$this->add_control(
			'btn_group',
			[
				'label'   => 'Button Group',
				'type'    => Controls_Manager::REPEATER,
				'fields' => [
					[
						'name'  => 'icon',
						'label' => 'Icon',
						'type'  => Controls_Manager::ICON
					],
					[
						'name' => 'name',
						'label' => 'Button Name',
						'type' => Controls_Manager::TEXT
					],
					[
						'name' => 'url',
						'label' => 'Button URL',
						'type' => Controls_Manager::TEXT
					],
					[
						'name'  => 'open_type',
						'label' => 'Open Type',
						'type'  => Controls_Manager::SELECT,
						'options'=> array(
							'_self' => 'In the same window',
							'_blank'=> 'In a New Window'
						)
					]
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
				'TYPE'                  => 'WAVE',
				'heading'               => '',
				'description'           => '',
				'btn_group'             => array(),
				'left_gradient_color'   => '#f06292',
				'right_gradient_color'  => '#f97f5f',
				'extra_class'           => ''
			)
		);
		wilcity_render_wiloke_wave($aSettings);
	}
}
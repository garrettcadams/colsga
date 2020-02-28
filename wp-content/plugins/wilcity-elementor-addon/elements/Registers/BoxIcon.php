<?php

namespace WILCITY_ELEMENTOR\Registers;


use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class BoxIcon extends Widget_Base {
	public function get_name() {
		return WILCITY_WHITE_LABEL.'-box-icon';
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
		return WILCITY_EL_PREFIX. 'Box Icon';
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

	protected function _register_controls() {
		$this->start_controls_section(
			'grid_general_section',
			[
				'label' => 'General Settings',
			]
		);

		$this->add_control(
			'icon',
			[
				'label'   => 'Icon',
				'type'    => Controls_Manager::ICON,
				'default' => 'Icon'
			]
		);

		$this->add_control(
			'heading',
			[
				'label'   => 'Heading',
				'type'    => Controls_Manager::TEXT,
				'default' => ''
			]
		);

		$this->add_control(
			'description',
			[
				'label'   => 'Description',
				'type'    => Controls_Manager::TEXTAREA,
				'default' => ''
			]
		);
		$this->end_controls_section();
	}

	protected function render() {
		$aSettings = $this->get_settings();
		$aSettings = wp_parse_args(
			$aSettings,
			array(
				'TYPE'              => 'BOX_ICON',
				'icon'              => '',
				'heading'           => '',
				'description'       => '',
				'extra_class'       => ''
			)
		);
		wilcity_render_box_icon($aSettings);
	}
}
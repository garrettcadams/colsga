<?php

namespace WILCITY_ELEMENTOR\Registers;


use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class IntroBox extends Widget_Base {
	use Helpers;

	public function get_name() {
		return WILCITY_WHITE_LABEL.'-intro-box';
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
		return WILCITY_EL_PREFIX. 'Intro Box';
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
			'intro_general_section',
			[
				'label' => 'General Settings',
			]
		);

		$this->add_control(
			'bg_img',
			[
				'label' => 'Background Image',
				'type'  => Controls_Manager::MEDIA
			]
		);

		$this->add_control(
			'video_intro',
			[
				'label' => 'Video Intro',
				'type'  => Controls_Manager::TEXT
			]
		);

		$this->add_control(
			'intro',
			[
				'label'     => 'Intro',
				'type'      => Controls_Manager::WYSIWYG,
				'default'   => ''
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
				'TYPE'          => 'INTRO_BOX',
				'bg_img'        => '',
				'video_intro'   => '',
				'intro'         => '',
				'extra_class'   => ''
			)
		);
		if ( isset($aSettings['bg_img']) ){
			if ( isset($aSettings['bg_img']['id']) ){
				$aSettings['bg_img'] = wp_get_attachment_image_url($aSettings['bg_img']['id'], 'larger');
			}else{
				$aSettings['bg_img'] = $aSettings['bg_img']['url'];
			}
		}
		wilcity_render_intro_box($aSettings);
	}
}
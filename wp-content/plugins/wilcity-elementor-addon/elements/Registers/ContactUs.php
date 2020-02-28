<?php

namespace WILCITY_ELEMENTOR\Registers;


use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class ContactUs extends Widget_Base {
	use Helpers;

	public function get_name() {
		return WILCITY_WHITE_LABEL.'-contact-us';
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
		return WILCITY_EL_PREFIX. 'Contact Us';
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
			'contact_info_heading',
			[
				'label' => 'Contact Info Heading',
				'type' => Controls_Manager::TEXT,
				'default' => 'Contact Info'
			]
		);
		$this->add_control(
			'contact_info',
			[
				'label' => 'Contact Info',
				'type'    => Controls_Manager::REPEATER,
				'fields' => [
					[
						'name' => 'icon',
						'label' => 'Icon',
						'type' => Controls_Manager::ICON
					],
					[
						'name' => 'info',
						'label' => 'Info',
						'type' => Controls_Manager::TEXTAREA
					],
					[
						'name' => 'link',
						'label' => 'Link',
						'description' => 'Enter in # if it is not a real link.',
						'type' => Controls_Manager::TEXT
					],
					[
						'name' => 'type',
						'label'=> 'Type',
						'default' => 'default',
						'type' => Controls_Manager::SELECT,
						'options' => array(
							'default' => 'Default',
							'phone'   => 'Phone',
							'mail'    => 'mail'
						)
					],
					[
						'name' => 'target',
						'label'=> 'Open Type',
						'type' => Controls_Manager::SELECT,
						'default' => '_self',
						'options' => array(
							'_self' => 'Self page',
							'_blank'=> 'New Window'
						)
					]
				]
			]
		);

		$this->add_control(
			'contact_form_heading',
			[
				'label' => 'Contact Form Heading',
				'type' => Controls_Manager::TEXT,
				'default' => 'Contact Us'
			]
		);

		$this->add_control(
			'contact_form_7',
			[
				'label' => 'Contact Form 7',
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => $this->getPosts('wpcf7_contact_form')
			]
		);

		$this->add_control(
			'contact_form_shortcode',
			[
				'label' => 'Contact Form Shortcode',
				'description'	=> 'If you are using another contact form plugin, please enter its own shortcode here.',
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'options' => $this->getPosts('wpcf7_contact_form')
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
				'TYPE'                  => 'ContactUs',
				'contact_info_heading'  => '',
				'contact_form_heading'  => '',
				'contact_form_7'        => '',
				'contact_form_shortcode'=> '',
				'contact_info'          => array(),
				'extra_class'           => ''
			)
		);
//		var_export($aSettings);die();
		wilcity_sc_render_contact_us($aSettings);
	}
}
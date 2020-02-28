<?php

namespace WILCITY_ELEMENTOR\Registers;


use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Testimonials extends Widget_Base {
	public function get_name() {
		return WILCITY_WHITE_LABEL.'-testimonials1';
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
		return WILCITY_EL_PREFIX. 'Testimonials';
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
			'autoplay',
			[
				'label'   => 'Auto Play',
				'description'   => 'Leave empty to disable this feature. Or specify auto-play each x seconds',
				'type'    => Controls_Manager::TEXT,
				'default' => ''
			]
		);

		$this->add_control(
			'testimonials',
			[
				'label'   => 'Testimonials',
				'type'    => Controls_Manager::REPEATER,
				'fields' => [
					[
						'name' => 'name',
						'label' => 'Customer Name',
						'type' => Controls_Manager::TEXT
					],
					[
						'name' => 'testimonial',
						'label' => 'Testimonial',
						'type' => Controls_Manager::TEXTAREA
					],
					[
						'name' => 'profesional',
						'label' => 'Customer Professional',
						'type' => Controls_Manager::TEXT
					],
					[
						'name' => 'avatar',
						'label' => 'Avatar',
						'type' => Controls_Manager::MEDIA
					]
				],
			]
		);
		$this->end_controls_section();
	}

	protected function render() {
		$aSettings = $this->get_settings();

		if ( !empty($aSettings) ){
			$aTestimonials = array();
			foreach ($aSettings['testimonials'] as $aTestimonial){
				$aTestimonials[] = array(
					'name' => $aTestimonial['name'],
					'testimonial' => $aTestimonial['testimonial'],
					'profesional' => $aTestimonial['profesional'],
					'avatar'      => isset($aTestimonial['avatar']['url']) ? $aTestimonial['avatar']['url'] : ''
				);
			}

			unset($aSettings['testimonials']);
			$aSettings['testimonials'] = (object)$aTestimonials;
		}

		$aSettings = wp_parse_args(
			$aSettings,
			array(
				'TYPE'      => 'TESTIMONIAL',
				'icon'        => 'la la-quote-right',
				'testimonials'=> array(),
				'autoplay'    => '',
				'extra_class' => ''
			)
		);

		wilcity_sc_render_testimonials($aSettings);
	}
}

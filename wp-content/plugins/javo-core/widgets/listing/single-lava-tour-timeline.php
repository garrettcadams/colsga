<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit;

class Jvbpd_Lava_Tour_Timeline extends Widget_Base {

	public function get_name() { return 'jvbpd-lava-tour-timeline'; }
	public function get_title() { return 'Lava Tour Timeline'; }
	public function get_icon() { return 'eicon-button'; }
	public function get_categories() { return [ 'jvbpd-single-listing' ]; }


	protected function _register_controls() {
		$this->start_controls_section( 'general_settings', Array(
			'label' => esc_html__( 'General', 'jvfrmtd' ),
		) );
			$this->add_control('display_type', Array(
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__( 'Type', 'jvfrmtd' ),
				'default' => 'general',
				'options' => Array(
					'general' => esc_html__( 'General', 'jvfrmtd' ),
					'cross' => esc_html__( 'Cross', 'jvfrmtd' ),
					'oneline' => esc_html__( 'One line', 'jvfrmtd' ),
				),
			));
		$this->end_controls_section();
		$this->start_controls_section(
			'section_title_style',
			array(
				'label'      => esc_html__( 'Title', 'jvfrmtd' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		 /*Title Color*/
		 $this->add_control('heading_color', 
		 [
			 'label'         => esc_html__('Color', 'jvfrmtd'),
			 'type'          => Controls_Manager::COLOR,
			 'scheme' => [
				 'type'  => Scheme_Color::get_type(),
				 'value' => Scheme_Color::COLOR_1,
				 ],
			 'selectors'     => [
				 '{{WRAPPER}} .group-title' => 'color: {{VALUE}};',
				 //'{{WRAPPER}} .heading-header a span'=> 'color: {{VALUE}};',
				 ],
			 ]
		 );

		  /*Title Typography*/
		  $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'          => 'title_typography',
				'scheme'        => Scheme_Typography::TYPOGRAPHY_1,
				'selector'      => '{{WRAPPER}} .group-title',
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'section_subtitle_style',
			array(
				'label'      => esc_html__( 'Subtitle', 'jvfrmtd' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		 /*Title Color*/
		 $this->add_control('subtitle_color', 
		 [
			 'label'         => esc_html__('Color', 'jvfrmtd'),
			 'type'          => Controls_Manager::COLOR,
			 'scheme' => [
				 'type'  => Scheme_Color::get_type(),
				 'value' => Scheme_Color::COLOR_1,
				 ],
			 'selectors'     => [
				 '{{WRAPPER}} .group-sub-title' => 'color: {{VALUE}};',
				 //'{{WRAPPER}} .heading-header a span'=> 'color: {{VALUE}};',
				 ],
			 ]
		 );

		  /*Title Typography*/
		  $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'          => 'subtitle_typography',
				'scheme'        => Scheme_Typography::TYPOGRAPHY_1,
				'selector'      => '{{WRAPPER}} .group-sub-title',
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'section_des_style',
			array(
				'label'      => esc_html__( 'Description', 'jvfrmtd' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		 /*Title Color*/
		 $this->add_control('des_color', 
		 [
			 'label'         => esc_html__('Heading Color', 'jvfrmtd'),
			 'type'          => Controls_Manager::COLOR,
			 'scheme' => [
				 'type'  => Scheme_Color::get_type(),
				 'value' => Scheme_Color::COLOR_1,
				 ],
			 'selectors'     => [
				 '{{WRAPPER}} .group-content' => 'color: {{VALUE}};',
				 //'{{WRAPPER}} .heading-header a span'=> 'color: {{VALUE}};',
				 ],
			 ]
		 );

		  /*Title Typography*/
		  $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'          => 'des_typography',
				'scheme'        => Scheme_Typography::TYPOGRAPHY_1,
				'selector'      => '{{WRAPPER}} .group-content',
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'section_box_style',
			array(
				'label'      => esc_html__( 'Box', 'jvfrmtd' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		

		$this->add_group_control(
            Group_Control_Border::get_type(),
                [
                    'name'          => 'box_border',
                    'selector'      => '{{WRAPPER}} .lava-tour-timeline section#timeline > article > div.panel, {{WRAPPER}} .lava-tour-timeline section#timeline article div.panel-body:after',
                    
				]			
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tooltip_box_shadow',
				'selector' => '{{WRAPPER}} .lava-tour-timeline section#timeline > article > div.panel',
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'section_line_style',
			array(
				'label'      => esc_html__( 'Line', 'jvfrmtd' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		/*Title Color*/
		$this->add_control('line_color', 
		[
			'label'         => esc_html__('line Color', 'jvfrmtd'),
			'type'          => Controls_Manager::COLOR,
			'scheme' => [
				'type'  => Scheme_Color::get_type(),
				'value' => Scheme_Color::COLOR_1,
				],
			'selectors'     => [
				'{{WRAPPER}} #timeline:before' => 'background-color: {{VALUE}};',
				'{{WRAPPER}} .lava-tour-timeline section#timeline article div.panel div.badge'=> 'color: {{VALUE}};',
				'{{WRAPPER}} .lava-tour-timeline section#timeline article div.panel div.badge'=> 'box-shadow: 0 0 0 2px {{VALUE}};',
				],
			]
		);		

		
		$this->end_controls_section();
	}

	protected function render() {
		if(!function_exists('lava_TourTimeline')) {
			return;
		}
		jvbpd_elements_tools()->switch_preview_post();
		echo lava_TourTimeline()->shortcode->tourTimeLine(Array(
			'type' => $this->get_settings('display_type'),
			'post_id' => get_the_ID(),
        ));
		jvbpd_elements_tools()->restore_preview_post();
	}

}
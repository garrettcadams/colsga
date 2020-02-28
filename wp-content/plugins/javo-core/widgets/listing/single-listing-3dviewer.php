<?php
/**
Widget Name: Single Video widget
Author: Javo
Version: 1.0.0.0
*/


namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;

if( ! defined( 'ABSPATH' ) )
	die();

class jvbpd_single_3dviewer extends Widget_Base {

	public function get_name() {
		return 'jvbpd_single_3dviewer';
	}

	public function get_title() {
		return '3DViewer';   // title to show on elementor
	}

	public function get_icon() {
		return 'jvic-map-streamline-user';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-single-listing' ];    // category of the widget
	}

    protected function _register_controls() {

        $this->start_controls_section(
			'section_general',
			array(
				'label' => esc_html__( '3D Viewer', 'jvfrmtd' ),
			)
		);

		$this->add_control(
		'Des',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => sprintf(
					'<div class="elementor-jv-notice" style="background-color:#9b0a46; color:#ffc6c6; padding:10px;"><ul>'.
					'<li class="doc-link">'.
					esc_html__('How to use this widget.','jvfrmtd'). 
					'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-single-listing-page/" style="color:#fff;"> ' . 
					esc_html__( 'Documentation', 'jvfrmtd' ) . 
					'</a></li><li>&nbsp;</li>'.
					'<li class="notice">'.
					esc_html__('This widget is for only single listing detail page.', 'jvfrmtd'). 
					'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-notice/" style="color:#fff;">' . 
					esc_html__( 'Detail', 'jvfrmtd' ) . 
					'</a><br/></li><li>&nbsp;</li><li>'.
					esc_html__( 'Please do not use in other pages.', 'jvfrmtd' ) .
					'</li></ul></div>'
				)
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
				'section_style_3dviewer',
				[
					'label' => __( '3D Viewer Style', 'jvfrmtd' ),
					'tab'   => Controls_Manager::TAB_STYLE,
				]
		);

		$this->add_responsive_control(
			'lava_3dviewer_height',
			[
				'label' => __( 'Height', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 350,
				],
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 700,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-widget-container iframe' => 'height:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'lava_3dviewer_width',
			[
				'label' => __( 'Width', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
				],
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 700,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-widget-container iframe' => 'width:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'lava_3dviewer_min_height',
			[
				'label' => __( 'Min-Height', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 250,
				],
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 700,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-widget-container iframe' => 'min-height:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'lava_3dviewer_min_width',
			[
				'label' => __( 'Min-Width', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
				],
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 700,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-widget-container iframe' => 'min-width:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
    }

    protected function render() {
		$isPreviewMode = is_admin();

		if( $isPreviewMode) {
			?>
			<img src="https://s3-us-west-1.amazonaws.com/listopia/wp-content/uploads/sites/3/2018/03/26174706/screenshot_780.png" width="500" height="500" alt="3DViewer picture of javo">
			<?php
		}else{
			if( class_exists( '\lvDirectory3DViewer_Render' ) ) {
			$obj3DViewer = new \lvDirectory3DViewer_Render( get_post() );
			$obj3DViewer->output();
			}
		}
	}
}
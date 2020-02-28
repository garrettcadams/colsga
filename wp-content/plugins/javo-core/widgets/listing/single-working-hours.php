<?php
/**
 * Widget Name: Single working hours widget
 * Author: Javo
 * Version: 1.0.0.0
 */

namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Scheme_Typography;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


class jvbpd_single_working_hours extends Widget_Base {

	public function get_name() {
		return 'jvbpd-single-working-hours';
	}

	public function get_title() {
		return 'Working Hours (Single Listing)';   // title to show on elementor
	}

	public function get_icon() {
		return 'jvic-clock-streamline-time';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-single-listing' ];    // category of the widget
	}

    protected function _register_controls() {

        $this->start_controls_section(
			'section_general',
			array(
				'label' => esc_html__( 'Working hours', 'jvfrmtd' ),
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
					'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-notice/" style="color:#fff;"> ' .
					esc_html__( 'Detail', 'jvfrmtd' ) .
					'</a><br/></li><li>&nbsp;</li><li>'.
					esc_html__( 'Please do not use in other pages.', 'jvfrmtd' ) .
					'</li></ul></div>'
				)
			)
		);

		$this->end_controls_section();

		$this->start_controls_section( 'section_setting', Array(
			'label' => esc_html__( 'Setting', 'jvfrmtd' ),
		) );
			$this->add_control('time_format', Array(
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__( 'Time format', 'jvfrmtd' ),
				'default' => '12',
				'options' => Array(
					'12' => esc_html__( '12 hour format', 'jvfrmtd' ),
					'24' => esc_html__( '24 hour format', 'jvfrmtd' ),
				),
			));
		$this->end_controls_section();

		$this->start_controls_section(
			'openhour_style',
			[
				'label' => __( 'Style','jvfrmtd'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(), [
				'name' => 'openhour_days_text_typography',
				'label' => __('Days Text Typography','jvfrmtd'),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} #javo-item-workinghours-section .hidding-timings .days',
			]
		);

		$this->add_control(
			'openhour_days_text_color',
			[
				'label' => __( 'Days Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#454545',
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} #javo-item-workinghours-section .hidding-timings .days' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(), [
				'name' => 'openhour_hours_text_typography',
				'label' => __('Hours Text Typography','jvfrmtd'),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} #javo-item-workinghours-section .hidding-timings .hours',
			]
		);

		$this->add_control(
			'openhour_hours_text_color',
			[
				'label' => __( 'Hours Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#cccccc',
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} #javo-item-workinghours-section .hidding-timings .hours' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(), [
				'name' => 'openhour_today_hours_text_typography',
				'label' => __('Today Hours Text Typography','jvfrmtd'),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} #javo-item-workinghours-section .today-timing .hours',
			]
		);

		$this->add_control(
			'openhour_today_hours_text_color',
			[
				'label' => __( 'Today Hours Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#454545',
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} #javo-item-workinghours-section .today-timing .hours' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'openhour_padding',
			[
				'label'      => esc_html__( 'Padding', 'jvfrmtd' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default'	   => [
					'top' => 30,
					'right' => 30,
					'bottom' => 30,
					'left' => 30,
				],
				'selectors'  => [
					'{{WRAPPER}} #javo-item-workinghours-section .open-hours' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'openhour_margin',
			[
				'label'      => esc_html__( 'Margin', 'jvfrmtd' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} #javo-item-workinghours-section .open-hours' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'openhour_today_condition_label_style',
			[
				'label' => __( 'Today Condition label','jvfrmtd'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(), [
				'name' => 'condition_label_condition_label_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} #javo-item-workinghours-section .today-timing .days',
			]
		);

		$this->add_control(
		'condition_label_text_color',
			[
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#454545',
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} #javo-item-workinghours-section .today-timing .days' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
		'condition_label_open_background_color',
			[
				'label' => __( 'Open Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default' => '#4c58a4',
				'selectors' => [
					'{{WRAPPER}} #javo-item-workinghours-section .today-timing .days.open-now' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
		'condition_label_close_background_color',
			[
				'label' => __( 'Close Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default' => '#dddddd',
				'selectors' => [
					'{{WRAPPER}} #javo-item-workinghours-section .today-timing .days.closed' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'condition_label_border',
				'selector' => '{{WRAPPER}} #javo-item-workinghours-section .today-timing .days',
			]
		);

		$this->add_responsive_control(
			'condition_label_radius',
			[
				'label'      => esc_html__( 'Radius', 'jvfrmtd' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%',],
				'default'	   => [
					'top' => 10,
					'right' => 10,
					'bottom' => 10,
					'left' => 10,
				],
				'selectors'  => [
					'{{WRAPPER}} #javo-item-workinghours-section .today-timing .days' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'condition_label_padding',
			[
				'label'      => esc_html__( 'Padding', 'jvfrmtd' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default'	   => [
					'top' => 3,
					'right' => 10,
					'bottom' => 3,
					'left' => 10,
				],
				'selectors'  => [
					'{{WRAPPER}} #javo-item-workinghours-section .today-timing .days.open-now' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} #javo-item-workinghours-section .today-timing .days.closed' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
    }

    protected function render() {
		jvbpd_elements_tools()->switch_preview_post();

		$settings = $this->get_settings();
		$isVisible = false;

		//wp_reset_postdata();
		$isPreviewMode = false;

		if( $isPreviewMode ) {
			$previewBaseURL = jvbpdCore()->assets_url . '/images/elementor/listipia/';
			$previewURL = $previewBaseURL . 'single-working-hours.jpg';
			printf( '<img src="%s">', esc_url_raw( $previewURL ) );
		}else{
			$this->getContent( $settings, get_post() );
		}
		jvbpd_elements_tools()->restore_preview_post();
    }

	public function getContent( $settings, $obj ) {
		?>
		<div class="" id="javo-item-workinghours-section" data-jv-detail-nav="">
				<h3 class="page-header"><?php esc_html_e( "Working Hours", 'listopia' ); ?></h3>
				<div class="panel panel-default">
					<div class="panel-body">
						<?php echo do_shortcode( sprintf('[lava_working_hours format="%1$s"]', $this->get_settings('time_format') ) ); ?>
					</div><!--/.panel-body -->
				</div><!--/.panel panel-default -->
			</div>
		<?php
	}
}
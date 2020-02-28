<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Scheme_Typography;

if ( ! defined( 'ABSPATH' ) ) exit;

class Jvbpd_Single_Post_Comment_List extends Widget_Base {

	public function get_name() { return 'jvbpd-single-post-comment-list'; }
	public function get_title() { return 'Single Post Comment List'; }
	public function get_icon() { return 'jvic-bubble-comment-streamline-talk'; }
	public function get_categories() { return [ 'jvbpd-single-post' ]; }

	protected function _register_controls() {
		$this->start_controls_section( 'section_general', array(
			'label' => esc_html__( 'General', 'jvfrmtd' ),
		) );

		$this->end_controls_section();



		$this->start_controls_section(
			'comments_counts',
			[
				'label' => __( 'Comments Counts Style','jvfrmtd'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(), [
				'name' => 'comments_counts_title',
				'label' => __('Typography','jvfrmtd'),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} #jv-single-comments-title .section-title',
			]
		);

		$this->add_control(
		'comments_counts_title_color',
			[
				'label' => __( 'Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} #jv-single-comments-title .section-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
		'comments_counts_title_bg_color',
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} #jv-single-comments-title' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'comments_counts_title_border',
				'selector' => '{{WRAPPER}} #jv-single-comments-title',
			]
		);

		$this->add_responsive_control(
			'comments_counts_title_radius',
			[
				'label'      => esc_html__( 'Radius', 'jvfrmtd' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%',],
				'selectors'  => [
					'{{WRAPPER}} #jv-single-comments-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'comments_counts_title_padding',
			[
				'label'      => esc_html__( 'Padding', 'jvfrmtd' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} #jv-single-comments-title .section-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'comments_counts_title_margin',
			[
				'label'      => esc_html__( 'Margin', 'jvfrmtd' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} #jv-single-comments-title .section-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();



		$this->start_controls_section(
			'comments_style',
			[
				'label' => __( 'Comments Style','jvfrmtd'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(), [
				'name' => 'comments_box_author',
				'label' => __('Author Typography','jvfrmtd'),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} #comments .author-meta b',
			]
		);

		$this->add_control(
		'comments_box_author_color',
			[
				'label' => __( 'Author text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} #comments .author-meta b' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(), [
				'name' => 'comments_box_content',
				'label' => __('Content Typography','jvfrmtd'),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} #comments .comment-content p',
			]
		);

		$this->add_control(
		'comments_box_content_color',
			[
				'label' => __( 'Content Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} #comments .comment-content p' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
		'comments_box_bg',
			[
				'label' => __( 'Comment Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .jv-single-post-comments .comment.media' => 'background: {{VALUE}}',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'comments_box_border',
				'label' => __( 'Comment Box border', 'jvfrmtd' ),
				'selector' => '{{WRAPPER}} .jv-single-post-comments .comment.media',
			]
		);

		$this->add_responsive_control(
			'omments_box_radius',
			[
				'label'      => esc_html__( 'Comment Box Radius', 'jvfrmtd' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%',],
				'selectors'  => [
					'{{WRAPPER}} .jv-single-post-comments .comment.media' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'comments_box_padding',
			[
				'label'      => esc_html__( 'Padding', 'jvfrmtd' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .jv-single-post-comments .comment.media' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'comments_box_margin',
			[
				'label'      => esc_html__( 'Margin', 'jvfrmtd' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .jv-single-post-comments .comment.media' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

	}


	protected function render() {
		jvbpd_elements_tools()->switch_preview_post();

		$this->add_render_attribute( 'wrap', Array(
			'class' => 'jvbpd-single-post-comments',
		) ); ?>

		<div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
			<?php comments_template( '', true ); ?>
		</div>
		<?php
		jvbpd_elements_tools()->restore_preview_post();
	}

}
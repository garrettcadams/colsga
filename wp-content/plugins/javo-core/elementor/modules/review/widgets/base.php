<?php
namespace jvbpdelement\Modules\Review\Widgets;

use jvbpdelement\Base\Base_Widget;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

abstract class Base extends Base_Widget {

	Const REVIEW_FORM = 'jvbpd-review-form';
	Const REVIEW_SCORE = 'jvbpd-review-score';
	Const REVIEW_NOTICE = 'jvbpd-review-notice';
	Const REVIEW_PROGRESS = 'jvbpd-review-progress';

	protected $review_instance = null;

	public function get_categories() { return [ 'jvbpd-single-listing' ]; }

	protected function singleRender() {
		$this->review_instance = function_exists( 'lv_directoryReview' ) ? lv_directoryReview() : null;
		$this->manager_instance = function_exists( 'lava_directory' ) ? lava_directory() : null;

		$type = $this->get_settings( 'type' );
		$this->add_render_attribute( 'wrap', Array(
			'class' => Array( 'jvbpd-single-review', 'review-item-' . $type ),
		) );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
			<?php
			if( method_exists( $this, $type ) ) {
				call_user_func( Array( $this, $type ) );
			} ?>
		</div>
		<?php
	}

	public function getReviewedCount( int $post_id=0 ) {
		return $this->manager_instance->admin->reviewCount( $post_id );
	}

	public function getReviewMeta( string $key='' ) {
		if( !$this->review_instance ) {
			return;
		}
		return $this->review_instance->core->get( $key );
	}

	public function getReviewFontStars() {
		if( !$this->review_instance ) {
			return;
		}
		return $this->review_instance->core->fa_get();
	}

	public function getReviewProgress() {
		if( !$this->review_instance ) {
			return;
		}
		$this->review_instance->core->part_progress();
	}

	public function getOption( string $key='', string $default='' ) {
		if( !$this->review_instance ) {
			return $default;
		}
		return $this->review_instance->core->get_option( $key, $default );
	}

	public function getReviewLists() {
		if( !$this->review_instance ) {
			return;
		}
		$this->review_instance->core->getReviewLists( Array(
			'type' => $this->get_settings( 'review_list_type', 'list' ),
			'number' => $this->get_settings( 'review_list_num', '3' ),
			'columns' => Array(
				'desktop' => $this->get_settings( 'review_list_col', '3' ),
				'tablet' => $this->get_settings( 'review_list_col_tablet' ),
				'mobile' => $this->get_settings( 'review_list_col_mobile' ),
			),
			'filter' => 'yes' == $this->get_settings( 'review_list_use_filter', 'no' ),
		) );
	}

	public function getReviewForm( array $args=Array() ) {
		if( !$this->review_instance ) {
			return;
		}
		$this->review_instance->core->getWriteForm( wp_parse_args( $args, Array(
			'lists'=>false,
			'raty_type' => $this->get_settings( 'form_type' ),
			'show_form' => 'yes' != $this->get_settings( 'form_collapse' ),
		) ) );
	}

	public function add_notice_controls( string $type='', string $label='' ) {
		$this->start_controls_section(
			'section_notice_typo_style', [
				'label' => __( 'Style', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => Array( 'type' => $type ),
			]
		);

		$this->add_control(
			'notice_heading_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Title', 'jvfrmtd' ),
				'separator' => 'before',
				'condition' => Array( 'type' => $type ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'notice_title_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'condition' => Array( 'type' => $type ),
				'selector' => '{{WRAPPER}} .review-notice-header',

			]
		);

		$this->add_control(
			'notice_title_color',
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'condition' => Array( 'type' => $type ),
				'selectors' => [
					'{{WRAPPER}} .review-notice-header' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'heading_msg',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Message', 'jvfrmtd' ),
				'condition' => Array( 'type' => $type ),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'notice_msg_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'condition' => Array( 'type' => $type ),
				'selector' => '{{WRAPPER}} .review-notice-body',
			]
		);

		$this->add_control(
			'notice_msg_color',
			[
				'label' => __( 'Message Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'condition' => Array( 'type' => $type ),
				'selectors' => [
					'{{WRAPPER}} .review-notice-body' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	public function add_status_controls( string $type='', string $label='' ) {

		$this->start_injection( Array(
			'of' => 'section_general',
			'at' => 'end',
			'type' => 'section'
		) );

		$this->add_control( 'status_collapse', array(
			'type' => Controls_Manager::SWITCHER,
			'label' => __( 'Collapse Status', 'jvfrmtd' ),
			'condition' => Array( 'type' => $type ),
		) );

		$this->end_injection();

		$this->start_controls_section(
			'section_tatus_typo_style',
			[
				'label' => __( 'Style', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => Array( 'type' => $type ),
			]
		);

		$this->add_control(
			'status_heading_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Titles', 'jvfrmtd' ),
				'condition' => Array( 'type' => $type ),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'review_title_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'condition' => Array( 'type' => $type ),
				'selector' => '{{WRAPPER}} .progress-title',
			]
		);

		$this->add_control(
			'review_title_color',
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'condition' => Array( 'type' => $type ),
				'selectors' => [
					'{{WRAPPER}} .progress-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'heading_bar',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Bar', 'jvfrmtd' ),
				'condition' => Array( 'type' => $type ),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'percent_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'condition' => Array( 'type' => $type ),
				'selector' => '{{WRAPPER}} .progress-bar',
			]
		);

		$this->add_control(
			'bar_bg_color',
			[
				'label' => __( 'Bar Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'condition' => Array( 'type' => $type ),
				'selectors' => [
					'{{WRAPPER}} .progress' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'bar_process_color',
			[
				'label' => __( 'Process Bar Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'condition' => Array( 'type' => $type ),
				'selectors' => [
					'{{WRAPPER}} .progress .progress-bar' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'bar_height',
			[
				'label' => __( 'Bar Height', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'condition' => Array( 'type' => $type ),
				'selectors' => [
					'{{WRAPPER}} .progress' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .progress-bar' => 'line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	public function add_average_controls( string $type='', string $label='' ) {
		$this->start_controls_section(
			'section_typo_style',
			[
				'label' => __( 'Style', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => Array( 'type' => $type ),
			]
		);

		$this->add_control(
			'heading_layout' . $type,
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Layout', 'jvfrmtd' ),
				'condition' => Array( 'type' => $type ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'review_bg_color_' . $type,
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'condition' => Array( 'type' => $type ),
				'selectors' => [
					'{{WRAPPER}} .jvbpd-single-review.review-item-score' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'layout',
			[
				'label' => __( 'Layout', 'jvfrmtd' ),
				'type' => Controls_Manager::CHOOSE,
				'condition' => Array( 'type' => $type ),
				'label_block' => false,
				'default' => 'above',
				'options' => [
					'left' => [
						'title' => __( 'Left', 'jvfrmtd' ),
						'icon'  => 'eicon-h-align-left',
					],
					'above' => [
						'title' => __( 'Above', 'jvfrmtd' ),
						'icon'  => 'eicon-v-align-top',
					],
					'right' => [
						'title' => __( 'Right', 'jvfrmtd' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'prefix_class' => 'module-card-%s-layout-image-',
			]
		);

		$this->add_control(
			'alignment_' . $type,
			[
				'label' => __( 'Alignment', 'jvfrmtd' ),
				'type' => Controls_Manager::CHOOSE,
				'condition' => Array( 'type' => $type ),
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'jvfrmtd' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'jvfrmtd' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'jvfrmtd' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .review-avg-score, {{WRAPPER}} .review-avg-stars, {{WRAPPER}} .review-amount' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'padding_' . $type,
			[
				'label' => __( 'Padding', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'condition' => Array( 'type' => $type ),
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .review-avg-score-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'heading_average_' . $type,
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Average', 'jvfrmtd' ),
				'condition' => Array( 'type' => $type ),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'review_average_typography_' . $type,
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'condition' => Array( 'type' => $type ),
				'selector' => '{{WRAPPER}} .review-avg-score',
			]
		);

		$this->add_control(
			'review_average_color_' . $type,
			[
				'label' => __( 'Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'condition' => Array( 'type' => $type ),
				'selectors' => [
					'{{WRAPPER}} .review-avg-score' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'heading_icons_' . $type,
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Icons', 'jvfrmtd' ),
				'condition' => Array( 'type' => $type ),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'icon_typography_' . $type,
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'condition' => Array( 'type' => $type ),
				'selector' => '{{WRAPPER}} .review-avg-stars',
			]
		);

		$this->add_control(
			'icon_color_' . $type,
			[
				'label' => __( 'Icon Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'condition' => Array( 'type' => $type ),
				'selectors' => [
					'{{WRAPPER}} .review-avg-stars' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icon_filled_color_' . $type,
			[
				'label' => __( 'Icon Filled Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'condition' => Array( 'type' => $type ),
				'selectors' => [
					'{{WRAPPER}} .progress.progress-bar-blug' => 'background-color: {{VALUE}}',
				],
			]
		);


		$this->add_control(
			'heading_amount_' . $type,
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Amount', 'jvfrmtd' ),
				'condition' => Array( 'type' => $type ),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'amount_typography_' . $type,
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'condition' => Array( 'type' => $type ),
				'selector' => '{{WRAPPER}} .review-amount',
			]
		);

		$this->add_control(
			'amount_color' . $type,
			[
				'label' => __( 'Amount Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'condition' => Array( 'type' => $type ),
				'selectors' => [
					'{{WRAPPER}} .review-amount' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	public function add_writeForm_controls( string $type='', string $label='' ) {

		$this->start_injection( Array(
			'of' => 'section_general',
			'at' => 'end',
			'type' => 'section'
		) );

		$this->add_control( 'form_collapse', array(
			'type' => Controls_Manager::SWITCHER,
			'label' => __( 'Collapse Form', 'jvfrmtd' ),
			'condition' => Array( 'type' => $type ),
		) );

		$this->add_control( 'form_type', array(
			'type' => Controls_Manager::SELECT,
			'label' => esc_html__( 'Form Type', 'jvfrmtd' ),
			'default' => 'star',
			'options' => Array(
				'star' => esc_html__( 'Star', 'jvfrmtd' ),
				'slider' => esc_html__( 'Slider', 'jvfrmtd' ),
			),
			'condition' => Array( 'type' => $type ),
		) );

		$this->end_injection();

		$this->start_controls_section(
			'section_form_style',
			[
				'label' => __( 'Form', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => Array( 'type' => $type ),
			]
		);

		$this->add_control(
			'heading_layout_' . $type,
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Layout', 'jvfrmtd' ),
				'condition' => Array( 'type' => $type ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'review_bg_color_' . $type,
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'condition' => Array( 'type' => $type ),
				'selectors' => [
					'{{WRAPPER}} .jvbpd-review-score-box' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'layout_' . $type,
			[
				'label' => __( 'Layout', 'jvfrmtd' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'condition' => Array( 'type' => $type ),
				'default' => 'above',
				'options' => [
					'left' => [
						'title' => __( 'Left', 'jvfrmtd' ),
						'icon'  => 'eicon-h-align-left',
					],
					'above' => [
						'title' => __( 'Above', 'jvfrmtd' ),
						'icon'  => 'eicon-v-align-top',
					],
					'right' => [
						'title' => __( 'Right', 'jvfrmtd' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'prefix_class' => 'module-card-%s-layout-image-',
			]
		);

		$this->add_control(
			'alignment_' . $type,
			[
				'label' => __( 'Alignment', 'jvfrmtd' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'condition' => Array( 'type' => $type ),
				'options' => [
					'left' => [
						'title' => __( 'Left', 'jvfrmtd' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'jvfrmtd' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'jvfrmtd' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .review-avg-score, {{WRAPPER}} .review-avg-stars, {{WRAPPER}} .review-amount' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'padding_' . $type,
			[
				'label' => __( 'Padding', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'condition' => Array( 'type' => $type ),
				'selectors' => [
					'{{WRAPPER}} .review-avg-score-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'heading_average_' . $type,
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Average', 'jvfrmtd' ),
				'condition' => Array( 'type' => $type ),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'review_average_typography_' . $type,
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'condition' => Array( 'type' => $type ),
				'selector' => '{{WRAPPER}} .review-avg-score',
			]
		);

		$this->add_control(
			'review_average_color_' . $type,
			[
				'label' => __( 'Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'condition' => Array( 'type' => $type ),
				'selectors' => [
					'{{WRAPPER}} .review-avg-score' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'heading_icons_' . $type,
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Icons', 'jvfrmtd' ),
				'condition' => Array( 'type' => $type ),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'icon_typography_' . $type,
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'condition' => Array( 'type' => $type ),
				'selector' => '{{WRAPPER}} .review-avg-stars',
			]
		);

		$this->add_control(
			'icon_color_' . $type,
			[
				'label' => __( 'Icon Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'condition' => Array( 'type' => $type ),
				'selectors' => [
					'{{WRAPPER}} .review-avg-stars' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icon_filled_color_' . $type,
			[
				'label' => __( 'Icon Filled Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'condition' => Array( 'type' => $type ),
				'selectors' => [
					'{{WRAPPER}} .progress.progress-bar-blug' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'heading_amount_' . $type,
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Amount', 'jvfrmtd' ),
				'condition' => Array( 'type' => $type ),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'amount_typography_' . $type,
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'condition' => Array( 'type' => $type ),
				'selector' => '{{WRAPPER}} .review-amount',
			]
		);

		$this->add_control(
			'amount_color' . $type,
			[
				'label' => __( 'Amount Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'condition' => Array( 'type' => $type ),
				'selectors' => [
					'{{WRAPPER}} .review-amount' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control( 'heading_slider_' . $type, Array(
			'type' => Controls_Manager::HEADING,
			'label' => __( 'Slider', 'jvfrmtd' ),
			'condition' => Array( 'type' => $type ),
			'separator' => 'before',
		) );

		$this->add_control( 'slider_base_color' . $type, Array(
			'label' => __( 'Slider Base Color', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'default' => '#eeeeee',
			'condition' => Array( 'type' => $type ),
			'selectors' => Array(
				'{{WRAPPER}} #javo-review-form-container .jvbpd-rat-slider-wrap .noUi-background' => 'background-color: {{VALUE}}',
			),
		));

		$this->add_control( 'slider_handle_color' . $type, Array(
			'label' => __( 'Slider Handle Color', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'default' => '#ffffff',
			'condition' => Array( 'type' => $type ),
			'selectors' => Array(
				'{{WRAPPER}} #javo-review-form-container  .jvbpd-rat-slider-wrap .noUi-base .noUi-handle-lower' => 'background-color: {{VALUE}}',
			),
		));

		$this->end_controls_section();
	}

	public function add_reviewList_controls( string $type='', string $label='' ) {
		$this->start_injection( Array(
			'of' => 'section_general',
			'at' => 'end',
			'type' => 'section'
		) );

		$this->add_control( 'review_list_collapse', array(
			'type' => Controls_Manager::SWITCHER,
			'label' => __( 'Collapse Review List', 'jvfrmtd' ),
			'condition' => Array( 'type' => $type ),
		) );

		$this->add_control( 'review_list_use_filter', array(
			'type' => Controls_Manager::SWITCHER,
			'label' => __( 'Use Review List Filter', 'jvfrmtd' ),
			'condition' => Array( 'type' => $type ),
		) );

		$this->add_control( 'review_list_type', array(
			'type' => Controls_Manager::SELECT,
			'label' => esc_html__( 'Review List Type', 'jvfrmtd' ),
			'condition' => Array( 'type' => $type ),
			'default' => 'list',
			'options' => Array(
				'list' => esc_html__( 'List Type', 'jvfrmtd' ),
				'grid' => esc_html__( 'Grid Type', 'jvfrmtd' ),
			),
		) );

		$this->add_control( 'review_list_num', array(
			'type' => Controls_Manager::NUMBER,
			'label' => esc_html__( 'Review List Load Count', 'jvfrmtd' ),
			'condition' => Array( 'type' => $type ),
			'default' => '3',
		) );

		$this->add_responsive_control( 'review_list_col', array(
			'type' => Controls_Manager::SELECT,
			'label' => esc_html__( 'Review List Columns', 'jvfrmtd' ),
			'condition' => Array( 'type' => $type ),
			'default' => '3',
			'options' => jvbpd_elements_tools()->get_select_range(4),
		) );

		$this->end_injection();
	}
}
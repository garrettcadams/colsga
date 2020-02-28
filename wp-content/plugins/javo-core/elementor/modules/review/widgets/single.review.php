<?php
namespace jvbpdelement\Modules\Review\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Box_Shadow;

class Single_Review extends Base {

	public function get_name() { return 'jvbpd-single-review'; }
	public function get_title() { return 'Single Review Form'; }
	public function get_icon() { return 'jvic-star-4'; }
	protected function render() {
		jvbpd_elements_tools()->switch_preview_post();
		if( class_exists( '\Lava\Role\Manager') ) {
			$post = get_post();
			$author = $post->post_author;
			$author_role = \Lava\Role\Manager::$instance->admin->getUserMember( $author );
			$role = \Lava\Role\Manager::$instance->admin->getRole( $author_role );
			if( $post->post_type == 'lv_listing' && $role && !$role->respond_reviews ) {
				return;
			}
        }

		$this->singleRender();
		jvbpd_elements_tools()->restore_preview_post();
	}

	public function types() {
		return Array(
			'notice' => esc_html__( "Review Notice", 'jvfrmtd' ),
			'status' => esc_html__( "Review Ave Status", 'jvfrmtd' ),
			'writeForm' => esc_html__( "Submit Form", 'jvfrmtd' ),
			'average' => esc_html__( "Total Average", 'jvfrmtd' ),
			'reviewList' => esc_html__( "Reviews List", 'jvfrmtd' ),
		);
	}

	protected function _register_controls() {
		$this->start_controls_section( 'section_general', Array(
			'label' => esc_html__( "General", 'jvfrmtd' ),
		) );

		$this->add_control( 'type', Array(
			'label' => esc_html__( "Type", 'jvfrmtd' ),
			'label_block' => true,
			'type' => Controls_Manager::SELECT,
			'default' => 'notice',
			'options' => $this->types(),
		) );

		$this->end_controls_section();

		foreach( $this->types() as $type => $typeLabel ) {
			$ctlType = sprintf( 'add_%s_controls', $type );
			if( method_exists( $this, $ctlType ) ) {
				call_user_func_array( Array( $this, $ctlType ), Array( $type, $typeLabel ) );
			}
		}


		$this->start_controls_section( 'section_list', Array(
			'label' => esc_html__( "List Options", 'jvfrmtd' ),
			'condition' => [
				'type' => 'reviewList',
			],
		) );

		$this->start_controls_tabs( 'loadmore_button_tabs' );
		$this->start_controls_tab( 'loadmore_button_normal',
			[
				'label' => __( 'Normal', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'loadmore_text_color',
			[
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#484848',
				'selectors' => [
					'{{WRAPPER}} .lv-review-loadmore button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'loadmore_background_color',
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .lv-review-loadmore button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'loadmore_border_color',
			[
				'label' => __( 'Border Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#dbdbdb',
				'selectors' => [
					'{{WRAPPER}} .lv-review-loadmore button' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'loadmore_typography',
				'selector' => '{{WRAPPER}}  .lv-review-loadmore button',
				'scheme' => Scheme_Typography::TYPOGRAPHY_4,
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'loadmore_shadow',
				'selector' => '{{WRAPPER}}  .lv-review-loadmore button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'loadmore_hover',
			[
				'label' => __( 'Hover', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'loadmore_hover_text_color',
			[
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .lv-review-loadmore button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'loadmore_hover_background_color',
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#484848',
				'selectors' => [
					'{{WRAPPER}} .lv-review-loadmore button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'loadmore_hover_border_color',
			[
				'label' => __( 'Border Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#484848',
				'selectors' => [
					'{{WRAPPER}} .lv-review-loadmore button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		// List options



		$this->start_controls_section( 'section_submit_form', Array(
			'label' => esc_html__( "Submit Form", 'jvfrmtd' ),
			'condition' => [
				'type' => 'writeForm',
			],
		) );

		$this->start_controls_tabs( 'submit_button_tabs' );
		$this->start_controls_tab( 'submit_button_normal',
			[
				'label' => __( 'Normal', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#484848',
				'selectors' => [
					'{{WRAPPER}} .jv-rating-form-wrap button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_color',
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .jv-rating-form-wrap button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_border_color',
			[
				'label' => __( 'Border Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#dbdbdb',
				'selectors' => [
					'{{WRAPPER}} .jv-rating-form-wrap button' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}}  .jv-rating-form-wrap button',
				'scheme' => Scheme_Typography::TYPOGRAPHY_4,
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_shadow',
				'selector' => '{{WRAPPER}}  .jv-rating-form-wrap button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'submit_button_hover',
			[
				'label' => __( 'Hover', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'button_hover_text_color',
			[
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .jv-rating-form-wrap button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_background_color',
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#484848',
				'selectors' => [
					'{{WRAPPER}} .jv-rating-form-wrap button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label' => __( 'Border Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#484848',
				'selectors' => [
					'{{WRAPPER}} .jv-rating-form-wrap button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		// Submit form options


		$this->start_controls_section( 'section_submit_status', Array(
			'label' => esc_html__( "Review Average", 'jvfrmtd' ),
			'condition' => [
				'type' => 'status',
			],
		) );

		$this->add_control(
			'bar_skin',
			[
				'label' => __( 'Skin', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style1',
				'options' => [
					'skin1' => __( 'Skin1', 'jvfrmtd' ),
					'skin2' => __( 'Skin2', 'jvfrmtd' ),
				],
				'condition' => [
					'type' => 'status',
				],
				'selectors' => [
					'{{WRAPPER}} .review-rated-percent' => 'display: inline-block; width: 80%;',
				],
			]
		);

		$this->add_responsive_control(
			'bar_title_width',
			[
				'label' => __( 'Width', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
					'unit' => '%',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ '%', 'px' ],
				'selectors' => [
					'{{WRAPPER}} .review-items' => 'display:inline-block; width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'bar_process_width',
			[
				'label' => __( 'Bar Process Width', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 80,
					'unit' => '%',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ '%', 'px' ],
				'selectors' => [
					'{{WRAPPER}} .review-rated-percent' => 'display:inline-block; width: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_responsive_control(
			'status_padding',
			[
				'label' => __( 'Padding', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '10',
					'right' => '8',
					'bottom' => '10',
					'left' => '8',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .review-avg-bar-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
		// Submit form options

	}




	public function notice() {
		?>
		<h3 class="review-notice-header"><?php echo $this->getOption( 'rating_alert_header' ); ?></h3>
		<div class="review-notice-body"><?php echo $this->getOption( 'rating_alert_content' ); ?></div>
		<?php
	}

	public function status() {
		$this->add_render_attribute( 'status_opener_wrap', Array(
			'class' => Array( 'text-center', 'status-opener-wrap' ),
		) );

		$this->add_render_attribute( 'status_opener', Array(
			'class' => Array( 'btn', 'status-opener' ),
			'data-toggle' => 'collapse',
			'data-target' => '#status-' . $this->get_id(),
		) );

		$this->add_render_attribute( 'status_content', Array(
			'id' => 'status-' . $this->get_id(),
			'class' => 'status-content',
		) );

		if( 'yes' === $this->get_settings( 'status_collapse' ) ) {
			$this->add_render_attribute( 'status_content', Array(
				'class' => Array( 'collapse' ),
			) );
		}else{
			$this->add_render_attribute( 'status_opener_wrap', Array(
				'class' => Array( 'hidden' ),
			) );
		} ?>
		<div <?php echo $this->get_render_attribute_string( 'status_content' ); ?>>
			<?php $this->getReviewProgress(); ?>
		</div>
		<div <?php echo $this->get_render_attribute_string( 'status_opener_wrap' ); ?>>
			<button <?php echo $this->get_render_attribute_string( 'status_opener' ); ?>>
				<?php esc_html_e( "View Status", 'jvfrmtd' ); ?>
			</button>
		</div>
		<?php
	}

	public function average() {
		$counts = $this->getReviewedCount( get_the_ID() );
		?>
		<div class="review-avg-score-box">
			<div class="review-avg-score"><?php echo $this->getReviewMeta( 'average' ); ?></div>
			<div class="review-avg-stars"><?php echo $this->getReviewFontStars();?></div>
			<div class="review-amount">
				<?php echo $counts.' '. _n('Review', 'Reviews', $counts,'lvdr-review');  ?>
			</div>
		</div>
		<?php
	}

	public function writeForm() {
		$this->add_render_attribute( 'form', Array(
			'id' => 'javo-review-form-container',
			'class' => 'jvbpd-review-form',
			'style' => 'display:block!important;',
		) ); ?>
		<div <?php echo $this->get_render_attribute_string( 'form' ); ?>>
			<?php $this->getReviewForm(); ?>
		</div>
		<?php
	}

	public function reviewList() {
		$this->add_render_attribute( 'review_list_opener_wrap', Array(
			'class' => Array( 'text-center', 'review-list-opener-wrap' ),
		) );

		$this->add_render_attribute( 'review_list_opener', Array(
			'class' => Array( 'btn', 'review-list-opener' ),
			'data-toggle' => 'collapse',
			'data-target' => '#review-list-' . $this->get_id(),
		) );

		$this->add_render_attribute( 'review_list_content', Array(
			'id' => 'review-list-' . $this->get_id(),
			'class' => 'review-list-content',
		) );

		if( 'yes' === $this->get_settings( 'review_list_collapse' ) ) {
			$this->add_render_attribute( 'review_list_content', Array(
				'class' => Array( 'collapse' ),
			) );
		}else{
			$this->add_render_attribute( 'review_list_opener_wrap', Array(
				'class' => Array( 'hidden' ),
			) );
		} ?>
		<div <?php echo $this->get_render_attribute_string( 'review_list_content' ); ?>>
			<?php $this->getReviewLists(); ?>
		</div>
		<div <?php echo $this->get_render_attribute_string( 'review_list_opener_wrap' ); ?>>
			<button <?php echo $this->get_render_attribute_string( 'review_list_opener' ); ?>>
				<?php esc_html_e( "View list", 'jvfrmtd' ); ?>
			</button>
		</div>
		<?php
	}

}
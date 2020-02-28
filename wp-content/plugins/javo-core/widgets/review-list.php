<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Scheme_Typography;

if ( ! defined( 'ABSPATH' ) ) exit;

class Jvbpd_ReviewList extends Widget_Base {

    public $_render_type = '';
	public function get_name() { return 'jvbpd-review-list'; }
	public function get_title() { return 'Review List'; }
	public function get_icon() { return 'eicon-button'; }
	public function get_categories() { return [ 'jvbpd-elements' ]; }

	protected function _register_controls() {
		$this->start_controls_section( 'section_general', array(
			'label' => esc_html__( 'General', 'jvfrmtd' ),
		) );
			$this->add_control( 'content_count', Array(
				'type' => Controls_Manager::NUMBER,
				'label' => esc_html__( 'Items Count', 'jvfrmtd' ),
				'default' => '10',
			));
			$this->add_control( 'content_length', Array(
				'type' => Controls_Manager::NUMBER,
				'label' => esc_html__( 'Content Length', 'jvfrmtd' ),
				'default' => '',
				'separator' => 'before',
			));
		$this->end_controls_section();

		$this->start_controls_section(
			'reviews_style',
			[
				'label' => __( 'General Style', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'reviews_bg_color',
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .review-item' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'reviews_align',
			[
				'label' => __( 'Alignment', 'jvfrmtd' ),
				'type' => Controls_Manager::CHOOSE,
				'default' =>'center',
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
				'selectors' => [
					'{{WRAPPER}} .jvbpd-swiper-carousel' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'reivews_margin',
			[
				'label' => __( 'Margin', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .review-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'reviews_padding',
			[
				'label' => __( 'Padding', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .review-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'reviews_border',
				'label' => __('Border','jvfrmtd'),
				'selector' => '{{WRAPPER}} .review-item',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'reviews_shadow',
				'selector' => '{{WRAPPER}} .review-item',
			]
		);

		$this->add_control( 'reviews_radius', [
			'label' => __( 'Border Radius', 'jvfrmtd' ),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors' => [
					'{{WRAPPER}} .review-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_section();

		$this->start_controls_section(
			'review_listing_title_style',
			[
				'label' => __( 'Listing Title Style', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'review_listing_title_typo',
			'selector' => '{{WRAPPER}} .review-listing-title > a',
			'scheme' => Scheme_Typography::TYPOGRAPHY_1,
			]
		);

		$this->add_control(
			'review_listing_title_color',
			[
				'label' => __( 'Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .review-listing-title > a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'review_listing_title_Hover_color',
			[
				'label' => __( 'Hover Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .review-listing-title > a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'review_listing_title_margin',
			[
				'label' => __( 'Margin', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .review-listing-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'review_listing_title_padding',
			[
				'label' => __( 'Padding', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .review-listing-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

        $this->end_controls_section();

		$this->start_controls_section(
			'review_description_style',
			[
				'label' => __( 'Description Style', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'review_description_typo',
			'selector' => '{{WRAPPER}} .review-content .jv-review-link',
			'scheme' => Scheme_Typography::TYPOGRAPHY_1,
			]
		);

		$this->add_control(
			'review_description_color',
			[
				'label' => __( 'Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .review-content .jv-review-link' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'review_description_margin',
			[
				'label' => __( 'Each Content Margin', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .review-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'review_description_padding',
			[
				'label' => __( 'Each Content Padding', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .review-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

        $this->end_controls_section();

		$this->start_controls_section(
			'review_rating_style',
			[
				'label' => __( 'Rating Style', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'reviews_rating_display_style',
			[
				'label' => __( 'Rating Display Type', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'frontend_available' => true,
				'options' => [
					'inline-block' => __( 'Inline', 'jvfrmtd' ),
					'block' => __( 'Block', 'jvfrmtd' ),
				],
				'selectors' => [
					'{{wrapper}} .reivew-score-icon' => 'display:{{VALUE}};',
					'{{wrapper}} .review-author .review-author-name' => 'display:{{VALUE}};',
				],
			]
		);

		$this->add_control(
			'review_rating_icon_color',
			[
				'label' => __( 'Rating Icon Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .reivew-score-icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'review_rating_icon_size',
			[
				'label' => __( 'Icon Size', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 15,
                    'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .reivew-score-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'review_rating_avg_typo',
			'selector' => '{{WRAPPER}} .review-score',
			'scheme' => Scheme_Typography::TYPOGRAPHY_1,
			]
		);

		$this->add_control(
			'review_rating_avg_color',
			[
				'label' => __( 'Rating Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .review-score' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'review_rating_margin',
			[
				'label' => __( 'Rating Margin', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .review-score' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'review_rating_padding',
			[
				'label' => __( 'Rating Padding', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .review-score' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

        $this->end_controls_section();

		$this->start_controls_section(
			'review_author_style',
			[
				'label' => __( 'Author Style', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'reviews_author_display_style',
			[
				'label' => __( 'Author Display Type', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'frontend_available' => true,
				'options' => [
					'inline-block' => __( 'Inline', 'jvfrmtd' ),
					'block' => __( 'Block', 'jvfrmtd' ),
				],
				'selectors' => [
					'{{wrapper}} .review-author .review-author-avatar' => 'display:{{VALUE}};',
					'{{wrapper}} .review-author .review-author-name' => 'display:{{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'review_author_margin',
			[
				'label' => __( 'Author Margin', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .review-author' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'review_author_padding',
			[
				'label' => __( 'Author Padding', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .review-author' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'author_name_typo',
			'selector' => '{{WRAPPER}} .review-author .review-author-name',
			'scheme' => Scheme_Typography::TYPOGRAPHY_1,
			]
		);

		$this->add_control(
			'review_author_name_color',
			[
				'label' => __( 'Author Name Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .review-author .review-author-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'reviews_author_alignment',
			[
				'label' => __( 'Author Name Alignment', 'jvfrmtd' ),
				'type' => Controls_Manager::CHOOSE,
				'default' =>'middle',
				'options' => [
					'top' => [
						'title' => __( 'Top', 'jvfrmtd' ),
						'icon' => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __( 'Middle', 'jvfrmtd' ),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'jvfrmtd' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .review-author .review-author-name' => 'vertical-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'review_author_avatar_size',
			[
				'label' => __( "Avatar Size", 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .review-author .review-author-avatar img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'review_author_avatar_margin',
			[
				'label' => __( 'Avatar Margin', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .review-author .review-author-avatar img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control( 'review_author_avatar_radius', [
			'label' => __( 'Avatar Border Radius', 'jvfrmtd' ),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors' => [
					'{{WRAPPER}} .review-author .review-author-avatar img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_section();

		$this->start_controls_section(
			'review_slide_style',
			[
				'label' => __( 'Slide Style', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_responsive_control( 'review_slide_icon',Array(
			'label' => esc_html__( 'Icon Size', 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'default' => [
				'size' => 20,
			],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 100,
				],
			'%' => [
					'min' => 0,
					'max' => 100,
				],
			],
			'size_units' => [ 'px', '%' ],
			'selectors' => [
				'{{WRAPPER}} .jvbpd-nav-button-prev > i, {{WRAPPER}} .jvbpd-nav-button-next > i' => 'font-size: {{SIZE}}{{UNIT}};',
			],
		) );

		$this->add_control(
			'review_slide_icon_color',
			[
				'label' => __( 'Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .jvbpd-nav-button-prev > i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .jvbpd-nav-button-next > i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control( 'review_slide_icon_left_space',Array(
			'label' => esc_html__( 'Left Arrow Button Space', 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'default' => [
				'size' => 5,
			],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 100,
				],
			'%' => [
					'min' => 0,
					'max' => 100,
				],
			],
			'size_units' => [ 'px', '%' ],
			'selectors' => [
				'{{WRAPPER}} .jvbpd-nav-button-prev > i' => 'left: {{SIZE}}{{UNIT}};',
			],
		) );

		$this->add_responsive_control( 'review_slide_icon_right_space',Array(
			'label' => esc_html__( 'Right Arrow Button Space', 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'default' => [
				'size' => 10,
			],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 100,
				],
			'%' => [
					'min' => 0,
					'max' => 100,
				],
			],
			'size_units' => [ 'px', '%' ],
			'selectors' => [
				'{{WRAPPER}} .jvbpd-nav-button-next > i' => 'right: {{SIZE}}{{UNIT}};',
			],
		) );

        $this->end_controls_section();

		$this->start_controls_section( 'section_slider', array(
			'label' => esc_html__( 'Slider Options', 'jvfrmtd' ),
		) );

		$this->add_control( 'effect', Array(
			'type' => Controls_Manager::SELECT,
			'label' => __( 'Effect', 'jvfrmtd' ),
			'default' => 'slide',
			'options' => Array(
				'slide' => __( 'Slide', 'jvfrmtd' ),
				'fade' => __( 'Fade', 'jvfrmtd' ),
				'cube' => __( 'Cube', 'jvfrmtd' ),
			),
			'separator' => 'before',
			'frontend_available' => true,
		) );

		$this->add_control( 'speed', Array(
			'label' => __( 'Transition Duration', 'jvfrmtd' ),
			'type' => Controls_Manager::NUMBER,
			'default' => 5000,
			'frontend_available' => true,
		) );

		$this->add_control( 'autoplay', Array(
			'label' => __( 'Autoplay', 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => '',
			'return_value' => 'yes',
			'separator' => 'before',
			'frontend_available' => true,
		) );

		$this->add_control( 'autoplay_speed', Array(
			'label' => __( 'Autoplay Speed', 'jvfrmtd' ),
			'type' => Controls_Manager::NUMBER,
			'default' => 500,
			'condition' => Array(
				'autoplay' => 'yes',
			),
			'frontend_available' => true,
		) );

		$slides_per_view = range( 1, 10 );
		$slides_per_view = array_combine( $slides_per_view, $slides_per_view );

		$this->add_responsive_control( 'slidesPerView', Array(
			'type' => Controls_Manager::SELECT,
			'label' => __( 'Slides Per View', 'jvfrmtd' ),
			'options' => Array( '' => __( 'Default', 'jvfrmtd' ) ) + $slides_per_view,
			'condition' => Array(
				'effect' => 'slide',
			),
			'frontend_available' => true,
		) );

		$this->add_control( 'spaceBetween', Array(
			'label' => __( 'Between Space', 'jvfrmtd' ),
			'type' => Controls_Manager::NUMBER,
			'default' => '0',
		) );

		$this->end_controls_section();
	}

	public function get_avatar( $comment ) {
		$output = false;
		if( function_exists( 'bp_core_fetch_avatar' ) ) {
			$output = bp_core_fetch_avatar( array( 'item_id' => $comment->user_id, 'type' => 'thumb' ) );
		}else{
			$output = get_avatar( $comment, 32 );
		}
		return $output;
	}

	public function get_score( $comment ) {
		$commentID = $comment->comment_ID;
		$score = get_comment_meta( $commentID, 'rating_average', true );
		return floatval( $score );
	}

	public function getListingTitle( $comment ) {
		$postID = $comment->comment_post_ID;
		return get_the_title( $postID );
	}

	public function getListingLink( $comment ) {
		$postID = $comment->comment_post_ID;
		return esc_url( get_permalink( $postID ) );
	}

	public function getSliderOption() {
		$output = Array();
		foreach(
			Array(
				'speed' => false,
				'autoplay' => false,
				'autoplay_speed' => false,
				'slidesPerView' => false,
				'skin' => false,
				'effect' => false,
				'spaceBetween' => false,
			)
		as $setting => $value ) {
			$output[ $setting ] = false != $value ? $value : $this->get_settings( $setting );
		}

		foreach( array( 'tablet', 'mobile' ) as $breakPoint ) {
			$slidesNumber = $this->get_settings( 'slidesPerView_' . $breakPoint );
			$bpWidth = $breakPoint == 'tablet' ? 768 : 481;
			if( 0 < intVal( $slidesNumber ) ) {
				$output[ 'breakpoints' ][ $bpWidth ] = Array( 'slidesPerView' => $slidesNumber );
			}
		}

		$output = array_filter( $output );
		return wp_json_encode( $output, JSON_NUMERIC_CHECK );
	}

	public function getRatingIcon( $comment ) {
		if( ! class_exists( 'Lava_Directory_Review_CORE')) {
			return;
		}
		$reviewID = $comment->comment_ID;
		return \Lava_Directory_Review_CORE::fa_get( Array( 'value' => get_comment_meta( $reviewID, 'rating_average', true ) ) );
	}

	public function render() {
		$args = Array(
			'post_type' => 'lv_listing',
			'order' => 'DESC',
			'status' => 'approve',
			'number' => $this->get_settings('content_count'),
		);

		$reviews = get_comments($args);

		$content_length = $this->get_settings( 'content_length' );
		$content_length = '' != $content_length ? intVal($content_length) : false;

		$this->add_render_attribute( 'wrap', Array(
            'class' => Array( 'jvbpd-review-list', 'is-jvcore-swiper' ),
		));
		$this->add_render_attribute( 'slide-option', Array(
			'type' => 'hidden',
			'class' => 'slider-value',
			'value' => $this->getSliderOption(),
		));
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
			<input <?php echo $this->get_render_attribute_string( 'slide-option' ); ?>>
			<div class="swiper-container jvbpd-swiper-carousel">
				<div class="swiper-wrapper">
					<?php
					foreach( $reviews as $review ) {
						$this->add_render_attribute( 'review-item', 'class', Array( 'swiper-slide', 'review-item', 'review-id-' . $review->comment_ID ), true );
						?>
						<div <?php echo $this->get_render_attribute_string( 'review-item' ); ?>>
							<div class="review-listing-title">
								<?php
								printf(
									'<a href="%1$s" title="%2$s" class="">%2$s</a>',
									$this->getListingLink( $review ),
									$this->getListingTitle( $review )
								); ?>
							</div>
							<div class="review-content">
								<?php
								printf(
									'<a href="%1$s" title="" class="jv-review-link">',
									$this->getListingLink( $review )
								); ?>
								<?php echo false !== $content_length ? wp_trim_words( $review->comment_content, $content_length ) : $review->comment_content; ?>
								</a>
							</div>

							<div class="review-score">
								<div class="reivew-score-icon"><?php echo $this->getRatingIcon( $review ); ?></div>
								<span class="review-score-text"><?php printf( '%1$.1f / %2$s', $this->get_score( $review ), 5 ); ?></span>
							</div>

							<div class="review-author">
								<div class="review-author-avatar">
									<?php echo $this->get_avatar( $review ); ?>
								</div>
								<div class="review-author-name">
									<?php echo $review->comment_author; ?>
								</div>
							</div>
						</div>
						<?php
					} ?>
				</div>
				<div class="jvbpd-nav-button jvbpd-nav-button-prev">
					<i class="eicon-chevron-left"></i>
				</div>
				<div class="jvbpd-nav-button jvbpd-nav-button-next">
					<i class="eicon-chevron-right"></i>
				</div>
			</div>
		</div>
		<?php
	}
}
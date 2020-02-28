<?php
namespace jvbpdelement\Modules\Carousel\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;
use jvbpdelement\Base\Base_Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

abstract class Base extends Base_Widget {

	Const CAROUSEL_SINGLE_LISTING = 'jv-media-carousel-single-listing';

	private $slide_prints_count = 0;
	private $video_instance = false;

	public function get_script_depends() { return [ 'imagesloaded' ]; }

	public function get_categories() { return [ 'jvbpd-elements' ]; }

	abstract protected function print_slide( array $slide, array $settings, $element_key );

	protected function _register_controls() {
		$this->start_controls_section( 'section_slides', [
			'label' => __( 'JV Slides', 'jvfrmtd' ),
			'tab' => Controls_Manager::TAB_CONTENT,
		] );

		if( self::CAROUSEL_SINGLE_LISTING !== $this->get_name() ) {
			$repeater = new Repeater();
			$this->add_repeater_controls( $repeater );
			$this->add_control( 'slides', [
				'label' => __( 'Slides', 'jvfrmtd' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => $this->get_repeater_defaults(),
			] );
		}

		$this->add_control(
			'effect',
			[
				'type' => Controls_Manager::SELECT,
				'label' => __( 'Effect', 'jvfrmtd' ),
				'default' => 'slide',
				'options' => [
					'slide' => __( 'Slide', 'jvfrmtd' ),
					'fade' => __( 'Fade', 'jvfrmtd' ),
					'cube' => __( 'Cube', 'jvfrmtd' ),
				],
				'separator' => 'before',
				'frontend_available' => true,
			]
		);

		$slides_per_view = range( 1, 10 );
		$slides_per_view = array_combine( $slides_per_view, $slides_per_view );

		$this->add_responsive_control(
			'slidesPerView',
			[
				'type' => Controls_Manager::SELECT,
				'label' => __( 'Slides Per View', 'jvfrmtd' ),
				'options' => [ '' => __( 'Default', 'jvfrmtd' ) ] + $slides_per_view,
				'condition' => [
					'effect' => 'slide',
					'skin!' => 'slideshow',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'slides_to_scroll',
			[
				'type' => Controls_Manager::SELECT,
				'label' => __( 'Slides to Scroll', 'jvfrmtd' ),
				'options' => [ '' => __( 'Default', 'jvfrmtd' ) ] + $slides_per_view,
				'condition' => [
					'effect' => 'slide',
				],
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => __( 'Height', 'jvfrmtd' ),
				'size_units' => [ 'px', 'vh' ],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
					'vh' => [
						'min' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jvbpd-swiper-carousel' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'width',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => __( 'Width', 'jvfrmtd' ),
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1140,
					],
					'%' => [
						'min' => 50,
					],
				],
				'size_units' => [ '%', 'px' ],
				'default' => [
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .jvbpd-swiper-carousel' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_additional_options',
			[
				'label' => __( 'Additional Options', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'show_arrows',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Arrows', 'jvfrmtd' ),
				'default' => 'yes',
				'label_off' => __( 'Hide', 'jvfrmtd' ),
				'label_on' => __( 'Show', 'jvfrmtd' ),
				'frontend_available' => true,
				'prefix_class' => 'jvbpd-arrows-',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'pagination',
			[
				'label' => __( 'Pagination', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'bullets',
				'options' => [
					'' => __( 'None', 'jvfrmtd' ),
					'bullets' => __( 'Dots', 'jvfrmtd' ),
					// 'fraction' => __( 'Fraction', 'jvfrmtd' ),
					'progress' => __( 'Progress', 'jvfrmtd' ),
				],
				'prefix_class' => 'jvbpd-pagination-type-',
				'render_type' => 'template',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'speed',
			[
				'label' => __( 'Transition Duration', 'jvfrmtd' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 5000,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => __( 'Autoplay', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'return_value' => 'yes',
				'separator' => 'before',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label' => __( 'Autoplay Speed', 'jvfrmtd' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 500,
				'condition' => [
					'autoplay' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'loop',
			[
				'label' => __( 'Infinite Loop', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'pause_on_interaction',
			[
				'label' => __( 'Pause on Interaction', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [
					'autoplay' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control( 'spaceBetween', Array(
			'label' => __( 'Between Space', 'jvfrmtd' ),
			'type' => Controls_Manager::NUMBER,
			'default' => '0',
		) );

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image_size',
				'default' => 'full',
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slides_style',
			[
				'label' => __( 'Slides', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'space_between',
			[
				'label' => __( 'Space Between', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'desktop_default' => [
					'size' => 10,
				],
				'tablet_default' => [
					'size' => 10,
				],
				'mobile_default' => [
					'size' => 10,
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'slide_background_color',
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jvbpd-swiper-carousel .swiper-slide' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'slide_border_size',
			[
				'label' => __( 'Border Size', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .jvbpd-swiper-carousel .swiper-slide' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'slide_border_color',
			[
				'label' => __( 'Border Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jvbpd-swiper-carousel .swiper-slide' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'slide_padding',
			[
				'label' => __( 'Padding', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .jvbpd-swiper-carousel .swiper-slide' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'slide_border_radius',
			[
				'label' => __( 'Border Radius', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'%' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jvbpd-swiper-carousel .swiper-slide' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_navigation',
			[
				'label' => __( 'Navigation', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_arrows',
			[
				'label' => __( 'Arrows', 'jvfrmtd' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'none',
			]
		);

		$this->add_control(
			'arrows_size',
			[
				'label' => __( 'Size', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'range' => [
					'px' => [
						'min' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jvbpd-nav-button' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'arrows_color',
			[
				'label' => __( 'Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jvbpd-nav-button' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'heading_pagination',
			[
				'label' => __( 'Pagination', 'jvfrmtd' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'pagination!' => '',
				],
			]
		);

		$this->add_control(
			'pagination_position',
			[
				'label' => __( 'Position', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'outside',
				'options' => [
					'outside' => __( 'Outside', 'jvfrmtd' ),
					'inside' => __( 'Inside', 'jvfrmtd' ),
				],
				'prefix_class' => 'jvbpd-pagination-position-',
				'condition' => [
					'pagination!' => '',
				],
			]
		);

		$this->add_control(
			'pagination_size',
			[
				'label' => __( 'Size', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .swiper-container-horizontal .swiper-pagination-progress' => 'height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .swiper-pagination-fraction' => 'font-size: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'pagination!' => '',
				],
			]
		);

		$this->add_control(
			'pagination_color',
			[
				'label' => __( 'Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet-active, {{WRAPPER}} .swiper-pagination-progressbar' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .swiper-pagination-fraction' => 'color: {{VALUE}}',
				],
				'condition' => [
					'pagination!' => '',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function slidesFilter( array &$slides=Array() ) {

		if( self::CAROUSEL_SINGLE_LISTING != $this->get_name() ) {
			return;
		}

		$queried = get_queried_object();
		$isFiltered = false;

		if( $queried instanceof \WP_Post ) {

			if( $queried->post_type == 'lv_listing' ) {
				$isFiltered = true;
				$images = get_post_meta( $queried->ID, 'detail_images', true );
				if( is_array( $images ) ) {
					$slides = Array();
					foreach( $images as $imageID ) {
						$slides[] = Array(
							'type' => 'image',
							'image_link_to_type' => '',
							'video' => Array(
								'url' => false,
							),
							'image' => Array(
								'id' => $imageID,
								'url' => wp_get_attachment_image_url( $imageID, 'jvbpd-medium' ),
							),
						);
					}
				}
			}
		}

		if( ! $isFiltered ) {
			for( $iSlide=0; $iSlide < 5; $iSlide++ ) {
				$slides[] = Array(
					'type' => 'image',
					'image_link_to_type' => '',
					'video' => Array(
						'url' => false,
					),
					'image' => Array(
						'id' => '',
						'url' => ELEMENTOR_ASSETS_URL . 'images/placeholder.png',
					),
				);
			}
		}
	}

	public function getSliderOption() {
		$output = Array();
		foreach(
			Array(
				'speed',
				'autoplay',
				'autoplay_speed',
				'slidesPerView',
				'slideshow_slides_per_view',
				'skin',
				'effect',
				'spaceBetween'
			)
		as $setting ) {
			$output[ $setting ] = $this->get_settings( $setting );
		}

		foreach( array( 'tablet', 'mobile' ) as $breakPoint ) {
			$slidesNumber = $this->get_settings( 'slidesPerView_' . $breakPoint );
			$bpWidth = $breakPoint == 'tablet' ? 768 : 380;
			if( 0 < intVal( $slidesNumber ) ) {
				$output[ 'breakpoints' ][ $bpWidth ] = Array( 'slidesPerView' => $slidesNumber );
			}
		}

		$output = array_filter( $output );
		return wp_json_encode( $output, JSON_NUMERIC_CHECK );
	}

	protected function print_slider( array $settings = null ) {
		if ( null === $settings ) {
			$settings = $this->get_active_settings();
		}

		$default_settings = [
			// 'container_class' => 'jvbpd-swiper-carousel',
			'container_class' => 'jvbpd-swiper-carousel',
			'video_play_icon' => true,
		];

		$settings = array_merge( $default_settings, $settings );

		if( self::CAROUSEL_SINGLE_LISTING === $this->get_name() ) {
			$settings['slides'] = Array();
			$this->slidesFilter( $settings['slides'] );
		}

		$this->add_render_attribute( 'wrap', Array(
			'class' => 'jvbpd-swiper',
		) );

		if( 'yes' == $this->get_settings( 'slide_lightbox' ) ) {
			$this->add_render_attribute( 'wrap', Array(
				'class' => 'lightbox-active',
			) );
		}

		$slides_count = count( $settings['slides'] ); ?>

		<input type="hidden" class="slider-value" value='<?php echo esc_attr( $this->getSliderOption() ); ?>'>

		<div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
			<div class="<?php echo esc_attr( $settings['container_class'] ); ?> swiper-container">
				<div class="swiper-wrapper">
					<?php
					foreach ( $settings['slides'] as $index => $slide ) :
						$this->slide_prints_count++;
						?>
						<div class="swiper-slide" data-src="<?php echo esc_attr( $slide['image']['url'] ); ?>">
							<?php $this->print_slide( $slide, $settings, 'slide-' . $index . '-' . $this->slide_prints_count ); ?>
							<?php // ><div class="carousel-image" style="background-image: url(http://localhost/homa/wp-content/uploads/sites/6/2017/12/jvh-living15.jpg)"></div> ?>
						</div>
					<?php endforeach; ?>
				</div>
				<?php if ( 1 < $slides_count ) : ?>
					<?php if ( $settings['pagination'] ) : ?>
						<div class="swiper-pagination"></div>
					<?php endif; ?>
					<?php if ( $settings['show_arrows'] ) : ?>
						<div class="jvbpd-nav-button jvbpd-nav-button-prev">
							<i class="eicon-chevron-left"></i>
						</div>
						<div class="jvbpd-nav-button jvbpd-nav-button-next">
							<i class="eicon-chevron-right"></i>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	protected function get_slide_image_url( $slide, array $settings ) {
		$settings[ 'image_size' ] = 'jvbpd-medium';
		$image_url = Group_Control_Image_Size::get_attachment_image_src( $slide['image']['id'], 'image', $settings );

		if ( ! $image_url ) {
			$image_url = $slide['image']['url'];
		}

		return $image_url;
	}

	public function getVideoModal() {
		?>
		<div class="modal fade jvbpd-single-carousel-cover-video">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-body">
						<?php
						if( !empty( $this->video_instance ) ) {
							echo $this->video_instance->output();
						} ?>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php esc_html_e( "Close", 'jvfrmtd' ); ?></button>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	public function get_header_rating( $post_id ){

		$intRatingScore	= $intReviewers = 0;
		$strRatingStars	= '';
		$intRatingScore		= call_user_func( Array( lv_directoryReview()->core, 'get' ), 'average' );
		$intReviewers		= call_user_func( Array( lv_directoryReview()->core, 'get' ), 'count'  );
		$strRatingStars		= call_user_func( Array( lv_directoryReview()->core, 'fa_get' ) );
		return join(
			"\n",
			Array(
				//'<a href="#javo-item-review-section" class="link-review">',
					$strRatingStars,
					'<span class="review-score">',
						$intRatingScore,
					'</span>',
					/*
					'<span class="review-count">',
						$intReviewers . ' ',
						_n( "Vote", "Votes", intVal( $intReviewers ), 'jvfrmtd' ),
					'</span>', */
				// '</a>',
				'<div class="cover-item rating-link">',
					'<a href="#javo-item-review-section" class="link-review btn btn-primary btn-large">',
						esc_html__( "Leave a Review", 'jvfrmtd' ),
					'</a>',
				'</div>',
			)
		);
	}

}

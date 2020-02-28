<?php
namespace jvbpdelement\Modules\Carousel\Widgets;

use Elementor\Controls_Manager;
use Elementor\Embed;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Scheme_Typography;
use Elementor\Utils;
use jvbpdelement\Base\Base_Widget;

if ( ! defined( 'ABSPATH' ) ) exit;

class Jv_Media_Carousel extends Base {

	private $lightbox_slide_index;

	public function get_name() {
		return 'jv-media-carousel';
	}

	public function get_title() {
		return __( 'Jv Media Carousel', 'jvfrmtd' );
	}

	public function get_icon() {
		return 'eicon-media-carousel';
	}

	protected function render() {
		$settings = $this->get_active_settings();

		if ( $settings['overlay'] ) {
			$this->add_render_attribute( 'image-overlay', 'class', [
				'carousel-img-overlay',
				'img-overlay-animation-' . $settings['overlay_animation'],
			] );
		}

		$this->print_slider();

		if ( 'slideshow' !== $settings['skin'] || count( $settings['slides'] ) <= 1 ) {
			return;
		}

		$settings['thumbs_slider'] = true;
		$settings['container_class'] = 'jvbpd-thumbs-swiper';
		$settings['show_arrows'] = false;

		$this->print_slider( $settings );

	}

	protected function _register_controls() {
		parent::_register_controls();

		$this->start_controls_section(
			'section_lightbox_style',
			[
				'label' => __( 'Lightbox', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'lightbox_color',
			[
				'label' => __( 'Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'#jvbpd-lightbox-slideshow-{{ID}}' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'lightbox_ui_color',
			[
				'label' => __( 'UI Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'#jvbpd-lightbox-slideshow-{{ID}} .dialog-lightbox-close-button, #jvbpd-lightbox-slideshow-{{ID}} .jvbpd-nav-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'lightbox_ui_hover_color',
			[
				'label' => __( 'UI Hover Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'#jvbpd-lightbox-slideshow-{{ID}} .dialog-lightbox-close-button:hover, #jvbpd-lightbox-slideshow-{{ID}} .jvbpd-nav-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'lightbox_video_width',
			[
				'label' => __( 'Video Width', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'units' => [ '%' ],
				'default' => [
					'unit' => '%',
				],
				'range' => [
					'%' => [
						'min' => 50,
					],
				],
				'selectors' => [
					'#jvbpd-lightbox-slideshow-{{ID}} .jvbpd-video-container' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->add_injections();
		$this->update_controls();
	}

	protected function add_repeater_controls( Repeater $repeater ) {
		$repeater->add_control(
			'type',
			[
				'type' => Controls_Manager::CHOOSE,
				'label' => __( 'Type', 'jvfrmtd' ),
				'default' => 'image',
				'options' => [
					'image' => [
						'title' => __( 'Image', 'jvfrmtd' ),
						'icon' => 'fa fa-image',
					],
					'video' => [
						'title' => __( 'Video', 'jvfrmtd' ),
						'icon' => 'fa fa-video-camera',
					],
				],
				'label_block' => false,
				'toggle' => false,
			]
		);

		$repeater->add_control(
			'image',
			[
				'label' => __( 'Image', 'jvfrmtd' ),
				'type' => Controls_Manager::MEDIA,
			]
		);

		$repeater->add_control(
			'image_link_to_type',
			[
				'label' => __( 'Link To', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __( 'None', 'jvfrmtd' ),
					'file' => __( 'Media File', 'jvfrmtd' ),
					'custom' => __( 'Custom URL', 'jvfrmtd' ),
				],
				'condition' => [
					'type' => 'image',
				],
			]
		);

		$repeater->add_control(
			'image_link_to',
			[
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'http://your-link.com', 'jvfrmtd' ),
				'condition' => [
					'type' => 'image',
					'image_link_to_type' => 'custom',
				],
				'separator' => 'none',
				'show_label' => false,
			]
		);

		$repeater->add_control(
			'video',
			[
				'label' => __( 'Video Link', 'jvfrmtd' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'Enter your video link', 'jvfrmtd' ),
				'description' => __( 'Insert YouTube or Vimeo link', 'jvfrmtd' ),
				'show_external' => false,
				'condition' => [
					'type' => 'video',
				],
			]
		);
	}

	protected function get_default_slides_count() {
		return 5;
	}

	protected function get_repeater_defaults() {
		$placeholder_image_src = Utils::get_placeholder_image_src();

		return array_fill( 0, $this->get_default_slides_count(), [
			'image' => [
				'url' => $placeholder_image_src,
			],
		] );
	}

	protected function get_image_caption( $slide ) {
		$caption_type = $this->get_settings( 'caption' );

		if ( empty( $caption_type ) ) {
			return '';
		}

		$attachment_post = get_post( $slide['image']['id'] );

		if ( 'caption' === $caption_type ) {
			return $attachment_post->post_excerpt;
		}

		if ( 'title' === $caption_type ) {
			return $attachment_post->post_title;
		}

		return $attachment_post->post_content;
	}

	protected function get_image_link_to( $slide ) {
		if ( $slide['video']['url'] ) {
			return $slide['image']['url'];
		}

		if ( ! $slide['image_link_to_type'] ) {
			return '';
		}

		if ( 'custom' === $slide['image_link_to_type'] ) {
			return $slide['image_link_to']['url'];
		}

		return $slide['image']['url'];
	}

	protected function print_slider( array $settings = null ) {
		$this->lightbox_slide_index = 0;

		parent::print_slider( $settings );
	}

	protected function print_slide( array $slide, array $settings, $element_key ) {
		if ( ! empty( $settings['thumbs_slider'] ) ) {
			$settings['video_play_icon'] = false;

			$this->add_render_attribute( $element_key . '-image', 'class', 'jvbpd-fit-aspect-ratio' );
		}

		$this->add_render_attribute( $element_key . '-image', [
			'class' => 'carousel-image',
			'style' => 'background-image: url(' . $this->get_slide_image_url( $slide, $settings ) . ')',
		] );

		$image_link_to = $this->get_image_link_to( $slide );

		if ( $image_link_to ) {
			$this->add_render_attribute( $element_key . '_link', 'href', $image_link_to );

			if ( 'custom' === $slide['image_link_to_type'] ) {
				if ( $slide['image_link_to']['is_external'] ) {
					$this->add_render_attribute( $element_key . '_link', 'target', '_blank' );
				}

				if ( $slide['image_link_to']['nofollow'] ) {
					$this->add_render_attribute( $element_key . '_link', 'nofollow', '' );
				}
			} else {
				$this->add_render_attribute( $element_key . '_link', [
					'class' => 'jvbpd-clickable',
					'data-jvbpd-lightbox-slideshow' => $this->get_id(),
					'data-jvbpd-lightbox-index' => $this->lightbox_slide_index,
				] );

				$this->lightbox_slide_index++;
			}

			if ( 'video' === $slide['type'] && $slide['video']['url'] ) {
				$embed_url_params = [
					'autoplay' => 1,
					'rel' => 0,
					'controls' => 0,
					'showinfo' => 0,
				];

				$this->add_render_attribute( $element_key . '_link', 'data-jvbpd-lightbox-video', Embed::get_embed_url( $slide['video']['url'], $embed_url_params ) );
			}

			echo '<a ' . $this->get_render_attribute_string( $element_key . '_link' ) . '>';
		}

		$this->print_slide_image( $slide, $element_key, $settings );

		if ( $image_link_to ) {
			echo '</a>';
		}
	}

	protected function print_slide_image( array $slide, $element_key, array $settings ) {
		?>
		<div <?php echo $this->get_render_attribute_string( $element_key . '-image' ); ?>>
			<?php if ( 'video' === $slide['type'] && $settings['video_play_icon'] ) : ?>
				<div class="jvbpd-video-play">
					<i class="eicon-play"></i>
				</div>
			<?php endif; ?>
		</div>
		<?php if ( $settings['overlay'] ) : ?>
			<div <?php echo $this->get_render_attribute_string( 'image-overlay' ); ?>>
				<?php if ( 'text' === $settings['overlay'] ) : ?>
					<?php echo $this->get_image_caption( $slide ); ?>
				<?php else : ?>
					<i class="fa fa-<?php echo $settings['icon']; ?>"></i>
				<?php endif; ?>
			</div>
		<?php endif;
	}

	private function add_injections() {
		$this->start_injection( [
			'type' => 'section',
			'at' => 'start',
			'of' => 'section_slides',
		] );

		$this->add_control(
			'skin',
			[
				'label' => __( 'Skin', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'carousel',
				'options' => [
					'carousel' => __( 'Carousel', 'jvfrmtd' ),
					'slideshow' => __( 'Slideshow', 'jvfrmtd' ),
				],
				'prefix_class' => 'carousel-skin-',
				'render_type' => 'template',
				'frontend_available' => true,
			]
		);

		$this->end_injection();

		$this->start_injection( [
			'of' => 'image_size_custom_dimension',
		] );

		$this->add_control(
			'image_fit',
			[
				'label' => __( 'Image Fit', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'Cover', 'jvfrmtd' ),
					'contain' => __( 'Contain', 'jvfrmtd' ),
					'auto' => __( 'Auto', 'jvfrmtd' ),
				],
				'selectors' => [
					'{{WRAPPER}} .jvbpd-swiper-carousel .carousel-image' => 'background-size: {{VALUE}}',
				],
			]
		);

		$this->end_injection();

		$this->start_injection( [
			'of' => 'pagination_color',
		] );

		$this->add_control(
			'play_icon_title',
			[
				'label' => __( 'Play Icon', 'jvfrmtd' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'play_icon_color',
			[
				'label' => __( 'Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jvbpd-video-play i' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'play_icon_size',
			[
				'label' => __( 'Size', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 150,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jvbpd-video-play i' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'play_icon_text_shadow',
				'selector' => '{{WRAPPER}} .jvbpd-video-play i',
				'fields_options' => [
					'text_shadow_type' => [
						'label' => _x( 'Shadow', 'Text Shadow Control', 'jvfrmtd' ),
					],
				],
			]
		);

		$this->end_injection();

		$this->start_injection( [
			'of' => 'pause_on_interaction',
		] );

		$this->add_control(
			'overlay',
			[
				'label' => __( 'Overlay', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'None', 'jvfrmtd' ),
					'text' => __( 'Text', 'jvfrmtd' ),
					'icon' => __( 'Icon', 'jvfrmtd' ),
				],
				'condition' => [
					'skin!' => 'slideshow',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'caption',
			[
				'label' => __( 'Caption', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'title',
				'options' => [
					'title' => __( 'Title', 'jvfrmtd' ),
					'caption' => __( 'Caption', 'jvfrmtd' ),
					'description' => __( 'Description', 'jvfrmtd' ),
				],
				'condition' => [
					'skin!' => 'slideshow',
					'overlay' => 'text',
				],
			]
		);

		$this->add_control(
			'icon',
			[
				'label' => __( 'Icon', 'jvfrmtd' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'search-plus',
				'options' => [
					'search-plus' => [
						'icon' => 'fa fa-search-plus',
					],
					'plus-circle' => [
						'icon' => 'fa fa-plus-circle',
					],
					'eye' => [
						'icon' => 'fa fa-eye',
					],
					'link' => [
						'icon' => 'fa fa-link',
					],
				],
				'condition' => [
					'skin!' => 'slideshow',
					'overlay' => 'icon',
				],
			]
		);

		$this->add_control(
			'overlay_animation',
			[
				'label' => __( 'Animation', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'fade',
				'options' => [
					'fade' => 'Fade',
					'slide-up' => 'Slide Up',
					'slide-down' => 'Slide Down',
					'slide-right' => 'Slide Right',
					'slide-left' => 'Slide Left',
					'zoom-in' => 'Zoom In',
				],
				'condition' => [
					'skin!' => 'slideshow',
					'overlay!' => '',
				],
			]
		);

		$this->end_injection();

		$this->start_injection( [
			'type' => 'section',
			'of' => 'section_navigation',
		] );


		$this->start_controls_section(
			'section_overlay',
			[
				'label' => __( 'Overlay', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'skin!' => 'slideshow',
					'overlay!' => '',
				],
			]
		);

		$this->add_control(
			'overlay_background_color',
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .carousel-img-overlay' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'overlay_color',
			[
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .carousel-img-overlay' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'caption_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .carousel-img-overlay',
				'condition' => [
					'overlay' => 'text',
				],
			]
		);

		$this->add_control(
			'icon_size',
			[
				'label' => __( 'Icon Size', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .carousel-img-overlay i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'overlay' => 'icon',
				],
			]
		);

		$this->end_controls_section();

		$this->end_injection();

		// Slideshow

		$this->start_injection( [
			'of' => 'effect',
		] );

		$this->add_responsive_control(
			'slideshow_height',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => __( 'Height', 'jvfrmtd' ),
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .jvbpd-swiper-carousel' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'skin' => 'slideshow',
				],
			]
		);

		$this->add_control(
			'thumbs_title',
			[
				'label' => __( 'Thumbnails', 'jvfrmtd' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'skin' => 'slideshow',
				],
			]
		);

		$this->end_injection();

		$this->start_injection( [
			'of' => 'slidesPerView',
		] );

		$this->add_control(
			'thumbs_ratio',
			[
				'label' => __( 'Ratio', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => '219',
				'options' => [
					'169' => '16:9',
					'219' => '21:9',
					'43' => '4:3',
					'11' => '1:1',
				],
				'prefix_class' => 'jvbpd-aspect-ratio-',
				'condition' => [
					'skin' => 'slideshow',
				],
			]
		);

		$this->end_injection();

		$this->start_injection( [
			'of' => 'slidesPerView',
		] );

		$slides_per_view = range( 1, 10 );

		$slides_per_view = array_combine( $slides_per_view, $slides_per_view );

		$this->add_responsive_control(
			'slideshow_slides_per_view',
			[
				'type' => Controls_Manager::SELECT,
				'label' => __( 'Slides Per View', 'jvfrmtd' ),
				'options' => [ '' => __( 'Default', 'jvfrmtd' ) ] + $slides_per_view,
				'condition' => [
					'skin' => 'slideshow',
				],
				'frontend_available' => true,
			]
		);

		$this->end_injection();
	}

	private function update_controls() {
		$carousel_controls = [
			'slides_to_scroll',
			'pagination',
			'heading_pagination',
			'pagination_size',
			'pagination_position',
			'pagination_color',
		];

		$carousel_responsive_controls = [
			'width',
			'height',
			'slidesPerView',
		];

		foreach ( $carousel_controls as $control_id ) {
			$this->update_control(
				$control_id,
				[
					'condition' => [
						'skin!' => 'slideshow',
					],
				],
				[ 'recursive' => true ]
			);
		}

		foreach ( $carousel_responsive_controls as $control_id ) {
			$this->update_responsive_control(
				$control_id,
				[
					'condition' => [
						'skin!' => 'slideshow',
					],
				],
				[ 'recursive' => true ]
			);
		}

		$this->update_responsive_control(
			'space_between',
			[
				'selectors' => [
					'{{WRAPPER}}.carousel-skin-slideshow .jvbpd-swiper-carousel' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
				'render_type' => 'ui',
			]
		);

		$this->update_control(
			'effect',
			[
				'condition' => [
					'skin!' => 'coverflow',
				],
			]
		);
	}
}

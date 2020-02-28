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
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Embed;
use Elementor\Plugin;


if( ! defined( 'ABSPATH' ) )
	die();

class jvbpd_single_video extends Widget_Base {

	public function get_name() {
		return 'jvbpd_single_video';
	}

	public function get_title() {
		return 'Single Video';   // title to show on elementor
	}

	public function get_icon() {
		return 'eicon-youtube';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-single-listing' ];    // category of the widget
	}

    protected function _register_controls() {

        $this->start_controls_section(
			'section_general',
			array(
				'label' => esc_html__( 'Single Detail Video', 'jvfrmtd' ),
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
			'section_video',
			[
				'label' => __( 'Video', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'video_type',
			[
				'label' => __( 'Video Type', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'youtube',
				'options' => [
					'youtube' => __( 'YouTube', 'jvfrmtd' ),
					'vimeo' => __( 'Vimeo', 'jvfrmtd' ),
				],
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __( 'Link', 'jvfrmtd' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
					],
				],
				'placeholder' => __( 'Enter your YouTube link', 'jvfrmtd' ),
				'default' => 'https://www.youtube.com/watch?v=ctvlUvN6wSE',
				'label_block' => true,
				'condition' => [
					'video_type' => 'youtube',
				],
			]
		);

		$this->add_control(
			'vimeo_link',
			[
				'label' => __( 'Link', 'jvfrmtd' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
					],
				],
				'placeholder' => __( 'Enter your Vimeo link', 'jvfrmtd' ),
				'default' => 'https://vimeo.com/235215203',
				'label_block' => true,
				'condition' => [
					'video_type' => 'vimeo',
				],
			]
		);

		$this->add_control(
			'hosted_link',
			[
				'label' => __( 'Link', 'jvfrmtd' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
					],
				],
				'placeholder' => __( 'Enter your video link', 'jvfrmtd' ),
				'default' => '',
				'label_block' => true,
				'condition' => [
					'video_type' => 'hosted',
				],
			]
		);

		$this->add_control(
			'heading_youtube',
			[
				'label' => __( 'Video Options', 'jvfrmtd' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		// YouTube.
		$this->add_control(
			'yt_autoplay',
			[
				'label' => __( 'Autoplay', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [
					'video_type' => 'youtube',
				],
			]
		);

		$this->add_control(
			'yt_rel',
			[
				'label' => __( 'Suggested Videos', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'jvfrmtd' ),
				'label_on' => __( 'Show', 'jvfrmtd' ),
				'condition' => [
					'video_type' => 'youtube',
				],
			]
		);

		$this->add_control(
			'yt_controls',
			[
				'label' => __( 'Player Control', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'jvfrmtd' ),
				'label_on' => __( 'Show', 'jvfrmtd' ),
				'default' => 'yes',
				'condition' => [
					'video_type' => 'youtube',
				],
			]
		);

		$this->add_control(
			'yt_showinfo',
			[
				'label' => __( 'Player Title & Actions', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'jvfrmtd' ),
				'label_on' => __( 'Show', 'jvfrmtd' ),
				'default' => 'yes',
				'condition' => [
					'video_type' => 'youtube',
				],
			]
		);

		$this->add_control(
			'yt_mute',
			[
				'label' => __( 'Mute', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [
					'video_type' => 'youtube',
				],
			]
		);

		$this->add_control(
			'yt_privacy',
			[
				'label' => __( 'Privacy Mode', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => __( 'When you turn on privacy mode, YouTube won\'t store information about visitors on your website unless they play the video.', 'jvfrmtd' ),
				'condition' => [
					'video_type' => 'youtube',
				],
			]
		);

		// Vimeo.
		$this->add_control(
			'vimeo_autoplay',
			[
				'label' => __( 'Autoplay', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [
					'video_type' => 'vimeo',
				],
			]
		);

		$this->add_control(
			'vimeo_loop',
			[
				'label' => __( 'Loop', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [
					'video_type' => 'vimeo',
				],
			]
		);

		$this->add_control(
			'vimeo_title',
			[
				'label' => __( 'Intro Title', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'jvfrmtd' ),
				'label_on' => __( 'Show', 'jvfrmtd' ),
				'default' => 'yes',
				'condition' => [
					'video_type' => 'vimeo',
				],
			]
		);

		$this->add_control(
			'vimeo_portrait',
			[
				'label' => __( 'Intro Portrait', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'jvfrmtd' ),
				'label_on' => __( 'Show', 'jvfrmtd' ),
				'default' => 'yes',
				'condition' => [
					'video_type' => 'vimeo',
				],
			]
		);

		$this->add_control(
			'vimeo_byline',
			[
				'label' => __( 'Intro Byline', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'jvfrmtd' ),
				'label_on' => __( 'Show', 'jvfrmtd' ),
				'default' => 'yes',
				'condition' => [
					'video_type' => 'vimeo',
				],
			]
		);

		$this->add_control(
			'vimeo_color',
			[
				'label' => __( 'Controls Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'condition' => [
					'video_type' => 'vimeo',
				],
			]
		);

		$this->add_control(
			'view',
			[
				'label' => __( 'View', 'jvfrmtd' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'youtube',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_image_overlay',
			[
				'label' => __( 'Image Overlay', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'show_image_overlay',
			[
				'label' => __( 'Image Overlay', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'jvfrmtd' ),
				'label_on' => __( 'Show', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'image_overlay',
			[
				'label' => __( 'Image', 'jvfrmtd' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'show_image_overlay' => 'yes',
				],
			]
		);

		$this->add_control(
			'lazy_load',
			[
				'label' => __( 'Lazy Load', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [
					'show_image_overlay' => 'yes',
					'video_type!' => 'hosted',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image_overlay', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_overlay_size` and `image_overlay_custom_dimension`.
				'default' => 'full',
				'separator' => 'none',
				'condition' => [
					'show_image_overlay' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_play_icon',
			[
				'label' => __( 'Play Icon', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'No', 'jvfrmtd' ),
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'return_value' => 'yes',
				'condition' => [
					'show_image_overlay' => 'yes',
					'image_overlay[url]!' => '',
				],
			]
		);

		$this->add_control(
			'lightbox',
			[
				'label' => __( 'Lightbox', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'label_off' => __( 'Off', 'jvfrmtd' ),
				'label_on' => __( 'On', 'jvfrmtd' ),
				'condition' => [
					'show_image_overlay' => 'yes',
					'image_overlay[url]!' => '',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_video_style',
			[
				'label' => __( 'Video', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'aspect_ratio',
			[
				'label' => __( 'Aspect Ratio', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'169' => '16:9',
					'43' => '4:3',
					'32' => '3:2',
				],
				'default' => '169',
				'prefix_class' => 'elementor-aspect-ratio-',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'play_icon_title',
			[
				'label' => __( 'Play Icon', 'jvfrmtd' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'show_image_overlay' => 'yes',
					'show_play_icon' => 'yes',
				],
			]
		);

		$this->add_control(
			'play_icon_color',
			[
				'label' => __( 'Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-custom-embed-play i' => 'color: {{VALUE}}',
				],
				'separator' => 'before',
				'condition' => [
					'show_image_overlay' => 'yes',
					'show_play_icon' => 'yes',
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
						'min' => 10,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-custom-embed-play i' => 'font-size: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'show_image_overlay' => 'yes',
					'show_play_icon' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'play_icon_text_shadow',
				'selector' => '{{WRAPPER}} .elementor-custom-embed-play i',
				'fields_options' => [
					'text_shadow_type' => [
						'label' => _x( 'Shadow', 'Text Shadow Control', 'jvfrmtd' ),
					],
				],
				'condition' => [
					'show_image_overlay' => 'yes',
					'show_play_icon' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_lightbox_style',
			[
				'label' => __( 'Lightbox', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_image_overlay' => 'yes',
					'image_overlay[url]!' => '',
					'lightbox' => 'yes',
				],
			]
		);

		$this->add_control(
			'lightbox_color',
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'#elementor-lightbox-{{ID}}' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'lightbox_ui_color',
			[
				'label' => __( 'UI Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'#elementor-lightbox-{{ID}} .dialog-lightbox-close-button' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'lightbox_ui_color_hover',
			[
				'label' => __( 'UI Hover Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'#elementor-lightbox-{{ID}} .dialog-lightbox-close-button:hover' => 'color: {{VALUE}}',
				],
				'separator' => 'after',
			]
		);

		$this->add_control(
			'lightbox_video_width',
			[
				'label' => __( 'Content Width', 'jvfrmtd' ),
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
					'(desktop+)#elementor-lightbox-{{ID}} .elementor-video-container' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'lightbox_content_position',
			[
				'label' => __( 'Content Position', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'frontend_available' => true,
				'options' => [
					'' => __( 'Center', 'jvfrmtd' ),
					'top' => __( 'Top', 'jvfrmtd' ),
				],
				'selectors' => [
					'#elementor-lightbox-{{ID}} .elementor-video-container' => '{{VALUE}}; transform: translateX(-50%);',
				],
				'selectors_dictionary' => [
					'top' => 'top: 60px',
				],
			]
		);

		$this->add_control(
			'lightbox_content_animation',
			[
				'label' => __( 'Entrance Animation', 'jvfrmtd' ),
				'type' => Controls_Manager::ANIMATION,
				'default' => '',
				'frontend_available' => true,
				'label_block' => true,
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
				'section_style_video',
				[
					'label' => __( 'Video Style', 'jvfrmtd' ),
					'tab'   => Controls_Manager::TAB_STYLE,
				]
		);

		$this->add_responsive_control(
			'lava_video_height',
			[
				'label' => __( 'Height', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 350,
				],
				'range' => [
					'px' => [
						'min' => 50,
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
					'{{WRAPPER}} .javo-widget-preview-video-image' => 'height:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'lava_video_width',
			[
				'label' => __( 'Width', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 100,
					'unit' => '%',
				],
				'range' => [
					'px' => [
						'min' => 50,
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
					'{{WRAPPER}} .javo-widget-preview-video-image' => 'width:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'lava_video_min_height',
			[
				'label' => __( 'Min-Height', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 250,
				],
				'range' => [
					'px' => [
						'min' => 50,
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
					'{{WRAPPER}} .javo-widget-preview-video-image' => 'min-height:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'lava_video_min_width',
			[
				'label' => __( 'Min-Width', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
				],
				'range' => [
					'px' => [
						'min' => 50,
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
					'{{WRAPPER}} .javo-widget-preview-video-image' => 'min-width:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
    }

    protected function render() {

		if( class_exists( '\Lava\Role\Manager') ) {
			$post = get_post();
			$author = $post->post_author;
			$author_role = \Lava\Role\Manager::$instance->admin->getUserMember( $author );
			$role = \Lava\Role\Manager::$instance->admin->getRole( $author_role );
			if( $post->post_type == 'lv_listing' && $role && !$role->use_video_cover ) {
				return;
			}
        }

		//****************************************************//
		$settings = $this->get_settings_for_display();
		$video_link = 'youtube' === $settings['video_type'] ? $settings['link'] : $settings['vimeo_link'];

		if( function_exists( 'Lava_Directory_Video' ) && 'lv_listing' == get_post()->post_type ) {
			$objVideo = new \lvDirectoryVideo_Render( get_post(), array() );
			$video_link = $objVideo->video_url;
		}


		if ( empty( $video_link ) ) {
			return;
		}

		$embed_params = $this->get_embed_params();

		$embed_options = [
			'privacy' => $settings['yt_privacy'],
			'lazy_load' => !empty( $settings['lazy_load'] ),
		];

		$video_html = Embed::get_embed_html( $video_link, $embed_params, $embed_options );

		if ( empty( $video_html ) ) {
			echo esc_url( $video_link );

			return;
		}

		$this->add_render_attribute( 'video-wrapper', 'class', 'elementor-wrapper' );

		if ( ! $settings['lightbox'] ) {
			$this->add_render_attribute( 'video-wrapper', 'class', 'elementor-fit-aspect-ratio' );
		}

		$this->add_render_attribute( 'video-wrapper', 'class', 'elementor-open-' . ( $settings['lightbox'] ? 'lightbox' : 'inline' ) );
		?>
		<div <?php echo $this->get_render_attribute_string( 'video-wrapper' ); ?>>
			<?php
			if ( ! $settings['lightbox'] ) {
				echo $video_html; // XSS ok.
			}

			if ( $this->has_image_overlay() ) {
				$this->add_render_attribute( 'image-overlay', 'class', 'elementor-custom-embed-image-overlay' );

				if ( $settings['lightbox'] ) {
					$lightbox_options = [
						'type' => 'video',
						'url' => Embed::get_embed_url( $video_link, $embed_params, $embed_options ),
						'modalOptions' => [
							'id' => 'elementor-lightbox-' . $this->get_id(),
							'entranceAnimation' => $settings['lightbox_content_animation'],
							'videoAspectRatio' => $settings['aspect_ratio'],
						],
					];

					$this->add_render_attribute( 'image-overlay', [
						'data-elementor-open-lightbox' => 'yes',
						'data-elementor-lightbox' => wp_json_encode( $lightbox_options ),
					] );

					if ( Plugin::$instance->editor->is_edit_mode() ) {
						$this->add_render_attribute( 'image-overlay', [
							'class' => 'elementor-clickable',
						] );
					}
				} else {
					$this->add_render_attribute( 'image-overlay', 'style', 'background-image: url(' . Group_Control_Image_Size::get_attachment_image_src( $settings['image_overlay']['id'], 'image_overlay', $settings ) . ');' );
				}
				?>
				<div <?php echo $this->get_render_attribute_string( 'image-overlay' ); ?>>
					<?php if ( $settings['lightbox'] ) : ?>
						<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'image_overlay' ); ?>
					<?php endif; ?>
					<?php if ( 'yes' === $settings['show_play_icon'] ) : ?>
						<div class="elementor-custom-embed-play" role="button">
							<i class="eicon-play" aria-hidden="true"></i>
							<span class="elementor-screen-only"><?php echo __( 'Play Video', 'jvfrmtd' ); ?></span>
						</div>
					<?php endif; ?>
				</div>
			<?php } ?>
		</div>
	<?php
	}

	/**
	 * Render video widget as plain content.
	 *
	 * Override the default behavior, by printing the video URL insted of rendering it.
	 *
	 * @since 1.4.5
	 * @access public
	 */
	public function render_plain_content() {
		$settings = $this->get_settings_for_display();
		$url = 'youtube' === $settings['video_type'] ? $settings['link'] : $settings['vimeo_link'];

		echo esc_url( $url );
	}

	/**
	 * Get embed params.
	 *
	 * Retrieve video widget embed parameters.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return array Video embed parameters.
	 */
	public function get_embed_params() {
		$settings = $this->get_settings_for_display();

		$params = [];

		if ( 'youtube' === $settings['video_type'] ) {
			$youtube_options = [ 'autoplay', 'rel', 'controls', 'showinfo', 'mute' ];

			foreach ( $youtube_options as $option ) {
				if ( 'autoplay' === $option && $this->has_image_overlay() ) {
					continue;
				}

				$value = ( 'yes' === $settings[ 'yt_' . $option ] ) ? '1' : '0';
				$params[ $option ] = $value;
			}

			$params['wmode'] = 'opaque';
		}

		if ( 'vimeo' === $settings['video_type'] ) {
			$vimeo_options = [ 'autoplay', 'loop', 'title', 'portrait', 'byline' ];

			foreach ( $vimeo_options as $option ) {
				if ( 'autoplay' === $option && $this->has_image_overlay() ) {
					continue;
				}

				$value = ( 'yes' === $settings[ 'vimeo_' . $option ] ) ? '1' : '0';
				$params[ $option ] = $value;
			}

			$params['color'] = str_replace( '#', '', $settings['vimeo_color'] );

			$params['autopause'] = '0';
		}

		return $params;
	}

	/**
	 * Whether the video widget has an overlay image or not.
	 *
	 * Used to determine whether an overlay image was set for the video.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return bool Whether an image overlay was set for the video.
	 */
	protected function has_image_overlay() {
		$settings = $this->get_settings_for_display();

		return ! empty( $settings['image_overlay']['url'] ) && 'yes' === $settings['show_image_overlay'];
	}
}
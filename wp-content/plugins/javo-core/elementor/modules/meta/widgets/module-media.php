<?php
namespace jvbpdelement\Modules\Meta\Widgets;

use Elementor\Controls_Manager;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;

class Block_Media_Meta extends Base {

	private $post = null;

	public function get_name() { return parent::BLOCK_MEDIA; }
	public function get_title() { return 'Module Media'; }
	public function get_icon() { return 'eicon-image-box'; }
	public function get_categories() { return [ 'jvbpd-page-builder-module' ]; }

	protected function _register_controls() {
		$this->start_controls_section( 'section_general', Array(
			'label' => esc_html__( "General", 'jvfrmtd' ),
		) );

		$this->add_control( 'media_type', Array(
			'label' => esc_html__( "Media Type", 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT,
			'default' => 'featured',
			'options' => Array(
				'featured' => esc_html__( "Featured Image", 'jvfrmtd' ),
				'category_featured' => esc_html__( "Category Featured Image", 'jvfrmtd' ),
				'slider' => esc_html__( "Slider", 'jvfrmtd' ),
				'video' => esc_html__( "video", 'jvfrmtd' ),
			),
			'selectors' => Array(
				'{{WRAPPER}} .image-wrap' => 'background-size:cover; background-repeat:no-repeat; min-height:50px; background-position:center center;'
			),
		) );


		$this->end_controls_section();

		$this->add_badge_control( $this, 'top_left', esc_html__( "Top Left", 'jvfrmtd' ) );
		$this->add_badge_control( $this, 'top_right', esc_html__( "Top Right", 'jvfrmtd' ) );
		$this->add_badge_control( $this, 'bottom_left', esc_html__( "Bottom Left", 'jvfrmtd' ) );
		$this->add_badge_control( $this, 'bottom_right', esc_html__( "Bottom Right", 'jvfrmtd' ) );
		// $this->add_image_top_control( $this, 'center', esc_html__( "Center", 'jvfrmtd' ) );


		$this->start_controls_section( 'section_custom_content', Array(
			'label' => esc_html__( "Custom Content", 'jvfrmtd' ),
		) );


		$this->add_control(
		  'custom_content',
		  [
			 'label'   => __( 'Custom HTML', 'jvfrmtd' ),
			 'type'    => Controls_Manager::CODE,
			 'language' => 'html',
		  ]
		);
		$this->end_controls_section();



		$this->start_controls_section( 'section_featured', Array(
			'label' => esc_html__( "Featured", 'jvfrmtd' ),
			'condition' => [
				'media_type' => Array( 'featured', 'category_featured' )
			],
		) );

		$this->add_responsive_control(
			'media_height',
			[
				'label' => __( 'Height', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 600,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 250,
				],
				'size_units' => [ 'px', 'vh', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .image-wrap' => 'height: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
		  'thumb_size',
		  [
			 'label'       => __( 'Image Size', 'jvfrmtd' ),
			 'type' => Controls_Manager::SELECT,
			 'default' => '',
			 'options' => jvbpd_elements_tools()->get_image_sizes(),
		  ]
		);


		$this->add_control(
			'link_detail',
			[
				'label' => __( 'Link to Detail', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'return_value' => 'yes',
				'selectors'		=>	[
					'{{WRAPPER}} .media-link-detail' => 'height: 100%;width: 100%;display: inline-block;position: absolute;',
				],
			]
		);

		$this->add_control(
			'link_detail_blank',
			[
				'label' => __( 'Popup', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'return_value' => 'yes',
				'separator' => 'none',
				'condition'=>[
					'link_detail'=>'yes',
				],
			]
		);


		$this->add_control(
		  'position',
		  [
			'label'       => __( 'Position', 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT,
			'default' => '',
			'options' => [
				'top left' => _x( 'Top Left', 'Background Control', 'elementor' ),
				'top center' => _x( 'Top Center', 'Background Control', 'elementor' ),
				'top right' => _x( 'Top Right', 'Background Control', 'elementor' ),
				'center left' => _x( 'Center Left', 'Background Control', 'elementor' ),
				'center center' => _x( 'Center Center', 'Background Control', 'elementor' ),
				'center right' => _x( 'Center Right', 'Background Control', 'elementor' ),
				'bottom left' => _x( 'Bottom Left', 'Background Control', 'elementor' ),
				'bottom center' => _x( 'Bottom Center', 'Background Control', 'elementor' ),
				'bottom right' => _x( 'Bottom Right', 'Background Control', 'elementor' ),
			],
			'selectors' => [
				'{{SELECTOR}}' => 'background-position: {{VALUE}};',
			],
		  ]
		);

		//width, height, overlay, hover overlay, poistion
		$this->end_controls_section();

		// Background Overlay
		$this->start_controls_section(
			'section_background_overlay',
			[
				'label' => __( 'Background Overlay', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				//'condition' => [
					//'background_background' => [ 'classic', 'gradient', 'video' ],
				//],
			]
		);

		$this->start_controls_tabs( 'tabs_background_overlay' );

		$this->start_controls_tab(
			'tab_background_overlay_normal',
			[
				'label' => __( 'Normal', 'elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_overlay',
				'selector' => '{{WRAPPER}} .media-overlay',
			]
		);

		$this->add_control(
			'background_overlay_opacity',
			[
				'label' => __( 'Opacity', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => .5,
				],
				'range' => [
					'px' => [
						'max' => 1,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .media-overlay' => 'opacity: {{SIZE}};',
				],
				'condition' => [
					'background_overlay_background' => [ 'classic', 'gradient' ],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_background_overlay_hover',
			[
				'label' => __( 'Hover', 'elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_overlay_hover',
				'selector' => '{{WRAPPER}}:hover .media-overlay',
			]
		);

		$this->add_control(
			'background_overlay_hover_opacity',
			[
				'label' => __( 'Opacity', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => .5,
				],
				'range' => [
					'px' => [
						'max' => 1,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}}:hover .media-overlay' => 'opacity: {{SIZE}};',
				],
				'condition' => [
					'background_overlay_hover_background' => [ 'classic', 'gradient' ],
				],
			]
		);

		$this->add_control(
			'background_overlay_hover_transition',
			[
				'label' => __( 'Transition Duration', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0.3,
				],
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					//'{{WRAPPER}} .module-media-wrap' => 'transition: background {{background_hover_transition.SIZE}}s, border {{SIZE}}s, border-radius {{SIZE}}s, box-shadow {{SIZE}}s',
					'{{WRAPPER}} .media-overlay' => 'transition: background {{background_overlay_hover_transition.SIZE}}s, border-radius {{SIZE}}s, opacity {{background_overlay_hover_transition.SIZE}}s',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

	}


	public function add_badge_control( $instance, $direction='', $dirLabel='' ) {

		$controls = Array(
			'badge_%s_setting' => Array(
				'label' => __( 'Badge %s Setting', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
				'separator' => 'before',
				 'condition'	=> Array(
				 	//'badge_%s' =>'yes',
				 	//'badge_%s_field!' => '',
				 ),
			),
			'badge_%s_content' => Array(
				'label' => __( 'Field', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT2,
				'default' => '{listing_category}',
				'options' => $instance->getReplaceOptions(),
				'condition' => [
					'badge_%s_setting' => 'yes',
				]
			),
			'badge_%s_field_position_x' => Array(
				'label' => __( 'From Left', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 12,
					'unit'	=> '%',
				],
				'size_units' => [ '%'],
				'selectors' => [
					'{{WRAPPER}} .cblock-media-%s' => 'position:absolute; left: {{SIZE}}{{UNIT}};',
				],
				 'condition' => [
				 	'badge_%s_setting' => 'yes',
				 ]
			),
			'badge_%s_field_position_y' => Array(
				'label' => __( 'From Top', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 12,
					'unit'	=> '%',
				],
				'size_units' => ['%'],
				'selectors' => [
					'{{WRAPPER}} .cblock-media-%s' => ' top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'badge_%s_setting' => 'yes',
				]
			),
			'badge_%s_text_color' => Array(
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'defalut' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .cblock-media-%s' => 'color: {{VALUE}};',
				],
				'condition' => [
					'badge_%s_setting' => 'yes',
				]
			),
			'badge_%s_text_bgcolor' => Array(
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'defalut' => '#000',
				'selectors' => [
					'{{WRAPPER}} .cblock-media-%s' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'badge_%s_setting' => 'yes',
				]
			),
			'badge_%s_typo' => Array(
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .cblock-media-%s',
				'condition' => [
					'badge_%s_setting' => 'yes',
					//'badge_%s'=>'yes',
					//'badge_%s_field!' => '',
				],
				'group_ctl' => Group_Control_Typography::get_type(),
			),
			'badge_%s_padding' => Array(
				'label' => __( '%s Padding', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '2',
					'right' => '8',
					'bottom' => '2',
					'left' => '8',
					'unit' => 'px',
				],
				'condition' => [
					'badge_%s_setting' => 'yes',
					//'badge_%s'=>'yes',
					//'badge_%s_field!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .cblock-media-%s' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'responsive_ctl' => true,
			),
			'badge_%s_radius' => Array(
				'label' => __( '%s Radius', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '2',
					'right' => '2',
					'bottom' => '2',
					'left' => '2',
					'unit' => 'px',
				],
				'condition' => [
					'badge_%s_setting' => 'yes',
					//'badge_%s'=>'yes',
					//'badge_%s_field!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .cblock-media-%s' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'responsive_ctl' => true,
			),
		);

		$instance->start_controls_section( 'section_badge_' . $direction, Array(
			'label' => sprintf( esc_html__( 'Badge %s', 'jvfrmtd' ), $dirLabel )
		) );

		foreach( $controls as $control_key => $control_meta ){
			$controlName = sprintf( $control_key, $direction );

			if( false !== strpos( $direction, '_right' ) && 'badge_%s_field_position_x' == $control_key ) {
				$control_meta[ 'label' ] = esc_html__( "From Right", 'jvfrmtd' );
				$control_meta[ 'selectors' ] = Array(
					'{{WRAPPER}} .cblock-media-%s' => 'position:absolute; right: {{SIZE}}{{UNIT}};',
				);
			}

			if( false !== strpos( $direction, 'bottom_' ) && 'badge_%s_field_position_y' == $control_key ) {
				$control_meta[ 'label' ] = esc_html__( "From Bottom", 'jvfrmtd' );
				$control_meta[ 'selectors' ] = Array(
					'{{WRAPPER}} .cblock-media-%s' => 'position:absolute; bottom: {{SIZE}}{{UNIT}};',
				);
			}

			if( isset( $control_meta['label'] ) ) {
				$control_meta['label'] = sprintf( $control_meta['label'], $dirLabel );
			}

			if( isset( $control_meta['condition'] ) && is_array( $control_meta['condition'] ) ) {
				foreach( $control_meta['condition'] as $conditionKey => $conditionValue ) {
					unset( $control_meta['condition'][ $conditionKey ] );
					$newKey = sprintf( $conditionKey, $direction );
					$control_meta['condition'][ $newKey ] = $conditionValue;
				}
			}

			if( isset( $control_meta['selector'] ) ) {
				$control_meta['selector'] = sprintf( $control_meta['selector'], str_replace( '_', '-', $direction ) );
			}

			if( isset( $control_meta['selectors'] ) && is_array( $control_meta['selectors'] ) ) {
				foreach( $control_meta['selectors'] as $conditionKey => $conditionValue ) {
					unset( $control_meta['selectors'][ $conditionKey ] );
					$newKey = sprintf( $conditionKey, str_replace( '_', '-', $direction ) );
					if( 'badge_%s_text_color' == $control_key ) {
						$newKey .= ','  . $newKey . '  a' ;
					}
					$control_meta['selectors'][ $newKey ] = $conditionValue;
				}
			}

			if( isset( $control_meta[ 'group_ctl' ] ) && $control_meta[ 'group_ctl' ] ) {
				$control_meta[ 'name' ] = $controlName;
				$instance->add_group_control( $control_meta[ 'group_ctl' ], $control_meta );
			}elseif( isset( $control_meta[ 'responsive_ctl' ] ) && $control_meta[ 'responsive_ctl' ] ) {
				$instance->add_responsive_control( $controlName, $control_meta );
			}else{
				$instance->add_control( $controlName, $control_meta );
			}

		}
		$instance->end_controls_section();
	}

	protected function featured() {
		$imageURL = wp_get_attachment_image_url( get_post_thumbnail_id(), 'medium' );
		if( is_admin() && !wp_doing_ajax() ) {
			$imageURL = ELEMENTOR_ASSETS_URL."images/placeholder.png";
		}else{
			$imageURL = '{thumbnail_url}';
		}
		printf( '<div class="image-wrap" style="background-image:url(%1$s);"></div>', $imageURL );
	}

	protected function category_featured() {
		if( is_admin() && !wp_doing_ajax() ) {
			$imageURL = ELEMENTOR_ASSETS_URL."images/placeholder.png";
		}else{
			$imageURL = '{listing_category_featured_image}';
		}
		printf( '<div class="image-wrap" style="background-image:url(%1$s);"></div>', $imageURL );
	}

	protected function slider() {
		echo 'Slider';
	}

	protected function video() {
		echo 'Video';
	}

	protected function render() {
		$settings = $this->get_settings();
		$mediaType = $this->get_settings( 'media_type' );
		$this->add_render_attribute( 'wrap', Array(
			'class' => Array( 'jvbpd-block-media-wrap', 'type-' . $mediaType ),
		) );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
		<div class="media-overlay"></div>
		<?php if ($settings['link_detail']) { ?>
		<a href="{permalink_url}" <?php if ($settings['link_detail_blank']){ echo "target='_blank'"; } ?>class="media-link-detail"></a> <!-- a tag -->
		<?php } ?>
			<?php if($settings['badge_top_left_setting']) { ?>
				<div class="media-badges cblock-media-top-left"><?php echo '{' . $settings['badge_top_left_content'] . '}'; ?></div>
			<?php } ?>
			<?php if($settings['badge_top_right_setting']) { ?>
				<div class="media-badges cblock-media-top-right"><?php echo '{' . $settings['badge_top_right_content'] . '}'; ?></div>
			<?php } ?>
			<?php if($settings['badge_bottom_left_setting']) { ?>
				<div class="media-badges cblock-media-bottom-left"><?php echo '{' . $settings['badge_bottom_left_content'] . '}'; ?></div>
			<?php } ?>
			<?php if($settings['badge_bottom_right_setting']) { ?>
				<div class="media-badges cblock-media-bottom-right"><?php echo '{' . $settings['badge_bottom_right_content'] . '}'; ?></div>
			<?php } ?>

			<?php if($settings['custom_content']) { ?>
				<div class="media-custom custom-content"><?php echo $settings['custom_content']; ?></div>
			<?php }
			if( method_exists( $this, $mediaType ) ) {
				call_user_func_array( Array( $this, $mediaType ), Array() );
			} ?>
		</div>
		<?php
	}
}
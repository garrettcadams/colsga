<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Plugin;
if ( ! defined( 'ABSPATH' ) ) exit;

class jvbpd_map_list_toggle extends Widget_Base {

	public function get_name() { return 'jvbpd-map-list-toggle'; }

	public function get_title() { return 'Map / List Toggle'; }

	public function get_icon() { return 'eicon-button'; }

	public function get_categories() { return Array( 'jvbpd-map-page' ); }

	protected function _register_controls() {

		$types = Array(
			'map' => esc_html__( "Map", 'jvfrmtd' ),
			'list' => esc_html__( "List", 'jvfrmtd' ),
		);

		$this->start_controls_section( 'section_general', Array(
			'label' => esc_html__( "Toggle Setting", 'jvfrmtd' ),
		) );
			$this->add_control( 'first_toggle', Array(
				'label' => esc_html__( "First to show", 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'map',
				'options' => $types,
			) );

			$this->start_controls_tabs( 'tabs_toggle_items' );
			foreach( $types as $type => $typeLabel ) {
				$this->start_controls_tab( 'tab_' . $type, Array(
					'label' => $typeLabel,
				) );
					$this->add_control( $type . '_class', Array(
						'label' => esc_html__( "Class", 'jvfrmtd' ),
						'type' => Controls_Manager::TEXT,
						'placeholder' => __( 'Please add a class', 'jvfrmtd' ),
					) );

					$this->add_control( $type . '_text', Array(
						'label' => esc_html__( "Text", 'jvfrmtd' ),
						'type' => Controls_Manager::TEXT,
						'placeholder' => __( 'Please add a text if you need', 'jvfrmtd' ),
					) );

					// require  jvbpdCore()->elementor_path . '/jv-icons.php';
					$this->add_control( $type . '_icon', [
						'label' => __( 'Icon', 'jvfrmtd' ),
						'type' => Controls_Manager::ICONS,
						'fa4compatibility' => $type . '_icon',
						'default' => Array(
							'value' => '',
							'library' => '',
						),
						/*
						'default' => '',
						'options' => get_jv_icons_options( $icons ),
						'include' => get_jv_icons( $icons ), */
						'description' => __('It may take time to load all icons', 'jvfrmtd'),
					] );

				$this->end_controls_tab();
			}
			$this->end_controls_tabs();
		$this->end_controls_section();


		$this->start_controls_section( 'section_icon', [
			'label' => __( 'Style', 'jvfrmtd' ),
		] );

			$this->add_responsive_control( 'item_align', [
				'label' => __( 'Item Alignment', 'jvfrmtd' ),
				'type' => Controls_Manager::CHOOSE,
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
					'{{WRAPPER}} .toggle-item' => 'text-align: {{VALUE}};',
				],
			] );


		//---------- Icon Style Tabs ---------//
		$this->add_control( 'icon_style_heading', [
			'label' => __( 'Icon Styles', 'jvfrmtd' ),
			'type' => Controls_Manager::HEADING,
			'separator' => 'after',
		] );

		$this->start_controls_tabs( 'icon_styles' );
		$this->start_controls_tab( 'icon_normal', [ 'label' => __( 'Normal', 'jvfrmtd' ) ] );

			$this->add_control( 'primary_color', [
				'label' => __( 'Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .toggle-item i' => 'color: {{VALUE}};',
				],
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
			] );

			$this->add_control( 'secondary_color', [
				'label' => __( 'Secondary Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'condition' => [
					'view!' => 'default',
				],
				'selectors' => [
					'{{WRAPPER}}.elementor-view-framed .elementor-icon' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}.elementor-view-stacked .elementor-icon' => 'color: {{VALUE}};',
				],
			] );

			$this->add_control( 'size', [
				'label' => __( 'Size', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size'	=> '10',
				],
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .toggle-item i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			] );

		$this->add_control( 'icon_spacing', [
			'label' => __( 'Spacing', 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'selectors' => [
				'{{WRAPPER}} .toggle-item:first-child' => 'margin-right: {{SIZE}}{{UNIT}};',
			],
			'range' => [
				'em' => [
					'min' => 0,
					'max' => 100,
				],
			],
		] );

		$this->add_control( 'rotate', [
			'label' => __( 'Rotate', 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'default' => [
				'size' => 0,
				'unit' => 'deg',
			],
			'selectors' => [
				'{{WRAPPER}} .toggle-item' => 'transform: rotate({{SIZE}}{{UNIT}});',
			],
		] );

		$this->add_control( 'border_width', [
			'label' => __( 'Border Width', 'jvfrmtd' ),
			'type' => Controls_Manager::DIMENSIONS,
			'selectors' => [
				'{{WRAPPER}} .elementor-icon' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'condition' => [
				'view' => 'framed',
			],
		] );

		$this->add_control( 'border_radius', [
			'label' => __( 'Border Radius', 'jvfrmtd' ),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors' => [
				'{{WRAPPER}} .elementor-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'condition' => [
				'view!' => 'default',
			],
		] );
		$this->end_controls_tab();

		$this->start_controls_tab( 'icon_hover', [ 'label' => __( 'Hover', 'jvfrmtd' ) ] );

		$this->add_control( 'hover_primary_color', [
			'label' => __( 'Color', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'default' => '',
			'selectors' => [
				'{{WRAPPER}} .toggle-item i:hover' => 'color: {{VALUE}}; border-color: {{VALUE}};',
			],
		] );


		$this->add_control( 'hover_animation', [
			'label' => __( 'Hover Animation', 'jvfrmtd' ),
			'type' => Controls_Manager::HOVER_ANIMATION,
		] );

		$this->end_controls_tab();

		$this->start_controls_tab( 'icon_actived', [ 'label' => __( 'Actived', 'jvfrmtd' ) ] );

		$this->add_control( 'actived_primary_color', [
			'label' => __( 'Color', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'default' => '',
			'selectors' => [
				'{{WRAPPER}} .toggle-item.active i' => 'color: {{VALUE}}; border-color: {{VALUE}};',
			],
		] );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		//---------- Icon Style Tabs ---------//
		$this->end_controls_section();


		$this->start_controls_section(
			'toggle_text_style',
			[
				'label' => __( 'Text Style','jvfrmtd'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'toggle_typography',
			'selector' => '{{WRAPPER}} .toggle-item',
			'scheme' => Scheme_Typography::TYPOGRAPHY_1,
		] );

		$this->add_control(
			'toggle_text_color',
			[
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#454545',
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .toggle-item' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'toggle_button_style',
			[
				'label' => __( 'Button Style','jvfrmtd'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control( 'button_align', [
			'label' => __( 'Alignment', 'jvfrmtd' ),
			'type' => Controls_Manager::CHOOSE,
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
				'{{WRAPPER}} .jvbpd-map-list-toogle-wrap' => 'text-align: {{VALUE}};',
			],
		] );

		$this->add_responsive_control(
		'toggle_button_height',
			[
				'label' => __( 'Button Height', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
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
					'{{WRAPPER}} .toggle-item' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
		'toggle_button_width',
			[
				'label' => __( 'Button Width', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
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
					'{{WRAPPER}} .toggle-item' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'toggle_button_styles' );
			$this->start_controls_tab( 'toggle_button_normal', [ 'label' => __( 'Normal', 'jvfrmtd' ) ] );

				$this->add_control(
					'toggle_nomal_bg_color',
					[
						'label' => __( 'Background Color', 'jvfrmtd' ),
						'type' => Controls_Manager::COLOR,
						'scheme' => [
							'type' => Scheme_Color::get_type(),
							'value' => Scheme_Color::COLOR_1,
						],
						'selectors' => [
							'{{WRAPPER}} .toggle-item' => 'background-color: {{VALUE}}',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'toggle_hover', [ 'label' => __( 'Hover', 'jvfrmtd' ) ] );

				$this->add_control(
					'toggle_hover_bg_color',
					[
						'label' => __( 'Background Color', 'jvfrmtd' ),
						'type' => Controls_Manager::COLOR,
						'scheme' => [
							'type' => Scheme_Color::get_type(),
							'value' => Scheme_Color::COLOR_1,
						],
						'selectors' => [
							'{{WRAPPER}} .toggle-item:hover' => 'background-color: {{VALUE}}',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'toggle_select', [ 'label' => __( 'select', 'jvfrmtd' ) ] );

				$this->add_control(
					'toggle_select_bg_color',
					[
						'label' => __( 'Background Color', 'jvfrmtd' ),
						'type' => Controls_Manager::COLOR,
						'scheme' => [
							'type' => Scheme_Color::get_type(),
							'value' => Scheme_Color::COLOR_1,
						],
						'selectors' => [
							'{{WRAPPER}} .toggle-item.active' => 'background-color: {{VALUE}}',
						],
					]
				);

			$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {
		if(null==($this->get_settings('map_class')) || empty($this->get_settings('map_class')) || null==($this->get_settings('list_class')) || empty($this->get_settings('list_class'))){
				return;
		}

		$this->add_render_attribute( 'wrap', Array(
			'class' => 'jvbpd-map-list-grid-toogle-wrap',
		) );

		if ( ! empty( $settings['hover_animation'] ) ) {
			$this->add_render_attribute( 'icon-wrapper', 'class', 'elementor-animation-' . $settings['hover_animation'] );
		}
		foreach( Array( 'list', 'map' ) as $type ) {
			$this->add_render_attribute( $type, Array(
				'class' => Array( 'toggle-item', 'type-' . $type, $type . '_toggle' ),
				'data-type' => $type,
			) );
			if( $type == $this->get_settings( 'first_toggle' ) ) {
				$this->add_render_attribute( $type, array( 'class' => 'active' ) );
			}
		}  ?>
		<div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
			<div <?php echo $this->get_render_attribute_string( 'map' ); ?>>
				<i class="<?php echo $this->get_settings('map_icon'); ?>"></i>
				<?php echo $this->get_settings('map_text'); ?>
			</div>
			<div <?php echo $this->get_render_attribute_string( 'list' ); ?>>
				<i class="<?php echo $this->get_settings('list_icon'); ?>"></i>
				<?php echo $this->get_settings('list_text'); ?>
			</div>
		</div>

		<?php
		$map_toggle_class = $this->get_settings('map_class') ?: $this->get_settings('map_class');
		$list_toggle_class = $this->get_settings('list_class') ?: $this->get_settings('list_class');

		$first_toggle = $this->get_settings('first_toggle');
		if($first_toggle == "map"){
			$init_hide_layout = $list_toggle_class;
			$init_show_layout = $map_toggle_class;
		}else{
			$init_hide_layout = $map_toggle_class;
			$init_show_layout = $list_toggle_class;
		}

		if ( is_admin() ) {
			$init_hide_layout = "not_hiding_preview_mode";
		}

		if(null!==($map_toggle_class) && !empty($map_toggle_class) && null!==($list_toggle_class) && !empty($list_toggle_class)){
			?>

			<script type="text/javascript">
				( function( $ ) {
					$(document).ready(function(){
						$(".<?php echo $init_hide_layout;?>").hide();
						$(".<?php echo $init_show_layout;?>").show();

						$('.list_toggle').click(function(){
							$(".<?php echo $map_toggle_class; ?>").hide();
							$(".<?php echo $list_toggle_class; ?>").show();
						});

						$('.map_toggle').click(function(){
							$(".<?php echo $list_toggle_class; ?>").hide();
							$(".<?php echo $map_toggle_class; ?>").show();
						});
					});
				})(jQuery);
			</script>
		<?php
		}
	}

}
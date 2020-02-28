<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Scheme_Color;

if ( ! defined( 'ABSPATH' ) ) exit;

class jvbpd_map_list_grid_toggle extends Widget_Base {

	public function get_name() { return 'jvbpd-map-list-grid-toggle'; }

	public function get_title() { return 'List/Grid Toggle'; }

	public function get_icon() { return 'eicon-button'; }

	public function get_categories() { return Array( 'jvbpd-map-page' ); }

	protected function _register_controls() {

		$types = Array(
			'grid' => esc_html__( "Grid", 'jvfrmtd' ),
			'list' => esc_html__( "List", 'jvfrmtd' ),
		);

		$this->start_controls_section( 'section_general', Array(
			'label' => esc_html__( "Toggle Setting", 'jvfrmtd' ),
		) );
			$this->add_control( 'first_toggle', Array(
				'label' => esc_html__( "First type", 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => $types,
			) );

			$this->start_controls_tabs( 'tabs_toggle_items' );
			foreach( $types as $type => $typeLabel ) {
				$this->start_controls_tab( 'tab_' . $type, Array(
					'label' => $typeLabel,
				) );
					$this->add_control( $type . '_module', Array(
						'label' => esc_html__( "Module", 'jvfrmtd' ),
						'type' => Controls_Manager::SELECT2,
						'options' => jvbpd_elements_tools()->getModuleIDs(),

					) );
					$this->add_group_control(
						\jvbpd_group_block_style::get_type(),
						Array(
							'name' => $type,
							'label' => esc_html__( "Block", 'jvfrmtd' ),
							'fields' => Array( 'columns' ),
						)
					);

					$this->add_control( $type . '__icon', [
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


		/* Disabled Masonry
		$this->start_controls_section( 'section_masonry_settings', Array(
			'label' => esc_html__( 'Masonry Settings', 'jvfrmtd' ),
		) );
			$this->add_control( 'use_masonry', array(
				'type' => Controls_Manager::SWITCHER,
				'label' => esc_html__( 'Use Masonry', 'jvfrmtd' ),
				'frontend_available' => true,
			) );
			$aniOptions = Array();
			for($aniID=1;$aniID<=11;$aniID++){
				$aniOptions[$aniID] = sprintf(esc_html__("Effect %s", 'jvfrmtd'), $aniID);
			}
			$this->add_control( 'masonry_ani', Array(
				'label' => esc_html__( "Animation type", 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 1,
				'options' => $aniOptions,
				'condition' => Array( 'use_masonry' => 'yes', ),
				'frontend_available' => true,
			));
			$this->add_responsive_control('masonry_cols', Array(
				'label' => esc_html__( "Columns", 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'options' => jvbpd_elements_tools()->getColumnsOption(1, 4),
				'devices' => Array( 'desktop', 'tablet', 'mobile' ),
				'desktop_default' => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'frontend_available' => true,
				'condition' => Array( 'use_masonry' => 'yes', ),
			));
		$this->end_controls_section();
		*/

		$this->start_controls_section( 'section_icon', [
			'label' => __( 'Style', 'jvfrmtd' ),
		] );

			$this->add_responsive_control( 'icon_align', [
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
					'{{WRAPPER}} .elementor-widget-container' => 'text-align: {{VALUE}};',
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
	}

	protected function render() {
		$this->add_render_attribute( 'wrap', Array(
			'class' => 'jvbpd-map-list-grid-toogle-wrap',
		) );

		if ( ! empty( $settings['hover_animation'] ) ) {
			$this->add_render_attribute( 'icon-wrapper', 'class', 'elementor-animation-' . $settings['hover_animation'] );
		}
		foreach( Array( 'list', 'grid' ) as $type ) {
			$this->add_render_attribute( $type, Array(
				'class' => Array( 'toggle-item', 'type-' . $type ),
				'data-type' => $type,
				'data-module' => $this->get_settings( $type . '_module' ),
				'data-columns' => $this->get_settings( $type . '_columns' )
			) );
			if( $type == $this->get_settings( 'first_toggle' ) ) {
				$this->add_render_attribute( $type, array( 'class' => 'active' ) );
			}
		}  ?>
		<div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
			<div <?php echo $this->get_render_attribute_string( 'grid' ); ?>>
				<i class="<?php echo $this->get_settings('grid_icon'); ?>"></i>
			</div>
			<div <?php echo $this->get_render_attribute_string( 'list' ); ?>>
				<i class="<?php echo $this->get_settings('list_icon'); ?>"></i>
			</div>
		</div>
		<?php
	}

}
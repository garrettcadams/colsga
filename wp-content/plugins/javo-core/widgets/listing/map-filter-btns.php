<?php
/**
Widget Name: Map filter buttons ( Map type ) widget
Author: Javo
Version: 1.0.0.0
*/

namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;

use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Scheme_Typography;

if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly


class jvbpd_map_filter_btns extends Widget_Base {

	public function get_name() {
		return 'jvbpd-map-rating-btn';
	}

	public function get_title() {
		return 'Filter Buttons(Map Type)';   // title to show on elementor
	}

	public function get_icon() {
		return 'fa fa-list-ul';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-map-page' ];    // category of the widget
	}

    protected function _register_controls() {

        $this->start_controls_section(
			'section_general',
			array(
				'label' => esc_html__( 'Map Filter Buttons Note', 'jvfrmtd' ),
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
					'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-filter-buttons-for-map//" style="color:#fff;"> ' .
					esc_html__( 'Documentation', 'jvfrmtd' ) .
					'</a></li><li>&nbsp;</li>'.
					'<li class="notice">'.
					esc_html__('This widget is for only map page.', 'jvfrmtd').
					'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-notice/" style="color:#fff;"> ' .
					esc_html__( 'Detail', 'jvfrmtd' ) .
					'</a><br/></li><li>&nbsp;</li><li>'.
					esc_html__( 'Please do not use in other pages.', 'jvfrmtd' ) .
					'</li></ul></div>'
				)
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'section_content', Array(
			'label' => esc_html__( 'Filter Setting', 'jvfrmtd' ),   //section name for controler view
		) );

		$repeater = new Repeater();
		$repeater->add_control( 'callback', Array(
			'label' => __( 'Select a Button', 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT,
			'options' => Array(
				'rating' => __('Rating', 'jvfrmtd'),
				'sort' => __('Sort by', 'jvfrmtd'),
				'most_reviewed' => __('Most Reviewed', 'jvfrmtd'),
				'favroites' => __('Favorites', 'jvfrmtd'),
				'open_hour' => __('Open Now', 'jvfrmtd'),
				'module_type_switcher' => __('Module Type Switcher', 'jvfrmtd'),
				'near_me' => __('Near Me', 'jvfrmtd'),
				'price_range' => __('Price Range', 'jvfrmtd'),
			),
		) );

		$repeater->add_control( 'filter_title', Array(
			'label' => esc_html__( "Filter Title", 'jvfrmtd' ),
			'type' => Controls_Manager::TEXT,
			'default' => '',
			'label_block' => true,
		) );

		$types = Array(
			'grid' => esc_html__( "Grid", 'jvfrmtd' ),
			'list' => esc_html__( "List", 'jvfrmtd' ),
		);

		$repeater->add_control( 'first_toggle', Array(
			'label' => esc_html__( "First type", 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT,
			'default' => 'grid',
			'options' => $types,
			'condition' => Array( 'callback' => 'module_type_switcher' ),
		) );

		$repeater->start_controls_tabs( 'tabs_toggle_items' );
		foreach( $types as $type => $typeLabel ) {
			$repeater->start_controls_tab( 'tab_' . $type, Array(
				'label' => $typeLabel,
				'condition' => Array( 'callback' => 'module_type_switcher' ),
			) );
				$repeater->add_control( $type . '_module', Array(
					'label' => esc_html__( "Module", 'jvfrmtd' ),
					'type' => Controls_Manager::SELECT2,
					'options' => jvbpd_elements_tools()->getModuleIDs(),
					'condition' => Array( 'callback' => 'module_type_switcher' ),
				) );
				$repeater->add_group_control(
					\jvbpd_group_block_style::get_type(),
					Array(
						'name' => $type,
						'label' => esc_html__( "Block", 'jvfrmtd' ),
						'fields' => Array( 'columns' ),
						'condition' => Array( 'callback' => 'module_type_switcher' ),
					)
				);

				// require  jvbpdCore()->elementor_path . '/jv-icons.php';
				$repeater->add_control( $type . '__icon', [
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
					'condition' => Array( 'callback' => 'module_type_switcher' ),
					'description' => __('It may take time to load all icons', 'jvfrmtd'),
				] );

			$repeater->end_controls_tab();
		}
		$repeater->end_controls_tabs();

		$this->add_control( 'filters',
			Array(
				'label' => __( 'Menu Filters', 'jvfrmtd' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => array_values( $repeater->get_controls() ),
				'title_field' => 'Button : {{{ filter_title }}}',
			)
		);

        $this->end_controls_section();

		/** Filter Style */
		$this->start_controls_section(
		'filter_style',
				[
					'label' => __( 'Filter Style','jvfrmtd'),
					'tab' => Controls_Manager::TAB_STYLE,
				]
			);

		$this->add_responsive_control('filter_align',
		[
			'label'         => esc_html__( 'Alignment', 'jvfrmtd' ),
			'type'          => Controls_Manager::CHOOSE,
			'options'       => [
				'flex-start'      => [
					'title'=> esc_html__( 'Left', 'jvfrmtd' ),
					'icon' => 'fa fa-align-left',
					],
				'center'    => [
					'title'=> esc_html__( 'Center', 'jvfrmtd' ),
					'icon' => 'fa fa-align-center',
					],
				'flex-end'     => [
					'title'=> esc_html__( 'Right', 'jvfrmtd' ),
					'icon' => 'fa fa-align-right',
					],
				],
			'default'       => 'left',
			'selectors'     => [
				'{{WRAPPER}} .map-filter-menu .btn' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_submenu_item_style' );
		$this->start_controls_tab(
			'btn_normal',
			[
				'label' => __( 'Normal', 'jvfrmtd' ),
			]
		);

			$this->add_group_control( Group_Control_Typography::get_type(), [
					'name' => 'value_typography',
					'scheme' => Scheme_Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .map-filter-menu .btn-group.menu-item button.btn',
				]
			);

			$this->add_control(
			'title_color',
				[
					'label' => __( 'Text Color', 'jvfrmtd' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#3c3d42',
					'scheme' => [
						'type' => Scheme_Color::get_type(),
						'value' => Scheme_Color::COLOR_1,
					],
					'selectors' => [
						'{{WRAPPER}} .map-filter-menu .btn-group.menu-item button.btn' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
			'btn_background_color',
				[
					'label' => __( 'Background Color', 'jvfrmtd' ),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Scheme_Color::get_type(),
						'value' => Scheme_Color::COLOR_1,
					],
					'default' => 'transparent',
					'selectors' => [
						'{{WRAPPER}} .map-filter-menu .btn-group.menu-item button.btn' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'filter_border',
					'default' => [
						'type' => 'solid',
						'top' => 0,
						'right' => 0,
						'bottom' => 0,
						'left' => 0,
					],
					'selector' => '{{WRAPPER}} .map-filter-menu .btn-group.menu-item button.btn',
				]
			);

			$this->add_responsive_control(
				'btn_radius',
				[
					'label'      => esc_html__( 'Button Radius', 'jvfrmtd' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%',],
					'default'	   => [
						'top' => 0,
						'right' => 0,
						'bottom' => 0,
						'left' => 0,
					],
					'selectors'  => [
						'{{WRAPPER}} .map-filter-menu .btn-group.menu-item button.btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'btn_padding',
				[
					'label'      => esc_html__( 'Button Padding', 'jvfrmtd' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'default'	   => [
						'top' => 0,
						'right' => 0,
						'bottom' => 0,
						'left' => 0,
					],
					'selectors'  => [
						'{{WRAPPER}} .map-filter-menu .btn-group.menu-item button.btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_tab(); // btn normal

		$this->start_controls_tab(
			'btn_hover',
			[
				'label' => __( 'Hover', 'jvfrmtd' ),
			]
		);

			$this->add_group_control( Group_Control_Typography::get_type(), [
					'name' => 'value_typography_hover',
					'scheme' => Scheme_Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .map-filter-menu .btn-group.menu-item button.btn:hover',
				]
			);

			$this->add_control(
			'title_color_hover',
				[
					'label' => __( 'Text Color Hover', 'jvfrmtd' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#5F6166',
					'scheme' => [
						'type' => Scheme_Color::get_type(),
						'value' => Scheme_Color::COLOR_1,
					],
					'selectors' => [
						'{{WRAPPER}} .map-filter-menu .btn-group.menu-item button.btn:hover' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
			'btn_background_color_hover',
				[
					'label' => __( 'Background Color Hover', 'jvfrmtd' ),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Scheme_Color::get_type(),
						'value' => Scheme_Color::COLOR_1,
					],
					'default' => 'transparent',
					'selectors' => [
						'{{WRAPPER}} .map-filter-menu .btn-group.menu-item button.btn:hover' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'filter_border_hover',
					'default' => [
						'type' => 'solid',
						'top' => 0,
						'right' => 0,
						'bottom' => 0,
						'left' => 0,
					],
					'selector' => '{{WRAPPER}} .map-filter-menu .btn-group.menu-item button.btn:hover',
				]
			);

			$this->add_responsive_control(
				'btn_radius_hover',
				[
					'label'      => esc_html__( 'Button Radius Hover', 'jvfrmtd' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%',],
					'default'	   => [
						'top' => 0,
						'right' => 0,
						'bottom' => 0,
						'left' => 0,
					],
					'selectors'  => [
						'{{WRAPPER}} .map-filter-menu .btn-group.menu-item button.btn:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'btn_padding_hover',
				[
					'label'      => esc_html__( 'Button Padding Hover', 'jvfrmtd' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors'  => [
						'{{WRAPPER}} .map-filter-menu .btn-group.menu-item button.btn:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_tab(); // btn hover


		$this->start_controls_tab(
			'btn_focus',
			[
				'label' => __( 'Selected', 'jvfrmtd' ),
			]
		);

			$this->add_group_control( Group_Control_Typography::get_type(), [
					'name' => 'value_typography_select',
					'scheme' => Scheme_Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .map-filter-menu .btn-group.menu-item button.btn.active',
				]
			);

			$this->add_control(
			'title_color_select',
				[
					'label' => __( 'Text Color Selected', 'jvfrmtd' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#6570b8',
					'scheme' => [
						'type' => Scheme_Color::get_type(),
						'value' => Scheme_Color::COLOR_1,
					],
					'selectors' => [
						'{{WRAPPER}} .map-filter-menu .btn-group.menu-item button.btn.active' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
			'btn_background_color_select',
				[
					'label' => __( 'Background Color Select', 'jvfrmtd' ),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Scheme_Color::get_type(),
						'value' => Scheme_Color::COLOR_1,
					],
					'default' => 'transparent',
					'selectors' => [
						'{{WRAPPER}} .map-filter-menu .btn-group.menu-item button.btn.active' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'filter_border_select',
					'default' => [
						'type' => 'solid',
						'top' => 0,
						'right' => 0,
						'bottom' => 0,
						'left' => 0,
					],
					'selector' => '{{WRAPPER}} .map-filter-menu .btn-group.menu-item button.btn.active',
				]
			);

			$this->add_responsive_control(
				'btn_radius_select',
				[
					'label'      => esc_html__( 'Button Radius Select', 'jvfrmtd' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%',],
					'default'	   => [
						'top' => 0,
						'right' => 0,
						'bottom' => 0,
						'left' => 0,
					],
					'selectors'  => [
						'{{WRAPPER}} .map-filter-menu .btn-group.menu-item button.btn.active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'btn_padding_select',
				[
					'label'      => esc_html__( 'Button Padding', 'jvfrmtd' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors'  => [
						'{{WRAPPER}} .map-filter-menu .btn-group.menu-item button.btn.active' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_tab(); // btn focus
		$this->end_controls_tabs();



		$this->add_responsive_control(
		'btn_space',
			[
				'label' => __( 'Filter Space', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 15,
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
					'{{WRAPPER}} .btn-group.menu-item' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
		'btn_width',
			[
				'label' => __( 'Button Width', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 19,
					'unit' => '%',
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
					'{{WRAPPER}} .btn-group.menu-item' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
        $this->end_controls_section();

		$this->start_controls_section(
			'dropdown_filter_style',
				[
						'label' => __( 'Drop Down Filter Style','jvfrmtd'),
						'tab' => Controls_Manager::TAB_STYLE,
				]
		);

		$this->start_controls_tabs( 'tabs_dropdown_style' );
		$this->start_controls_tab(
			'dropdown_filter_nomal',
			[
				'label' => __( 'Normal', 'jvfrmtd' ),
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(), [
				'name' => 'dropdown_nomal_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .dropdown-item',
			]
		);

		$this->add_control(
		'dropdown_nomal_color',
			[
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#333',
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .dropdown-item' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'dropdown_nomal_bg_color',
				[
					'label' => __( 'Background Color', 'jvfrmtd' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#fff',
					'scheme' => [
						'type' => Scheme_Color::get_type(),
						'value' => Scheme_Color::COLOR_1,
					],
					'selectors' => [
						'{{WRAPPER}} .dropdown-item' => 'background-color: {{VALUE}}',
					],
				]
			);
		$this->end_controls_tab(); // dropdown filter nomal

		$this->start_controls_tab(
			'dropdown_filter_hover',
			[
				'label' => __( 'Hover', 'jvfrmtd' ),
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(), [
				'name' => 'dropdown_hover_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .dropdown-item:hover',
			]
		);

		$this->add_control(
		'dropdown_hover_color',
			[
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#333',
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .dropdown-item:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'dropdown_hover_bg_color',
				[
					'label' => __( 'Background Color', 'jvfrmtd' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#fff',
					'scheme' => [
						'type' => Scheme_Color::get_type(),
						'value' => Scheme_Color::COLOR_1,
					],
					'selectors' => [
						'{{WRAPPER}} .dropdown-item:hover' => 'background-color: {{VALUE}}',
					],
				]
			);
		$this->end_controls_tab(); // dropdown filter hover

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'dropdown_filter_padding',
			[
				'label'      => esc_html__( 'Dropdown Filter Padding', 'jvfrmtd' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .dropdown-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'dropdown_filter_border',
				'label' => esc_html__( 'Dropdown Wrap Border', 'jvfrmtd' ),
				'selector' => '{{WRAPPER}} .dropdown-menu',
			]
		);

		$this->add_responsive_control(
			'dropdown_filter_radius',
			[
				'label'      => esc_html__( 'Dropdown Wrap Radius', 'jvfrmtd' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%',],
				'selectors'  => [
					'{{WRAPPER}} .dropdown-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
    }

    protected function render() {

		$settings = $this->get_settings();
		wp_reset_postdata();
		$isPreviewMode = is_admin();
		$isPreviewMode = false;

		if( $isPreviewMode) {
			$previewBaseURL = jvbpdCore()->assets_url . '/images/elementor/listipia/';
			$previewURL = $previewBaseURL . 'single-button.jpg';
			printf( '<img src="%s">', esc_url_raw( $previewURL ) );
		}else{
			$this->getContent( $settings, get_post() );
		}
    }

	public function rating( $settings=Array() ) {
		if( function_exists( 'lv_directoryReview' ) ) {
			?>
			<div class="btn-group menu-item" data-menu-filter="rating">
				<button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<?php esc_html_e( "Rating", 'jvfrmtd' ); ?>
				</button>
				<div class="dropdown-menu">
					<div class="dropdown-item" data-value="high"><?php esc_html_e( "By High Rated", 'jvfrmtd'); ?></div>
					<div class="dropdown-item" data-value="low"><?php esc_html_e( "By Low Rated", 'jvfrmtd'); ?></div>
					<div class="dropdown-divider"></div>
					<?php
					for( $intRateNumeric=5; $intRateNumeric>=1;$intRateNumeric-- ) {
						printf( '<div class="dropdown-item" data-value="%1$s">%1$s</div>', $intRateNumeric );
					} ?>
				</div>
			</div>
			<?php
		}
	}

	public function sort( $settings=Array() ) {
		$label = isset( $settings[ 'filter_title' ] ) ? $settings[ 'filter_title' ] : esc_html__( "Sort By", 'jvfrmtd' );
		?>
		<div class="btn-group menu-item" data-menu-filter="order">
			<button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<?php echo esc_html( $label ); ?>
			</button>
			<div class="dropdown-menu">
				<div class="dropdown-item desc" data-value="name" data-type="desc">
					<?php esc_html_e( "Name", 'jvfrmtd'); ?>
					<span class="glyphicon glyphicon-arrow-up pull-right asc"></span>
					<span class="glyphicon glyphicon-arrow-down pull-right desc"></span>
				</div>
				<div class="dropdown-item desc" data-value="date" data-type="desc">
					<?php esc_html_e( "Date", 'jvfrmtd'); ?>
					<span class="glyphicon glyphicon-arrow-up pull-right asc"></span>
					<span class="glyphicon glyphicon-arrow-down pull-right desc"></span>
				</div>
			</div>
		</div>
		<?php
	}

	public function most_reviewed( $settings=Array() ) {
		$label = isset( $settings[ 'filter_title' ] ) ? $settings[ 'filter_title' ] : esc_html__( "Most reviewed", 'jvfrmtd' );
		?>
		<div class="btn-group menu-item" data-menu-filter="reviewed">
			<button class="btn btn-sm" type="button" data-toggle="button" data-value="1" aria-haspopup="true" aria-expanded="false">
				<?php echo esc_html( $label ); ?>
			</button>
		</div>
		<?php
	}

	public function favroites( $settings=Array() ) {
		$label = isset( $settings[ 'filter_title' ] ) ? $settings[ 'filter_title' ] : esc_html__( "Favorites", 'jvfrmtd' );
		?>
		<div class="btn-group menu-item" data-menu-filter="favorite">
			<button class="btn btn-sm" type="button" data-toggle="button" data-value="1" aria-haspopup="true" aria-expanded="false">
				<?php echo esc_html( $label ); ?>
			</button>
		</div>
		<?php
	}

	public function near_me( $settings=Array() ) {
		$label = isset( $settings[ 'filter_title' ] ) ? $settings[ 'filter_title' ] : esc_html__( "Near Me", 'jvfrmtd' );
		?>
		<div class="btn-group menu-item" data-menu-filter="nearme">
			<button class="btn dropdown-toggle btn-sm" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<?php echo esc_html( $label ); ?>
			</button>
			<div class="dropdown-menu">
				<div class="jvbpd-map-distance-bar-wrap">
					<button type="button" data-close="">
						<i class="fa fa-remove"></i>
					</button>
					<div class="javo-geoloc-slider"></div>
				</div>
			</div>
		</div>
		<?php
	}

	public function open_hour( $settings=Array() ) {
		$label = isset( $settings[ 'filter_title' ] ) ? $settings[ 'filter_title' ] : esc_html__( "Open now", 'jvfrmtd' );
		?>
		<div class="btn-group menu-item" data-menu-filter="openhour">
			<button class="btn btn-sm" type="button" data-toggle="button" data-value="1" aria-haspopup="true" aria-expanded="false">
				<?php echo esc_html( $label ); ?>
			</button>
		</div>
		<?php
	}

	public function price_range( $settings=Array() ) {
		$label = isset( $settings[ 'filter_title' ] ) ? $settings[ 'filter_title' ] : esc_html__( "Price Range", 'jvfrmtd' );
		?>
		<div class="btn-group menu-item" data-menu-filter="price_range">
			<button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<?php echo esc_html( $label ); ?>
			</button>
			<div class="dropdown-menu">
				<div class="dropdown-item" data-value=""><?php esc_html_e( "None", 'jvfrmtd'); ?></div>
				<div class="dropdown-item" data-value="inexpensivve"><?php esc_html_e( "$ - Inexpensive", 'jvfrmtd'); ?></div>
				<div class="dropdown-item" data-value="moderate"><?php esc_html_e( "$$ - Moderate", 'jvfrmtd'); ?></div>
				<div class="dropdown-item" data-value="pricey"><?php esc_html_e( "$$$ - Pricey", 'jvfrmtd'); ?></div>
				<div class="dropdown-item" data-value="ultra_high"><?php esc_html_e( "$$$$ - Ultra High", 'jvfrmtd'); ?></div>
			</div>
		</div>
		<?php
	}

	public function module_type_switcher( $settings=Array() ) {
		$this->add_render_attribute( 'wrap', Array(
			'class' => Array( 'btn-group', 'module-switcher' ),
			'data-toggle' => 'buttons',
		) );

		foreach( Array( 'list', 'grid' ) as $type ) {
			$this->add_render_attribute( $type, Array(
				'class' => Array( 'btn', 'type-' . $type ),
				'data-type' => $type,
				'data-module' => $settings[ $type . '_module' ],
				'data-columns' => $settings[ $type . '_columns' ],
			) );
			if( $type == $settings[ 'first_toggle' ] ) {
				$this->add_render_attribute( $type, array( 'class' => 'active' ) );
			}
		} ?>

		<div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
			<label <?php echo $this->get_render_attribute_string( 'grid' ); ?>>
				<input type="radio" name="module_switcher" value="grid" checked="checked">
				<span class='<?php echo $settings['grid_icon']; ?>'></span>
			</label>
			<label <?php echo $this->get_render_attribute_string( 'list' ); ?>>
				<input type="radio" name="module_switcher" value="list">
				<span class='<?php echo $settings['list_icon']; ?>'></span>
			</label>
		</div>
		<?php
	}

	public function getContent( $settings, $obj ) {
		?>
		<div class="map-filter-menu">
			<?php
			$arrCallBack = $settings[ 'filters' ];
			if( !empty( $arrCallBack ) && is_array( $arrCallBack ) ) {
				foreach( $arrCallBack as $strCallBack ) {
					if( method_exists( $this, $strCallBack[ 'callback' ] ) ) {
						call_user_func( Array( $this, $strCallBack[ 'callback' ] ), $strCallBack );
					}
				}
			}?>
		</div>
		<?php
	}
}
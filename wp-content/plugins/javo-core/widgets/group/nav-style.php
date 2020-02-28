<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists( 'Elementor\Group_control_Base' ) ) {
	return false;
}

class jvbpd_group_nav_menu extends Elementor\Group_Control_Base {

	protected static $fields;

	public static function get_type() { return 'jvbpd-group-nav-menu'; }

	public function is_jvbpd_menu( $params=Array() ) {
		$_return = false;
		$jvbpd_menus = Array( 'add-new-button', 'my-menu', 'my-notifications', 'right-sidebar-opener' );
		if( isset( $params[ 'menu_item' ] ) && is_object( $params[ 'menu_item' ] ) ) {
			if( isset( $params[ 'menu_item' ]->jv_menu ) && in_array( $params[ 'menu_item' ]->jv_menu, $jvbpd_menus ) ) {
				$_return = true;
			}
		}
		return $_return;
	}

	public function menu_type_select( &$output=Array(), $name='', $params=Array() ) {
		$output[$name . '_menu_type']=Array(
		 'label' => __( 'Menu Type', 'jvfrmtd' ),
		 'type' => Controls_Manager::SELECT,
		 'default' => 'normal',
		 'options' => [
			'normal'  => __( 'Normal', 'jvfrmtd' ),
			'mega' => __( 'Mega', 'jvfrmtd' ),
			'post-cat' => __( 'Post Category', 'jvfrmtd' ),
		 ],
		);
	}

	public function mega_menu( &$output=Array(), $name='', $params=Array() ) {
		$output[ $name . '_enable' ] = Array(
			'label' => esc_html__( "Use mega menu", 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
		);
	}

	public function mega_menu_columns_width( &$output=Array(), $name='', $params=Array() ) {
		$output[ $name . '_columns_width' ] = Array(
			'label' => esc_html__( "Each column width", 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'default' => Array( 'size' => 22, 'unit' => '%' ),
			'responsive' => true,
			'range' => Array(
				'px' => Array(
					'min' => 0,
					'max' => 2000,
					'step' => 1,
				),
				'%' => Array(
					'min' => 0,
					'max' => 100,
				),
			),
			'size_units' => Array( 'px', '%' ),
			//'condition'	=> [
				//'mega_menu_enable'=>'yes',
			//],
			'selectors' => Array(
				$this->getCurrentSelector( $params ) . ' .wide-nav-overlay li.menu-item-depth-1' => 'width: {{SIZE}}{{UNIT}};',
			),
		);
	}

	public function submenu( &$output=Array(), $name='', $params=Array() ) {
		$output[ $name . '_text_align' ] = Array(
			'label' => esc_html__( "Sub-menu text alignment", 'jvfrmtd' ),
			'type' => Controls_Manager::CHOOSE,
			'default' =>'left',
			'options' => Array(
				'left' => Array(
					'title' => __( 'Left', 'jvfrmtd' ),
					'icon' => 'fa fa-align-left',
				),
				'center' => Array(
					'title' => __( 'Center', 'jvfrmtd' ),
					'icon' => 'fa fa-align-center',
				),
				'right' => Array(
					'title' => __( 'Right', 'jvfrmtd' ),
					'icon' => 'fa fa-align-right',
				),
			),
			'selectors' => Array(
				$this->getCurrentSelector( $params ) . ' .wide-nav-overlay li.menu-item-depth-1' => 'text-align: {{VALUE}};',
			),
		);
	}

	public function mega_menu_width( &$output=Array(), $name='', $params=Array() ) {
		$output[ $name . '_mega_width' ] = Array(
			'label' => esc_html__( "mega menu width", 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'default' => Array( 'size' => 1000 ),
			'responsive' => true,
			'range' => Array(
				'px' => Array(
					'min' => 0,
					'max' => 2000,
					'step' => 1,
				),
				'%' => Array(
					'min' => 0,
					'max' => 100,
				),
			),
			'size_units' => Array( 'px', '%' ),
			'selectors' => Array(
				$this->getCurrentSelector( $params ) . ' .wide-nav-overlay' => 'width: {{SIZE}}{{UNIT}};',
			),
		);
	}

	public function mega_menu_left( &$output=Array(), $name='', $params=Array() ) {
		$output[ $name . '_mega_left' ] = Array(
			'label' => esc_html__( "Left position", 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'default' => Array(
				'size' => -400,
			),
			'responsive' => true,
			'range' => Array(
					'px' => Array(
							'min' => -1000,
							'max' => 1000,
							'step' => 1,
					),
					'%' => Array(
							'min' => 0,
							'max' => 100,
					),
			),
			'size_units' => Array( 'px', '%' ),
			'selectors' => Array(
				$this->getCurrentSelector( $params ) . ' .wide-nav-overlay' => 'left: {{SIZE}}{{UNIT}};',
			),
		);
	}

	public function background( &$output=Array(), $name='', $params=Array() ) {
		$output[ $name . '_color' ] = Array(
			'label' => esc_html__( "Background Color", 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'selectors' => Array(
				$this->getCurrentSelector( $params ) . ' ul.wide-nav-overlay' => 'background-color:{{VALUE}};'
			)
		);
		$output[ $name . '_image' ] = Array(
			'label' => esc_html__( "Background Image", 'jvfrmtd' ),
			'type' => Controls_Manager::MEDIA,
		);
	}

	public function icon( &$output=Array(), $name='', $params=Array() ) {
		$output[ $name . '_icon' ] = Array(
			'label' => esc_html__( "Menu Icon", 'jvfrmtd' ),
			'type' => Controls_Manager::ICON,
		);
	}

	public function block( &$output=Array(), $name='', $params=Array() ) {
		$output[ $name . '_enable' ] = Array(
			'label' => esc_html__( "Use post category", 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'selectors' => Array(
				$this->getCurrentSelector( $params ) . ' .javo-shortcode .shortcode-header .shortcode-nav' => 'padding: 0px;',
			),
		);

		$postTaxonomies_options = jvbpd_elements_tools()->get_taxonomies( 'post' );

		$output[ $name . '_taxonomy' ] = Array(
			'label' => esc_html__( "Category", 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT2,
			/*
			'condition' => Array(
				$name . '_enable' => 'yes'
			), */
			'options' => $postTaxonomies_options,
		);

		$output = jvbpd_elements_tools()->add_tax_term_control( false, 'post_%1$s_term', Array(
			'taxonomies' => array_keys( $postTaxonomies_options ),
			'parent' => $name . '_taxonomy',
			'label' => esc_html__( '%1$s Terms', 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT2,
			'repeat_items' => &$output,
			'is_group' => true,
		) );

		$output[ $name . '_filter_background' ] = Array(
			'label' => esc_html__( "Filter background color", 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'selectors' => Array(
				$this->getCurrentSelector( $params ) . ' .javo-shortcode .shortcode-header .shortcode-nav .filter-nav-item' => 'background-color: {{VALUE}};',

			),
		);

		$output[ $name . '_filter_background_hover' ] = Array(
			'label' => esc_html__( "Filter background hover color", 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'selectors' => Array(
				$this->getCurrentSelector( $params ) . ' .javo-shortcode .shortcode-header .shortcode-nav .filter-nav-item:hover' => 'background-color: {{VALUE}};',
			),
		);
	}

	public function getCurrentSelector( $param ) {
		$output = false;

		if( ! isset( $param[ 'menu_item' ] ) ) {
			return $output;
		}

		if( ! $param[ 'menu_item' ] instanceof \WP_Post ) {
			return $output;
		}

		$output = sprintf( '{{WRAPPER}} li#nav-menu-item-%1$s', $param[ 'menu_item' ]->ID );
		return $output;
	}

	public function custom_menu( &$output=Array(), $name='', $params=Array() ) {

		if( ! $this->is_jvbpd_menu( $params ) ) {
			return false;
		}

		$menu_item = $params[ 'menu_item' ];
		$output_classes = $this->getCurrentSelector( $params );

		$classes = (Array) $menu_item->classes;

		foreach( $classes as $cssClass ) {
			$output_classes .= sprintf( '.%s', $cssClass );
		}

		$css_border_radius = $css_background_color = $output_classes;

		switch( $menu_item->jv_menu ) {
			case 'add-new-button' :
				$css_border_radius = $css_background_color = $output_classes . ' button.btn.btn-sm.btn-primary';
				break;

			case 'my-menu' :
				$css_border_radius = $output_classes . ' .header-userinfo > img';
				break;

			case 'my-notifications' :
				$css_border_radius = $css_background_color = $output_classes . ' a.dropdown-toggle > i';
				break;
		}

		$output[ $name . '_border_radius' ] = Array(
			'label' => esc_html__( "Menu Border Radius", 'jvfrmtd' ),
			'type' => Controls_Manager::DIMENSIONS,
			'default' => Array(
					'top' => 50,
					'right' => 50,
					'bottom' => 50,
					'left' => 50,
					'unit' => 'px'
			),
			'responsive' => true,
			'size_units' => [ 'px', '%', 'em' ],
			'selectors' => Array(
				$css_border_radius => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
		);

		if( in_array( $menu_item->jv_menu, Array( 'my-notifications' ) ) ) {
			$output[ $name . '_background_color' ] = Array(
				'label' => esc_html__( "Background Color", 'jvfrmtd' ),
				'default' => '#000000',
				'type' => Controls_Manager::COLOR,
				'selectors' => Array(
					$css_background_color => 'background-color: {{VALUE}};',
				),
			);
		}

		if( in_array( $menu_item->jv_menu, Array( 'my-notifications' ) ) ) {
			$output[ $name . '_padding' ] = Array(
				'label' => esc_html__( "Padding", 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'responsive' => true,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => Array(
					$css_border_radius => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			);

			$output[ $name . '_margin' ] = Array(
				'label' => esc_html__( "Margin", 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'responsive' => true,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => Array(
					$css_border_radius => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			);

			$output[ $name . '_font_color' ] = Array(
				'label' => esc_html__( "Font Color", 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => Array(
					$css_border_radius => 'color: {{VALUE}};',
				),
			);
		}

		if( in_array( $menu_item->jv_menu, Array( 'add-new-button', ) ) ) {

			$output[ $name . '_background_color' ] = Array(
				'label' => esc_html__( "Background Color", 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => Array(
					$css_background_color => 'background-color: {{VALUE}};',
				),
			);

			$output[ $name . '_label_padding' ] = Array(
				'label' => esc_html__( "Label padding", 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'responsive' => true,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => Array(
					$css_border_radius . ' > span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			);

			$output[ $name . '_padding' ] = Array(
				'label' => esc_html__( "Padding", 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'responsive' => true,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => Array(
					'top' => 3,
					'right' => 20,
					'bottom' => 3,
					'left' => 20,
					'unit' => 'px'
				),
				'selectors' => Array(
					$css_border_radius => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			);

			$output[ $name . '_margin' ] = Array(
				'label' => esc_html__( "Margin", 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'responsive' => true,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => Array(
					$css_border_radius => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			);

			$output[ $name . '_font_color' ] = Array(
				'label' => esc_html__( "Font Color", 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => Array(
					$css_border_radius => 'color: {{VALUE}};',
				),
			);
		}

		if( in_array( $menu_item->jv_menu, Array('right-sidebar-opener' ) ) ) {
			$output[ $name . '_padding' ] = Array(
				'label' => esc_html__( "Padding", 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'responsive' => true,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => Array(
					$css_border_radius => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			);

			$output[ $name . '_icon_size' ] = Array(
				'label' => esc_html__( 'Opener Icon Size', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				'	%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					$output_classes . ' .overlay-sidebar-opener i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			);
		}

		if( in_array( $menu_item->jv_menu, Array( 'my-menu' ) ) ) {
			$output[ $name . '_avatar_radius' ] = Array(
				'label' => esc_html__( "Profile Radius on dropdown menu", 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'responsive' => true,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => Array(
					$output_classes . ' li.my-menu-logged-user-info .user-info-avatar > img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			);

			$output[ $name . '_mymenu_background_color' ] = Array(
				'label' => esc_html__( "Background color on dropdown menu", 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => Array(
					$output_classes . ' ul' => 'background-color:{{VALUE}};',
					$output_classes . ' div.menu-my-user-menu-container > ul' => 'list-style:none; padding:0;',
					$output_classes . ' div.menu-my-user-menu-container > ul >li' => 'padding:5px;',
				),
			);
		}

		if( in_array( $menu_item->jv_menu, Array( 'add-new-button', 'my-notifications' ) ) ) {
			$output[ $name . '_dropdown_font_color' ] = Array(
				'label' => esc_html__( "Font color on dropdown menu", 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => Array(
					$output_classes . ' ul.dropdown-menu li > a' => 'color:{{VALUE}};',
					$output_classes . ' ul.collapse li > a' => 'color:{{VALUE}};',
				),
			);
		}

	}

	public function filter_fields() {
		$args = $this->get_args();
		if( isset( $args[ 'fields' ] ) && is_array( $args[ 'fields' ] ) ) {
			if( $this->is_jvbpd_menu( $args[ 'params' ] ) ) {
				$args_fields = array_diff( $args[ 'fields' ], Array( 'mega_menu', 'mega_menu_columns_width', 'mega_menu_left', 'mega_menu_width', 'block', 'background', 'submenu' ) );
			}else{
				$args_fields = $args[ 'fields' ];
			}

			foreach( $args_fields as $field ) {
				if( method_exists( $this, $field ) ) {
					call_user_func_array( Array( $this, $field ), Array( &$fields, $field, $args[ 'params' ] ) );
				}
			}
		}
		return $fields;
	}
	protected function init_fields() { return Array(); }
	protected function get_default_options() { return Array('popover' => true); }

}

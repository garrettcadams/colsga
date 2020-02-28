<?php
/**
 * Widget Name: Single amenities widget
 * Author: Javo
 * Version: 1.0.0.0
 */


namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


class jvbpd_single_amenities extends Widget_Base {

	public function get_name() { return 'jvbpd-single-amenities'; }
	public function get_title() { return 'Amenities (Single Listing)'; }
	public function get_icon() { return 'jvic-garage'; }
	public function get_categories() { return [ 'jvbpd-single-listing' ]; }

    protected function _register_controls() {

		$this->start_controls_section( 'section_general', Array(
			'label' => esc_html__( 'Amenities Single Page', 'jvfrmtd' ),
		) );

		$this->add_control( 'Des', Array(
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
		) );

		$this->add_control(
			'list_only_selected',
			[
				'label' => __( 'List only selected', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'label_on',
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'description' => __( 'Selected items only (yes) or Show all (no)', 'jvfrmtd' ),
				'return_value' => 'yes',
			]
		);

		/*
		$this->add_control( 'show_icons', [
			'label' => __( 'Show custom icons', 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => 'label_on',
			'label_on' => __( 'Yes', 'jvfrmtd' ),
			'label_off' => __( 'No', 'jvfrmtd' ),
			'return_value' => 'yes',
			'description' => __('Custom icons or default icons.<br/>Custom icons : you can upload icons in backend ( Listings > Amenities )', 'jvfrmtd'),
		] ); */

		$this->add_control('icon_type', Array(
			'label' => esc_html__("Icon Type", 'jvfrmtd'),
			'type' => Controls_Manager::SELECT,
			'default' => 'icon',
			'options' => Array(
				'' => esc_html__("Default", 'jvfrmtd'),
				'icon' => esc_html__("Custom Icon", 'jvfrmtd'),
				'image' => esc_html__("Custom Image Icon", 'jvfrmtd'),
			),
		));

		$this->end_controls_section();

		$this->start_controls_section( 'section_image_icon_style', Array(
			'label' => esc_html__( 'Icon Style', 'jvfrmtd' ),
			'condition' => [
				'icon_type'	=>	'image',
			],
		) );
			$this->add_control( 'image_icon_size', Array(
				'label' => __( 'Icon Size', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => Array(
					'size' => 14,
				),
				'range' => Array(
					'px' => Array(
						'min' => 0,
						'max' => 500,
						'step' => 1,
					),
					'%' => Array(
						'min' => 0,
						'max' => 100,
					),
				),
				'size_units' => Array('px', '%'),
				'selectors' => Array(
					'{{WRAPPER}} .lava-amenity img.amenities-icon' => 'width: {{SIZE}}{{UNIT}}; height:auto;',
				),
			) );
			$this->add_control( 'image_icon_spacing', Array(
				'label' => __( 'Icon Gap', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => Array(
					'size' => 5,
				),
				'range' => Array(
					'px' => Array(
						'min' => 0,
						'max' => 50,
						'step' => 1,
					),
					'%' => Array(
						'min' => 0,
						'max' => 100,
					),
				),
				'size_units' => Array('px', '%'),
				'selectors' => Array(
					'{{WRAPPER}} .lava-amenity img.amenities-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
				),
			) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_icon_style', Array(
			'label' => esc_html__( 'Icon Style', 'jvfrmtd' ),
			'condition' => [
				'icon_type'	=>	'icon',
			],
		) );

	/*
	   	$this->add_control(
			'vertical_layout',
			[
				'label' => __( 'Vertical ', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'return_value' => 'yes',
				'description' => __('Default : Horizon ( inline )', 'jvfrmtd'),
				'selectors' => [
					'{{WRAPPER}} #javo-item-amenities-section .lava-amenity >i' => 'max-width:100%; display:table-cell; text-align: center; vertical-align: middle;',
					'{{WRAPPER}} #lava-directory-amenities .lava-amenity' => 'display:block; text-align: center; border-radius:50%;',
				],
			]
		);
*/


		$this->add_control(
		'icon_bg_color',
			[
				'label' => __( 'Icon Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} #javo-item-amenities-section .lava-amenity >i' => 'color: {{VALUE}}',
				],
			]
		);


		$this->add_control(
		'icon_color',
			[
				'label' => __( 'Icon Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} #javo-item-amenities-section .lava-amenity >i' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icon_size',
			[
				'label' => __( 'Icon Size', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 14,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} #javo-item-amenities-section .lava-amenity >i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
		'icon_circle_size',
		[
			'label' => __( 'Icon circle size', 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'default' => [
				'size' => 30,
			],
			'range' => [
				'%' => [
					'min' => 0,
					'max' => 100,
				],
			],
			'size_units' => ['%' ],
			'selectors' => [
				'{{WRAPPER}} #javo-item-amenities-section .lava-amenity i' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
			],
			'description' =>  __( 'Icon outline, circle width, if you use background', 'jvfrmtd' ),
		]
	);

		$this->end_controls_section();

		$this->start_controls_section(
			'amenities_style',
			[
				'label' => __( 'Style','jvfrmtd'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'amenities_tab' );
		$this->start_controls_tab( 'selected', [ 'label' => __( 'selected', 'jvfrmtd' ) ] );

		$this->add_control(
			'amenities_selected_text_color',
			[
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#333333',
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} #javo-item-amenities-section #lava-directory-amenities .lava-amenity:not(.showall).active' => 'color: {{VALUE}}',
					'{{WRAPPER}} #javo-item-amenities-section #lava-directory-amenities .lava-amenity.showall > span' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'selected_typography',
			'selector' => '{{WRAPPER}} #javo-item-amenities-section #lava-directory-amenities .lava-amenity:not(.showall).active ,{{WRAPPER}} #javo-item-amenities-section #lava-directory-amenities .lava-amenity.showall > span',
			'scheme' => Scheme_Typography::TYPOGRAPHY_1,
		] );

		$this->end_controls_tab();

		$this->start_controls_tab( 'unselected', [ 'label' => __( 'Unselected', 'jvfrmtd' ) ] );

		$this->add_control(
			'amenities_unselected_text_color',
			[
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#cccccc',
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} #javo-item-amenities-section #lava-directory-amenities .lava-amenity:not(.showall)' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'unselected_typography',
			'selector' => '{{WRAPPER}} #javo-item-amenities-section #lava-directory-amenities .lava-amenity:not(.showall)',
			'scheme' => Scheme_Typography::TYPOGRAPHY_1,
		] );

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'amenities_width',
			[
				'label' => __( 'Width (%)', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 25,
					'unit' => '%',
				],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 300,
					],
				],
				'size_units' => ['%','px'],
				'selectors' => [
					'{{WRAPPER}} #javo-item-amenities-section .lava-amenity' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'amenities_padding',
			[
				'label'      => esc_html__( 'Amenities Wrap Padding', 'jvfrmtd' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} #lava-directory-amenities' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		 $this->end_controls_section();
    }

    protected function render() {

		jvbpd_elements_tools()->switch_preview_post();
		$settings = $this->get_settings();
		$isVisible = function_exists( 'lava_directory_amenities' );
		//wp_reset_postdata();

		$this->getContent( $settings, get_post() );
		jvbpd_elements_tools()->restore_preview_post();

	}

	public function getContent( $settings, $obj ) {
		$this->lava_directory_amenities( $obj->ID, Array(
				'container_before' => sprintf( '
				<div class="item-amenities" id="javo-item-amenities-section" data-jv-detail-nav>
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="expandable-content" >',
							esc_html__( "Amenities", 'jvfrmtd' )
				),
				'container_after' => '
							</div>
						</div><!-- panel-body -->
					</div>
				</div><!-- /#javo-item-amenities-section -->'
			)
		);
	}


	public function getItem( $term_id=0, $term_name='', $hasTerm=false ) {
		$icon = '';
		$this->add_render_attribute('item', 'class', 'lava-amenity', true);
		$this->add_render_attribute('item-label', 'class', 'amenities-name', true);
		$this->add_render_attribute('item-icon', 'class', 'amenities-icon', true);
		if('no' == $this->get_settings( 'list_only_selected' )){
			$this->add_render_attribute('item', 'class', 'showall');
		}else{
			if(in_array( $this->get_settings( 'icon_type' ), Array( 'icon', 'image' ) )){
				$this->add_render_attribute('item', 'class', 'with-own-' . $this->get_settings( 'icon_type' ));
			}
		}
		if($hasTerm){
			$this->add_render_attribute('item', 'class', 'active');
		}
		switch($this->get_settings( 'icon_type' )) {
			case 'icon':
				$this->add_render_attribute('item-icon', 'class', lava_directory()->admin->getTermOption( get_term( $term_id ), 'icon'));
				$format ='<div %2$s><i %4$s></i> <span %3$s>%1$s</span></div>';
				break;
			case 'image':
				$imageIconID = lava_directory()->admin->getTermOption( get_term( $term_id ), 'image_icon');
				$this->add_render_attribute('item-icon', 'src', wp_get_attachment_image_url($imageIconID), true );
				$format ='<div %2$s><img %4$s/> <span %3$s>%1$s</span></div>';
				break;
			default:
				$format = '<div %2$s>%1$s</div>';
		}
		return sprintf($format, $term_name, $this->get_render_attribute_string('item'), $this->get_render_attribute_string('item-label'), $this->get_render_attribute_string('item-icon') );


		/*
		$output_format = '<div class="lava-amenity%1$s">%2$s</div>';
		if( $is_with_icon ) {
			$output_format = '<div class="lava-amenity%1$s"><i class="%3$s"></i> <span class="amenities-name">%2$s</span></div>';
		}
		/*
				$output[] = sprintf(
					$output_format,
					( $is_show_all ? ( $is_with_icon ? ( $hasTerm ? ' with-own-icon active' : ' with-own-icon' ) : ( $hasTerm ? ' active' : ''  )  ) : ' showall' ),
					$term_name, lava_directory()->admin->getTermOption( get_term( $term_id ), 'icon' )
				); */

	}


	public function lava_directory_amenities( $post, $args=Array() ) {

		if( is_numeric( $post ) ) {
			$post = get_post( $post );
		}

		if( ! $post instanceof WP_Post ) {
			$post = get_post();
		}

		$args = shortcode_atts(
			Array(
				'container_before' => '',
				'container_after' => '',
			), $args
		);

		$corePostType = lava_directory()->core->getSlug();
		$taxonomy = 'listing_amenities';

		if( ! apply_filters( 'lava_' . $corePostType . '_amenties_display', true, $post->ID ) ) {
			return false;
		}

		if( ! taxonomy_exists( $taxonomy ) ) {
			return false;
		}

		// $is_show_all = lava_directory()->admin->get_settings( 'display_amenities', 'showall' ) == 'showall';
		$is_show_all = $this->get_settings( 'list_only_selected' ) == 'no';
		// $is_with_icon = lava_directory()->admin->get_settings( 'display_amenities_icon', 'showall' ) == 'with-own-icon';
		$is_with_icon = in_array($this->get_settings( 'icon_type' ), Array( 'icon', 'image' ));
		$queried_terms = get_terms( Array( 'taxonomy' => $taxonomy, 'hide_empty' => false, 'fields' => 'id=>name' ) );
		$terms_in_post = wp_get_object_terms( $post->ID, $taxonomy, Array( 'fields' => 'ids' ) );
		if( is_wp_error( $queried_terms ) ) {
			printf( "<div align=\"center\">%s</div>", $queried_terms->get_error_message() );
			return;
		}
		$output = Array();
		/*
		$output_format = '<div class="lava-amenity%1$s">%2$s</div>';
		if( $is_with_icon ) {
			$output_format = '<div class="lava-amenity%1$s"><i class="%3$s"></i> <span class="amenities-name">%2$s</span></div>';
		} */
		echo $args[ 'container_before' ];
			echo '<div id="lava-directory-amenities">';
			foreach( $queried_terms as $term_id => $term_name ) {
				$hasTerm = in_array( $term_id, $terms_in_post );
				if( ! $is_show_all && ! $hasTerm ) {
					continue;
				}
				$output[] = $this->getItem($term_id, $term_name, $hasTerm);
				/*
				$output[] = sprintf(
					$output_format,
					( $is_show_all ? ( $is_with_icon ? ( $hasTerm ? ' with-own-icon active' : ' with-own-icon' ) : ( $hasTerm ? ' active' : ''  )  ) : ' showall' ),
					$term_name, lava_directory()->admin->getTermOption( get_term( $term_id ), 'icon' )
				); */
			}

			if( !empty( $output ) ) {
				echo join( '', $output );
			}else{
				printf( '<div style="text-align:center;">%s</div>', esc_html__( "Data not found.", 'jvfrmtd' ) );
			}
			echo '</div>';
		echo $args[ 'container_after' ];
	}
}
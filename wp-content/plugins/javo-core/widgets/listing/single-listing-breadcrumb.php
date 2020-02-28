<?php
/**
Widget Name: Single breadcrumb
Author: Javo
Version: 1.0.0.1
*/

namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;

if( !defined( 'ABSPATH' ) ) {
    exit;
}

class jvbpd_single_breadcrumb extends Widget_Base {

	public function get_name() { return 'jvbpd-single-breadcrumb'; }
	public function get_title() { return 'BreadCrumb'; }
	public function get_icon() { return 'eicon-form-vertical'; }
	public function get_categories() { return Array( 'jvbpd-single-listing' ); }

	protected function _register_controls() {
		$this->start_controls_section( 'section_general', Array(
			'label' => esc_html__( "General", 'jvfrmtd' ),
		) );

		$this->add_control( 'listing_page', Array(
			'label' => esc_html__( "Listing Page", 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT2,
			'multiple' => false,
			'options' => array_flip( apply_filters(
				'jvbpd_get_map_templates',
				Array( esc_html__( "Default Search Page", 'jvfrmtd' ) => '' )
			) ),
		) );

    $this->add_control(
      'text_color',
      [
        'label' => __( 'Background Color', 'jvfrmtd' ),
        'type' => Controls_Manager::COLOR,
        'defalut' => '#ffffff',
        'selectors' => [
          '{{WRAPPER}} {{CURRENT_ITEM}} .jvbpd-single-breadcrumb' => 'color: {{VALUE}};',
          '{{WRAPPER}} {{CURRENT_ITEM}} .jvbpd-single-breadcrumb a' => 'color: {{VALUE}};',
        ],
      ]
    );


    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'fields_typo',
        'selector' => '{{WRAPPER}} .jvbpd-single-breadcrumb',
        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
      ]
    );


		$this->end_controls_section();
	}

	public function getCategory( $taxonomy='', $type='slug' ) {
		$post = get_post();
		$terms = wp_get_object_terms( $post->ID, $taxonomy );
		$output = false;

		if( !is_wp_error( $terms ) && isset( $terms[0] ) && ( $terms[0] instanceof \WP_Term ) ) {
			$output = $terms[0]->{$type};
		}

		return $output;
	}

	protected function render() {

		wp_reset_postdata(); // Get single listing POST ID

		$separator = '<span class="crumb-separator"> > </span>';
		$taxonomy = 'listing_category';
		$output = $crumbs = Array();

		$crumbs[ 'home' ] = Array(
			'label' => esc_html__( "Home", 'jvfrmtd' ),
			'link' => home_url( '/' ),
		);

		$listing_page_id = $this->get_settings( 'listing_page' );
		if( $listing_page_id ) {
			$crumbs[ 'listings' ] = Array(
				'label' => esc_html__( "Listings", 'jvfrmtd' ),
				'link' => get_permalink( $listing_page_id ),
			);
		}

		$term_slug = $this->getCategory( $taxonomy, 'slug' );
		if( $term_slug ) {
			$crumbs[ 'category' ] = Array(
				'label' => $this->getCategory( $taxonomy, 'name' ),
				'link' => add_query_arg( Array( 'category' => $term_slug ), get_permalink( $listing_page_id ) ),
			);
		}

		foreach( $crumbs as $crumb_id => $crumb ) {
			$output[] = sprintf( '<a href="%1$s" class="link-%2$s">%3$s</a>', esc_url( $crumb[ 'link' ] ), $crumb_id, $crumb[ 'label' ] );
		}

		printf( '<div class="jvbpd-single-breadcrumb">%s</div>', join( $separator, $output ) );
	}
}

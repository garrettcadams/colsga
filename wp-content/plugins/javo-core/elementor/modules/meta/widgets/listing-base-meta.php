<?php
namespace jvbpdelement\Modules\Meta\Widgets;

use Elementor\Controls_Manager;

class Listing_Base_Meta extends Base {
	public function get_name() { return parent::LISTING_META; }
	public function get_title() { return 'Listing Post Meta'; }
	public function get_icon() { return 'eicon-sidebar'; }
	public function get_categories() { return [ 'jvbpd-page-builder-module' ]; }

	protected function _register_controls() {
		parent::_register_controls();
		$this->add_label_settings_control();
		$this->add_price_settings_control();
		$this->add_tax_multiple_control();
		$this->add_more_tax_control();
	}

	public function add_price_settings_control() {
		$this->start_injection(Array(
			'type' => 'control',
			 'of' => 'meta',
		) );
			$this->add_control( 'currency_settings', Array(
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__("Currency Setting", 'jvfrmtd'),
				'default' => '',
				'options' => Array(
					'' => esc_html__("None", 'jvfrmtd'),
					'before' => esc_html__("Before", 'jvfrmtd'),
					'after' => esc_html__("After", 'jvfrmtd'),
				),
				'condition' => Array('meta'=>Array('_price', '_sale_price', '_print_price')),
			));
		$this->end_injection();
	}

	public function add_tax_multiple_control() {
		$this->start_injection(Array(
			'type' => 'control',
			 'of' => 'meta'
		) );
			$this->add_control( 'terms_display', Array(
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__("Display one / all", 'jvfrmtd'),
				'default' => '',
				'options' => Array(
					'' => esc_html__("One (By alphabet)", 'jvfrmtd'),
					'all' => esc_html__("All", 'jvfrmtd'),
				),
				'condition' => Array(
					'meta'=>Array(
						'more_tax',
						'listing_category',
						'listing_location',
						'listing_amenities',
						'listing_keyword',
					),
				),
			));
			$this->add_control( 'terms_link', Array(
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__("Link", 'jvfrmtd'),
				'default' => '',
				'options' => array_flip( apply_filters(
					'jvbpd_get_map_templates',
					Array( esc_html__( "Default Search Page", 'jvfrmtd' ) => '' )
				) ),
				'condition' => Array(
					'meta'=>Array(
						'more_tax',
						'listing_category',
						'listing_location',
						'listing_amenities',
						'listing_keyword',
					),
				),
			));
			$this->add_control( 'term_link_color', Array(
				'type' => Controls_Manager::COLOR,
				'label' => esc_html__("Link Color", 'jvfrmtd'),
				'default' => '#454545',
				'condition' => Array( 'terms_link!' => '' ),
				'selectors' => Array(
					'{{WRAPPER}}.elementor-element .item-value a' => 'color:{{VALUE}};',
				),
			));
			$this->add_control( 'term_link_hover_color', Array(
				'type' => Controls_Manager::COLOR,
				'label' => esc_html__("Link Hover Color", 'jvfrmtd'),
				'default' => '#000000',
				'condition' => Array( 'terms_link!' => '' ),
				'selectors' => Array(
					'{{WRAPPER}}.elementor-element .item-value a:hover' => 'color:{{VALUE}};',
				),
			));

		$this->end_injection();

	}

	public function add_more_tax_control() {
		$this->start_injection(Array(
			'type' => 'control',
			 'of' => 'meta'
		) );
			$this->add_control( 'more_taxonomy', Array(
				'type' => Controls_Manager::SELECT2,
				'label' => esc_html__("Taxonomy", 'jvfrmtd'),
				'options' => Array('' => esc_html__("None", 'jvfrmtd')) +jvbpd_elements_tools()->getMoreTaxonoiesOptions(),
				'default' => '',
				'condition' => Array(
					'meta' => 'more_tax',
				),
			));
		$this->end_injection();
	}

	protected function render() {
		jvbpd_elements_tools()->switch_preview_post();
		$this->_render();
		jvbpd_elements_tools()->restore_preview_post();
	}
}
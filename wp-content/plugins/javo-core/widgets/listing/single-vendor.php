<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) exit;

class jvbpd_single_vender extends Widget_Base {

	public function get_name() { return 'jvbpd-single-vendor'; }

	public function get_title() { return 'Single Vendor'; }

	public function get_icon() { return 'eicon-button'; }

	public function get_categories() { return [ 'jvbpd-single-listing' ]; }

	protected function _register_controls() {
		$this->start_controls_section( 'section_general', array(
			'label' => esc_html__( 'General', 'jvfrmtd' ),
        ) );
		$this->end_controls_section();
	}

	protected function render() {
		if( !function_exists( 'lava_directory_vendor' ) )
			return;

		$settings = $this->get_settings();

		jvbpd_elements_tools()->switch_preview_post();

		if( ! $intVendorID = intVal( lava_directory_vendor()->core->getVendorID() ) )
			return;

		$vendor_products = lava_directory_vendor()->core->getProducts(
			Array(
				'vendor' => $intVendorID,
				'exclude_booking' => true,
			)
		);

		if( !empty( $vendor_products ) ) : foreach( $vendor_products as $objProduct ) {
			$objModule = new \moduleWC1( $objProduct );
			echo $objModule->output();
		} endif;
		jvbpd_elements_tools()->restore_preview_post();
    }
}
<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) exit;

class jvbpd_single_booking extends Widget_Base {

	public function get_name() { return 'jvbpd-single-booking'; }
	public function get_title() { return 'Single Booking'; }
	public function get_icon() { return 'eicon-button'; }
	public function get_categories() { return [ 'jvbpd-single-listing' ]; }

	protected function _register_controls() {
		$this->start_controls_section( 'section_general', Array(
			'label' => esc_html__( 'General', 'jvfrmtd' ),
        ) );
		$this->end_controls_section();
	}

	protected function render() {
        $settings = $this->get_settings();

        if( !function_exists( 'lava_directory_booking' ) || !function_exists( 'wc_get_template') || !class_exists( 'WC_Booking_Form' ) ) {
			return;
		}

		jvbpd_elements_tools()->switch_preview_post();
		$bookingProductID = lava_directory_booking()->core->getProductID();
		$GLOBALS[ 'product' ] = $bookingProduct = wc_get_product( $bookingProductID );

		if( !$bookingProduct ) {
			return;
		}

		if( $bookingProduct->product_type != 'booking' ){
			return;
		}

		$objForm = new \WC_Booking_Form( $bookingProduct );
        function_exists('wc_get_template') && defined('WC_BOOKINGS_TEMPLATE_PATH') && wc_get_template(
            'single-product/add-to-cart/booking.php',
            Array(
                'booking_form' => $objForm
            ),
            'woocommerce-bookings',
            WC_BOOKINGS_TEMPLATE_PATH
		);
		jvbpd_elements_tools()->restore_preview_post();
	}
}
<?php
/**
 * Widget Name: Listing map
 * Author: Javo
 * Version: 1.0.0.0
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

class jvbpd_listing_map extends Widget_Base {

	public function get_name() { return 'jvbpd-listing-map'; }
	public function get_title() { return 'Listing Map'; }
	public function get_icon() { return 'eicon-google-maps'; }
	public function get_categories() { return Array( 'jvbpd-map-tempalte' ); }

	protected function _register_controls() {}

	protected function render() {
		?>
		<div <?php jvbpd_map_class( 'javo-maps-area-wrap' ); ?>>
			<div class="javo-maps-area"></div>
		</div>
		<?php
	}
}

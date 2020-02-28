<?php
namespace jvbpdelement\Modules\Testimonial;

use jvbpdelement\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Module_Base {
	
	// Modue name : should not be duplicated
	public function get_name() { return 'jvbpd_testimonials'; }

	public function get_widgets() {
		return Array(
			'testimonial',
			'testimonial_wide',
			'featured_block',
			'members',
		);
	}
}

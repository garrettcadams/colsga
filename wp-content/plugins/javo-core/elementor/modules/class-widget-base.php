<?php
namespace jvbpdelement\Base;

use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class Base_Widget extends Widget_Base {
	public function get_categories() {
		return Array( 'jvbpd-elements' );
	}
}

<?php
namespace jvbpdelement\Modules\Meta\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module_Card extends Base {
	public function get_name() { return 'jvbpd-module-card'; }
	public function get_title() { return 'Module Card'; }
	public function get_icon() { return 'eicon-image-rollover'; }
	public function get_categories() { return [ 'jvbpd-page-builder-module' ]; }

	protected function _register_controls() { parent::__card_register_controls(); }
	protected function render() { parent::__card_render(); }
	protected function content_template() { parent::__card_content_template(); }
}
<?php
use Elementor\Modules\Library\Documents\Library_Document;

class CoreDOC extends Library_Document {

    public function get_name() {
		return 'core-page-builder';
	}

    public function get_elements_raw_data( $data = null, $with_html_content = false ) {
        die('aaaa');
		$preview_manager = Module::instance()->get_preview_manager();

        // $preview_manager->switch_to_preview_query();
        Jvbpd_Listing_Elementor::switch_to_preview_query();

		$editor_data = parent::get_elements_raw_data( $data, $with_html_content );

        // $preview_manager->restore_current_query();
        Jvbpd_Listing_Elementor::restore_current_query();

		return $editor_data;
    }
    
}
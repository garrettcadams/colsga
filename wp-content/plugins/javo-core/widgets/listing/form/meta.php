<?php
/**
Widget Name: Add form ( Meta ) Widget
Author: Javo
Version: 1.0.0.0
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


class jvbpd_add_form_meta extends Widget_Base {

	public function get_name() {
		return 'jvbpd-add-form-meta';
	}

	public function get_title() {
		return 'Add form ( Meta )';   // title to show on elementor
	}

	public function get_icon() {
		return 'eicon-form-horizontal';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-core-add-form' ];    // category of the widget
	}

    protected function _register_controls() {}

    protected function render() {

		$settings = $this->get_settings();
		$isPreviewMode = is_admin();
		/*
		if( $isPreviewMode) {
			$previewBaseURL = jvbpdCore()->assets_url . '/images/elementor/listipia/';
			$previewURL = $previewBaseURL . 'single-button.jpg';
			printf( '<img src="%s">', esc_url_raw( $previewURL ) );
		}else{ */
			$this->getContent( $settings, get_post() );
		/* } */
    }

	public function getContent( $settings, $obj ) {
		$lava_item_fields	= apply_filters( "lava_lv_listing_more_meta", Array() );

		if( !empty( $lava_item_fields ) && is_Array( $lava_item_fields ) ) {
			foreach( $lava_item_fields as $fID => $meta ) {
				$objField = new \Lava_Directory_Manager_Field( $fID, $meta );
				$objField->value = get_post_meta( intVal( get_query_var( 'edit' ) ), $fID, true );
				echo $objField->output();
			}
		}
	}
}
<?php
/**
Widget Name: Map sortby button widget
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


class jvbpd_map_sortby_btn extends Widget_Base {

	public function get_name() {
		return 'jvbpd-map-sortby-btn';
	}

	public function get_title() {
		return 'Sort By Button';   // title to show on elementor
	}

	public function get_icon() {
		return 'fa fa-user-o';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-map-page' ];    // category of the widget
	}

    protected function _register_controls() {

        $this->start_controls_section(
            'map_sortby_btn',
            [
                'label' => __('Map Rating Button', 'jvfrmtd'),
            ]
        );

        $this->add_control(

            'style', [
                'type' => Controls_Manager::SELECT,
                'label' => __('Choose Team Style', 'jvfrmtd'),
                'default' => 'style1',
                'options' => [
                    'style1' => __('Style 1', 'jvfrmtd'),
                    'style2' => __('Style 2', 'jvfrmtd'),
                ],
                'prefix_class' => 'lae-team-members-',
            ]
        );
        $this->end_controls_section();
    }

    protected function render() {

		$settings = $this->get_settings();
		wp_reset_postdata();
		$isPreviewMode = is_admin();

		if( $isPreviewMode) {
			$previewBaseURL = jvbpdCore()->assets_url . '/images/elementor/listipia/';
			$previewURL = $previewBaseURL . 'single-button.jpg';
			printf( '<img src="%s">', esc_url_raw( $previewURL ) );
		}else{
			$this->getContent( $settings, get_post() );
		}
    }

	public function getContent( $settings, $obj ) {
		?>

		<?php
	}
}
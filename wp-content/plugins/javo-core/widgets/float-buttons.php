<?php
/**Widget Name: Float Buttons
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


class Jvbpd_Float_buttons extends Widget_Base {

	public function get_name() {
		return 'jvbpd-float-btns';
	}

	public function get_title() {
		return 'Float buttons';   // title to show on elementor
	}

	public function get_icon() {
		return 'fa fa-newspaper-o';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-elements' ];    // category of the widget
	}

  protected function _register_controls() {

    $this->start_controls_section(
			'section_general',
			array(
				'label' => esc_html__( 'Banner Spot', 'jvfrmtd' ),
			)
		);

		$this->add_control(
		'Des',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => sprintf(
					'<div class="elementor-jv-notice" style="background-color:#9b0a46; color:#ffc6c6; padding:10px;"><ul>'.
					'<li class="doc-link">'.
					esc_html__('How to use this widget.','jvfrmtd').
					'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-single-listing-page/" style="color:#fff;"> ' .
					esc_html__( 'Documentation', 'jvfrmtd' ) .
					'</a></li><li>&nbsp;</li>'.
					'<li class="notice">'.
					esc_html__('This widget is for only single listing detail page.', 'jvfrmtd').
					'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-notice/" style="color:#fff;">' .
					esc_html__( 'Detail', 'jvfrmtd' ) .
					'</a><br/></li><li>&nbsp;</li><li>'.
					esc_html__( 'Please do not use in other pages.', 'jvfrmtd' ) .
					'</li></ul></div>'
				)
			)
		);

    $this->add_control(
      'ad_title',
      [
         'label'       => __( 'Title', 'jvfrmtd' ),
         'type'        => Controls_Manager::TEXT,
         'default'     => __( 'Default title text', 'jvfrmtd' ),
         'placeholder' => __( 'Type your title text here', 'jvfrmtd' ),
      ]
    );

    $this->add_control(
    	'ad_type',
    	[
    		'label' => __( 'Image / Script', 'your-plugin' ),
    		'type' => Controls_Manager::SWITCHER,
    		'default' => '',
    		'label_on' => __( 'image', 'your-plugin' ),
    		'label_off' => __( 'script', 'your-plugin' ),
    		'return_value' => 'yes',
    	]
    );

    $this->add_control(
      'ad_link',
      [
         'label' => __( 'Ad Landing URL', 'your-plugin' ),
         'type' => Controls_Manager::URL,
         'default' => [
            'url' => 'http://',
            'is_external' => '',
         ],
         'condition' => [
 					'ad_type' => 'yes',
 				 ],
         'show_external' => true, // Show the 'open in new tab' button.
      ]
    );

    $this->add_control(
      'image',
      [
         'label' => __( 'Choose Image', 'your-plugin' ),
         'type' => Controls_Manager::MEDIA,
         'default' => [
            'url' => Utils::get_placeholder_image_src(),
         ],
         'condition' => [
           'ad_type' => 'yes',
         ]
      ]
    );


    $this->add_control(
      'ad_code',
      [
         'label'   => __( 'Description', 'your-plugin' ),
         'type'    => Controls_Manager::TEXTAREA,
         'default' => __( 'Default description', 'your-plugin' ),
         'condition' => [
 					'ad_type' => '',
 				 ],
      ]
    );



		$this->end_controls_section();
    }

    protected function render() {
		$settings = $this->get_settings();


    ?>
      Hello

      <div id="app">
    <h1>{{ message }}</h1>
</div>

<script type="text/javascript">

const app = new Vue({

    el: "#app",
    template: `

        <div>
            <h1>Siddharth Knows It All</h1>
        </div>

    `

});
</script>
<!--       
      <script>
        Vue.component('example-component', require('./components/js/ExampleComponent.vue').default);
      </script> -->

<?php

// Get image by id
//echo wp_get_attachment_image( $image['id'], 'thumbnail' );

    }
}

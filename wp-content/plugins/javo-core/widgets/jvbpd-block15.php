<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Style for header
 *
 *
 * @since 1.0.0
 */

class jvbpd_mailchimp extends Widget_Base {   //this name is added to plugin.php of the root folder

	public function get_name() {
		return 'jvbpd-mail-chimp';
	}

	public function get_title() {
		return 'MailChimp Form';   // title to show on elementor
	}

	public function get_icon() {
		return 'fa-envelope-o';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-elements' ];    // category of the widget
	}

	/**
	 * A list of scripts that the widgets is depended in
	 * @since 1.3.0
	 **/
protected function _register_controls() {

//start of a control box
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Category Block', 'jvfrmtd' ),   //section name for controler view
			]
		);

		$this->add_control(
			'mailchimp_form',
			[
				'label' => esc_html__( 'Mailchimp Form', 'jvbpd' ),
                'description' => esc_html__('Please select a mailchimp form you created. If not, please create one.','jvfrmtd'),
				'type' => Controls_Manager::SELECT2,
				'multiple' => false,
				'options' => get_mailchimp_forms(),
			]
		);

		$this->end_controls_section();
	}


	protected function render() {				//to show on the fontend
		static $v_veriable=0;

		$settings = $this->get_settings();
        if(!empty($settings['mailchimp_form'])){
                echo do_shortcode('[mc4wp_form id="'.$settings['mailchimp_form'].'"]');    
    	}
    }
}

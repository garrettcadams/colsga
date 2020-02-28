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

class jvbpd_cf7 extends Widget_Base {   //this name is added to plugin.php of the root folder

	public function get_name() {
		return 'jvbpd-section-cf7';
	}

	public function get_title() {
		return 'Javo Contact From 7';   // title to show on elementor
	}

	public function get_icon() {
		return 'eicon-mail';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
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
			'section_general',
			array(
				'label' => esc_html__( 'Note', 'jvfrmtd' ),
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
					'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-contact-form-7/" style="color:#fff;"> ' . 
					esc_html__( 'Documentation', 'jvfrmtd' ) . 
					'</a></li></ul></div>'
				)
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Contact Form 7', 'jvfrmtd' ),   //section name for controler view
			]
		);

		$this->add_control(
			'cf7',
			[
				'label' => esc_html__( 'Select Contact Form', 'jvbpd' ),
                'description' => esc_html__('Contact form 7 - plugin must be installed and there must be some contact forms made with the contact form 7','jvfrmtd'),
				'type' => Controls_Manager::SELECT2,
				'multiple' => false,
				'options' => get_contact_form_7_posts(),
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_stype',
			[
				'label' => esc_html__( 'Style Contact Form', 'jvfrmtd' ),   //section name for controler view
			]
		);

		$this->add_control(
			'cf7_direct_css',
			[
				'label' => __( 'Global CSS', 'elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => 'color:#000;',
				'selectors' => [
					'{{WRAPPER}} ' => '{{VALUE}}',
				],
			]
		);

		$this->add_control(
			'alllabel',
			[
				'label' => __( 'All Label CSS', 'elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => 'color:#fff;',
				'selectors' => [
					'{{WRAPPER}} label' => '{{VALUE}}',
				],
			]
		);	
		$this->add_control(
			'allinput',
			[
				'label' => __( 'All Input CSS', 'elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => 'width:100%;
							      background:red;',
				'selectors' => [
					'{{WRAPPER}} input' => '{{VALUE}}',
				],
			]
		);

		$this->add_control(
			'textinput',
			[
				'label' => __( 'Input Type Text CSS', 'elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => 'width:100%;
							      background:red;',
				'selectors' => [
					'{{WRAPPER}} .wpcf7-text' => '{{VALUE}}',
				],
			]
		);	
		$this->add_control(
			'textarea',
			[
				'label' => __( 'Textarea CSS', 'elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => 'height:100px; 
								  width:100%;',
				'selectors' => [
					'{{WRAPPER}} textarea' => '{{VALUE}}',
				],
			]
		);

		$this->add_control(
			'checkbox',
			[
				'label' => __( 'Checkbox/ Radio CSS', 'elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => 'display: block;',
				'selectors' => [
					'{{WRAPPER}} .wpcf7-list-item' => '{{VALUE}}',
				],
			]
		);		

		$this->add_control(
			'file',
			[
				'label' => __( 'File CSS', 'elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => 'display: block;',
				'selectors' => [
					'{{WRAPPER}} input[type="file"]' => '{{VALUE}}',
				],
			]
		);	
		$this->add_control(
			'date',
			[
				'label' => __( 'Date CSS', 'elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => 'display: block;',
				'selectors' => [
					'{{WRAPPER}} .wpcf7-date' => '{{VALUE}}',
				],
			]
		);	
		$this->add_control(
			'inputsubmit',
			[
				'label' => __( 'Submit Button CSS', 'elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => 'width:100%;
							      background:red;',
				'selectors' => [
					'{{WRAPPER}} input[type="submit"]' => '{{VALUE}}',
				],
			]
		);
		$this->add_control(
			'responce',
			[
				'label' => __( 'Responce CSS', 'elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => 'color:red;',
				'selectors' => [
					'{{WRAPPER}} .wpcf7-response-output' => '{{VALUE}}',
				],
			]
		);


		$this->end_controls_section();



		$this->start_controls_section(
			'section_redirect',
			[
				'label' => esc_html__( 'After Submit Redirect Setting', 'jvfrmtd' ),   //section name for controler view
			]
		);

		$this->add_control(
			'cf7_redirect_page',
			[
				'label' => esc_html__( 'On Success Redirect To', 'jvfrmtd' ),
                'description' => esc_html__('Select a page which you want users to redirect to when the contact fom is submitted and is successful. Leave Blank to Disable','jvfrmtd'),
				'type' => Controls_Manager::SELECT2,
				'multiple' => false,
				'options' => jvbpd_get_all_pages(),
			]
		);

		$this->end_controls_section();
	}


	protected function render() {				//to show on the fontend 
		static $v_veriable=0;

		$settings = $this->get_settings();
        if(!empty($settings['cf7'])){
    	   echo'<div class="elementor-shortcode jvbpd-cf7-'.$v_veriable.'">';
                echo do_shortcode('[contact-form-7 id="'.$settings['cf7'].'"]');    
           echo '</div>';  
    	}

 		if(!empty($settings['cf7_redirect_page'])) {  ?>
 			<script>
 			        var theform = document.querySelector('.jvbpd-cf7-<?php echo $v_veriable; ?>');
						theform.addEventListener( 'wpcf7mailsent', function( event ) {
					    location = '<?php echo get_permalink( $settings['cf7_redirect_page'] ); ?>';
					}, false );
			</script>

		<?php  $v_veriable++;
 		}

    }
}

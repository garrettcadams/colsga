<?php
/**
 * Widget Name: Single contact form7 widget
 * Author: Javo
 * Version: 1.0.0.0
*/

namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;
use Elementor\Group_Control_Border;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Typography;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Style for header
 *
 *
 * @since 1.0.0
 */

class jvbpd_single_cf7 extends Widget_Base {   //this name is added to plugin.php of the root folder
	public $animation='';

	public function get_name() {
		return 'jvbpd-single-cf7';
	}

	public function get_title() {
		return 'JV Contact From 7';   // title to show on elementor
	}

	public function get_icon() {
		return 'eicon-mail';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-single-listing' ];    // category of the widget
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
                'description' => esc_html__('Set Mail recipient : Ensure you set a proper recipient setup. ___Link___ ','jvfrmtd'),
				'type' => Controls_Manager::SELECT2,
				'multiple' => false,
				'options' => get_contact_form_7_posts(),
			]
		);
		$this->add_control(
			'cf7_change_btn',
			[
				'label' => __( 'Change Button', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => __( 'ON', 'jvfrmtd' ),
				'label_off' => __( 'OFF', 'jvfrmtd' ),
			]
		);
		$this->end_controls_section();



		/*Contact Button Settings*/
		$this->start_controls_section(
			'cf7_button',
			[
				'label' => esc_html__( 'Contact Button', 'jvfrmtd' ),   //section name for controler view
				'condition' => [
						'cf7_change_btn' => 'yes',
				],
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'cf7_button_typo',
			'selector' => '{{WRAPPER}} .cf7-button-Control > input',
			'scheme' => Scheme_Typography::TYPOGRAPHY_1,
			]
		);

		$this->add_control(
		  'cf7_button_title',
		  [
			 'label'       => __( 'Button Title', 'jvfrmtd' ),
			 'type'        => Controls_Manager::TEXT,
			 'default'     => __( 'SEND MESSGE', 'jvfrmtd' ),
			 'placeholder' => __( 'Type your text here', 'jvfrmtd' ),
		  ]
		);

		$this->add_control(
			'cf7_button_title_color',
			[
				'label' => __( 'Title Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} input.cf7-view-button' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'cf7_button_title_hover_color',
			[
				'label' => __( 'Button Title Hover Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} input.cf7-view-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cf7_button_Background_color',
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} input.cf7-view-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cf7_button_hover_color',
			[
				'label' => __( 'Button Hover Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#6ec1e4',
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} input.cf7-view-button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_animation',
			[
				'label' => __( 'Hover Animation', 'elementor' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->add_responsive_control(
			'cf7_button_align',
			[
				'label' => __( 'Alignment', 'jvfrmtd' ),
				'type' => Controls_Manager::CHOOSE,
				'default' =>'center',
				'options' => [
					'left' => [
						'title' => __( 'Left', 'jvfrmtd' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'jvfrmtd' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'jvfrmtd' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .cf7-button-Control' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
		'cf7_button_width',
			[
				'label' => __( 'Button Width', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 125,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .cf7-view-button' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
		'cf7_button_height',
			[
				'label' => __( 'Button Height', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 35,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .cf7-view-button' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'cf7_button_border',
				'default' => '',
				'selector' => '{{WRAPPER}} .cf7-view-button',
			]
		);

		$this->add_control( 'cf7_button_radius', [
			'label' => __( 'Border Radius', 'jvfrmtd' ),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors' => [
					'{{WRAPPER}} .cf7-button-Control > input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

		$this->animation= 'cf7-button-Control '.'elementor-animation-'.$settings['hover_animation'];

		add_action('wp_footer',array($this,'get_modal'));


		$settings = $this->get_settings();
        if(!empty($settings['cf7'])){
    	   echo'<div class="elementor-shortcode jvbpd-cf7-'.$v_veriable.'">';
				if ( 'yes' != $settings['cf7_change_btn'] ){

					echo do_shortcode('[contact-form-7 id="'.$settings['cf7'].'"]');

				}else{

					echo '<div class="'.$this->animation.'"><input type="button" value="'.$settings['cf7_button_title'].'" class="cf7-view-button"  data-toggle="modal" data-target="#cf7-button-Modal" style=" display: inline;"></input></div>';

				}
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
	public function get_modal(){
		$settings = $this->get_settings();
		?>
		<div class="modal fade" id="cf7-button-Modal" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				    <div class="modal-body">
							<?php
								echo do_shortcode('[contact-form-7 id="'.$settings['cf7'].'"]');
							?>
					</div>
				</div>
			</div>
		</div>
<?php
	}
}


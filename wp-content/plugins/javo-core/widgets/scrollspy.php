<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Scheme_Typography;

if ( ! defined( 'ABSPATH' ) ) exit;

class Jvbpd_Scrollspy extends Widget_Base {

    public $_render_type = '';
	public function get_name() { return 'jvbpd-scrollspy'; }
	public function get_title() { return 'Scrollspy'; }
	public function get_icon() { return 'eicon-scroll'; }
	public function get_categories() { return [ 'jvbpd-elements' ]; }

	protected function _register_controls() {
		$this->start_controls_section( 'section_general', array(
			'label' => esc_html__( 'General', 'jvfrmtd' ),
		) );
		$this->add_control( 'landing', Array(
			'type' => Controls_Manager::TEXT,
            'label' => esc_html__( 'Landing ID', 'jvfrmtd' ),
        	'placeholder' => __( 'Type an ID without #', 'jvfrmtd' ),
		));
		$this->end_controls_section();
    }


	public function render() {
		
        $settings = $this->get_settings_for_display();
           


		$this->add_render_attribute( 'wrap', Array(
            'class' => Array( 'jv-scrollspy' ),
		));
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>

            <a href="#<?php echo $settings['landing']; ?>" class="jv-spyscroll">
                <div class="mouse"></div>
            </a>
		
		</div>
		<?php
	}
}
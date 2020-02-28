<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class Jvbpd_Single_Badge extends Widget_Base {

	public function get_name() { return 'jvbpd-single-badge'; }
	public function get_title() { return 'Single listing badge'; }
	public function get_icon() { return 'eicon-button'; }
	public function get_categories() { return [ 'jvbpd-single-listing' ]; }

	protected function _register_controls() {
		$this->start_controls_section( 'section_general', array(
			'label' => esc_html__( 'General', 'jvfrmtd' ),
		) );
        $this->end_controls_section();

        $this->start_controls_section( 'section_badge_style', array(
            'label' => __( 'Video', 'jvfrmtd' ),
            'tab' => Controls_Manager::TAB_STYLE,
        ) );
        $this->add_control( 'badge_color', Array(
            'label' => __( 'Color', 'jvfrmtd' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => Array(
                '{{WRAPPER}} .verified-badge' => 'color: {{VALUE}}',
            ),
            'default' => '#ffffff',
            'separator' => 'before',
        ) );;
        $this->end_controls_section();
    }

    public function render() {
        if( !class_exists( '\Lava\Role\Manager') ) {
            return;
        }
        $post = get_post();
        $author = $post->post_author;
        $author_role = \Lava\Role\Manager::$instance->admin->getUserMember( $author );
        $role = \Lava\Role\Manager::$instance->admin->getRole( $author_role );

        $this->add_render_attribute( 'wrap', array(
            'class' => 'jvbpd-single-listing-badge',
        ) );
        ?>
        <div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
            <?php
            if( $role && $role->bedge_shown ) {
                printf( '<span class="verified-badge"><i class="%1$s"></i> %2$s</span>', 'fa fa-check', esc_html__( "Verified", 'jvfrmtd' ) );
            } ?>
        </div>
        <?php
    }
}
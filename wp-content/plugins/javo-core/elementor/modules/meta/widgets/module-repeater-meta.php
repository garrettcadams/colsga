<?php
namespace jvbpdelement\Modules\Meta\Widgets;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module_Repeater_Meta extends Base {
	public function get_name() { return 'jvbpd-module-repeater-meta'; }
	public function get_title() { return 'Module Repeater Meta'; }
    public function get_icon() { return 'eicon-image-rollover'; }
    public function get_categories() { return [ 'jvbpd-page-builder-module' ]; }

	protected function _register_controls() {
        $this->start_controls_section( 'section_general', Array(
            'label' => esc_html__("General", 'jvfrmtd'),
        ));
            $this->add_control( 'meta', Array(
                'type' => Controls_Manager::REPEATER,
                'label' => esc_html__("Meta Field", 'jvfrmtd'),
                'fields' => Array(
                    Array(
                        'name' => 'field',
                        'label' => __( 'Button Type', 'jvfrmtd' ),
                        'default' => 'post_title',
                        'type' => Controls_Manager::SELECT,
                        'options' => $this->getReplaceOptions(),
                    ),
                ),
            ));

        $this->end_controls_section();
    }
	protected function render() {
        $this->add_render_attribute('wrap', 'class', 'jvbpd-repeater-meta-wrap');
        $output_format = '{%1$s}';
        ?>
        <div <?php echo $this->get_render_attribute_string('wrap'); ?>>
            <?php
            foreach($this->get_settings('meta') as $meta ){
                $this->add_render_attribute('meta', 'class', Array('jvbpd-repeater-meta', 'meta-'.$meta['field']), true);
                ?>
                <div <?php echo $this->get_render_attribute_string('meta'); ?>>
                    <?php printf( $output_format, $meta['field'] ); ?>
                </div>
                <?php
            } ?>
        </div>
        <?php
    }
	protected function content_template() { parent::__card_content_template(); }
}
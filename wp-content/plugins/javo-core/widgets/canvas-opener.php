<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class Jvbpd_Canvas_Opener extends Widget_Base {

	public function get_name() { return 'jvbpd-canvas-opener'; }
	public function get_title() { return 'Canvas Opener'; }
	public function get_icon() { return 'eicon-button'; }
	public function get_categories() { return [ 'jvbpd-elements-canvas' ]; }

	protected function _register_controls() {
		$this->start_controls_section( 'section_general', array(
			'label' => esc_html__( 'General', 'jvfrmtd' ),
		) );
			$this->add_control( 'canvas_menu', Array(
				'label' => esc_html__( 'Canvas', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT2,
				'options' => jvbpd_elements_tools()->getCanvasIDs(),
                'description' => esc_html__('Please select a canvas menu template. If you don`t have any, please create one in Javo page builder ( Javo setting > Page Builder ).', 'jvfrmtd'),
            ) );

            $this->add_control( 'canvas_animate_direction', Array(
				'label' => esc_html__( 'Open Direction', 'jvfrmtd' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'ltr',
                'options' => Array(
                    'ltr' => esc_html__( "Left to right", 'jvfrmtd' ),
                    'center' => esc_html__( "Center scale", 'jvfrmtd' ),
                    'rtl' => esc_html__( "Right to left", 'jvfrmtd' ),
                ),
            ) );

        $this->end_controls_section();

        $this->start_controls_section( 'section_style_tab', [
            'label' => __( 'Style', 'jvfrmtd' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'icon_align', [
            'label' => __( 'Alignment', 'jvfrmtd' ),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'flex-start' => [
                    'title' => __( 'Left', 'jvfrmtd' ),
                    'icon' => 'fa fa-align-left',
                ],
                'center' => [
                    'title' => __( 'Center', 'jvfrmtd' ),
                    'icon' => 'fa fa-align-center',
                ],
                'flex-end' => [
                    'title' => __( 'Right', 'jvfrmtd' ),
                    'icon' => 'fa fa-align-right',
                ],
            ],
            'default' => 'center',
            'selectors' => [
                '{{WRAPPER}} .jvbpd-menu-opener i' => 'justify-content: {{VALUE}};',
            ],
        ] );

        $this->end_controls_section();

        $this->start_controls_section(
			'opener_icon',
			[
				'label' => __( 'Icons', 'jvfrmtd' ),
			]
		);

		$this->add_control( '_icon', [
				'label' => __( 'Icon', 'jvfrmtd' ),
                'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => Array(
					'value' => 'fab jvic-three-bars',
					'library' => 'solid',
                ),
				'description' => __('It may take time to load all icons', 'jvfrmtd'),
			]
		);

        $this->add_control( 'icon_color', [
            'label' => __( 'Color', 'jvfrmtd' ),
            'type' => Controls_Manager::COLOR,
            'default' => '#888888',
            'selectors' => [
                '{{WRAPPER}} .jvbpd-menu-opener i' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_control(
            'icon_size',
            array(
                'label'   => esc_html__( 'Icon Size', 'jvfrmtd' ),
                'type'    => Controls_Manager::SLIDER,
                'default' => array(
                    'size' => 20,
                    'unit' => 'px',
                ),
                'range' => array(
                    'px' => array(
                        'min' => 6,
                        'max' => 90,
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} .jvbpd-menu-opener i' => 'font-size: {{SIZE}}{{UNIT}};',
                ),
            )
        );


        // $this->add_control(
        //     'icon_line_height',
        //     array(
        //         'label'   => esc_html__( 'Icon Line Height', 'jvfrmtd' ),
        //         'type'    => Controls_Manager::SLIDER,
        //         'default' => array(
        //             'size' => 25,
        //             'unit' => 'px',
        //         ),
        //         'range' => array(
        //             'px' => array(
        //                 'min' => 6,
        //                 'max' => 90,
        //             ),
        //         ),
        //         'selectors' => array(
        //             '{{WRAPPER}} .jvbpd-menu-opener i' => 'line-height: {{SIZE}}{{UNIT}};',
        //         ),
        //     )
        // );
        $this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();
        $use_icon = $this->get_settings( 'icon' );
		add_action( 'wp_footer', Array( $this, 'loadCanvas' ) );

		$this->add_render_attribute( 'opener', array(
			'class' => 'jvbpd-menu-opener',
			'href' => 'javascript:',
			'data-id' => $this->get_id(),
			'data-template' => $this->get_settings( 'canvas_menu' ),
		) );?>

        <a <?php echo $this->get_render_attribute_string( 'opener' ); ?>>
            <?php
            if(
                isset($settings['__fa4_migrated']['_icon']) ||
                (empty($settings['icon']) && Icons_Manager::is_migration_allowed())
            ) {
                Icons_Manager::render_icon( $settings['_icon'], Array(
                    'aria-hidden' => 'true',
                    'style' => 'display:flex;'
                ) );
            }else{
                printf('<i class="%s" style="display:flex;"></i>', $use_icon);
            } ?>
		</a>
		<?php
	}

	public function loadCanvas() {

		$this->add_render_attribute( 'container', array(
			'class' => 'jvbpd-canvas-container',
			'data-id' => $this->get_id(),
        ) );
        $this->add_render_attribute( 'container', 'class', 'ani-' . $this->get_settings( 'canvas_animate_direction', 'ltr' ) );
        ?>
		<div <?php echo $this->get_render_attribute_string( 'container' ); ?>></div>
		<?php
	}
}
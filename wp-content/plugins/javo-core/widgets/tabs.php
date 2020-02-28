<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Scheme_Color;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor tabs widget.
 *
 * Elementor widget that displays vertical or horizontal tabs with different
 * pieces of content.
 *
 * @since 1.0.0
 */
class jvbpd_tabs extends Widget_Base {

	public function get_name() { return 'jv_tabs'; }
	public function get_title() { return __( 'JV Tabs', 'jvfrmtd' ); }
	public function get_icon() { return 'eicon-tabs'; }
	public function get_categories() { return [ 'jvbpd-elements' ]; }

	protected function _register_controls() {
		$this->start_controls_section(
			'section_tabs',
			[
				'label' => __( 'JV Tabs', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'tabs',
			[
				'label' => __( 'Tabs Items', 'jvfrmtd' ),
				'type' => Controls_Manager::REPEATER,
				'default' => [
					[
						'tab_title' => __( 'Tab #1', 'jvfrmtd' ),
						'tab_content' => __( 'I am tab content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'jvfrmtd' ),
					],
					[
						'tab_title' => __( 'Tab #2', 'jvfrmtd' ),
						'tab_content' => __( 'I am tab content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'jvfrmtd' ),
					],
				],
				'fields' => [
					[
						'name' => 'tab_title',
						'label' => __( 'Title & Content', 'jvfrmtd' ),
						'type' => Controls_Manager::TEXT,
						'default' => __( 'Tab Title', 'jvfrmtd' ),
						'placeholder' => __( 'Tab Title', 'jvfrmtd' ),
						'label_block' => true,
					],
					[
						'name' => 'tab_content',
						'label' => __( 'Content', 'jvfrmtd' ),
						'default' => __( 'Tab Content', 'jvfrmtd' ),
						'placeholder' => __( 'Tab Content', 'jvfrmtd' ),
						'type' => Controls_Manager::WYSIWYG,
						'show_label' => false,
					],
				],
				'title_field' => '{{{ tab_title }}}',
			]
		);


		$this->add_control(
			'horizontal_alignment',
			[
				'label' => __( 'Alignment', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'Default', 'jvfrmtd' ),
					'nav-justified' => __( 'Justified', 'jvfrmtd' ),
					'nav-fill' => __( 'Fill', 'jvfrmtd' ),
					'justify-content-center' => __( 'Justify content center', 'jvfrmtd' ),
					'justify-content-end' => __( 'Justify content end', 'jvfrmtd' ),
					//'flex-column' => __( 'flex-column', 'jvfrmtd' ),  // It needs to add wrap div like col-md-3, col-md-9
				],
			]
		);


		$this->add_control(
			'tabs_pill',
			[
				'label' => __( 'Tabs or Pills', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'nav-pills',
				'options' => [
					'nav-tabs' => __( 'Tabs', 'jvfrmtd' ),
					'nav-pills' => __( 'Pills', 'jvfrmtd' ),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_tabs_style',
			[
				'label' => __( 'Tabs Title', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);


        $this->add_responsive_control('tab_padding',
		 [
			'label'         => esc_html__('Padding', 'jvfrmtd'),
			'type'          => Controls_Manager::DIMENSIONS,
			'size_units'    => ['px', 'em', '%'],
			'default' => Array(
				'top' => 10,
				'right' => 20,
				'bottom' => 10,
				'left' => 20,
				'unit' => 'px'
			),
			'selectors'     => [
				'{{WRAPPER}} .nav-tabs .nav-link, {{WRAPPER}} .nav-pills .nav-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
         );


		$this->add_control(
			'background_color',
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .nav-pills .nav-link.active' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .nav-tabs .nav-link.active' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'heading_title',
			[
				'label' => __( 'Title', 'jvfrmtd' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'tab_color',
			[
				'label' => __( 'Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .nav-link' => 'color: {{VALUE}};',
				],
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
			]
		);

		$this->add_control(
			'tab_active_color',
			[
				'label' => __( 'Active Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .nav-link.active' => 'color: {{VALUE}};',
				],
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_4,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'tab_typography',
				'selector' => '{{WRAPPER}} .nav-link',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_contents_style',
			[
				'label' => __( 'Tabs Contents', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);


        $this->add_responsive_control('tab_content_padding',
		 [
			'label'         => esc_html__('Padding', 'jvfrmtd'),
			'type'          => Controls_Manager::DIMENSIONS,
			'size_units'    => ['px', 'em', '%'],
			'default' => Array(
				'top' => 20,
				'right' => 20,
				'bottom' => 20,
				'left' => 20,
				'unit' => 'px'
			),
			'selectors'     => [
				'{{WRAPPER}} .tab-content'=> 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
         );

		$this->add_control(
			'heading_content',
			[
				'label' => __( 'Content', 'jvfrmtd' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'content_color',
			[
				'label' => __( 'Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tab-content' => 'color: {{VALUE}};',
				],
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'selector' => '{{WRAPPER}} .tab-content',
				'scheme' => Scheme_Typography::TYPOGRAPHY_3,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render tabs widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$tabs = $this->get_settings_for_display( 'tabs' );
		$settings = $this->get_settings();

		$id_int = substr( $this->get_id_int(), 0, 3 );

		$this->add_render_attribute( 'nav_style_class', [
			'class' => 'nav',
		] );

		if( !empty($settings['horizontal_alignment']) ) {
			$this->add_render_attribute( 'nav_style_class', [
				'class' => $settings['horizontal_alignment'],
			] );
		}

		if( !empty($settings['tabs_pill']) ) {
			$this->add_render_attribute( 'nav_style_class', [
				'class' => $settings['tabs_pill'],
			] );
		}



		?>
		<nav>
		  <div <?php echo $this->get_render_attribute_string( 'nav_style_class' ); ?> id="nav-tab" role="tablist">
		  <?php
				foreach ( $tabs as $index => $item ) :
					$tab_count = $index + 1;

					$tab_title_setting_key = $this->get_repeater_setting_key( 'tab_title', 'tabs', $index );

					$this->add_render_attribute( $tab_title_setting_key, [
						'class' => [ 'nav-item', 'nav-link' ],
						'id' => 'nav-tab-title-' . $id_int . $tab_count,
						'data-toggle' => 'tab',
						'href' => '#nav-' . $id_int . $tab_count,
						'role' => 'tab',
						'data-tab' => $tab_count,
						'aria-controls' => 'nav-' . $id_int . $tab_count,
						//'aria-selected' => 'false',
					] );
					if( $tab_count == 1 ) {
						$this->add_render_attribute( $tab_title_setting_key, [
							'class' => 'active',
						] );
					}
		  ?>
		  	<a <?php echo $this->get_render_attribute_string( $tab_title_setting_key ); ?>><span class="tab-title"><?php echo $item['tab_title']; ?></span></a>
			<?php endforeach; ?>
		  </div>
		</nav>

		<div class="tab-content" id="nav-tabContent">
			<?php
			foreach ( $tabs as $index => $item ) :
				$tab_count = $index + 1;

				$tab_content_setting_key = $this->get_repeater_setting_key( 'tab_content', 'tabs', $index );
				$this->add_render_attribute( $tab_content_setting_key, [
					'class' => [ 'tab-pane', 'fade' ],
					'id' => 'nav-' . $id_int . $tab_count,
					'role' => 'tabpanel',
					'aria-labelledby' => 'nav-' . $id_int . $tab_count,
				] );

				if( $tab_count == 1 ) {
					$this->add_render_attribute( $tab_content_setting_key, [
						'class' => ['active', 'show'],
					] );
				}
				?>
				<div <?php echo $this->get_render_attribute_string( $tab_content_setting_key ); ?>><?php echo $this->parse_text_editor( $item['tab_content'] ); ?></div>
			<?php endforeach; ?>
		</div>

		<?php
	}

	/**
	 * Render tabs widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @access protected
	 */

}

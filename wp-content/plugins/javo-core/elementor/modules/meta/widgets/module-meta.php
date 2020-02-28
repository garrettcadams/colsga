<?php
namespace jvbpdelement\Modules\Meta\Widgets;

use Elementor\Controls_Manager;

class Module_Meta extends Base {

	private $post = null;

	public function get_name() { return parent::MODULE_META; }
	public function get_title() { return 'Module Meta'; }
	public function get_icon() { return 'eicon-align-left'; }
	public function get_categories() { return [ 'jvbpd-page-builder-module' ]; }
	protected function this_get_field_control() { return false; }

	protected function _register_controls() {
		$this->start_controls_section( 'section_moudle_field', Array(
			'label' => esc_html__( "Field ( Meta Key )", 'jvfrmtd' ),
		) );

		$this->add_control( 'meta_key', Array(
			'label' => __( 'Select a field', 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT2,
			'default' => 'post_title',
			'options' => Array( '' => esc_html__( "Select once", 'jvfrmtd' ) ) + $this->getReplaceOptions(),
			'multiple' => false,
		) );

		$this->add_control( 'meta_label_hidden', Array(
			'label' => __( 'Hide Field Label', 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'condition' => Array(
				'meta_key' => 'favorite',
			),
			'selectors' => Array(
				'{{WRAPPER}} .lava-favorite .button-label' => 'display:none;',
			),
		) );

		$this->add_control( 'meta_length', Array(
			'label' => __( 'Meta length', 'jvfrmtd' ),
			'type' => Controls_Manager::NUMBER,
			'default' => '',
			'condition' => Array(
				'meta_key' => Array( 'post_title', 'post_content', 'post_excerpt' ),
			)
		) );

		$this->add_control(
		  'custom_meta',
		  [
			 'label'   => __( 'Custom Meta', 'jvfrmtd' ),
			 'type'    => Controls_Manager::CODE,
			 'language' => 'html',
			 'condition' => [
				'meta_key' => 'custom_meta',
			]
		  ]
		);

		$this->add_control( 'link_single_page', Array(
			'label' => __( 'Link to the posts', 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
		) );

		$this->add_control( 'link_new_win', Array(
			'label' => __( 'Link as a new window', 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'separator' => 'before',
			'condition' => [
				'link_single_page' => 'yes',
			]
		) );

		$this->add_control(
			'value_hover_color',
			[
				'label' => __( 'Hover Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .item-value a:hover' => 'color: {{VALUE}}',
				],
				'condition' => [
					'link_single_page' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		$this->add_rating_control();
		$this->add_price_control();
		$this->add_more_tax_control();
		parent::_register_controls();
	}

	protected function render() {
		$this->_render();
	}

	protected function getModuleMeta() {
		$format = '{%1$s}';
		$link_new_window = '';
		if( 0 < intVal( $this->get_settings( 'meta_length', 0 ) ) ) {
			$format = '{%1$s:' . $this->get_settings( 'meta_length' ) . '}';
		}
		if( 'yes' === $this->get_settings( 'link_single_page' ) ) {
			if( 'yes' === $this->get_settings( 'link_new_win' )) {
				$link_new_window =  " target='_blank'";
			}
			$format = '<a href="{permalink_url}" '. $link_new_window . ' >' . $format . '</a>';
		}
		if( 'rating' == $this->get_settings( 'meta_key')) {
			$format = '{%1$s:' . $this->get_settings( 'rating_type' ) . '}';
		}
		if( 'custom_meta' == $this->get_settings( 'meta_key')) {
			return $this->get_settings('custom_meta');
		}
		if( 'more_tax' == $this->get_settings( 'meta_key')) {
			$params = sprintf(
				'%s|%s|%s',
				$this->get_settings('more_taxonomy'),
				$this->get_settings('more_taxonomy_display'),
				$this->get_settings('more_taxonomy_link')
			);
			$format = '{%1$s:' . $params . '}';
		}
		if( in_array( $this->get_settings( 'meta_key'), Array('_price', '_sale_price', '_print_price'))) {
			$format = '{%1$s:' . $this->get_settings( 'price_currency_unit' ) . '}';
		}
		return sprintf( $format, $this->get_settings( 'meta_key' ) );
	}

}
<?php
namespace jvbpdelement\Modules\Meta\Widgets;

use Elementor\Controls_Manager;

class Listing_Base_Field extends Base {
	public function get_name() { return parent::LISTING_FIELD; }
	public function get_title() { return 'Listing Field(Submit)'; }
	public function get_icon() { return 'eicon-sidebar'; }
	public function get_categories() { return [ 'jvbpd-core-add-form' ]; }

	protected function _register_controls() {
		parent::_register_controls();

		$this->add_more_tax_control();
		$this->start_controls_section( 'field_options', [
			'label' => __( 'Field Options', 'jvfrmtd' ),
		] );

			$this->add_control( 'field_style', [
				'label' => __( 'Field Style', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'block',
				'options' => [
					'block' => __( '2 Lines', 'jvfrmtd' ),
					'inline' => __( '1 Line', 'jvfrmtd' ),
					'hide' => __( 'Hide Label', 'jvfrmtd' ),
				],
				'prefix_class' => 'field-type-',
				'selectors' => Array(
					'{{WRAPPER}}.field-type-block .single-item' . ',' .
					'{{WRAPPER}}.field-type-block .single-item .item-label' . ',' .
					'{{WRAPPER}}.field-type-block .single-item .item-value' => 'display:block; width:auto; height:auto;',

					'{{WRAPPER}}.field-type-hide .single-item' . ',' .
					'{{WRAPPER}}.field-type-hide .single-item .item-label' . ',' .
					'{{WRAPPER}}.field-type-hide .single-item .item-value' => 'display:block; width:auto; height:auto;',

					'{{WRAPPER}}.field-type-hide .single-item' . ',' .
					'{{WRAPPER}}.field-type-hide .single-item .item-value' => 'display:block; width:auto;',
					'{{WRAPPER}}.field-type-hide .single-item .item-label' => 'display:none;',
				),
			] );

			$this->add_control( 'use_own_label', Array(
				'label' => __( 'Use Own Label', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'return_value' => 'yes',
			) );

			$this->add_control(
			  'field_name_label',
			  [
				 'label'       => __( 'Label name', 'jvfrmtd' ),
				 'type'        => Controls_Manager::TEXT,
				 'placeholder' => __( 'Type your label name here', 'jvfrmtd' ),
				 'description' => __('It is for only input, text fields', 'jvfrmtd'),
				 'condition'	=>	[
					'use_own_label' => 'yes'
				 ],
			  ]
			);

			$this->add_control( 'field_placeholder', Array(
				'label'       => __( 'Placeholder', 'jvfrmtd' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Type your placeholder here', 'jvfrmtd' ),
				'description' => __('It is for only input, text fields', 'jvfrmtd'),
			) );

			$this->add_control( 'field_description', Array(
				'label' => __( 'Add a Tip', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
			) );

			$this->add_control( 'field_description_type', Array(
				'label' => __( 'Tips Type', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'div',
				'options' => Array(
					'div' => esc_html__( "Normal", 'jvfrmtd' ),
					'tooltip' => esc_html__( "Tooltip", 'jvfrmtd' ),
				),
				'condition' => Array(
					'field_description' => 'yes'
				),
			) );

		$this->add_responsive_control(
			'tooltip-position-right',
			[
				'label' => __( 'Position:right', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', 'vh' ],
				'selectors' => [
					'{{WRAPPER}} a.item-description' => 'position:absolute; right:{{SIZE}}{{UNIT}}; z-index:1;',
				],
				'condition' => [
					'field_description' => 'yes',
					'field_description_type' => 'tooltip',
				],
			]
		);

		$this->add_responsive_control(
			'tooltip-position-top',
			[
				'label' => __( 'Position:top', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', 'vh' ],
				'selectors' => [
					'{{WRAPPER}} a.item-description' => 'top:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'field_description' => 'yes',
					'field_description_type' => 'tooltip',
				],
			]
		);

			$this->add_control( 'field_description_content', Array(
				'label' => __( 'Description', 'jvfrmtd' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( "Description text", 'jvfrmtd' ),
				'condition' => Array(
					'field_description' => 'yes'
				),
			) );


						/*
			$this->add_control(
				'mark_required',
				[
					'label' => __( 'Required field', 'jvfrmtd' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => '',
					'label_on' => __( 'Yes', 'jvfrmtd' ),
					'label_off' => __( 'No', 'jvfrmtd' ),
					'return_value' => 'yes',
				]
			);

			$this->add_control(
			  'required_msg',
			  [
				 'label'       => __( 'Required message', 'jvfrmtd' ),
				 'type'        => Controls_Manager::TEXT,
				 'placeholder' => __( 'Required message if not typed', 'jvfrmtd' ),
				 'default'     => __( 'This field is required', 'jvfrmtd' ),
				 'condition'	=>	[
					'mark_required' => 'yes'
				 ],
			  ]
			);*/

			$this->add_control(
			  'required_setup_msg',
			  [
				 'type'    => Controls_Manager::RAW_HTML,
				 'raw' => __( '<h3>Requried Setting</h3><p>You can setup in lava directory setting page.</p>', 'jvfrmtd' ),
				 'content_classes' => 'required-setup-msg',
			  ]
			);

		$this->end_controls_section();
	}

	public function add_more_tax_control() {
		$this->start_injection(Array(
			'type' => 'control',
			 'of' => 'meta'
		) );
			$this->add_control( 'more_taxonomy', Array(
				'type' => Controls_Manager::SELECT2,
				'label' => esc_html__("Taxonomy", 'jvfrmtd'),
				'options' => Array('' => esc_html__("None", 'jvfrmtd')) +jvbpd_elements_tools()->getMoreTaxonoiesOptions(),
				'default' => '',
				'condition' => Array(
					'meta' => 'more_tax',
				),
			));
		$this->end_injection();
	}

	protected function render() {
		$this->_render();
	}

	public function getDescription() {
		$format = '<div class="item-description">%1$s</div>';
		if( 'tooltip' === $this->get_settings( 'field_description_type' ) ) {
			$format = '<a class="item-description" data-toggle="tooltip" title="%1$s"><i class="fa fa-exclamation-circle"></i></a>';
		}
		printf( $format, $this->get_settings( 'field_description_content' ) );
	}

	public function after() {
		if( 'yes' === $this->get_settings( 'field_description' ) ) {
			$this->getDescription();
		}
	}

}
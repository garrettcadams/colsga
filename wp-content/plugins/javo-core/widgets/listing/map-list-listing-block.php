<?php
/**
Widget Name: Map list listing block
Author: Javo
Version: 1.0.0.4
*/

namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Scheme_Typography;

if( !defined( 'ABSPATH' ) ) {
	die;
}

class jvbpd_map_list_listing_blocks extends Widget_Base {

	public function get_name() {
		return 'jvbpd-map-list-listing-blocks';
	}

	public function get_title() {
		return 'Listing Blocks (List Type)';
	}

	public function get_icon() {
		return 'fa fa-newspaper-o';
	}

	public function get_categories() {
		return [ 'jvbpd-map-page' ];
	}

    protected function _register_controls() {

        $this->start_controls_section(
			'section_general',
			array(
				'label' => esc_html__( 'Listing Blocks (List Type)', 'jvfrmtd' ),
			)
		);

		$this->add_control(
			'Des', array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => sprintf(
					'<div class="elementor-jv-notice" style="background-color:#9b0a46; color:#ffc6c6; padding:10px;"><ul>'.
					'<li class="doc-link">'.
					esc_html__('How to use this widget.','jvfrmtd').
					'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-map-page/" style="color:#fff;"> ' .
					esc_html__( 'Documentation', 'jvfrmtd' ) .
					'</a></li><li>&nbsp;</li>'.
					'<li class="notice">'.
					esc_html__('This widget is for only map page.', 'jvfrmtd').
					'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-notice/" style="color:#fff;"> ' .
					esc_html__( 'Detail', 'jvfrmtd' ) .
					'</a><br/></li><li>&nbsp;</li><li>'.
					esc_html__( 'Please do not use in other pages.', 'jvfrmtd' ) .
					'</li></ul></div>'
				)
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'section_block_settings', array(
				'show_label' => false,
				'label' => esc_html__( "Block Settings", 'jvfrmtd' ),
		) );

		$this->add_control( 'featured_first', array(
			'label' => esc_html__( 'First Featured Listings', 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => '',
			'label_on' => __( 'Yes', 'jvfrmtd' ),
			'label_off' => __( 'No', 'jvfrmtd' ),
			'return_value' => '1',
		) );

		$this->add_group_control(
			\jvbpd_group_block_style::get_type(),
			Array(
				'name' => 'block',
				'label' => esc_html__( "Module Settings", 'jvfrmtd' ),
				'fields' => Array( 'module', 'columns' ),
			)
		);

		$this->add_control( 'minHeight', Array(
			'label' => esc_html__( "Min height (px)", 'jvfrmtd' ),
			'type' => Controls_Manager::NUMBER,
			'default' => '500',
			'selectors' => Array(
				'{{WRAPPER}} #javo-listings-wrapType-container' => 'min-height: {{VALUE}}px;',
			),
		) );

		$this->end_controls_section();

    /** Filter Panel Style */
    $this->start_controls_section(
			'listing_wrap',
			[
				'label' => __( 'Listing Wrap', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

    $this->add_control(
			'list_wrap_margin',
			[
				'label' => __( 'Margin', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} #results #spaces' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'list_wrap_padding',
			[
				'label' => __( 'Padding', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} #results' => 'padding: 0;',
					'{{WRAPPER}} #results #spaces' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'list_wrap_border',
				'selector' => '{{WRAPPER}} .panel',
			]
		);

		$this->add_control(
			'List_wrap_border_radius',
			[
				'label' => __( 'Border Radius', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} #results #spaces' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'list_wrap_shadow',
				'selector' => '{{WRAPPER}} #results #spaces',
			]
		);

		$this->add_control(
		'list_wrap_bg',
				[
						'label' => __( 'List Wrap Background Color', 'jvfrmtd' ),
						'type' => Controls_Manager::COLOR,
						'scheme' => [
								'type' => Scheme_Color::get_type(),
								'value' => Scheme_Color::COLOR_1,
						],
						'selectors' => [
								'{{WRAPPER}} #results #spaces' => 'background-color: {{VALUE}}',
						],
				]
		);

		$this->end_controls_section();


	    /* Filter Title */
		$this->start_controls_section(
		  'pagination_style',
		  [
			'label' => __( 'Pagination & Load More', 'jvfrmtd' ),
			'tab' => Controls_Manager::TAB_STYLE,
		  ]
		);


		$this->add_control(
			'pgn_btn_bg',
			[
				'label' => __( 'Button Color', 'jvfrmtd' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				'default' => '#aaaaaa',
				'selectors' => [
					'{{WRAPPER}} .javo-map-box-morebutton ' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'pgn_btn_color',
			[
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .javo-map-box-morebutton ' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'pgn_btn_border',
				'selector' => '{{WRAPPER}} #results .javo-map-box-morebutton',
			]
		);

		$this->add_control(
			'pgn_btn_radius',
			[
				'label' => __( 'Border Radius', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} #results .javo-map-box-morebutton' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_group_control( Group_Control_Typography::get_type(), [
		  'name' => 'pgn_btn_typography',
		  'selector' => '{{WRAPPER}} #results .javo-map-box-morebutton',
		  'scheme' => Scheme_Typography::TYPOGRAPHY_1,
		] );

		$this->end_controls_section();





    /* Filter Title */
    $this->start_controls_section(
      'module_styles',
      [
        'label' => __( 'Module', 'jvfrmtd' ),
        'tab' => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control(
			'module_out_padding',
			[
				'label' => __( 'Out Padding', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} div.col-md-4' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} div.col-md-6' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} div.col-md-12' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

    $this->add_control(
    'title_color',
        [
            'label' => __( 'Title Color', 'jvfrmtd' ),
            'type' => Controls_Manager::COLOR,
			'default' => '#cccccc',
            'scheme' => [
                'type' => Scheme_Color::get_type(),
                'value' => Scheme_Color::COLOR_1,
            ],
            'selectors' => [
                '{{WRAPPER}} .panel-heading h3' => 'color: {{VALUE}}',
            ],
        ]
    );

    $this->add_group_control( Group_Control_Typography::get_type(), [
      'name' => 'title_typography',
      'selector' => '{{WRAPPER}} .panel-heading h3',
      'scheme' => Scheme_Typography::TYPOGRAPHY_1,
    ] );
		$this->end_controls_section();


    /* Filter options Style */
    $this->start_controls_section(
      'filter_options',
      [
        'label' => __( 'Filter Options', 'jvfrmtd' ),
        'tab' => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control(
			'filter_panel_body_padding',
			[
				'label' => __( 'Padding', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .panel-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

    $this->add_control(
    'body_bg_color',
        [
            'label' => __( 'Bacground Color', 'jvfrmtd' ),
            'type' => Controls_Manager::COLOR,
            'scheme' => [
                'type' => Scheme_Color::get_type(),
                'value' => Scheme_Color::COLOR_1,
            ],
            'selectors' => [
                '{{WRAPPER}} .panel-body' => 'background-color: {{VALUE}}',
            ],
        ]
    );

    $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'filter_selector_border',
				'selector' => '{{WRAPPER}} .form-control',
			]
		);

    $this->add_control(
    'body_color',
        [
            'label' => __( 'Title Color', 'jvfrmtd' ),
            'type' => Controls_Manager::COLOR,
            'scheme' => [
                'type' => Scheme_Color::get_type(),
                'value' => Scheme_Color::COLOR_1,
            ],
            'selectors' => [
                '{{WRAPPER}} .form-control' => 'color: {{VALUE}}',
            ],
        ]
    );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'body_typography',
			'selector' => '{{WRAPPER}} .form-control',
			'scheme' => Scheme_Typography::TYPOGRAPHY_1,
		] );

		$this->end_controls_section();

    }

    protected function render() {

		$settings = $this->get_settings();
		wp_reset_postdata();
		$isPreviewMode = is_admin();
		//$isPreviewMode = false;

		/*
		if( $isPreviewMode) {
			$previewBaseURL = jvbpdCore()->assets_url . '/images/elementor/listipia/';
			$previewURL = $previewBaseURL . 'map-listing-modules.png';
			printf( '<img src="%s">', esc_url_raw( $previewURL ) );
		}else{ */
			$this->getContent( $settings, get_post() );
			/*
		} */
    }

	public function getContent( $settings, $obj ) {
		$strListOutputClass = sprintf( 'class="%s"', join(
			' ', apply_filters( 'jvbpd_map_list_output_class', Array( 'javo-shortcode', 'row', 'ajax-processing' ) )
		) );

		$this->add_render_attribute( 'wrapper', Array(
			'class' => 'list-block-wrap',
			'data-first-featured' => (boolean) $settings[ 'featured_first' ] == '1' ? 'true' : 'false',
			'data-module' => !empty( $settings[ 'block_module' ] ) ? $settings[ 'block_module' ] : false,
			'data-columns' => !empty( $settings[ 'block_columns' ] ) ? intVal( $settings[ 'block_columns' ] ) : 1,
		) ); ?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<div id="results">
				<div id="spaces">
					<?php do_action( 'jvbpd_' . jvbpdCore()->getSlug() . '_map_list_container_before', $GLOBALS[ 'post' ] ); ?>
					<div id="space-0" class="space" itemscope="" itemtype="http://schema.org/LodgingBusiness">
						<div id="javo-listings-wrapType-container" <?php echo $strListOutputClass;?>></div>
					</div><!--/.space row-->
				</div><!--/#spaces-->
				<button type="button" class="btn btn-default btn-block javo-map-box-morebutton" data-javo-map-load-more>
					<i class="fa fa-spinner fa-spin"></i>
					<?php esc_html_e("Load More", 'jvfrmtd');?>
				</button>
				<?php do_action( 'jvbpd_' . jvbpdCore()->getSlug() . '_map_list_container_after', $GLOBALS[ 'post' ] ); ?>
			</div><!--/#results-->
		</div><!--/.row-->
		<?php
	}
}

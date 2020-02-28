<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) exit;

class jvbpd_block2 extends Widget_Base {   //this name is added to plugin.php of the root folder

	Const PREFIX = 'jv_bpd_block2_';

	public function get_name() {
		return 'jvbpd-block2';
	}

	public function get_title() {
		return 'Block';   // title to show on elementor
	}

	public function get_icon() {
		return 'eicon-sidebar';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
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
					'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-block/" style="color:#fff;"> ' .
					esc_html__( 'Documentation', 'jvfrmtd' ) .
					'</a></li></ul></div>'
				)
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_block_style', [
				'label' => esc_html__( 'Block Style', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'jv_bpd_block2_display_type',
			[
				'label' => esc_html__( "Block type", 'jvfrmtd' ),
                'description' => '',
				'type' => Controls_Manager::SELECT,
				'multiple' => false,
				'default' => '',
				'options' => [
					''  => __( 'Grid', 'jvfrmtd' ),
					'carousel' => __( 'Carousel', 'jvfrmtd' ),
				 ]
			]
		);


		// Grid
		$this->add_control(
            'pagination', [
                'label' => esc_html__( 'Load More Type', 'jvfrmtd' ),
                'type' => Controls_Manager::SELECT,
                'default' => '',
				'condition' => [
					'jv_bpd_block2_display_type' => '',
				],
                'options' => [
                    '' => esc_html__( 'None', 'jvfrmtd' ),
                    'number' => esc_html__( 'Pagination', 'jvfrmtd' ),
                    'loadmore' => esc_html__( 'Load More', 'jvfrmtd' ),
                ]
            ]
        );

		// Carousel
		$this->add_control(
            'carousel_autoplay', [
                'label' => esc_html__( 'Carousel Autoplay', 'jvfrmtd' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
				'condition' => [
					'jv_bpd_block2_display_type' => 'carousel',
				],
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'return_value' => '1',
            ]
        );

		$this->add_control(
            'carousel_loop', [
                'label' => esc_html__( 'Carousel Infinity Loop', 'jvfrmtd' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
				'condition' => [
					'jv_bpd_block2_display_type' => 'carousel',
				],
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'return_value' => '1',
            ]
        );

		$this->add_control(
            'carousel_navigation', [
                'label' => esc_html__( 'Carousel Navigation', 'jvfrmtd' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
				'condition' => [
					'jv_bpd_block2_display_type' => 'carousel',
				],
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'return_value' => '1',
            ]
        );

		$this->add_control(
            'carousel_navi_position', [
				'label' => __( 'Carousel Navigation Position', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'condition' => [
					'carousel_navigation' => '1',
				],
				'default' => 'bottom',
				'options' => [
					'top' => __( 'Top', 'jvfrmtd' ),
					'middle' => __( 'Side', 'jvfrmtd' ),
					'bottom'  => __( 'Bottom', 'jvfrmtd' ),
				],
				'separator' => 'none',
            ]
        );

		$this->add_control(
            'carousel_dots', [
                'label' => esc_html__( 'Carousel Dots Navigation', 'jvfrmtd' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
				'condition' => [
					'jv_bpd_block2_display_type' => 'carousel',
				],
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'return_value' => '1',
            ]
        );

		/**
		$this->add_control(
            'carousel_lazyload', [
                'label' => esc_html__( 'Carousel Lazy Load', 'jvfrmtd' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
				'condition' => [
					'jv_bpd_block2_display_type' => 'carousel',
				],
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'return_value' => '1',
            ]
        ); **/

		$this->add_control(
            'carousel_mouse_wheel', [
                'label' => esc_html__( 'Carousel Enable MouseWheel', 'jvfrmtd' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
				'condition' => [
					'jv_bpd_block2_display_type' => 'carousel',
				],
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'return_value' => '1',
            ]
        );

		$this->add_control(
            'carousel_items_per_slide', [
                'label' => esc_html__( 'Carousel Items Per Slide', 'jvfrmtd' ),
                'type' => Controls_Manager::NUMBER,
                'default' => 3,
				'condition' => [
					'jv_bpd_block2_display_type' => 'carousel',
				],
            ]
        );

		$this->end_controls_section();

		$this->start_controls_section( 'section_block_post_type', [
			'label' => esc_html__( 'Block Post Type', 'jvfrmtd' ),   //section name for controler view
		] );

		$this->add_control( 'jv_bpd_block2_post_type', [
			'label' => esc_html__( 'Post type', 'jvfrmtd' ),
			'description' => esc_html__('Please select a post type you want.','jvfrmtd'),
			'type' => Controls_Manager::SELECT,
			'multiple' => false,
			'default' => '',
			'options' => [
				'lv_listing'  => __( 'Listing', 'jvfrmtd' ),
				'post' => __( 'Post', 'jvfrmtd' ),
			],
			'selectors' => [ '
				{{WRAPPER}} .module.type-post .jv-meta-distance,
				{{WRAPPER}} .module.type-post .module-meta,
				{{WRAPPER}} .module.type-post .detail-icons' => 'display:none;',   //the selector used above in add_control
			]
		]);

		$this->add_control(
			'jv_bpd_block2_custom_term_listing', [
				'label' => __( 'Select / Add Terms', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'condition' => [
					'jv_bpd_block2_post_type' => 'lv_listing',
				],
				'default' => '',
				'options' => [
					'select_terms' => __( 'Select Terms', 'jvfrmtd' ),
					'custom_ids'  => __( 'Add Custom IDs', 'jvfrmtd' ),
				],
				'separator' => 'none',
				'description' => 'Add custom term id or select terms.',
			]
		);

		$this->add_control(
			'jv_bpd_block2_custom_term_post',
			[
				'label' => __( 'Terms Selection Type : Post', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'condition' => [
					'jv_bpd_block2_post_type' => 'post',
				],
				'default' => '',
				'options' => [
					'select_terms' => __( 'Select Terms', 'jvfrmtd' ),
					'custom_ids'  => __( 'Add Custom IDs', 'jvfrmtd' ),
				],
				'separator' => 'none',
				'description' => 'Add custom term id or select terms.',
			]
		);

		$this->add_control(
			'jv_bpd_block2_custom_listing_terms_ids',
			[
				'label' => esc_html__( 'Custom Terms IDs', 'jvfrmtd' ),
				'description' => esc_html__('Enter category IDs separated by commas (ex: 13,23,18). To exclude categories please add "-" (ex: -9, -10)','jvfrmtd'),
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'jv_bpd_block2_post_type' => 'lv_listing',
					'jv_bpd_block2_custom_term_listing' => 'custom_ids',
				],
				'default' => __('','jvfrmtd'),
				'separator' => 'none',
			]
		);

		$this->add_control(
			'jv_bpd_block2_custom_post_terms_ids',
			[
				'label' => esc_html__( 'Custom Terms IDs', 'jvfrmtd' ),
				'description' => esc_html__('Enter category IDs separated by commas (ex: 13,23,18). To exclude categories please add "-" (ex: -9, -10)','jvfrmtd'),
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'jv_bpd_block2_post_type' => 'post',
					'jv_bpd_block2_custom_term_post' => '1',
				],
				'default' => __('','jvfrmtd'),
				'separator' => 'none',
			]
		);

		$postTaxonomies_options = jvbpd_elements_tools()->get_taxonomies( 'post' );
		$listingTaxonomies_options = jvbpd_elements_tools()->get_taxonomies( 'lv_listing' );

		$this->add_control(
			'post_taxonomy',
			Array(
				'label'       => __( 'Post Taxonomy', 'jvfrmtd' ),
				'type'        => Controls_Manager::SELECT2,
				'condition' => Array(
					'jv_bpd_block2_post_type' => 'post',
					'jv_bpd_block2_custom_term_listing' => 'select_terms',
				),
				'options' => $postTaxonomies_options,
				'separator' => 'none',
			)
		);

		jvbpd_elements_tools()->add_tax_term_control( $this, 'post_%1$s_term', Array(
			'taxonomies' => array_keys( $postTaxonomies_options ),
			'parent' => 'post_taxonomy',
			'label' => esc_html__( '%1$s Terms', 'jvfrmtd' ),
			'condition' => Array( 'jv_bpd_block2_post_type' => 'post' ),
			'type' => Controls_Manager::SELECT2,
		) );

		$this->add_control(
			'lv_listing_taxonomy',
			Array(
				'label'       => __( 'Listing Taxonomy', 'jvfrmtd' ),
				'type'        => Controls_Manager::SELECT2,
				'condition' => Array(
					'jv_bpd_block2_post_type' => 'lv_listing',
					'jv_bpd_block2_custom_term_listing' => 'select_terms',
				),
				'options' => $listingTaxonomies_options,
				'separator' => 'none',
			)
		);

		jvbpd_elements_tools()->add_tax_term_control( $this, 'lv_listing_%1$s_term', Array(
			'taxonomies' => array_keys( $listingTaxonomies_options ),
			'label' => esc_html__( '%1$s Terms', 'jvfrmtd' ),
			'parent' => 'lv_listing_taxonomy',
			'condition' => Array( 'jv_bpd_block2_post_type' => 'lv_listing' ),
			'type' => Controls_Manager::SELECT2,
		) );

		$this->add_control(
			'jv_bpd_block2_featured',
			[
				'label' => __( 'Display only featured items', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [
					'jv_bpd_block2_post_type' => 'lv_listing',
				],
				'default' => '',
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'return_value' => '1',
				'condition' => [
					'jv_bpd_block2_post_type' => 'lv_listing',
				],
				'separator' => 'none',
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_block_setting',
			[
				'label' => esc_html__( 'Block Setting', 'jvfrmtd' ),   //section name for controler view
			]
		);

		$this->add_control(
			'jv_bpd_block2_filter_style',
			[
				'label' => esc_html__( 'Filter Style', 'jvfrmtd' ),
                'description' => esc_html__('Please select a filter type you want.','jvfrmtd'),
				'type' => Controls_Manager::SELECT,
				'multiple' => false,
				'default' => '',
				'options' => [
					'' => __( 'None', 'jvfrmtd' ),
					'general' => esc_html__( 'Style1', 'jvfrmtd' ),
					'linear' => esc_html__( 'Style2', 'jvfrmtd' ),
					'box' => esc_html__( 'Style3', 'jvfrmtd' ),
				 ]
			]
		);

		$this->add_control(
			'jv_bpd_block2_title',
			[
				'label' => esc_html__( 'Title', 'jvfrmtd' ),
				'type' => Controls_Manager::TEXT,
				'default' => __('','jvfrmtd'),
				'placeholder' => __('Please add a title.','jvfrmtd'),
				'separator' => 'none',
				'condition' => [
					'jv_bpd_block2_filter_style!'=>'',
				],
			]
		);

		$this->add_control(
			'jv_bpd_block_num',
			[
				'label' => esc_html__( 'Block Number', 'jvfrmtd' ),
                'description' => esc_html__('Please select a block you want','jvfrmtd'),
				'type' => Controls_Manager::SELECT,
				'multiple' => false,
				'default' => 'block2',
				'options' => [
					'block1' => __( 'Block 1', 'jvfrmtd' ),
					'block2'  => __( 'Block 2', 'jvfrmtd' ),
					'block3'  => __( 'Block 3', 'jvfrmtd' ),
					'block4'  => __( 'Block 4', 'jvfrmtd' ),
					//'block5'  => __( 'Block 5', 'jvfrmtd' ),
					//'block8'  => __( 'Block 8', 'jvfrmtd' ),
					//'block10'  => __( 'Block 10', 'jvfrmtd' ),
					'block11'  => __( 'Block 11', 'jvfrmtd' ),
					//'block12'  => __( 'Block 12', 'jvfrmtd' ),
					//'block16'  => __( 'Block 16', 'jvfrmtd' ),
				 ]
			]
		);

		$this->add_control(
			'jv_bpd_block2_column',
			[
				'label' => esc_html__( 'Columns', 'jvfrmtd' ),
                'description' => esc_html__('Please select a column you want','jvfrmtd'),
				'type' => Controls_Manager::SELECT,
				'multiple' => false,
				'default' => '2',
				'options' => [
					'1' => __( '1 Column', 'jvfrmtd' ),
					'2'  => __( '2 Columns', 'jvfrmtd' ),
					'3'  => __( '3 Columns', 'jvfrmtd' ),
					'4'  => __( '4 Columns', 'jvfrmtd' ),
				 ]
			]
		);

		$this->add_control(
			'jv_bpd_block2_contents_length',
			[
				'label' => esc_html__( 'Limit length of description', 'jvfrmtd' ),
				'type' => Controls_Manager::TEXT,
				'default' => __('','jvfrmtd'),
				'placeholder' => __('Contents length','jvfrmtd')
			]
		);

		$this->add_control(
			'jv_bpd_block2_count',
			[
				'label' => esc_html__( 'Number of posts to load', 'jvfrmtd' ),
				'type' => Controls_Manager::TEXT,
				'default' => __('6','jvfrmtd'),
				'placeholder' => __('Type the number of posts you want to load.','jvfrmtd')
			]
		);

		$this->add_control( 'jv_bpd_block2_order_by', Array(
			'label' => esc_html__( 'Order By', 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT,
			'options' => Array(
				'' => __( 'None', 'jvfrmtd' ),
				'title'  => __( 'Post Title', 'jvfrmtd' ),
				'date'  => __( 'Date', 'jvfrmtd' ),
				'rand'  => __( 'Random', 'jvfrmtd' ),
				'rating' => esc_html__( "Rating", 'jvfrmtd' ),
			)
		) );

		$this->add_control(
			'jv_bpd_block2_order_type',
			[
				'label' => esc_html__( 'Order Type', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => [
					'DESC' => __( 'Descending', 'jvfrmtd' ),
					'ASC'  => __( 'Ascending', 'jvfrmtd' )
				 ]
			]
		);

		$this->add_control(
			'jv_bpd_block2_loading_style',
			[
				'label' => esc_html__( 'Loading Style', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'None', 'jvfrmtd' ),
					'rect'  => __( 'Rectangle', 'jvfrmtd' ),
					'circle'  => __( 'Circle', 'jvfrmtd' ),
				 ]
			]
		);

		$this->end_controls_section();


        $this->start_controls_section(
            'section_style_filter',
            [
                'label' => esc_html__( 'Filter', 'jvfrmtd' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

		 $this->add_control(
            'filter_title_text_transform',
            [
                'label' => esc_html__( 'Filter Title Text Transform', 'jvfrmtd' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'uppercase',
                'options' => [
                    '' => esc_html__( 'None', 'jvfrmtd' ),
                    'uppercase' => esc_html__( 'UPPERCASE', 'jvfrmtd' ),
                    'lowercase' => esc_html__( 'lowercase', 'jvfrmtd' ),
                    'capitalize' => esc_html__( 'Capitalize', 'jvfrmtd' ),
                ],
                'selectors' => [
					'{{WRAPPER}} .javo-shortcode .shortcode-header .shortcode-title' => 'text-transform: {{VALUE}};',   //the selector used above in add_control
                ],
            ]
        );

        $this->add_responsive_control(
            'filter_title_font_size',
            [
                'label' => esc_html__( 'Filter Title Size', 'jvfrmtd' ),
                'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 14,
				],
                'range' => [
                    'px' => [
                        'min' => 8,
                        'max' => 50,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .javo-shortcode .shortcode-header .shortcode-title' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'filter_title_color',
            [
                'label' => esc_html__( 'Fitler Title Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
				'default' => '#fff',
                'selectors' => [
					'{{WRAPPER}} .javo-shortcode .shortcode-header .shortcode-title' => 'color: {{VALUE}};',
                ],
            ]
        );

		$this->add_responsive_control(
            'filter_primary_color',
            [
                'label' => esc_html__( 'Filter Primary Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
				'default' => '#4c58a4',
                'selectors' => [
					'{{WRAPPER}} .javo-shortcode.filter-general .shortcode-header .shortcode-title' => 'background-color: {{VALUE}} !important;',
					'{{WRAPPER}} .javo-shortcode.filter-general .shortcode-header' => 'border-color: {{VALUE}} !important;',
					'{{WRAPPER}} .javo-shortcode.filter-linear .shortcode-header .shortcode-title' => 'border-bottom:2px solid {{VALUE}};margin-bottom: -2px;',
					'{{WRAPPER}} .javo-shortcode.filter-box .shortcode-header .shortcode-title' => 'background-color: {{VALUE}} !important;',
					'{{WRAPPER}} .javo-shortcode.filter-box .shortcode-header' => 'border-color: {{VALUE}} !important;',
                ],
            ]
        );

				$this->add_responsive_control(
		            'filter_loading_overlay_color',
		            [
		                'label' => esc_html__( 'Filter Loading Overlay', 'jvfrmtd' ),
		                'type' => Controls_Manager::COLOR,
										'default' => 'rgba(237, 239, 245, 0.82)',
		             		'selectors' => [
									 	'{{WRAPPER}} .javo-shortcode .output-cover' => 'background-color: {{VALUE}};',
		                ],
		            ]
		        );

		$this->add_responsive_control(
            'filter_tax_size',
            [
                'label' => esc_html__( 'Filter Taxonomy Size', 'jvfrmtd' ),
                'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 12,
				],
                'range' => [
                    'px' => [
                        'min' => 8,
                        'max' => 50,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .javo-shortcode .shortcode-header .shortcode-nav ul.shortcode-filter > li' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'filter_tax_color',
            [
                'label' => esc_html__( 'Fitler Tax Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
				'default' => '#7a7a7a',
                'selectors' => [
					'{{WRAPPER}} .javo-shortcode .shortcode-header .shortcode-nav ul.shortcode-filter > li' => 'color: {{VALUE}};',
                ],
            ]
        );

		  $this->add_control(
            'filter_tax_transform',
            [
                'label' => esc_html__( 'Filter Tax Transform', 'jvfrmtd' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'uppercase',
                'options' => [
                    '' => esc_html__( 'None', 'jvfrmtd' ),
                    'uppercase' => esc_html__( 'UPPERCASE', 'jvfrmtd' ),
                    'lowercase' => esc_html__( 'lowercase', 'jvfrmtd' ),
                    'capitalize' => esc_html__( 'Capitalize', 'jvfrmtd' ),
                ],
                'selectors' => [
					'{{WRAPPER}} .javo-shortcode.filter-linear .shortcode-header .shortcode-nav ul.shortcode-filter > li' => 'text-transform: {{VALUE}};',   //the selector used above in add_control
                ],
            ]
        );
		$this->end_controls_section();


        $this->start_controls_section(
            'section_style_content',
            [
                'label' => esc_html__( 'Content', 'jvfrmtd' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

				$this->add_responsive_control(
            'image_default_overlay',
            [
                'label' => esc_html__( 'Image Default Overlay', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
								'default' => 'rgba(64,84,178,0.04)',
                'selectors' => [
									'{{WRAPPER}} .javo-shortcode .module.javo-module12 .thumb-wrap .javo-thb:after' => 'background-color: {{VALUE}};',
                ],
            ]
        );


        $this->add_control(
            'title_text_transform',
            [
                'label' => esc_html__( 'Title Text Transform', 'jvfrmtd' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'uppercase',
                'options' => [
                    '' => esc_html__( 'None', 'jvfrmtd' ),
                    'uppercase' => esc_html__( 'UPPERCASE', 'jvfrmtd' ),
                    'lowercase' => esc_html__( 'lowercase', 'jvfrmtd' ),
                    'capitalize' => esc_html__( 'Capitalize', 'jvfrmtd' ),
                ],
                'selectors' => [
					'{{WRAPPER}} .module.card > .card-block .card-title' => 'text-transform: {{VALUE}};',   //the selector used above in add_control
                ],
            ]
        );

        $this->add_responsive_control(
            'title_font_size',
            [
                'label' => esc_html__( 'Title Size', 'jvfrmtd' ),
                'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 14,
				],
                'range' => [
                    'px' => [
                        'min' => 8,
                        'max' => 50,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .javo-shortcode .module.card > .card-block .card-title a' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_color',
            [
                'label' => esc_html__( 'Title Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
				'default' => '#333',
                'selectors' => [
					'{{WRAPPER}} .javo-shortcode .module.card > .card-block .card-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_color_hover',
            [
                'label' => esc_html__( 'Title Hover Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .entry-title a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'meta_color',
            [
                'label' => esc_html__( 'Meta Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .entry-meta a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'meta_hover_color',
            [
                'label' => esc_html__( 'Meta Hover Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .entry-meta a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'meta_color_i',
            [
                'label' => esc_html__( 'Meta Icon Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .entry-meta' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'excerpt_text_transform',
            [
                'label' => esc_html__( 'Excerpt Transform', 'jvfrmtd' ),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => esc_html__( 'None', 'jvfrmtd' ),
                    'uppercase' => esc_html__( 'UPPERCASE', 'jvfrmtd' ),
                    'lowercase' => esc_html__( 'lowercase', 'jvfrmtd' ),
                    'capitalize' => esc_html__( 'Capitalize', 'jvfrmtd' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .blog-excerpt p' => 'text-transform: {{VALUE}};',   //the selector used above in add_control
                ],
            ]
        );

        $this->add_responsive_control(
            'excerpt_font_size',
            [
                'label' => esc_html__( 'Excerpt Size', 'jvfrmtd' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                        'step' => 5,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .blog-excerpt p' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'exceprt_color',
            [
                'label' => esc_html__( 'Excerpt Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .blog-excerpt p' => 'color: {{VALUE}};',
                ],
            ]
        );


        $this->add_responsive_control(
            'te_align',
            [
                'label' => __( 'Text Alignment', 'jvfrmtd' ),
                'type' => Controls_Manager::CHOOSE,
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
                    'justify' => [
                        'title' => __( 'Justified', 'jvfrmtd' ),
                        'icon' => 'fa fa-align-justify',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .blog-excerpt p' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'pagination_align',
            [
                'label' => __( 'Pagination Alignment', 'jvfrmtd' ),
                'type' => Controls_Manager::CHOOSE,
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
                    'justify' => [
                        'title' => __( 'Justified', 'jvfrmtd' ),
                        'icon' => 'fa fa-align-justify',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .void-grid-nav' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'pagi_font_size',
            [
                'label' => esc_html__( 'Pagination Size', 'jvfrmtd' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .void-grid-nav' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
		$this->end_controls_section();


        $this->start_controls_section(
            'section_style_custom',
            [
                'label' => esc_html__( 'Custom Code', 'jvfrmtd' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );


		$this->add_responsive_control(
            'custom_css',
            [
                'label' => esc_html__( 'Custom CSS', 'jvfrmtd' ),
                'type' => Controls_Manager::TEXTAREA,
			    'default' => __( 'Add your own CSS code', 'jvfrmtd' ),
                'selectors' => [
                    '' => '{{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings();
		$carouselOptions = Array(
			'autoplay' => $settings[ 'carousel_autoplay' ] === '1',
			'loop' => $settings[ 'carousel_loop' ] === '1',
			'mousewheel' => $settings[ 'carousel_mouse_wheel' ] === '1',
			'nav' => $settings[ 'carousel_navigation' ] === '1',
			'nav_pos' => $settings[ 'carousel_navi_position' ],
			'dots' => $settings[ 'carousel_dots' ] === '1',
			'items' => intVal( $settings[ 'carousel_items_per_slide' ] ),
		);

		$post_type = $this->get_settings( self::PREFIX . 'post_type' );
		$shortcode_attributes = '';
		$shortcode_attributes_args = Array(
			'title' => $this->get_settings( self::PREFIX . 'title' ),
			'post_type' => $this->get_settings( self::PREFIX . 'post_type' ),

			'carousel' => esc_attr( json_encode( $carouselOptions ) ),
			'featured_' . $post_type => $this->get_settings( self::PREFIX . 'featured' ),

			'module_contents_length' => $this->get_settings( self::PREFIX . 'contents_length' ),
			'filter_style' => $this->get_settings( self::PREFIX . 'filter_style' ),

			'order_' => $this->get_settings( self::PREFIX . 'order_type' ),
			'order_by' => $this->get_settings( self::PREFIX . 'order_by' ),

			'filter_by' => $this->get_settings( $post_type . '_taxonomy' ),

			'columns' => $this->get_settings( self::PREFIX . 'column' ),
			'count' => $this->get_settings( self::PREFIX . 'count' ),
			'loading_style' => $this->get_settings( self::PREFIX . 'loading_style' ),
			'pagination' => $this->get_settings( 'pagination' ),
			'title_text_transform' => $this->get_settings( 'title_text_transform' ),
			'block_display_type' => $this->get_settings( self::PREFIX . 'display_type' ),
		);

		$filter_by = 'post' == $post_type ? 'filter_by' : $post_type . '_filter_by';
		$custom_filter = $post_type ? 'custom_filter' : 'custom_filter_' . $post_type;
		$selectTermField = 'post' == $post_type ? 'custom_filter_by_post' : '';
		$select_term_type = 'post' == $post_type ? self::PREFIX . '_custom_term_post' : self::PREFIX . '_custom_term_listing';
		$custom_term_ids = 'post' == $post_type ? self::PREFIX . '_custom_post_terms_ids' : self::PREFIX . '_custom_listing_terms_ids';

		if( $this->get_settings( $select_term_type ) == 'select_terms' ) {
		}else{
			$shortcode_attributes_args[ $custom_filter ] = $this->get_settings( self::PREFIX . 'custom_term_listing' );
			$shortcode_attributes_args[ 'custom_filter_by_' . $post_type ] = $this->get_settings( $custom_term_ids );
		}

		foreach( $shortcode_attributes_args as $key => $value ) {
			$shortcode_attributes .= join( '', Array( $key, '=', "'", $value, "'", ' ' ) );
		}

		$str_shortcode = sprintf(
			'[jvbpd_%1$s %2$s]',
			$this->get_settings( 'jv_bpd_block_num' ),
			$shortcode_attributes
		);

		add_filter( \Jvbpd_Core::get_instance()->prefix . '_shotcode_query', array( $this, 'rating_query' ), 10, 2 );
		echo do_shortcode( $str_shortcode );
		remove_filter( \Jvbpd_Core::get_instance()->prefix . '_shotcode_query', array( $this, 'rating_query' ), 10, 2 );
    }

	public function rating_query( $args, $obj ) {
		if( 'rating' != $this->get_settings( self::PREFIX . 'order_by' ) ) {
			return $args;
		}

		$args[ 'meta_query' ] = Array(
			'relation' => 'OR',
			'rating' => Array(
				'key' => 'rating_average',
				'type' => 'NUMERIC',
				'compare' => 'EXISTS',
			),
			'rating_not' => Array(
				'key' => 'rating_average',
				'type' => 'NUMERIC',
				'compare' => 'NOT_EXISTS',
			),
		);

		$args[ 'orderby' ] = Array(
			'rating' => $this->get_settings( self::PREFIX . 'order_type' ),
			'rating_not' => $this->get_settings( self::PREFIX . 'order_type' ),
		);

		unset( $args[ 'order' ] );

		return $args;
	}
}

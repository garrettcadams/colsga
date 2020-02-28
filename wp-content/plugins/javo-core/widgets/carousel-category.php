<?php
/*
Widget Name: Javo Widget
Description: Javo widget
Author: Javothemes
Author URI: https://www.javothemes.com
*/
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Utils;

use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


class jvbpd_carousel_category extends Widget_Base {

	public function get_name() {
		return 'jvbpd-carousel-category';
	}

	public function get_title() {
		return 'Carousel category';   // title to show on elementor
	}

	public function get_icon() {
		return 'fa fa-info-circle';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-elements' ];    // category of the widget
	}

	protected function _register_controls() {

    $this->start_controls_section(
			'section_general',
			array(
				'label' => esc_html__( 'Carousel category', 'jvfrmtd' ),
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
					'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-single-listing-page/" style="color:#fff;"> ' .
					esc_html__( 'Documentation', 'jvfrmtd' ) .
					'</a></li><li>&nbsp;</li>'.
					'<li class="notice">'.
					esc_html__('This widget is for only single listing detail page.', 'jvfrmtd').
					'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-notice/" style="color:#fff;">' .
					esc_html__( 'Detail', 'jvfrmtd' ) .
					'</a><br/></li><li>&nbsp;</li><li>'.
					esc_html__( 'Please do not use in other pages.', 'jvfrmtd' ) .
					'</li></ul></div>'
				)
			)
    );

	$this->end_controls_section();


	$this->start_controls_section(
				'section_block_setting',
					[
						'label' => esc_html__( 'Carousel category Setting', 'jvfrmtd' ),   //section name for controler view
					]
	);

	$taxonomies_options = jvbpd_elements_tools()->get_taxonomies( 'lv_listing' );
	$this->add_control( 'list', Array(
		'label' => __( 'Repeater List', 'jvfrmtd' ),
		'type' => Controls_Manager::REPEATER,
		'default' => [
			[
				'list_title' => __( 'Menu #1', 'jvfrmtd' ),
			],
			[
				'list_title' => __( 'Menu #2', 'jvfrmtd' ),
			],
			[
				'list_title' => __( 'Menu #3', 'jvfrmtd' ),
			],
			[
				'list_title' => __( 'Menu #4', 'jvfrmtd' ),
			],
			[
				'list_title' => __( 'Menu #5', 'jvfrmtd' ),
			],
		],
		'fields' => jvbpd_elements_tools()->add_tax_term_control( $this, '%1$s_term', Array(
			'taxonomies' => array_keys( $taxonomies_options ),
			'label' => esc_html__( '%1$s Terms', 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT2,
			'repeat_items' => Array(
				Array(
					'name' => 'list_title',
					'label' => __( 'Title', 'jvfrmtd' ),
					'type' => Controls_Manager::TEXT,
					'default' => __( 'List Title' , 'jvfrmtd' ),
					'label_block' => true,
					),
				Array(
					'name' => 'switcher_icon_image',
					'label' => __( 'Switch icon or image', 'jvfrmtd' ),
					'type' => Controls_Manager::SWITCHER,
					'label_off' => __( 'icon', 'jvfrmtd' ),
					'label_on' => __( 'image', 'jvfrmtd' ),
				),
				Array(
					'name' => 'category_image',
					'label' => __( 'Choose Image', 'jvfrmtd' ),
					'type' => Controls_Manager::MEDIA,
					'description' => 'It gets a thumbnail image.',

					'default' => [
						'url' => Utils::get_placeholder_image_src(),
					],
					'condition' => [
					    'switcher_icon_image' => 'yes',
					],
				),
				Array(
					'name' => '_category_icon',
					'label' => __( 'Choose Icon', 'jvfrmtd' ),
					'type' => Controls_Manager::ICONS,
					'fa4compatibility' => 'category_icon',
					'default' => Array(
						'value' => 'fas fa-star',
						'library' => 'solid',
					),
					'condition' => [
						'switcher_icon_image!' => 'yes',
					],
				),
				Array(
					'name' => 'switcher_custom_tax',
					'label' => __( 'Switch Link', 'jvfrmtd' ),
					'type' => Controls_Manager::SWITCHER,
					'label_off' => __( 'Custom', 'jvfrmtd' ),
					'label_on' => __( 'Tax', 'jvfrmtd' ),
				),
				Array(
					'name' => 'taxonomy',
					'label' => __( 'Taxonomy', 'jvfrmtd' ),
					'type' => Controls_Manager::SELECT2,
					'options' => $taxonomies_options,
					'condition' => [
						'switcher_custom_tax' => 'yes',
					],
				),
				Array(
					'name' => 'custom_link',
					'label' => __( 'Custom Link', 'jvfrmtd' ),
					'type' => Controls_Manager::URL,
					'default' => [
						 'url' => 'http://',
						 'is_external' => '',
					 ],
					'show_external' => true, // Show the 'open in new tab' button.
					'condition' => [
						'switcher_custom_tax!' => 'yes',
					],
				),
			)
		) ),
		'title_field' => '{{{ list_title }}}',
	) );

	$this->end_controls_section();

	//Style
	$this->start_controls_section(
			'section_style_icon',
			[
				'label' => __( 'Icon', 'jvfrmtd' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Icon Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .mediabc i' => 'color: {{VALUE}}; border-color: {{VALUE}};',
					'{{WRAPPER}} .mediabc img' => 'color: {{VALUE}}; border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
		  'border_color',
		  [
			'label' => __( 'Border Color', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'default' => '#ffcf05',
			'scheme' => [
			  'type' => Scheme_Color::get_type(),
			  'value' => Scheme_Color::COLOR_1,
			],
			'selectors' => [
			  '{{WRAPPER}} .mediabc i' => 'border: 2px solid {{VALUE}};',
			  '{{WRAPPER}} .mediabc img' => 'border: 2px solid {{VALUE}};',
			],
		  ]
		);

		$this->add_control(
		'media-wrap-padding',
		[
			'label' => __( 'Media Wrap Padding', 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'default' => [
				'size' => 20,
			],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 100,
				],
				'%' => [
					'min' => 0,
					'max' => 100,
				],
			],
			'size_units' => [ 'px', '%' ],
			'selectors' => [
				'{{WRAPPER}} .mediabc i' => 'padding: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .mediabc img' => 'padding: {{SIZE}}{{UNIT}};',
			],
		]
		);


		$this->add_control(
		'media-wrap-radius',
		[
			'label' => __( 'Media Wrap Radius', 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'default' => [
				'size' => 100,
			],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 100,
				],
				'%' => [
					'min' => 0,
					'max' => 100,
				],
			],
			'size_units' => [ 'px', '%' ],
			'selectors' => [
				'{{WRAPPER}} .mediabc i' => 'border-radius: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .mediabc img' => 'border-radius: {{SIZE}}{{UNIT}};',
			],
		]
		);



		$this->add_responsive_control(
			'icon_space',
			[
				'label' => __( 'Spacing', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .mediabc i' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'(mobile){{WRAPPER}} .mediabc i' => 'margin-bottom: {{SIZE}}{{UNIT}};',

					'{{WRAPPER}} .mediabc img' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'(mobile){{WRAPPER}} .mediabc img' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Icon Size', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 35,
				],
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .mediabc i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
		'image_size',
			[
				'label' => __( 'Image Size', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 60,
				],
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} img.cate-img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'rotate',
			[
				'label' => __( 'Rotate', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
					'unit' => 'deg',
				],
				'selectors' => [
					'{{WRAPPER}} .mediabc i' => 'transform: rotate({{SIZE}}{{UNIT}});',
					'{{WRAPPER}} .mediabc img' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_hover',
			[
				'label' => __( 'Icon Hover', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_control(
			'swicher_icon_background',
			[
				'label' => __( 'Hover Icon', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => __( 'On', 'jvfrmtd' ),
				'label_off' => __( 'Off', 'jvfrmtd' ),
			]
		);
		$this->add_control(
			'hover_iconbackground_color',
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffcf05',
				'selectors' => [
					' {{WRAPPER}} .mediabc i:hover' => 'background-color: {{VALUE}};',
					' {{WRAPPER}} .mediabc img:hover' => 'background-color: {{VALUE}};',
				],
				'condition' => [
						'swicher_icon_background!' => 'yes',
				],
			]
		);
		$this->add_control(
			'hover_icon_color',
			[
				'label' => __( 'Icon Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffcf05',
				'selectors' => [
					' {{WRAPPER}} .mediabc i:hover' => 'color: {{VALUE}};',
					' {{WRAPPER}} .mediabc img:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
						'swicher_icon_background' => 'yes',
				],
			]
		);

		$this->add_control(
			'hover_animation',
			[
				'label' => __( 'Hover Animation', 'jvfrmtd' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_content',
			[
				'label' => __( 'Content', 'jvfrmtd' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'carousel_background_color',
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(0,0,0,0.6)',
				'selectors' => [
					' {{WRAPPER}} .owl-stage-outer' => 'background-color: {{VALUE}};',
				],
			]
		);
 		$this->add_control(
		'carousel_pading_top',
		[
			'label' => __( 'Content Pading Top', 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'default' => [
				'size' => 25,
			],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 100,
				],
				'%' => [
					'min' => 0,
					'max' => 100,
				],
			],
			'size_units' => [ 'px', '%' ],
			'selectors' => [
				'{{WRAPPER}} .mediabc' => 'padding-top: {{SIZE}}{{UNIT}};',
			],
		]
		);
		$this->add_control(
		'carousel_pading_bottom',
		[
			'label' => __( 'Content Pading Bottom', 'jvfrmtd' ),
			'type' => Controls_Manager::SLIDER,
			'default' => [
				'size' => 20,
			],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 100,
				],
				'%' => [
					'min' => 0,
					'max' => 100,
				],
			],
			'size_units' => [ 'px', '%' ],
			'selectors' => [
				'{{WRAPPER}} .mediabc' => 'padding-bottom: {{SIZE}}{{UNIT}};',
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
			'title_color',
			[
				'label' => __( 'Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .carousel-icon-title' => 'color: {{VALUE}};',
				],
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .carousel-icon-title',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
			]
		);

		$this->end_controls_section();
	}



    protected function render() {

  	$settings = $this->get_settings();
	$list = $this->get_settings( 'list' );

		if( ! class_exists( '\Jvbpd_Shortcode_Parse' ) ) {
			return;
		}

		$carouselOptions = Array(
			'autoplay' => true, // $settings[ 'carousel_autoplay' ] === '1',
			'loop' => true, // $settings[ 'carousel_loop' ] === '1',
			'mousewheel' => true, // $settings[ 'carousel_mouse_wheel' ] === '1',
			'nav' => true, // $settings[ 'carousel_navigation' ] === '1',
			'nav_pos' => true, // $settings[ 'carousel_navi_position' ],
			'dots' => true, // $settings[ 'carousel_dots' ] === '1',
			'items' => 6,
			/* 'items' => intVal( $settings[ 'carousel_items_per_slide' ] ), */
		);
		if ( $list ) {
			$objWideCateShortcode = new \Jvbpd_Shortcode_Parse( Array(
				'block_display_type' => 'carousel',
				'carousel' => json_encode( $carouselOptions ),
			) );
			$objWideCateShortcode->sHeader();
			?>
			<div id="<?php echo esc_attr( $objWideCateShortcode->getID() ); ?>" class="shortcode-container no-flex-menu nav-active is-carousel">
				<?php
				echo '<div class="shortcode-output">';
					foreach ( $list as $item ) {

						if($item['switcher_custom_tax']=='yes'){
							if( isset( $item[ 'taxonomy' ] ) ) {
								$taxonomy = $item[ 'taxonomy' ];
								$term = isset( $item[ $taxonomy . '_term' ] ) ? get_term_by( 'slug', $item[ $taxonomy . '_term' ], $taxonomy ) : false;
								if( $term instanceof \WP_Term ) {
									//echo get_term_link( $term );
									$get_title_link = '<a href="'. get_term_link( $term ).'">';
								}
							}
						}else{ // custom link
							$website_link = $item['custom_link'];
							$url = $website_link['url'];
							$target = $website_link['is_external'] ? 'target="_blank"' : '';
							$get_title_link ='<a href="' . $url . '" ' . $target .'>';
						}

						//category_image
						$image = $item['category_image'];
						if($item['switcher_icon_image']=='yes'){
							echo $get_title_link.'<div class="mediabc" style="display: table-cell;">'.wp_get_attachment_image( $image['id'], 'thumbnail', "", ["class" => "cate-img"]);
							//echo $get_title_link.'<div class="mediabc" style="display:grid;"><i class="cate-img" src="'.$image['id'].'" style="margin-left:auto;margin-right:auto;"></i>';
						}else{
							echo $get_title_link.'<div class="mediabc" style="display:grid;">';
							if(
								isset($item['__fa4_migrated']['_category_icon']) ||
								(empty($item['category_icon']) && Icons_Manager::is_migration_allowed())
							) {
								Icons_Manager::render_icon( $item['_category_icon'], Array('aria-hidden' => 'true') );
							}else{
								printf('<i class="%s" style="margin:0 auto;"></i>', $item['category_icon']);
							}
							/*
							echo '<i class="'.$item['category_icon'].'" style="margin-left:auto;margin-right:auto;"></i>'; */
						}

						echo '<p class="carousel-icon-title" style="text-align:center;">'. $item['list_title'].'</p>';
						echo '</div>';
					}
				echo '</div> <!-- shortcode-output -->';
				$objWideCateShortcode->sParams();
				$objWideCateShortcode->sFooter();
				// echo '</div>';
			echo '</div>';
		}
	}
}
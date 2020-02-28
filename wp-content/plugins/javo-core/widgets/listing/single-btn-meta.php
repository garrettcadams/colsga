<?php
/**Widget Name: Single Button Meta widget
Author: Javo
Version: 1.0.0.1
*/

namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

use Elementor\Scheme_Color;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class jvbpd_single_btn_meta extends Widget_Base {

	public $review_instance = NULL;

	public function get_name() {
		return 'jvbpd-single-btn-meta';
	}

	public function get_title() {
		return 'Single Header Buttons';   // title to show on elementor
	}

	public function get_icon() {
		return 'eicon-gallery-group';    // eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-single-listing' ];    // category of the widget
	}

    protected function _register_controls() {
		$this->getReviewInstance();

		$this->start_controls_section(
			'section_selection',
			array(
				'label' => esc_html__( 'Select a Meta', 'jvfrmtd' ),
			)
		);

		$this->add_control(
			'select_meta',
			[
				'label' => __( 'Select', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'switch' => __( 'Header Switch', 'jvfrmtd' ),
					'single_buttons' => __( 'Buttons', 'jvfrmtd' ),
				],
			]
		);

		/** Switch Buttons **/
        $this->add_responsive_control( 'width', Array(
            'label' => __( "Button's Width", 'jvfrmtd' ),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => 260,
            ],
            'description' => __( 'Default : 260px to show 4 icons and hide others', 'jvfrmtd'),
            'range' => [
                'px' => [
                    'min' => 50,
                    'max' => 360,
                    'step' => 1,
                ],
                '%' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'size_units' => [ 'px', '%' ],
            'selectors' => [
                '{{WRAPPER}} .javo-core-single-featured-switcher' => 'width: {{SIZE}}{{UNIT}};',
			],
			'condition' => [
				'select_meta' => 'switch',
			],
		) );



		$this->add_control( 'header_switchers', Array(
			'label' => __( 'Header Switchers', 'jvfrmtd' ),
			'type' => Controls_Manager::REPEATER,
			'fields' => Array(
				Array(
					'name' => 'list_class',
					'label' => __( 'Button Type', 'jvfrmtd' ),
					'type' => Controls_Manager::SELECT,
					'options' => jvbpd_elements_tools()->getHeaderSwitcherOptions(),
				),
				Array(
					'name' => 'list_title',
					'label' => esc_html__( "Icon Title", 'jvfrmtd' ),
					'type' => Controls_Manager::TEXT,
					'default' => '',
					'label_block' => true,
				),
				Array(
					'name' => 'list_icon',
					'label' => esc_html__( "Icon", 'jvfrmtd' ),
					'type' => Controls_Manager::TEXT,
					'default' => '',
					'label_block' => true,
				),
			),
			'title_field' => 'Button : {{{ list_title }}}',

			'condition' => [
				'select_meta' => 'switch',
			],
		) );


		/** Single Buttons **/

		$this->add_control(
			'select_collapse',
			[
				'label' => __( 'collapse', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'responsive-tabdrop' => __( 'Tabs', 'jvfrmtd' ),
					'scroll-tabs' => __( 'scroll-tabs', 'jvfrmtd' ),
				],
				'defalut' => 'responsive-tabdrop',
				'condition' => [
					'select_meta' => 'single_buttons',
				],
			]
		);

		$this->add_responsive_control('header_btn_align',
		[
			'label'         => esc_html__( 'Alignment', 'jvfrmtd' ),
			'type'          => Controls_Manager::CHOOSE,
			'options'       => [
				'flex-start'      => [
					'title'=> esc_html__( 'Left', 'jvfrmtd' ),
					'icon' => 'fa fa-align-left',
					],
				'center'    => [
					'title'=> esc_html__( 'Center', 'jvfrmtd' ),
					'icon' => 'fa fa-align-center',
					],
				'flex-end'     => [
					'title'=> esc_html__( 'Right', 'jvfrmtd' ),
					'icon' => 'fa fa-align-right',
					],
				],
			'default'       => 'center',
			'selectors'     => [
				'{{WRAPPER}} .javo-core-single-featured-switcher' => 'justify-content: {{VALUE}};',
				'{{WRAPPER}} .title-line-btns' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'single_btn_width',
			[
				'label' => __( "Single Button Width", 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 500,
				],
				'description' => __( 'Default : 260px to show 4 icons and hide others.', 'jvfrmtd'),
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 800,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .title-line-btns' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'select_meta' => 'single_buttons',
				],
			]
		);

		$this->add_control( 'single_btns', Array(
			'label' => esc_html__( 'Buttons', 'jvfrmtd' ),
			'type' => Controls_Manager::REPEATER,
			'fields' => Array(
				Array(
					'name' => 'list_class',
					'label' => __( 'Button Type', 'jvfrmtd' ),
					'type' => Controls_Manager::SELECT,
					'options' => Array(
						'' => esc_html__( 'Select a button', 'jvfrmtd' ),
						'score-review' => esc_html__( 'Average', 'jvfrmtd' ),
						'share' =>  esc_html__( 'Share', 'jvfrmtd' ),
						'amount-review' => esc_html__( 'Ratings', 'jvfrmtd' ),
						'submit-review' => esc_html__( 'Submit Review', 'jvfrmtd' ),
						'favorite' => esc_html__( 'Favorite', 'jvfrmtd' ),
						'post_count' => esc_html__( 'Post count', 'jvfrmtd' ),
						'favorite_count' => esc_html__( 'Favorite Count', 'jvfrmtd' ),
					),
				),
				Array(
					'name' => 'list_title',
					'label' => esc_html__( "Icon Title", 'jvfrmtd' ),
					'type' => Controls_Manager::TEXT,
					'default' => '',
					'label_block' => true,
				),
				Array(
					'name' => 'review_landing_id',
					'label' => esc_html__( "Landing ID", 'jvfrmtd' ),
					'type' => Controls_Manager::TEXT,
					'default' => '',
					'label_block' => true,
					'condition' => [
						'list_class' => 'submit-review',
					],
					'description' => esc_html__("Please add an ID (without #) of section to move. It should be a section of review area", "jvfrmtd"),
				),
				Array(
					'name' => 'favorite_count',
					'label' => esc_html__( "Show count", 'jvfrmtd' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'condition' => Array(
						'list_class' => 'favorite',
					),
				),
			),
			'title_field' => 'Button : {{{ list_title }}}',
			'condition' => [
				'select_meta' => 'single_buttons',
			],
		) );

		$this->end_controls_section();

		$this->start_controls_section(
			'switch_style',
			[
				'label' => __( 'Switch Style', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'select_meta' => 'switch',
				],
			]
		);

		/** border **/
		$this->add_control(
			'switch_btn_size',
			[
				'label' => __( "Size", 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30,
				],
				'description' => __( 'Default : 260px to show 4 icons and hide others', 'jvfrmtd'),
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .javo-core-single-featured-switcher > li > a' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control( 'switch_btn_radius', [
			'label' => __( 'Border Radius', 'jvfrmtd' ),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors' => [
					'{{WRAPPER}} .javo-core-single-featured-switcher > li > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'switch_btn_border',
				'label' => __('Border','jvfrmtd'),
				'selector' => '{{WRAPPER}} .javo-core-single-featured-switcher > li > a',
			]
		);



		/** Color **/
		$this->start_controls_tabs( 'tabs_background' );

		$this->start_controls_tab(
			'tab_background_normal',
			[
				'label' => __( 'Normal', 'elementor' ),
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => __( 'Text & Icon Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .javo-core-single-featured-switcher > li > a i' => 'color: {{VALUE}};',
				],
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background',
				'selector' => '{{WRAPPER}} .javo-core-single-featured-switcher > li > a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_background_hover',
			[
				'label' => __( 'Hover', 'elementor' ),
			]
		);

		$this->add_control(
			'text_color_hover',
			[
				'label' => __( 'Text & Icon Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .javo-core-single-featured-switcher > li > a:hover i' => 'color: {{VALUE}};',
				],
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_hover',
				'selector' => '{{WRAPPER}} .javo-core-single-featured-switcher > li > a:hover ',
			]
		);

		$this->add_control(
			'background_hover_transition',
			[
				'label' => __( 'Transition Duration', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0.3,
				],
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'render_type' => 'ui',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
		/* switch buttons */


		$this->start_controls_section(
			'single_buttons_style',
			[
				'label' => __( 'single_buttons Style', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'select_meta' => 'single_buttons',
				],
			]
		);

		/** border **/

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'single_btn_typo',
				'label' => __( 'Typography', 'plugin-domain' ),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .jvbpd-meta-details-right .title-line-btns li > div > a, {{WRAPPER}} .jvbpd-meta-details-right .title-line-btns li > div > button',
			]
		);


		$this->add_control( 'single_btn_padding', [
			'label' => __( 'Button Padding', 'jvfrmtd' ),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px'],
			'selectors' => [
					'{{WRAPPER}} .jvbpd-meta-details-right .title-line-btns li > div.btn-score-review a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .jvbpd-meta-details-right .title-line-btns li > div > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .jvbpd-meta-details-right .title-line-btns li > div > button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .jvbpd-meta-details-right .title-line-btns li > div > .post-views' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control( 'single_btn_radius', [
			'label' => __( 'Border Radius', 'jvfrmtd' ),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors' => [
					'{{WRAPPER}} .jvbpd-meta-details-right .title-line-btns li > div > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .jvbpd-meta-details-right .title-line-btns li > div > button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'single_btn_border',
				'label' => __('Border','jvfrmtd'),
				'selector' => '{{WRAPPER}} .jvbpd-meta-details-right .title-line-btns li > div > a, {{WRAPPER}} .jvbpd-meta-details-right .title-line-btns li > div > button',
			]
		);



		/** Color **/
		$this->start_controls_tabs( 'single_btn_tabs_background' );

		$this->start_controls_tab(
			'single_btn_tab_background_normal',
			[
				'label' => __( 'Normal', 'elementor' ),
			]
		);

		$this->add_control(
			'single_btn_text_color',
			[
				'label' => __( 'Text & Icon Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .jvbpd-meta-details-right .title-line-btns li > div a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .jvbpd-meta-details-right .title-line-btns li > div button' => 'color: {{VALUE}};',
				],
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'single_btn_background',
				'selector' => '{{WRAPPER}} .jvbpd-meta-details-right .title-line-btns li > div a, {{WRAPPER}} .jvbpd-meta-details-right .title-line-btns li > div button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'single_btn_tab_background_hover',
			[
				'label' => __( 'Hover', 'elementor' ),
			]
		);

		$this->add_control(
			'single_btn_text_color_hover',
			[
				'label' => __( 'Text & Icon Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .jvbpd-meta-details-right .title-line-btns li > div a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .jvbpd-meta-details-right .title-line-btns li > div button:hover' => 'color: {{VALUE}};',
				],
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'single_btn_background_hover',
				'selector' => '{{WRAPPER}} .jvbpd-meta-details-right .title-line-btns li > div a:hover, {{WRAPPER}} .jvbpd-meta-details-right .title-line-btns li > div button:hover',
			]
		);

		$this->add_control(
			'single_btn_background_hover_transition',
			[
				'label' => __( 'Transition Duration', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0.3,
				],
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'render_type' => 'ui',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();


    }

	public function getReviewInstance() {
		$instance = false;
		if( function_exists( 'lv_directoryReview' ) ) {
			$instance = lv_directoryReview();
		}
		$this->review_instance = $instance;
	}

    protected function render() {

		$settings = $this->get_settings();
		$this->getReviewInstance();

		$select_meta = $settings['select_meta'];

		switch($select_meta) {
			case 'switch':
				$this->getSwitcher();
				break;
			case 'single_buttons';
				$this->render_meta_detail();
				break;
		}

		wp_reset_postdata();
		//$isPreviewMode = is_admin();

		// if( $isPreviewMode) {
		// 	$previewBaseURL = jvbpdCore()->assets_url . '/images/elementor/listipia/';
		// 	$previewURL = $previewBaseURL . 'single-title-line.png';
		// 	printf( '<img src="%s">', esc_url_raw( $previewURL ) );
		// }else{
			//$this->getContent( $settings, get_post() );
		//}
    }

	public function getSwitcher(){
		$list = $this->get_settings( 'header_switchers' );
		if ( $list ) {
			echo '<div class="scrolltabs"><ul class="javo-core-single-featured-switcher list-inline responsive-tabdrop">';
			foreach ( $list as $item ) {
				if( ! $this->verify( $item[ 'list_class' ] ) ) {
					continue;
				}
				echo '<li role="presentation" class="switch-'. $item['list_class'] .'"><a class="javo-tooltip" data-original-title="'. $item['list_title'] .'"><i class="'. $item['list_icon'] .'" aria-hidden="true"></i><span class="switcher-label">'. $item['list_title'] .'</span></a></li>';
			}
		echo '</ul></div>';
		}
	}

	public function getSingleButtons(){	}

	public function verify( $section='' ) {
		$result = true;
		$addons = Array(
			'3dview' => Array(
				'meta_key' => '_3dViewer',
				'core' => 'Lava_Directory_3DViewer',
			),
			'video' => Array(
				'meta_key' => Array( '_video_portal', '_video_id' ),
				'core' => 'Lava_Directory_Video',
			),
		);
		if( array_key_exists( $section, $addons ) ) {
			$result = false;
			// Check this plugin activated
			if( class_exists( $addons[ $section ][ 'core' ] ) ) {
				if( is_array( $addons[ $section ][ 'meta_key' ] ) ) {
					$value = true;
					foreach( $addons[ $section ][ 'meta_key' ] as $meta_key ) {
						$getMeta = get_post_meta( get_the_ID(), $meta_key, true );
						$value = $value && !empty( $getMeta );
					}
				}else{
					$value = get_post_meta( get_the_ID(), $addons[ $section ][ 'meta_key' ], true );
				}
				$result = !empty( $value );
			}
		}
		return $result;
	}

	public function render_meta_detail() {

		$element_prefix = 'jvbpd_widget_single_title_line_';
		foreach(
			Array(
				'share',
				'favorite',
				'favorite_count',
				'post_count',
				'amount_review',
				'submit_review',
				'score_review',
			) as $callbackName
		) {
			add_filter( $element_prefix . $callbackName, array( $this, $callbackName ), 10, 2 );
		}

		$metaRender = Array();
		$detailMetaLists = $this->get_settings( 'single_btns' );
		if( ! is_array( $detailMetaLists ) ) {
			return false;
		}

		foreach( $detailMetaLists as $metaItem ) {
			$callback = str_replace( '-', '_', $metaItem[ 'list_class' ] );
			if( ! method_exists( $this, $callback ) ) {
				continue;
			}
			if( $callback == 'share' ) {
				add_action( 'wp_footer', array( __CLASS__, 'share_modal' ) );
			}
			$item_render = '';
			$item_render .= '<li>';
			$item_render .= apply_filters( $element_prefix . $callback, null, $metaItem );
			$item_render .= '</li>';
			$metaRender[] = $item_render;
		}
		$select_collapse = $this->get_settings( 'select_collapse');
		$output = '';
		$output .= '<div class="scrolltabs"><ul class="title-line-btns '. $select_collapse .'">';
		$output .= join( '', $metaRender );
		$output .= '</ul></div>';
		echo '<div class="jvbpd-meta-details-right"> '. $output .' </div>';
	}

	public function favorite( $output='', $meta=Array() ) {

		if( ! class_exists( '\lvDirectoryFavorite_button' ) ) {
			return $output;
		}

		$showCount = isset($meta['favorite_count']) &&'yes' == $meta['favorite_count'] ? '{text} {count}' : '{text}';

		ob_start(); ?>
		<div class="btn-favorite">
			<?php
			$objFavorite = new \lvDirectoryFavorite_button(
				Array(
					'post_id' => get_the_ID(),
					'show_count' => true,
					'format' => $showCount,
					'save' => sprintf( "<i class='fa fa-heart-o'></i> %s", esc_html__( " ", 'jvfrmtd' ) ),
					'unsave' => sprintf( "<i class='fa fa-heart'></i> %s", esc_html__( " ", 'jvfrmtd' ) ),
					'class' => Array( 'btn', 'lava-single-page-favorite', 'admin-color-setting-hover' ),
				)
			);
			add_filter( 'lava_' . get_post_type() . '_favorite_button_template', array( $this, 'favorite_template' ) );
			$objFavorite->output();
			remove_filter( 'lava_' . get_post_type() . '_favorite_button_template', array( $this, 'favorite_template' ) ); ?>
		</div> <!-- btn-favorite -->

		<?php
		return ob_get_clean();
	}

	public function post_count( $output='', $meta=Array() ) {
		$count = 0;
		if(function_exists('pvc_get_post_views')) {
			$count = pvc_get_post_views(get_the_ID());
		}
		return sprintf('<div class="btn-view-count"><a>%1$s</a></div', $count);
	}

	public function favorite_count($output='', $meta=Array()) {
		$count = get_post_meta(get_the_ID(), '_save_count', true);
		return sprintf('<div class="btn-favorite-count"><a>%1$s</a></div>', intVal($count));
	}

	public function share( $output='', $meta=Array() ) {
		return sprintf( '<div class="btn-%1$s"><button type="button" class="btn jvbpd-single-share-opener"><span>%2$s</span></button></div>', $meta['list_class'], $meta['list_title'] );
	}

	public function amount_review( $output='', $meta=Array() ) {
		$amount = 0;
		if( function_exists( 'lava_directory' ) ){
			$amount = intVal( lava_directory()->admin->reviewCount( get_the_ID() ) );
		}
		return sprintf( '<div class="btn-amount-review"><a href="#javo-item-review-section" class="admin-color-setting-hover">%1$s %2$s</a></div>', $amount, esc_html__( " Ratings", 'jvfrmtd' ) );
	}

	public function submit_review( $output='', $meta=Array() ) {
		return sprintf( '<div class="btn-submit-review"><a href="#%2$s" class="admin-color-setting-hover">%1$s</a></div>', esc_html__( "Submit Review", 'jvfrmtd' ), $meta['review_landing_id']);
	}

	public function score_review( $output='', $meta=Array() ) {
		$nowScore = 0;
		$maxScore = 5;

		if( $this->review_instance ) {
			$nowScore = get_post_meta( get_the_ID(), 'rating_average', true );
		}

		return sprintf( '<div class="btn-score-review"><a href="#javo-item-review-section" class="admin-color-setting-hover">%1$s / %2$s</a></div>', number_format( floatVal( $nowScore ), 1 ), $maxScore );
	}

	public static function share_modal() {
		wp_enqueue_script( jvbpdCore()->var_instance->getHandleName( 'clipboard' ) );
		printf( '<script type="text/html" id="%s">', 'jvbpd-single-share-contents' );
		jvbpdCore()->template_instance->load_template( 'part-single-share-modal' );
		printf( '</script>' );
	}

	public function favorite_template( $template='' ) {
		return '<a href="#" class="%1$s" data-save="%2$s %4$s (##)" data-saved="%3$s %4$s (##)" data-post-id="%5$s" data-show-count="%8$s">%6$s %4$s %7$s</a>';
	}
}

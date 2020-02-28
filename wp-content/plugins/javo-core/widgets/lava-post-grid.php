<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * @since 1.0.0
 */

class jvbpd_lava_post_grid extends Widget_Base {   //this name is added to plugin.php of the root folder

	public function get_name() {
		return 'lava-post-grid';
	}

	public function get_title() {
		return 'Lava Post Grid';   // title to show on elementor
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_categories() {
		return [ 'jvbpd-elements' ];    // category of the widget
	}

	public function is_reload_preview_required() {
		return true;
	}

	public function get_script_depends() {		//load the dependent scripts defined in the voidgrid-elements.php
		return [ 'void-grid-equal-height-js', 'void-grid-custom-js' ];
	}

	/**
	 * A list of scripts that the widgets is depended in
	 * @since 1.3.0
	 **/
protected function _register_controls() {

//start of a control box
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Post Grid Setting', 'jvfrmtd' ),   //section name for controler view
			]
		);

		$this->add_control(
			'refer_wp_org',
			[
				'raw' => __( 'For more detail about following filters please refer <a href="https://codex.wordpress.org/Template_Tags/get_posts" target="_blank">here</a>', 'jvfrmtd' ),
				'type' => Controls_Manager::RAW_HTML,
				'classes' => 'elementor-descriptor',
			]
		);
		if( function_exists( 'void_grid_post_type' ) ) {
			$this->add_control(
				'post_type',
				[
					'label' => esc_html__( 'Select post type', 'jvfrmtd' ),
					'type' => Controls_Manager::SELECT2,
					'options' => void_grid_post_type(),
					//'options' => lg_post_type(),
					//'options' => [
					//'post'  => __( 'Posts', 'jvfrmtd' ),
					//'lv_listing' => __( 'Listings', 'jvfrmtd' ),
					//],
				]
			);
		}
        $this->add_control(
            'taxonomy_type',
            [
                'label' => __( 'Select Taxonomy', 'jvfrmtd' ),
                'type' => Controls_Manager::SELECT2,
                'options' => '',
            ]
        );

        $this->add_control(
            'terms',
            [
                'label' => __( 'Select Terms (usually categories/tags) * Must Select Taxonomy First', 'jvfrmtd' ),
                'type' => Controls_Manager::SELECT2,
                'options' => '',
                'multiple' => true,
            ]
        );
	   $this->add_control(
            'display_type',
            [
                'label' => esc_html__( 'Choose your desired style', 'jvfrmtd' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '1' => 'Grid Layout',
                    '2' => 'List Layout',
                    '3' => '1st Full Post then Grid',
                    '4' => '1st Full Post then List',
                    '5' => 'Minimal Grid'
                ],
                'default' => '1'
            ]
        );

        $this->add_control(
            'posts_per_row',
            [
                'label' => esc_html__( 'Posts Per Row', 'jvfrmtd' ),
                'type' => Controls_Manager::SELECT,
                'condition' => [
                    'display_type' => ['1','5'],
                ],
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '6' => '6',
                ],
                'default' => '2',
            ]
        );
		$this->add_control(
			'posts',
			[
				'label' => esc_html__( 'Post Per Page', 'jvfrmtd' ),
				'description' => esc_html__( 'Give -1 for all post & No Pagination', 'jvfrmtd' ),
				'type' => Controls_Manager::NUMBER,
				'default' => -1,
			]
		);

        $this->add_control(
                'pagination_yes',
                [
                    'label' => esc_html__( 'Pagination Enabled', 'jvfrmtd' ),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        1 => 'Yes',
                        2 => 'No'
                    ],
                    'default' => 1,

                ]
            );
        $this->add_control(
            'offset',
            [
                'label' => esc_html__( 'Post Offset', 'jvfrmtd' ),
                'type' => Controls_Manager::NUMBER,
                'default' => '0'
            ]
        );

		if( function_exists( 'voidgrid_post_orderby_options' ) ) {
			$this->add_control(
				'orderby',
				[
					'label' => esc_html__( 'Order By', 'jvfrmtd' ),
					'type' => Controls_Manager::SELECT,
					'options' => voidgrid_post_orderby_options(),
					'default' => 'date',
				]
			);
		}
        $this->add_control(
            'image_style',
            [
                'label' => esc_html__('Choose your desired featured image style', 'jvfrmtd'),
                'type'  => Controls_Manager::SELECT2,
                'options' => [
                    '1' => 'Standard',
                    '2' => 'left top rounded',
                    '3' => 'left bottom rounded'
                ],
                'default'   => '1',
            ]
        );
        $this->add_control(
            'order',
            [
                'label' => esc_html__( 'Order', 'jvfrmtd' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'asc' => 'Ascending',
                    'desc' => 'Descending'
                ],
                'default' => 'desc',

            ]
        );

        $this->add_control(
            'sticky_ignore',
            [
                'label' => esc_html__( 'Sticky Condition', 'jvfrmtd' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '1' => 'Remove Sticky',
                    '0' => 'Keep Sticky'
                ],

                'default' => '1',
            ]
        );

		$this->end_controls_section();




        $this->start_controls_section(
            'section_style_grid',
            [
                'label' => esc_html__( 'Style', 'jvfrmtd' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_text_transform',
            [
                'label' => esc_html__( 'Title Text Transform', 'jvfrmtd' ),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => esc_html__( 'None', 'jvfrmtd' ),
                    'uppercase' => esc_html__( 'UPPERCASE', 'jvfrmtd' ),
                    'lowercase' => esc_html__( 'lowercase', 'jvfrmtd' ),
                    'capitalize' => esc_html__( 'Capitalize', 'jvfrmtd' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .entry-title' => 'text-transform: {{VALUE}};',   //the selector used above in add_control
                ],
            ]
        );

        $this->add_responsive_control(
            'title_font_size',
            [
                'label' => esc_html__( 'Title Size', 'jvfrmtd' ),
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
                    '{{WRAPPER}} .entry-title' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_color',
            [
                'label' => esc_html__( 'Title Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .entry-title a' => 'color: {{VALUE}};',
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

	}


	protected function render() {				//to show on the fontend
		$settings = $this->get_settings();

        if( !empty($settings['taxonomy_type'])){
            $terms = get_terms( array(
                'taxonomy' => $settings['taxonomy_type'],
                'hide_empty' => true,
            ));
            foreach ( $terms as $term ){
                $term_id[] = $term -> term_id;
            }
        }
		if(!empty($settings['terms'])){
				$category = implode (", ", $settings['terms']);
		}
        elseif( !empty($settings['taxonomy_type'])) {
            $category=implode(", ", $term_id);
        }
        else{
            $category = '';
        }
		echo'<div class="elementor-shortcode">';
            echo do_shortcode('[voidgrid_sc_post_grid post_type="'.$settings['post_type'].'" pagination_yes="'.$settings['pagination_yes'].'" display_type="'.$settings['display_type'].'" posts="'.$settings['posts'].'" posts_per_row="'.$settings['posts_per_row'].'" image_style="'.$settings['image_style'].'" sticky_ignore="'.$settings['sticky_ignore'].'"  orderby="'.$settings['orderby'].'" order="'.$settings['order'].'" offset="'.$settings['offset'].'"  terms="'.$category.'" taxonomy_type="'.$settings['taxonomy_type'].'" ]');
		echo'</div>';
	}

}

$current_url=esc_url("//".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

if( strpos( $current_url, 'elementor') == true ){
    add_action( 'wp_footer', function() {

    if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
        return;
    }

    // load our jquery file that sends the $.post request
    wp_enqueue_script( "void-grid-ajax", plugins_url('assets/js/void-ajax.js', dirname(__FILE__)) , array( 'jquery', 'json2' ) );

    // make the ajaxurl var available to the above script
    wp_localize_script( 'void-grid-ajax', 'void_grid_ajax', array(
                                                            'ajaxurl'          => admin_url( 'admin-ajax.php' ),
                                                            'postTypeNonce' => wp_create_nonce( 'voidgrid-post-type-nonce' ),
                                                            )
    );
} );
}




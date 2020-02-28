<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Style for header
 *
 *
 * @since 1.0.0
 */

class jvbpd_post_box_Block extends Widget_Base {   //this name is added to plugin.php of the root folder

	public function get_name() {
		return 'jvbpd-post-box-block';
	}

	public function get_title() {
		return 'Post Box Block';   // title to show on elementor
	}

	public function get_icon() {
		return 'eicon-gallery-grid';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
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
					'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-category-block/" style="color:#fff;"> ' .
					esc_html__( 'Documentation', 'jvfrmtd' ) .
					'</a></li></ul></div>'
				)
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Post Box Block', 'jvfrmtd' ),   //section name for controler view
			]
		);

		//repeat
		$this->add_control(
			'block-list',
			[
				'label' => __( 'Repeater List', 'jvfrmtd' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => [
					[
							'name' => 'get_posts',
							'label' => esc_html__( 'Use posts ( listings )', 'jvfrmtd' ),
							'type' => Controls_Manager::SWITCHER,
							'default' => '',
							'label_on' => __( 'Yes', 'jvfrmtd' ),
							'label_off' => __( 'No', 'jvfrmtd' ),
							'return_value' => '1',
					],
					[
						'name' => 'select_listing',
						'label' => esc_html__( 'Listing', 'jvfrmtd' ),
		        'description' => esc_html__('Choose a listing to show','jvfrmtd'),
						'type' => Controls_Manager::SELECT2,
						'multiple' => false,
						'options' => get_listing_list(),
						'condition' => [
							'get_posts' => ''
						],
						'separator' => 'none',
					],


					[
						 'name'	=> 'image_size',
						 'label'       => __( 'Image Size', 'jvfrmtd' ),
						 'type' => Controls_Manager::SELECT2,
						 'default' => 'full',
						 'options' => [
							'full'  => __( 'full', 'jvfrmtd' ),
							'1-3' => __( '1-3', 'jvfrmtd' ),
							'2-3' => __( '2-3', 'jvfrmtd' ),
						 ],
						'description' => esc_html__('Select a fit image size depends on the columns.','jvfrmtd'),
					],

					/* Custom blocks */
					[
						'name' => 'list_title',
						'label' => __( 'Title', 'jvfrmtd' ),
						'type' => Controls_Manager::TEXT,
						'default' => __( 'List Title' , 'jvfrmtd' ),
						'label_block' => true,
						'condition' => [
							'get_posts' => '1'
						],
					],
					[
						'name' => 'list_image',
						'label' => __( 'Choose Image', 'jvfrmtd' ),
		 			  'type' => Controls_Manager::MEDIA,
		 			  'default' => [
		 				 'url' => Utils::get_placeholder_image_src(),
		 			  ],
						'condition' => [
							'get_posts' => '1'
						],
					],
				],
				'title_field' => '{{{ list_title }}}',
			]
		);
		/* repeat end */

		$this->end_controls_section();

		$this->start_controls_section(
				'section_style_filter',
				[
						'label' => esc_html__( 'Style Blocks', 'jvfrmtd' ),
						'tab' => Controls_Manager::TAB_STYLE,
				]
		);

		$this->add_control(
		'overlay_color',
		[
			'label' => __( 'Overlay Color', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'default' => 'transparent',
			'scheme' => [
				'type' => Scheme_Color::get_type(),
				'value' => Scheme_Color::COLOR_1,
			],
			'selectors' => [
				'{{WRAPPER}} .overlay-color' => 'color: {{VALUE}}',
			],
		]
		);

		$this->add_control(
		'title_color',
		[
			'label' => __( 'Title Color', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'scheme' => [
				'type' => Scheme_Color::get_type(),
				'value' => Scheme_Color::COLOR_1,
			],
			'selectors' => [
				'{{WRAPPER}} .title-color' => 'color: {{VALUE}}',
			],
		]
		);

		$this->add_control(
		'des_color',
		[
			'label' => __( 'Des Color', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'scheme' => [
				'type' => Scheme_Color::get_type(),
				'value' => Scheme_Color::COLOR_1,
			],
		]
		);

		$this->add_control(
			'border_radius',
			[
				'label' => __( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .javo-featured-block' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_control(
			'block_margin',
			[
				'label' => __( 'Block Margin', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
                'description' => esc_html__('Default: bottom 15px','jvfrmtd'),
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .javo-featured-block' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'block_max_width',
			[
				'label' => __( 'Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
                'description' => esc_html__('Default: 350px (1 col), 730px (2 cols), 1110 (3 cols) ','jvfrmtd'),
				'default' => [
					'size' => 350,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .javo-featured-block' => 'max-width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'block_height',
			[
				'label' => __( 'Height', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
                'description' => esc_html__('Default: 200px','jvfrmtd'),
				'default' => [
					'size' => 200,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .javo-featured-block' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	public function add_tax_term_control( $option_format, $args=Array() ) {
		$options = shortcode_atts(
			Array(
				'taxonomies' => false,
				'type' => Controls_Manager::SELECT2,
				'label' => esc_html__( '%1$s Terms', 'jvfrmtd' ),
				'condition' => Array(),
			), $args
		);

		$taxonomies = $options[ 'taxonomies' ] ? $options[ 'taxonomies' ] : array();

		if( ! is_array( $taxonomies ) ) {
			return false;
		}

		foreach( $taxonomies as $taxonomy ) {
			$option_args = Array(
				'label'       => sprintf( $options[ 'label' ], get_taxonomy( $taxonomy )->label ),
				'type'        => $options[ 'type' ],
				'condition' => Array(
					'taxonomy' => $taxonomy,
				),
				'options' => jvbpd_elements_tools()->get_taxonomy_terms( $taxonomy, 'slug', 'name' ),
				'separator' => 'none',
			);
			$this->add_control( sprintf( $option_format, $taxonomy ), $option_args );
		}
	}


	protected function render() {				//to show on the fontend
		static $v_veriable=0;
		$settings = $this->get_settings();
    if ($settings['block-list'][0]['select_listing']){


    $list = $this->get_settings( 'block-list' );
    if ( $list ) {
    	echo '<div class="post-box-block-wrap">';
    	foreach ( $list as $item ) {
				$post_id = $item['select_listing'];
				$jvbpd_this_attachment_meta = get_the_post_thumbnail($post_id, 'medium');
				$termsArray = wp_get_object_terms( $post_id, "listing_category" );  //Get the terms for this particular item
				 $termSlugs = ""; //initialize the string that will contain the terms
				 $termNames = "";
				 foreach ( $termsArray as $term ) { // for each term
					 $termSlugs .= $term->slug.' '; //create a string that has all the slugs
					 $termNames .= $term->name.' '; //create a string that has all the names
				 }

				 $getLocationTerms = wp_get_object_terms( $post_id, "listing_location" );  //Get the terms for this particular item
 				 $LocationTermSlugs = ""; //initialize the string that will contain the terms
 				 $LocationTermNames = "";
 				 foreach ( $getLocationTerms as $term ) { // for each term
 					 $LocationTermSlugs .= $term->slug.' '; //create a string that has all the slugs
 					 $LocationTermNames .= $term->name.' '; //create a string that has all the names
 				 }
				 $strImageSize		= 'full';
		 		$strClassName		= 'javo-image-full-size';

		 		if( $item['image_size'] == '1-3' ) {
		 			$strImageSize	= 'jvbpd-large';
		 			$strClassName	= 'javo-image-min-size';
		 		}elseif( $item['image_size'] == '2-3' ) {
		 			$strImageSize	= 'jvbpd-item-detail';
		 			$strClassName	= 'javo-image-middle-size';
		 		}
				?>
					<div class="javo-featured-block <?php echo $strClassName; ?>">
						<a href="<?php echo get_permalink($post_id); ?>">
							<?php echo $jvbpd_this_attachment_meta; ?>
							<div class="javo-image-overlay" style="background-color:<?php echo $settings['overlay_color']; ?>"></div>
							<div class="javo-text-wrap">
								<h4 style="color:<?php echo $settings['title_color']; ?>"><?php echo get_the_title($post_id); ?></h4>
								<div class="jvbpd_text_description-wrap">
									<span class="jvbpd_text_description" style="color:<?php echo $settings['des_color']; ?>"><?php echo $termNames; ?></span>
									<span class="jvbpd_text_description" style="color:<?php echo $settings['des_color']; ?>"><?php echo $LocationTermNames; ?></span>
								</div>
							</div> <!--javo-text-wrap -->
						</a>
					</div>
			<?php
    	}
    	echo '</div>';
    }
	} // select list not empty
} //render
} //jvbpd_post_box_Block

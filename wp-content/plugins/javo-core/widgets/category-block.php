<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Style for header
 *
 *
 * @since 1.0.0
 */

class jvbpd_categoryBlock extends Widget_Base {   //this name is added to plugin.php of the root folder

	public function get_name() {
		return 'jvbpd-category-block';
	}

	public function get_title() {
		return 'Category Block';   // title to show on elementor
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
				'label' => esc_html__( 'Category Block', 'jvfrmtd' ),   //section name for controler view
			]
		);

			$this->add_control(
				'block_title',
				[
				'label'       => __( 'Title', 'jvfrmtd' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Type your title text here', 'jvfrmtd' ),
				]
			);

			$this->add_control(
				'block_des',
				[
				'label'       => __( 'Short Description', 'jvfrmtd' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Type your description here', 'jvfrmtd' ),
				]
			);


			$this->add_control(
				'image_size', [
				'label'       => __( 'Image Size', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT2,
				'default' => 'full',
				'options' => [
					'full'  => __( 'full', 'jvfrmtd' ),
					'1-3' => __( '1-3', 'jvfrmtd' ),
					'2-3' => __( '2-3', 'jvfrmtd' ),
				],
				'description' => esc_html__('Select a fit image size depends on the columns.','jvfrmtd'),
				]
			);

			$this->add_control(
			'image',
				[
				'label' => __( 'Choose Image', 'jvfrmtd' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				]
			);


			$this->add_control( 'link_type', Array(
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__("Link Type", 'jvfrmtd'),
				'default' => 'term_link',
				'options' => Array(
					'term_link' => esc_html__("Term link", 'jvfrmtd'),
					'custom_link' => esc_html__("Custom link", 'jvfrmtd'),
				),
			) );

			$taxonomies_options = \jvbpd_elements_tools()->get_taxonomies( 'lv_listing' );
			$this->add_control( 'taxonomy', Array(
				'label'       => __( 'Taxonomy', 'jvfrmtd' ),
				'type'        => Controls_Manager::SELECT2,
				'options' => $taxonomies_options,
				'condition' => Array(
					'link_type' => 'term_link'
				),
				'separator' => 'none',
			) );

			$this->add_tax_term_control( '%1$s_term', Array(
				'taxonomies' => array_keys( $taxonomies_options ),
				'label' => esc_html__( '%1$s Terms', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT2,
				'condition' => Array(
					'link_type' => 'term_link'
				),
			) );

			$this->add_control( 'custom_link', Array(
				'type' => Controls_Manager::URL,
				'label' => esc_html__("Custom URL", 'jvfrmtd'),
				'default' => Array(
					'url' => home_url(),
				),
				'condition' => Array(
					'link_type' => 'custom_link'
				),
			) );

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
			'scheme' => [
				'type' => Scheme_Color::get_type(),
				'value' => Scheme_Color::COLOR_1,
			],
			'default' => 'transparent',
			'selectors' => [
				'{{WRAPPER}} .overlay-color' => 'color: {{VALUE}}',
			],
		]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Title Color', 'jvfrmtd' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .category-block-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => __( 'Title Typography', 'jvfrmtd' ),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .category-block-title',
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
			'selectors' => [
				'{{WRAPPER}} .jvbpd_text_description' => 'color:{{VALUE}}',
			],
		]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'des_typography',
				'label' => __( 'Description Typography', 'jvfrmtd' ),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .jvbpd_text_description',
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
				'condition' => wp_parse_args( Array( 'taxonomy' => $taxonomy ), $options['condition'] ),
				'options' => jvbpd_elements_tools()->get_taxonomy_terms( $taxonomy, 'slug', 'name' ),
				'separator' => 'none',
			);
			$this->add_control( sprintf( $option_format, $taxonomy ), $option_args );
		}
	}


	protected function render() {				//to show on the fontend
		static $v_veriable=0;

		$settings = $this->get_settings();
        if(!empty($settings[ 'image_size' ])){

		$strImageSize		= 'full';
		$strClassName		= 'javo-image-full-size';

		if( $settings['image_size'] == '1-3' ) {
			$strImageSize	= 'jvbpd-large';
			$strClassName	= 'javo-image-min-size';
		}elseif( $settings['image_size'] == '2-3' ) {
			$strImageSize	= 'jvbpd-item-detail';
			$strClassName	= 'javo-image-middle-size';
		}

		$image = $settings['image'];


		//$jvbpd_this_attachment_meta = get_the_post_thumbnail( $jvbpd_featured_block_id, $strImageSize );
		$jvbpd_this_attachment_meta = wp_get_attachment_image( $image['id'], $strImageSize );

		ob_start();
		$output_link = '#';
		$target_type = '_self';
		if('custom_link' == $this->get_settings('link_type')) {
			$custom_link_meta = $this->get_settings('custom_link');
			$output_link = $custom_link_meta['url'];
			if($custom_link_meta['is_external']) {
				$target_type = '_blank';
			}
		}else{
			$listing_taxonomy = $settings[ 'taxonomy' ];
			$listing_taxonomy_term = isset( $settings[ $listing_taxonomy . '_term' ] ) ? $settings[ $listing_taxonomy . '_term' ] : false;

			if( taxonomy_exists( $listing_taxonomy ) && $listing_taxonomy_term ) {
				$termLink = get_term_link( $listing_taxonomy_term, $listing_taxonomy );
				if( !is_wp_error( $termLink ) ) {
					$output_link = $termLink;
				}
			}
		} ?>
		<div class="javo-featured-block <?php echo $strClassName; ?>">
			<a href="<?php echo esc_url($output_link); ?>" target="<?php echo esc_attr($target_type); ?>">
				<?php echo $jvbpd_this_attachment_meta; ?>
				<div class="javo-image-overlay" style="background-color:<?php echo $settings['overlay_color']; ?>"></div>
				<div class="javo-text-wrap">
					<h4 class='category-block-title'><?php echo $settings['block_title']; ?></h4>
					<div class="jvbpd_text_description-wrap">
						<span class="jvbpd_text_description"><?php echo $settings['block_des']; ?></span>
					</div>
				</div> <!--javo-text-wrap -->
			</a>
		</div>

		<?php
		//ob_get_clean();
		$content = ob_get_clean();
		echo $content;
    	}
    }
}

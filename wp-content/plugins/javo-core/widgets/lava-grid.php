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

class jvbpd_lava_grid extends Widget_Base {   //this name is added to class-elementor.php of the root folder

	public function get_name() {
		return 'jvbpd-lava-grid';
	}

	public function get_title() {
		return 'Lava Grid';   // title to show on elementor
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
			'section_content',
			[
				'label' => esc_html__( 'Lava Grid', 'jvfrmtd' ),   //section name for controler view
			]
		);

		$this->add_control(
		  'post-type',
		  [
			 'label'       => __( 'Post Type', 'jvfrmtd' ),
			 'type' => Controls_Manager::SELECT,
			 'default' => 'Post',
			 'options' => [
				'Post'  => __( 'Posts', 'jvfrmtd' ),
				'lv_listing' => __( 'Listings', 'jvfrmtd' ),
			 ],
             'description' => esc_html__('Select a post type.','jvfrmtd'),			 
		  ]
		);

		$this->add_control(
		'price',
		  [
			 'label'   => __( 'Price', 'your-plugin' ),
			 'type'    => Controls_Manager::NUMBER,
			 'default' => 10,
			 'min'     => 5,
			 'max'     => 100,
			 'step'    => 5,
		  ]
		);


		$this->add_control(
			'more_options',
			[
				'label' => __( 'Additional Options', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
		  'categories',
		  [
			 'label'       => __( 'Categories', 'jvfrmtd' ),
			 'type' => Controls_Manager::SELECT2,
			 'default' => 'Post',
			 'multiple' => true,
			 //'label_block' => true,
			 'options' => jvbpd_get_category('category',''),
             'description' => esc_html__('Select a post type.','jvfrmtd'),			 
		  ]
		);

		$this->add_control(
			'columns',
			[
				'label' => __( 'Columns', 'your-plugin' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 3,
				],
				'description' => esc_html__('Select a colum amount.','jvfrmtd'),
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 6,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'cols' ],
				'selectors' => [
					'{{WRAPPER}} .box' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
	}


	protected function render() {				//to show on the fontend
		static $v_veriable=0;

		$settings = $this->get_settings();
		$cols = $this->get_settings( 'columns' ); 
		$width = $this->get_settings( 'columns' ); 

                echo do_shortcode('[lava_grid_list post-type="'.$settings['post-type'].'" cols="'.$width['size'].'"]');    
    }
}

<?php

namespace WILCITY_ELEMENTOR\Registers;


use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use WILCITY_SC\SCHelpers;

class MasonryTermBoxes extends Widget_Base {
	use Helpers;

	public function get_name() {
		return WILCITY_WHITE_LABEL.'-masonry-term-boxes';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return WILCITY_EL_PREFIX. 'Masonry Term Boxes';
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'fa fa-picture-o';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'theme-elements' ];
	}



	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.1.0
	 *
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'grid_general_section',
			[
				'label' => 'General Settings',
			]
		);

		$this->add_control(
			'heading',
			[
				'label'         => 'Heading',
				'type'          => Controls_Manager::TEXT,
				'default'       => 'The Latest Listings'
			]
		);

		$this->add_control(
			'heading_color',
			[
				'label'         => 'Heading Color',
				'type'          => Controls_Manager::COLOR,
				'default'       => ''
			]
		);

		$this->add_control(
			'description',
			[
				'label' => 'Description',
				'type' => Controls_Manager::TEXTAREA,
				'default' => ''
			]
		);

		$this->add_control(
			'description_color',
			[
				'label'         => 'Description Color',
				'type'          => Controls_Manager::COLOR,
				'default'       => ''
			]
		);

		$this->add_control(
			'header_desc_text_align',
			[
				'label'         => 'Heading and Description Text Alignment',
				'type'          => Controls_Manager::SELECT,
				'default'       => 'wil-text-center',
				'admin_label'   => true,
				'options'		=> array(
					'wil-text-center' => 'Center',
					'wil-text-left'   => 'Left',
					'wil-text-right'  => 'Right'
				)
			]
		);

		$this->add_control(
			'taxonomy',
			[
				'label'         => 'Taxonomy',
				'type'          => Controls_Manager::SELECT,
				'default'       => 'listing_cat',
				'options'		=> array(
					'listing_cat'    	=> 'Listing Categories',
					'listing_location'  => 'Listing Locations',
					'listing_tag'    	=> 'Listing Tags',
					'_self'             => 'Depends on Taxonomy Page'
				)
			]
		);

		if ( $this->getTerms('listing_cat') !== 'toomany' ){
			$this->add_control(
				'listing_cats',
				[
					'label' => 'Select Categories',
					'type' => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple' => true,
					'options' => $this->getTerms('listing_cat'),
					'condition' => [
						'taxonomy' => 'listing_cat'
					]
				]
			);
		}else{
			$this->add_control(
				'listing_cats',
				[
					'label' => 'Enter in Category IDs',
					'description' => 'Each category is separated by a comma. For example: 1,2,3',
					'type' => Controls_Manager::TEXT,
					'label_block' => true,
					'options' => $this->getTerms('listing_cat'),
					'condition' => [
						'taxonomy' => 'listing_cat'
					]
				]
			);
		}

		if ( $this->getTerms('listing_location') !== 'toomany' ){
			$this->add_control(
				'listing_locations',
				[
					'label' => 'Select Locations',
					'type' => Controls_Manager::SELECT2,
					'multiple' => true,
					'label_block' => true,
					'options' => $this->getTerms('listing_location'),
					'condition' => [
						'taxonomy' => 'listing_location'
					]
				]
			);
		}else{
			$this->add_control(
				'listing_locations',
				[
					'label' => 'Enter in Location IDs',
					'description' => 'Each location is separated by a comma. For example: 1,2,3',
					'type' => Controls_Manager::TEXT,
					'label_block' => true,
					'options' => $this->getTerms('listing_location'),
					'condition' => [
						'taxonomy' => 'listing_location'
					]
				]
			);
		}

		if ( $this->getTerms('listing_tag') !== 'toomany' ){
			$this->add_control(
				'listing_tags',
				[
					'label' => 'Select Tags',
					'type' => Controls_Manager::SELECT2,
					'multiple' => true,
					'label_block' => true,
					'options' => $this->getTerms('listing_tag'),
					'condition' => [
						'taxonomy' => 'listing_tag'
					]
				]
			);
		}else{
			$this->add_control(
				'listing_tags',
				[
					'label' => 'Enter in Tag IDs',
					'description' => 'Each tag is separated by a comma. For example: 1,2,3',
					'type' => Controls_Manager::TEXT,
					'label_block' => true,
					'options' => $this->getTerms('listing_tag'),
					'condition' => [
						'taxonomy' => 'listing_tag'
					]
				]
			);
		}

		$this->add_control(
			'number',
			[
				'label' => 'Maximum Items',
				'type' => Controls_Manager::TEXT,
				'default'=> 6
			]
		);

		$this->add_control(
			'is_show_parent_only',
			[
				'label'     => 'Show Parent Only',
				'type'      => Controls_Manager::SELECT,
				'default'   => 'no',
				'options' => array(
					'no'  => 'No',
					'yes' => 'Yes'
				)
			]
		);

		$this->add_control(
			'is_hide_empty',
			[
				'label'     => 'Hide Empty Term',
				'type'      => Controls_Manager::SELECT,
				'default'   => 'no',
				'options' => array(
					'no'  => 'No',
					'yes' => 'Yes'
				)
			]
		);

		$this->add_control(
			'orderby',
			[
				'label' => 'Order By',
				'type' => Controls_Manager::SELECT,
				'description'   => 'This feature is not available if the "Select Locations/Select Tags/Select Categories" is not empty',
				'default' => 'count',
				'options'     => array(
					'count'     => 'Number of children',
					'name'      => 'Term Name',
					'term_order'=> 'Term Order',
					'id'        => 'Term ID',
					'slug'      => 'Term Slug',
					'none'      => 'None'
				)
			]
		);

		$this->add_control(
			'order',
			[
				'label' => 'Order',
				'type' => Controls_Manager::SELECT,
				'default' => 'DESC',
				'options'     => array(
					'DESC'  => 'DESC',
					'ASC'   => 'ASC'
				)
			]
		);

		$this->add_control(
			'extra_class',
			[
				'label'     => 'Extra Class',
				'type'      => Controls_Manager::TEXT
			]
		);


		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.1.0
	 *
	 * @access protected
	 */
	protected function render() {
		$aSettings = $this->get_settings();
		$aSettings = wp_parse_args(
			$aSettings,
			array(
				'TYPE'      => 'MASONRY_TERM_BOXES',
				'heading' => '',
				'heading_color' => '',
				'description' => '',
				'description_color' => '',
				'header_desc_text_align' => '',
				'taxonomy'      => 'listing_cat',
				'col_gap'      => 30,
				'listing_cats'  => '',
				'listing_locations' => '',
				'number' => 6,
				'image_size' => 'wilcity_560x300',
				'listing_tags'  => '',
				'is_hide_empty'  => 'no',
				'is_show_parent_only'  => 'no',
				'orderby'       => 'count',
				'order'         => 'DESC',
				'extra_class'   => ''
			)
		);
		wilcity_render_term_masonry_items($aSettings);
	}
}
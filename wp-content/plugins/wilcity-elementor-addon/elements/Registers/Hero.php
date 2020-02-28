<?php

namespace WILCITY_ELEMENTOR\Registers;


use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use WilokeListingTools\Framework\Helpers\General;

class Hero extends Widget_Base {
	use Helpers;

	public function get_name() {
		return WILCITY_WHITE_LABEL.'-hero';
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
		return WILCITY_EL_PREFIX. 'Hero';
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
			'section_hero',
			[
				'label' => 'Settings',
			]
		);

		$this->add_control(
			'heading',
			[
				'label' => 'Heading',
				'type' => Controls_Manager::TEXT,
				'default' => 'Explore This City'
			]
		);

		$this->add_control(
			'heading_color',
			[
				'label' => 'Heading Color',
				'type' => Controls_Manager::COLOR,
				'default' => ''
			]
		);

		$this->add_control(
			'heading_font_size',
			[
				'label' => 'Heading Font Size',
				'description' => 'Eg: 100px',
				'type' => Controls_Manager::TEXT,
				'default' => ''
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
				'label' => 'Description Color',
				'type' => Controls_Manager::COLOR,
				'default' => ''
			]
		);

		$this->add_control(
			'description_font_size',
			[
				'label' => 'Description Font Size',
				'description' => 'Eg: 20px',
				'type' => Controls_Manager::TEXT,
				'default' => ''
			]
		);

		$this->add_control(
			'button_icon',
			[
				'label' => 'Button Icon',
				'type' => Controls_Manager::ICON,
				'condition' => [
					'toggle_button' => 'enable'
				],
				'default' => ''
			]
		);

		$this->add_control(
			'button_background_color',
			[
				'label' => 'Button Background Color',
				'type' => Controls_Manager::COLOR,
				'default' => ''
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => 'Button Text Color',
				'type' => Controls_Manager::COLOR,
				'default' => ''
			]
		);

		$this->add_control(
			'button_size',
			[
				'label' => 'Button Size',
				'type' => Controls_Manager::SELECT,
				'default'   => 'wil-btn--sm',
				'options'   => array(
					'wil-btn--sm'  => 'Small',
					'wil-btn--md'  => 'Medium',
					'wil-btn--lg'  => 'Large'
				)
			]
		);

		$this->add_control(
			'toggle_button',
			[
				'label' => 'Toggle Button',
				'type' => Controls_Manager::SELECT,
				'default'   => 'enable',
				'options'   => array(
					'enable'   => 'Enable',
					'disable'  => 'Disable'
				)
			]
		);

		$this->add_control(
			'button_name',
			[
				'label' => 'Button Name',
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'toggle_button' => 'enable'
				],
                'default' => 'Check out'
			]
		);

		$this->add_control(
			'button_link',
			[
				'label' => 'Button Link',
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'toggle_button' => 'enable'
				],
				'default' => '#'
			]
		);

		$this->add_control(
			'toggle_dark_and_white_background',
			[
				'label' => 'Toggle Dark and White background',
				'type' => Controls_Manager::SELECT,
				'default' => 'disable',
				'options'       => array(
					'enable'     => 'Enable',
					'disable'    => 'Disable'
				)
			]
		);

		$this->add_control(
			'bg_overlay',
			[
				'label' => 'Background Overlay',
				'type' => Controls_Manager::COLOR,
				'default' => ''
			]
		);

		$this->add_control(
			'bg_type',
			[
				'label' => 'Is Using Slider Background?',
				'type' => Controls_Manager::SELECT,
				'default' => 'image',
				'options'       => array(
					'image'     => 'Image Background',
					'slider'    => 'Slider Background'
				)
			]
		);

		$this->add_control(
			'image_bg',
			[
				'label' => 'Background Image',
				'type' => Controls_Manager::MEDIA,
				'condition' => [
					'bg_type' => 'image'
				],
			]
		);

		$this->add_control(
			'slider_bg',
			[
				'label' => 'Background Slider',
				'type' => Controls_Manager::GALLERY,
				'condition' => [
					'bg_type' => 'slider'
				],
			]
		);

		$this->add_control(
			'img_size',
			[
				'label'   => 'Image Size',
				'type'    => Controls_Manager::TEXT,
				'default' => 'large',
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

		$this->start_controls_section('section_search_form',
			[
				'label' => 'Search Form',
			]
		);

		$this->add_control(
			'toggle_search_form',
			[
				'label' => 'Toggle Search Form',
				'type' => Controls_Manager::SELECT,
				'default' => 'disable',
				'options'   => array(
					'enable'    => 'Enable',
					'disable'   => 'Disable'
				)
			]
		);

		$this->add_control(
			'search_form',
			[
				'label'   => 'Search Form',
				'type'    => Controls_Manager::REPEATER,
				'condition' => array(
				    'toggle_search_form' => 'enable'
                ),
				'fields' => [
					array(
						'type' 	=> Controls_Manager::TEXT,
						'label' => 'Tab Name',
						'name' 	=> 'name'
					),
					array(
						'type' 	=> Controls_Manager::SELECT,
						'label' => 'Directory Type',
						'name' 	=> 'post_type',
                        'options' => General::getPostTypeOptions(false, false)
					)
				],
			]
		);

		$this->add_control(
			'search_form_position',
			[
				'label' => 'Search Form Position',
				'type' => Controls_Manager::SELECT,
				'condition' => array(
					'toggle_search_form' => 'enable'
				),
				'default' => 'bottom',
				'options'       => array(
					'right'     => 'Right of Screen',
					'bottom'    => 'Bottom'
				)
			]
		);

		$this->add_control(
			'search_form_background',
			[
				'label' => 'Search Form Style',
				'type' => Controls_Manager::SELECT,
				'condition' => array(
					'toggle_search_form' => 'enable'
				),
				'default' => 'hero_formDark__3fCkB',
				'options' => array(
					'hero_formWhite__3fCkB'   => 'White',
					'hero_formDark__3fCkB'    => 'Black'
				),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section('section_list_of_suggestions',
			[
				'label' => 'List Of Suggestions',
			]
        );

		$this->add_control(
			'toggle_list_of_suggestions',
			[
				'label' => 'Toggle The List Of Suggestions',
				'type' => Controls_Manager::SELECT,
				'description'  => 'A list of suggestion locations/categories will be shown on the Hero section if this feature is enabled.',
				'default'   => 'enable',
				'options'   => array(
					'enable'   => 'Enable',
					'disable'  => 'Disable'
				)
			]
		);

		$this->add_control(
			'maximum_terms_suggestion',
			[
				'label' => 'Maximum Locations / Categories',
				'type' => Controls_Manager::TEXT,
				'default'   => 6
			]
		);

		$this->add_control(
			'taxonomy',
			[
				'label' => 'Taxonomy',
				'type' => Controls_Manager::SELECT,
				'default'   => 'listing_cat',
				'options'   => array(
					'listing_cat'       => 'Listing Category',
					'listing_location'  => 'Listing Location'
				)
			]
		);

		$this->add_control(
			'orderby',
			[
				'label' => 'Order By',
				'type' => Controls_Manager::SELECT,
				'default'   => 'count',
				'options'   => array(
					'count' => 'Number of children',
					'id'    => 'ID',
					'slug'  => 'Slug',
					'specify_terms' => 'Specify Locations/Categories'
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

//		$this->add_control(
//			'taxonomy_position',
//			[
//				'label' => 'Taxonomy Position',
//				'type' => Controls_Manager::SELECT,
//				'label_block' => true,
//				'default' => 'above_search_form',
//				'options' => array(
//                    'above_search_form' => 'Above Search Form',
//                    'below_search_form' => 'Below Search Form'
//                )
//			]
//		);

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
		if ( isset($aSettings['slider_bg']) && !empty($aSettings['slider_bg']) ){
			$aSliderBg = array();
		    foreach ($aSettings['slider_bg'] as $aImg){
			    $aSliderBg[] = $aImg['id'];
            }
			$aSettings['slider_bg'] = implode(',', $aSliderBg);
        }
		$aSettings = wp_parse_args(
			$aSettings,
			array(
				'TYPE'              => 'HERO',
				'heading'           => '',
				'heading_color'     => '',
				'heading_font_size' => '',
				'description'       => '',
				'description_color' => '',
				'description_cfont_size' => '',
				'bg_type'           => 'image',
				'img_size'          => 'large',
				'bg_overlay'        => '',
				'image_bg'          => '',
				'slider_bg'         => '',
				'toggle_button'     => 'enable',
				'button_name'       => 'Check out',
				'button_background_color' => '',
				'button_text_color' => '#fff',
				'button_size' => 'wil-btn--sm',
				'button_link'       => '#',
				'button_icon'       => '',
				'toggle_list_of_suggestions' => 'enable',
				'maximum_terms_suggestion' => 6,
				'taxonomy'               => 'listing_cat',
				'listing_cats'           => '',
				'listing_locations'      => '',
				'toggle_search_form'     => 'disable',
				'taxonomy_position'      => 'above_search_form',
				'search_form_background' => 'hero_formDark__3fCkB',
				'search_form_position'   => 'bottom',
				'search_form'       => array(),
				'extra_class'       => ''
			)
		);
//		rgba(97,206,112,0.58)
		$aSettings['image_bg'] = isset($aSettings['image_bg']['id']) ? wp_get_attachment_image_url($aSettings['image_bg']['id'], 'large') : '';

		if ( $aSettings['toggle_search_form'] == 'enable' && !empty($aSettings['search_form']) ){
		    $items = base64_encode(json_encode($aSettings['search_form']));
			$searchForm = '[wilcity_general_sc_hero_search_form items="'.$items.'"]';
        }else{
		    $searchForm = '';
        }

        wilcity_sc_render_hero($aSettings, $searchForm);
	}
}
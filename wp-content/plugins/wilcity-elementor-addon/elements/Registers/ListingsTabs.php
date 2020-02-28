<?php

namespace WILCITY_ELEMENTOR\Registers;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use WILCITY_SC\SCHelpers;

class ListingsTabs extends Widget_Base
{
    use Helpers;
    
    public function get_name()
    {
        return WILCITY_WHITE_LABEL.'-listings-tabs';
    }
    
    /**
     * Retrieve the widget title.
     *
     * @since  1.1.0
     *
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title()
    {
        return WILCITY_EL_PREFIX.'Listings Tabs (New)';
    }
    
    /**
     * Retrieve the widget icon.
     *
     * @since  1.1.0
     *
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon()
    {
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
     * @since  1.1.0
     *
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories()
    {
        return ['theme-elements'];
    }
    
    /**
     * Register the widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since  1.1.0
     *
     * @access protected
     */
    protected function _register_controls()
    {
        $this->start_controls_section(
            'listings_tabs_settings',
            [
                'label' => 'Main Settings',
            ]
        );
        
        $this->add_control(
            'heading',
            [
                'label'   => 'Heading',
                'type'    => Controls_Manager::TEXT,
                'default' => 'The Latest Listings'
            ]
        );
        
        $this->add_control(
            'heading_color',
            [
                'label'   => 'Heading Color',
                'type'    => Controls_Manager::COLOR,
                'default' => ''
            ]
        );
        
        $this->add_control(
            'taxonomy',
            [
                'label'   => 'Get Listings in',
                'type'    => Controls_Manager::SELECT,
                'default' => 'listing_cat',
                'options' => [
                    'listing_cat'      => 'Listing Categories',
                    'listing_location' => 'Listing Locations',
                    'custom'           => 'Custom Taxonomy'
                ]
            ]
        );
        
        $this->add_control(
            'get_term_type',
            [
                'label'       => 'Get Terms Type',
                'type'        => Controls_Manager::SELECT,
                'description' => 'Warning: If you want to use Get Term Children mode, You can use select 1 Listing Location / Listing Category only',
                'default'     => 'term_children',
                'options'     => [
                    'term_children' => 'Get Term Children',
                    'specify_terms' => 'Specify Terms'
                ]
            ]
        );
        
        if ($this->getTerms('listing_cat') !== 'toomany') {
            $this->add_control(
                'listing_cats',
                [
                    'label'       => 'Select Listing Category / Categories',
                    'description' => 'If you are using Get Term Children mode, you can enter in 1 Listing Category only',
                    'type'        => Controls_Manager::SELECT2,
                    'label_block' => true,
                    'multiple'    => true,
                    'options'     => $this->getTerms('listing_cat'),
                    'condition'   => [
                        'taxonomy' => 'listing_cat'
                    ]
                ]
            );
        } else {
            $this->add_control(
                'listing_cats',
                [
                    'label'       => 'Enter in Category IDs',
                    'description' => 'Each category is separated by a comma. For example: 1,2,3',
                    'type'        => Controls_Manager::TEXT,
                    'label_block' => true,
                    'options'     => $this->getTerms('listing_cat'),
                    'condition'   => [
                        'taxonomy' => 'listing_cat'
                    ]
                ]
            );
        }
        
        if ($this->getTerms('listing_location') !== 'toomany') {
            $this->add_control(
                'listing_locations',
                [
                    'label'       => 'Select Listing Locations[s]',
                    'description' => 'If you are using Get Term Children mode, you can enter in 1 Listing Location only',
                    'type'        => Controls_Manager::SELECT2,
                    'multiple'    => true,
                    'label_block' => true,
                    'options'     => $this->getTerms('listing_location'),
                    'condition'   => [
                        'taxonomy' => 'listing_location'
                    ]
                ]
            );
        } else {
            $this->add_control(
                'listing_locations',
                [
                    'label'       => 'Enter in Location IDs',
                    'description' => 'Each location is separated by a comma. For example: 1,2,3',
                    'type'        => Controls_Manager::TEXT,
                    'label_block' => true,
                    'options'     => $this->getTerms('listing_location'),
                    'condition'   => [
                        'taxonomy' => 'listing_location'
                    ]
                ]
            );
        }
        
        $this->add_control(
            'custom_taxonomy_key',
            [
                'label'       => 'Taxonomy Key',
                'type'        => Controls_Manager::TEXT,
                'description' => 'This feature is useful if you want to use show up your custom taxonomy',
                'default'     => '',
                'condition'   => [
                    'taxonomy' => 'custom'
                ]
            ]
        );
        
        $this->add_control(
            'custom_taxonomies_id',
            [
                'label'       => 'Your Custom Taxonomies IDs',
                'type'        => Controls_Manager::TEXT,
                'description' => 'Each taxonomy should separated by a comma, Eg: 1,2,3,4. Leave empty if you are working on Taxonomy Template',
                'default'     => '',
                'condition'   => [
                    'taxonomy' => 'custom'
                ]
            ]
        );
        
        $this->add_control(
            'number_of_term_children',
            [
                'label'   => 'Maximum Term Children',
                'type'    => Controls_Manager::TEXT,
                'default' => 6
            ]
        );
        
        $this->add_control(
            'posts_per_page',
            [
                'label'   => 'Maximum Items',
                'type'    => Controls_Manager::TEXT,
                'default' => 6
            ]
        );
        
        $this->add_control(
            'orderby',
            [
                'label'   => 'Order By',
                'type'    => Controls_Manager::SELECT,
                'default' => 'post_date',
                'options' => [
                    'post_date'        => 'Listing Date',
                    'post_title'       => 'Listing Title',
                    'menu_order'       => 'Listing Order',
                    'best_viewed'      => 'Popular Viewed',
                    'best_rated'       => 'Popular Rated',
                    'best_shared'      => 'Popular Shared',
                    'premium_listings' => 'Premium Listings',
                    'nearbyme'         => 'Near By Me'
                ]
            ]
        );
        
        $this->add_control(
            'radius',
            [
                'label'     => 'Get listings within X radius',
                'type'      => Controls_Manager::TEXT,
                'default'   => 6,
                'condition' => [
                    'orderby' => 'nearbyme'
                ]
            ]
        );
        
        $this->add_control(
            'order',
            [
                'label'   => 'Order',
                'type'    => Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'DESC' => 'DESC',
                    'ASC'  => 'ASC'
                ]
            ]
        );
        
        $this->add_control(
            'post_types_filter',
            [
                'label'    => 'Post Types Filter',
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'options'  => SCHelpers::getPostTypeKeys(false, false)
            ]
        );
        
        $this->add_control(
            'extra_class',
            [
                'label' => 'Extra Class',
                'type'  => Controls_Manager::TEXT
            ]
        );
        
        $this->end_controls_section();
        
        $this->start_controls_section(
            'listings_on_screens',
            [
                'label' => 'General Settings',
            ]
        );
        
        $this->add_control(
            'terms_tab_id',
            [
                'label'   => 'Wrapper ID',
                'type'    => Controls_Manager::TEXT,
                'default' => uniqid('terms_tab_id')
            ]
        );
        
        $this->add_control(
            'img_size',
            [
                'label'       => 'Image Size',
                'description' => 'You can use the defined image sizes like: full, large, medium, wilcity_560x300 or 400,300 to specify the image width and height.',
                'type'        => Controls_Manager::TEXT,
                'default'     => 'wilcity_360x200'
            ]
        );
        
        $this->add_control(
            'toggle_viewmore',
            [
                'label'   => 'Toggle Viewmore',
                'type'    => Controls_Manager::SELECT,
                'default' => 'enable',
                'options' => [
                    'enable'  => 'Enable',
                    'disable' => 'Disable'
                ]
            ]
        );
        
        $this->add_control(
            'tab_alignment',
            [
                'label'   => 'Tab Alignment',
                'type'    => Controls_Manager::SELECT,
                'default' => 'wil-text-right',
                'options' => [
                    'wil-text-center' => 'wil-text-center',
                    'wil-text-right'  => 'wil-text-right'
                ]
            ]
        );
        
        $this->add_control(
            'maximum_posts_on_lg_screen',
            [
                'label'       => 'Items / row on >=1600px',
                'description' => 'Set number of listings will be displayed when the screen is larger or equal to 1600px ',
                'type'        => Controls_Manager::SELECT,
                'default'     => 'col-lg-4',
                'options'     => [
                    'col-lg-2'  => '6 Items / row',
                    'col-lg-3'  => '4 Items / row',
                    'col-lg-4'  => '3 Items / row',
                    'col-lg-6'  => '2 Items / row',
                    'col-lg-12' => '1 Item / row'
                ]
            ]
        );
        
        $this->add_control(
            'maximum_posts_on_md_screen',
            [
                'label'       => 'Items / row on >=1200px',
                'description' => 'Set number of listings will be displayed when the screen is larger or equal to 1200px ',
                'type'        => Controls_Manager::SELECT,
                'default'     => 'col-md-3',
                'options'     => [
                    'col-md-2'  => '6 Items / row',
                    'col-md-3'  => '4 Items / row',
                    'col-md-4'  => '3 Items / row',
                    'col-md-6'  => '2 Items / row',
                    'col-md-12' => '1 Item / row'
                ]
            ]
        );
        
        $this->add_control(
            'maximum_posts_on_sm_screen',
            [
                'label'       => 'Items on >=992px',
                'description' => 'Set number of listings will be displayed when the screen is larger or equal to 992px ',
                'type'        => Controls_Manager::SELECT,
                'options'     => [
                    'col-sm-2'  => '6 Items / row',
                    'col-sm-3'  => '4 Items / row',
                    'col-sm-4'  => '3 Items / row',
                    'col-sm-6'  => '2 Items / row',
                    'col-sm-12' => '1 Item / row'
                ],
                'default'     => 'col-sm-12'
            ]
        );
        
        $this->end_controls_section();
    }
    
    /**
     * Render the widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since  1.1.0
     *
     * @access protected
     */
    protected function render()
    {
        $aSettings = $this->get_settings();
        $aSettings = wp_parse_args(
            $aSettings,
            [
                'TYPE'                       => 'LISTINGS_TABS',
                'terms_tab_id'               => '',
                'heading'                    => '',
                'heading_color'              => '#fff',
                'taxonomy'                   => '',
                'get_term_type'              => '',
                'listing_cats'               => '',
                'listing_locations'          => '',
                'custom_taxonomy_key'        => '',
                'custom_taxonomies_id'       => '',
                'number_of_term_children'    => 6,
                'posts_per_page'             => 6,
                'orderby'                    => 'post_date',
                'order'                      => 'DESC',
                'post_types_filter'          => '',
                'extra_class'                => '',
                'img_size'                   => '',
                'tab_alignment'              => 'wil-text-right',
                'maximum_posts_on_lg_screen' => 'col-lg-4',
                'maximum_posts_on_md_screen' => 'col-md-3',
                'maximum_posts_on_sm_screen' => 'col-sm-12'
            ]
        );
        
        wilcityRenderListingsTabsSC($aSettings);
    }
}

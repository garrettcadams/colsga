<?php

namespace WILCITY_ELEMENTOR\Registers;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class AuthorSlider extends Widget_Base
{
    use Helpers;

    public function get_name()
    {
        return WILCITY_WHITE_LABEL.'-author-slider';
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
        return WILCITY_EL_PREFIX.'Author Slider';
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
            'intro_general_section',
            [
                'label' => 'General Settings',
            ]
        );

        $this->add_control(
            'role__in',
            [
                'label'    => 'Role in',
                'type'     => Controls_Manager::SELECT2,
                'default'  => 'administrator',
                'multiple' => true,
                'options'  => [
                    'administrator' => 'Administrator',
                    'editor'        => 'Editor',
                    'contributor'   => 'Contributor',
                    'subscriber'    => 'Subscriber',
                    'seller'        => 'Vendor',
                    'author'        => 'Author'
                ]
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label'    => 'Order By',
                'type'     => Controls_Manager::SELECT,
                'default'  => 'post_count',
                'options'  => [
                    'registered' => 'Registered',
                    'post_count' => 'Post Count',
                    'ID'         => 'ID'
                ]
            ]
        );

        $this->add_control(
            'number',
            [
                'label'    => 'NumberMaximum Users',
                'type'     => Controls_Manager::TEXT,
                'default'  => 8
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
                'TYPE'          => 'AUTHOR_SLIDER',
                'role__in'      => 'administrator,contributor',
                'orderby'       => 'post_count',
                'number'        => 8,
                'extra_class' => ''
            ]
        );
        wilcity_render_author_slider($aSettings);
    }
}

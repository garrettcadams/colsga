<?php

namespace WILCITY_ELEMENTOR\Registers;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class TeamIntroSlider extends Widget_Base
{
    use Helpers;

    public function get_name()
    {
        return WILCITY_WHITE_LABEL.'-team-intro-slider';
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
        return WILCITY_EL_PREFIX.'Team Intro Slider';
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
            'get_by',
            [
                'label'   => 'Show all members who are',
                'type'    => Controls_Manager::SELECT,
                'default' => 'administrator',
                'options' => [
                    'administrator' => 'Administrator',
                    'editor'        => 'Editor',
                    'contributor'   => 'Contributor',
                    'vendor'        => 'Vendor',
                    'author'        => 'Author',
                    'custom'        => 'Custom'
                ]
            ]
        );

        $this->add_control(
            'members',
            [
                'label'    => 'Members',
                'type'     => Controls_Manager::REPEATER,
                'relation' => [
                    'parent'    => 'get_by',
                    'show_when' => ['get_by', '=', 'custom']
                ],
                'fields'   => [
                    [
                        'type'  => Controls_Manager::MEDIA,
                        'label' => 'Avatar',
                        'name'  => 'avatar'
                    ],
                    [
                        'type'  => Controls_Manager::MEDIA,
                        'label' => 'Picture',
                        'name'  => 'picture'
                    ],
                    [
                        'type'  => Controls_Manager::TEXT,
                        'label' => 'Name',
                        'name'  => 'display_name'
                    ],
                    [
                        'type'  => Controls_Manager::TEXT,
                        'label' => 'Position',
                        'name'  => 'position'
                    ],
                    [
                        'type'  => Controls_Manager::TEXTAREA,
                        'label' => 'Intro',
                        'name'  => 'intro'
                    ],
                    [
                        'name'        => 'social_networks',
                        'label'       => 'Social Networks',
                        'description' => 'Eg: facebook:https://facebook.com,google-plus:https://googleplus.com',
                        'type'        => Controls_Manager::TEXTAREA,
                    ]
                ],
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
                'TYPE'        => 'TEAM_INTRO_SLIDER',
                'pageBuilder' => 'elementor',
                'get_by'      => 'administrator',
                'members'     => [],
                'extra_class' => ''
            ]
        );
        wilcity_render_team_intro_slider($aSettings);
    }
}

<?php

/*
Widget Name: Javo Widget
Description: Javo widget
Author: Javothemes
Author URI: https://www.javothemes.com
*/

namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


class jvbpd_team_members extends Widget_Base {

	public function get_name() {
		return 'jvbpd-team-members';
	}

	public function get_title() {
		return 'Team Members';   // title to show on elementor
	}

	public function get_icon() {
		return 'fa fa-user-o';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-elements' ];    // category of the widget
	}

    public function get_script_depends() {
        return [
            'lae-widgets-scripts',
            'lae-frontend-scripts'
        ];
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'section_team',
            [
                'label' => __('Team', 'jvfrmtd'),
            ]
        );

        $this->add_control(

            'style', [
                'type' => Controls_Manager::SELECT,
                'label' => __('Choose Team Style', 'jvfrmtd'),
                'default' => 'style1',
                'options' => [
                    'style1' => __('Style 1', 'jvfrmtd'),
                    'style2' => __('Style 2', 'jvfrmtd'),
                ],
                'prefix_class' => 'lae-team-members-',
            ]
        );

        $this->add_control(
            'per_line',
            [
                'label' => __('Columns per row', 'jvfrmtd'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 6,
                'step' => 1,
                'default' => 3,
                'condition' => [
                    'style' => 'style1',
                ],
            ]
        );


        $this->add_control(
            'team_members',
            [
                'label' => __('Team Members', 'jvfrmtd'),
                'type' => Controls_Manager::REPEATER,
                'separator' => 'before',
                'default' => [
                    [
                        'member_name' => __('Team Member #1', 'jvfrmtd'),
                        'member_position' => __('CEO', 'jvfrmtd'),
                        'member_details' => __('I am member details. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'jvfrmtd'),
                    ],
                    [
                        'member_name' => __('Team Member #2', 'jvfrmtd'),
                        'member_position' => __('Lead Developer', 'jvfrmtd'),
                        'member_details' => __('I am member details. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'jvfrmtd'),
                    ],
                    [
                        'member_name' => __('Team Member #3', 'jvfrmtd'),
                        'member_position' => __('Finance Manager', 'jvfrmtd'),
                        'member_details' => __('I am member details. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'jvfrmtd'),
                    ],
                ],
                'fields' => [
                    [
                        'name' => 'member_name',
                        'label' => __('Member Name', 'jvfrmtd'),
                        'type' => Controls_Manager::TEXT,
                    ],
                    [
                        'name' => 'member_position',
                        'label' => __('Position', 'jvfrmtd'),
                        'type' => Controls_Manager::TEXT,
                    ],

                    [
                        'name' => 'member_image',
                        'label' => __('Team Member Image', 'jvfrmtd'),
                        'type' => Controls_Manager::MEDIA,
                        'default' => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
                        'label_block' => true,
                    ],
                    [
                        'name' => 'member_details',
                        'label' => __('Team Member details', 'jvfrmtd'),
                        'type' => Controls_Manager::TEXTAREA,
                        'default' => __('Details about team member', 'jvfrmtd'),
                        'description' => __('Provide a short writeup for the team member', 'jvfrmtd'),
                        'label_block' => true,
                    ],
                    [
                        'name' => 'social_profile',
                        'label' => __('Social Profile', 'jvfrmtd'),
                        'type' => Controls_Manager::HEADING,
                    ],
                    [
                        'type' => Controls_Manager::TEXT,
                        'name' => 'member_email',
                        'label' => __('Email Address', 'jvfrmtd'),
                        'description' => __('Enter the email address of the team member.', 'jvfrmtd'),
                    ],

                    [
                        'type' => Controls_Manager::TEXT,
                        'name' => 'facebook_url',
                        'label' => __('Facebook Page URL', 'jvfrmtd'),
                        'description' => __('URL of the Facebook page of the team member.', 'jvfrmtd'),
                    ],

                    [
                        'type' => Controls_Manager::TEXT,
                        'name' => 'twitter_url',
                        'label' => __('Twitter Profile URL', 'jvfrmtd'),
                        'description' => __('URL of the Twitter page of the team member.', 'jvfrmtd'),
                    ],

                    [
                        'type' => Controls_Manager::TEXT,
                        'name' => 'linkedin_url',
                        'label' => __('LinkedIn Page URL', 'jvfrmtd'),
                        'description' => __('URL of the LinkedIn profile of the team member.', 'jvfrmtd'),
                    ],

                    [
                        'type' => Controls_Manager::TEXT,
                        'name' => 'pinterest_url',
                        'label' => __('Pinterest Page URL', 'jvfrmtd'),
                        'description' => __('URL of the Pinterest page for the team member.', 'jvfrmtd'),
                    ],

                    [
                        'type' => Controls_Manager::TEXT,
                        'name' => 'dribbble_url',
                        'label' => __('Dribbble Profile URL', 'jvfrmtd'),
                        'description' => __('URL of the Dribbble profile of the team member.', 'jvfrmtd'),
                    ],

                    [
                        'type' => Controls_Manager::TEXT,
                        'name' => 'google_plus_url',
                        'label' => __('GooglePlus Page URL', 'jvfrmtd'),
                        'description' => __('URL of the Google Plus page of the team member.', 'jvfrmtd'),
                    ],

                    [
                        'type' => Controls_Manager::TEXT,
                        'name' => 'instagram_url',
                        'label' => __('Instagram Page URL', 'jvfrmtd'),
                        'description' => __('URL of the Instagram feed for the team member.', 'jvfrmtd'),
                    ],

                ],
                'title_field' => '{{{ member_name }}}',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_team_profiles_style',
            [
                'label' => __('General', 'jvfrmtd'),
                'tab' => Controls_Manager::TAB_STYLE,
                'show_label' => false,
            ]
        );

        $this->add_responsive_control(
            'team_member_spacing',
            [
                'label' => __('Team Member Spacing', 'jvfrmtd'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .lae-team-members .lae-team-member-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'isLinked' => false,
                'condition' => [
                    'style' => ['style2'],
                ],
            ]
        );

        $this->add_responsive_control(
            'thumbnail_hover_brightness',
            [
                'label' => __('Thumbnail Hover Brightness (%)', 'jvfrmtd'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 50,
                ],
                'range' => [
                    'px' => [
                        'max' => 100,
                        'min' => 1,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .lae-team-members .lae-team-member:hover .lae-image-wrapper img' => '-webkit-filter: brightness({{SIZE}}%);-moz-filter: brightness({{SIZE}}%);-ms-filter: brightness({{SIZE}}%); filter: brightness({{SIZE}}%);',
                ],
            ]
        );


        $this->add_control(
            'thumbnail_border_radius',
            [
                'label' => __('Thumbnail Border Radius', 'jvfrmtd'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .lae-team-members .lae-team-member .lae-image-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_team_member_title',
            [
                'label' => __('Member Title', 'jvfrmtd'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_tag',
            [
                'label' => __('Title HTML Tag', 'jvfrmtd'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'h1' => __('H1', 'jvfrmtd'),
                    'h2' => __('H2', 'jvfrmtd'),
                    'h3' => __('H3', 'jvfrmtd'),
                    'h4' => __('H4', 'jvfrmtd'),
                    'h5' => __('H5', 'jvfrmtd'),
                    'h6' => __('H6', 'jvfrmtd'),
                    'div' => __('div', 'jvfrmtd'),
                ],
                'default' => 'h3',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Color', 'jvfrmtd'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-team-members .lae-team-member .lae-team-member-text .lae-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .lae-team-members .lae-team-member .lae-team-member-text .lae-title',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_team_member_position',
            [
                'label' => __('Member Position', 'jvfrmtd'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'position_color',
            [
                'label' => __('Color', 'jvfrmtd'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-team-members .lae-team-member .lae-team-member-text .lae-team-member-position' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'position_typography',
                'selector' => '{{WRAPPER}} .lae-team-members .lae-team-member .lae-team-member-text .lae-team-member-position',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_team_member_details',
            [
                'label' => __('Member Details', 'jvfrmtd'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __('Color', 'jvfrmtd'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-team-members .lae-team-member .lae-team-member-details' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'selector' => '{{WRAPPER}} .lae-team-members .lae-team-member .lae-team-member-details',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_social_icon_styling',
            [
                'label' => __('Social Icons', 'jvfrmtd'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'social_icon_size',
            [
                'label' => __('Icon size in pixels', 'jvfrmtd'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em' ],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 128,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .lae-team-members .lae-team-member .lae-image-wrapper .lae-social-list i' => 'font-size: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_control(
            'social_icon_spacing',
            [
                'label' => __('Spacing', 'jvfrmtd'),
                'description' => __('Space between icons.', 'jvfrmtd'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => 0,
                    'right' => 15,
                    'bottom' => 0,
                    'left' => 0,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .lae-team-members .lae-team-member .lae-social-list .lae-social-list-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'isLinked' => false
            ]
        );

        $this->add_control(
            'social_icon_color',
            [
                'label' => __('Icon Color', 'jvfrmtd'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .lae-team-members .lae-team-member .lae-image-wrapper .lae-social-list i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hover_color',
            [
                'label' => __('Icon Hover Color', 'jvfrmtd'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .lae-team-members .lae-team-member .lae-image-wrapper .lae-social-list i:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings();
        ?>

        <?php $column_style = ''; ?>

        <?php if ($settings['style'] == 'style1'): ?>
            <?php
            $column_style = NULL;
            if(function_exists('lae_get_column_class')){
                $column_style = lae_get_column_class(intval($settings['per_line']));
            } ?>

        <?php endif; ?>

        <div class="lae-team-members lae-<?php echo $settings['style']; ?> lae-container">

            <?php foreach ($settings['team_members'] as $team_member): ?>

                <div class="lae-team-member-wrapper <?php echo $column_style; ?>">

                    <div class="lae-team-member">

                        <div class="lae-image-wrapper">

                            <?php $member_image = $team_member['member_image']; ?>

                            <?php if (!empty($member_image)): ?>

                                <?php echo wp_get_attachment_image($member_image['id'], 'full', false, array('class' => 'lae-image full')); ?>

                            <?php endif; ?>

                            <?php if ($settings['style'] == 'style1'): ?>

                                <?php $this->social_profile($team_member) ?>

                            <?php endif; ?>

                        </div>

                        <div class="lae-team-member-text">

                            <<?php echo $settings['title_tag']; ?> class="lae-title"><?php echo esc_html($team_member['member_name']) ?></<?php echo $settings['title_tag']; ?>>

                            <div class="lae-team-member-position">

                                <?php echo do_shortcode($team_member['member_position']) ?>

                            </div>

                            <div class="lae-team-member-details">

                                <?php echo do_shortcode($team_member['member_details']) ?>

                            </div>

                            <?php if ($settings['style'] == 'style2'): ?>

                                <?php $this->social_profile($team_member) ?>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

                <?php

            endforeach;

            ?>

        </div>

        <div class="lae-clear"></div>

        <?php
    }

    private function social_profile($team_member) {
        ?>

        <div class="lae-social-wrap">

            <div class="lae-social-list">

                <?php

                $email = $team_member['member_email'];
                $facebook_url = $team_member['facebook_url'];
                $twitter_url = $team_member['twitter_url'];
                $linkedin_url = $team_member['linkedin_url'];
                $dribbble_url = $team_member['dribbble_url'];
                $pinterest_url = $team_member['pinterest_url'];
                $googleplus_url = $team_member['google_plus_url'];
                $instagram_url = $team_member['instagram_url'];


                if ($email)
                    echo '<div class="lae-social-list-item"><a class="lae-email" href="mailto:' . $email . '" title="' . __("Send an email", 'jvfrmtd') . '"><i class="lae-icon-email"></i></a></div>';
                if ($facebook_url)
                    echo '<div class="lae-social-list-item"><a class="lae-facebook" href="' . $facebook_url . '" target="_blank" title="' . __("Follow on Facebook", 'jvfrmtd') . '"><i class="lae-icon-facebook"></i></a></div>';
                if ($twitter_url)
                    echo '<div class="lae-social-list-item"><a class="lae-twitter" href="' . $twitter_url . '" target="_blank" title="' . __("Subscribe to Twitter Feed", 'jvfrmtd') . '"><i class="lae-icon-twitter"></i></a></div>';
                if ($linkedin_url)
                    echo '<div class="lae-social-list-item"><a class="lae-linkedin" href="' . $linkedin_url . '" target="_blank" title="' . __("View LinkedIn Profile", 'jvfrmtd') . '"><i class="lae-icon-linkedin"></i></a></div>';
                if ($googleplus_url)
                    echo '<div class="lae-social-list-item"><a class="lae-googleplus" href="' . $googleplus_url . '" target="_blank" title="' . __("Follow on Google Plus", 'jvfrmtd') . '"><i class="lae-icon-googleplus"></i></a></div>';
                if ($instagram_url)
                    echo '<div class="lae-social-list-item"><a class="lae-instagram" href="' . $instagram_url . '" target="_blank" title="' . __("View Instagram Feed", 'jvfrmtd') . '"><i class="lae-icon-instagram"></i></a></div>';
                if ($pinterest_url)
                    echo '<div class="lae-social-list-item"><a class="lae-pinterest" href="' . $pinterest_url . '" target="_blank" title="' . __("Subscribe to Pinterest Feed", 'jvfrmtd') . '"><i class="lae-icon-pinterest"></i></a></div>';
                if ($dribbble_url)
                    echo '<div class="lae-social-list-item"><a class="lae-dribbble" href="' . $dribbble_url . '" target="_blank" title="' . __("View Dribbble Portfolio", 'jvfrmtd') . '"><i class="lae-icon-dribbble"></i></a></div>';

                ?>

            </div>

        </div>
        <?php
    }

    protected function content_template() {
    }

}
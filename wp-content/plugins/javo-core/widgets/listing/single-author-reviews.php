<?php

namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Background;


if (!defined('ABSPATH')) exit;

class jvbpd_single_author_reviews extends Widget_Base
{

    const MAX_SCORE = 5;

    public function get_name()
    {
        return 'jvbpd-single-author-reviews';
    }

    public function get_title()
    {
        return 'Lava Author Reviews';
    }

    public function get_icon()
    {
        return 'eicon-rating';
    }

    public function get_categories()
    {
        return ['jvbpd-single-listing'];
    }

    protected function _register_controls()
    {
        $this->start_controls_section('section_general', array(
            'label' => esc_html__('General', 'jvfrmtd'),
        ));

        $this->add_control('display_mode', array(
            'type' => Controls_Manager::SELECT,
            'label' => esc_html__('Template ', 'jvfrmtd'),
            'default' => '',
            'options' => array(
                '' => esc_html__('Single detail page', 'jvfrmtd'),
                'module' => esc_html__('Module', 'jvfrmtd'),
            ),
        ));
        $this->add_control('review_field', array(
            'type' => Controls_Manager::SELECT,
            'label' => esc_html__('Review Field', 'jvfrmtd'),
            'default' => '',
            'options' => array('' => esc_html__('Select a Review', 'jvfrmtd')) + self::getFields(),
        ));
        $this->add_control(
            'divider-tabs',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );

        $this->start_controls_tabs('review_types');
        $this->start_controls_tab('icon_on_tab', array(
            'label' => esc_html__('On', 'jvfrmtd'),
        ));
        $this->add_control('on_icon', array(
            'type' => Controls_Manager::MEDIA,
            'label' => esc_html__('On Icon', 'jvfrmtd'),
            'default' => array(
                'url' => \Elementor\Utils::get_placeholder_image_src(),
            ),
        ));
        $this->add_control('on_icon_width', array(
            'type' => Controls_Manager::SLIDER,
            'label' => esc_html__('Width', 'jvfrmtd'),
            'default' => array(
                'size' => '',
                'unit' => 'px',
            ),
            'range' => array(
                'px' => array(
                    'min' => 8,
                    'max' => 800,
                    'step' => 1,
                ),
                '%' => array(
                    'min' => 0,
                    'max' => 100,
                ),
            ),
            'size_units' => array('px', '%'),
            'selectors' => array(
                '{{WRAPPER}} img.author-review-icon.lvar-on' => 'width:{{SIZE}}{{UNIT}};',
            ),
        ));
        $this->end_controls_tab();
        $this->start_controls_tab('icon_off_tab', array(
            'label' => esc_html__('Off', 'jvfrmtd'),
        ));
        $this->add_control('off_icon', array(
            'type' => Controls_Manager::MEDIA,
            'label' => esc_html__('Off Icon', 'jvfrmtd'),
            'default' => array(
                'url' => \Elementor\Utils::get_placeholder_image_src(),
            ),
        ));
        $this->add_control('off_icon_width', array(
            'type' => Controls_Manager::SLIDER,
            'label' => esc_html__('Width', 'jvfrmtd'),
            'default' => array(
                'size' => '15',
                'unit' => 'px',
            ),
            'range' => array(
                'px' => array(
                    'min' => 8,
                    'max' => 800,
                    'step' => 1,
                ),
                '%' => array(
                    'min' => 0,
                    'max' => 100,
                ),
            ),
            'size_units' => array('px', '%'),
            'selectors' => array(
                '{{WRAPPER}} img.author-review-icon.lvar-off' => 'width:{{SIZE}}{{UNIT}};',
            ),
        ));
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control(
            'divider-description',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );

        $this->add_control(
            'review_description_onoff',
            [
                'label' => __('Use Modal Description', 'jvfrmtd'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Use', 'jvfrmtd'),
                'label_off' => __('Hide', 'jvfrmtd'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
			'des_title',
			[
				'label' => __( 'Title', 'jvfrmtd' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Learn More', 'jvfrmtd' ),
				'placeholder' => __( 'Type your title here', 'jvfrmtd' ),
			]
		);

        $this->add_control(
            'review_description',
            [
                'label' => __('Description', 'jvfrmtd'),
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'default' => __('Default description', 'jvfrmtd'),
                'placeholder' => __('Type your description here', 'jvfrmtd'),
                'condition' => Array(
					'review_description_onoff' => 'yes'
				),
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section('section_field', array(
            'label' => __('Field Style', 'jvfrmtd'),
            'tab' => Controls_Manager::TAB_STYLE,
        ));
        $this->add_control('image_spacing', array(
            'type' => Controls_Manager::SLIDER,
            'label' => esc_html__('Review Icon Spacing between', 'jvfrmtd'),
            'default' => array(
                'size' => '3',
                'unit' => 'px',
            ),
            'range' => array(
                'px' => array(
                    'min' => 0,
                    'max' => 500,
                    'step' => 1,
                ),
            ),
            'size_units' => array('px'),
            'selectors' => array(
                '{{WRAPPER}} img.author-review-icon' => 'margin-right:{{SIZE}}{{UNIT}};',
                '{{WRAPPER}} img.author-review-icon:last-child' => 'margin-right:0px;',
            ),
        ));

        $this->add_group_control(Group_Control_Typography::get_type(), array(
            'name' => 'label_style',
            'label' => __('Review Label', 'jvfrmtd'),
            'selector' => '{{WRAPPER}} .review-label-wrap > span.review-label',
            'scheme' => Scheme_Typography::TYPOGRAPHY_3,
        ));
        $this->add_control('label_color', array(
            'type' => Controls_Manager::COLOR,
            'label' => esc_html__('Review Label Color', 'jvfrmtd'),
            'selectors' => array(
                '{{WRAPPER}} .review-label-wrap > span.review-label' => 'color:{{VALUE}};',
            ),
            'default' => '#000000',
        ));

        $this->add_group_control(Group_Control_Typography::get_type(), array(
            'name' => 'learn_more_style',
            'label' => __('Learn More Link', 'jvfrmtd'),
            'selector' => '{{WRAPPER}} a.review-detail-opener',
            'scheme' => Scheme_Typography::TYPOGRAPHY_3,
        ));
        $this->add_control('learn_more_color', array(
            'type' => Controls_Manager::COLOR,
            'label' => esc_html__('Learn More Link Color', 'jvfrmtd'),
            'selectors' => array(
                '{{WRAPPER}} a.review-detail-opener' => 'color:{{VALUE}};',
            ),
            'default' => '#000000',
        ));
        $this->end_controls_section();
    }

    protected function render()
    {
        jvbpd_elements_tools()->switch_preview_post();

        $settings = $this->get_settings();
        $field = $this->get_settings('review_field');
        $review_description_onoff = $this->get_settings('review_description_onoff');
        $des_title = $this->get_settings('des_title');
        $reviewFields = self::getFields();
        $reviewFieldLabel = isset($reviewFields[$field]) ? $reviewFields[$field] : false;

        $imageIDs = array();
        $isModuleMode = 'module' == $this->get_settings('display_mode');

        $this->add_render_attribute('wrap', 'class', array('lvar-wrap', 'text-center'));
        $this->add_render_attribute('icon-wrap', 'class', 'author-review-icon-wrap');
        $this->add_render_attribute('review_label', 'class', 'review-label-wrap');
        $this->add_render_attribute('on_icon', array(
            'class' => array('author-review-icon', 'lvar-on'),
            'alt' => esc_html__("Review On Image", 'jvfrmtd'),
        ));
        $this->add_render_attribute('off_icon', array(
            'class' => array('author-review-icon', 'lvar-off'),
            'alt' => esc_html__("Review Off Image", 'jvfrmtd'),
        ));
        // Image
        foreach (array('on_icon', 'off_icon') as $image) {
            $imageArgs = $this->get_settings($image);
            $imageURL = $imageArgs['url'];
            $imageIDs[$image] = $imageArgs['id'];
            $this->add_render_attribute($image, 'src', $imageURL);
        }
        $this->add_render_attribute('modal-opener', array(
            'class' => array('review-detail-opener', 'jvbpd-author-review-modal'),
            'href' => 'javascript:',
        )); ?>
        <div <?php echo $this->get_render_attribute_string('wrap'); ?>>
            <div <?php echo $this->get_render_attribute_string('icon-wrap'); ?>>
                <?php
                if ($isModuleMode) {
                    // Ajax
                    printf(
                        '{author_review:%1$s|%2$s|%3$s}',
                        $field,
                        $imageIDs['on_icon'],
                        $imageIDs['off_icon']
                    );
                } else {
                    $onRepeatCount = $this->getReviewData($field);
                    $offRepeatCount = intVal(self::MAX_SCORE) - $onRepeatCount;

                    $reviewLoop = 0;
                    while ($reviewLoop < $onRepeatCount) {
                        printf('<img %s>', $this->get_render_attribute_string('on_icon'));
                        $reviewLoop++;
                    }
                    $reviewLoop = 0;
                    while ($reviewLoop < $offRepeatCount) {
                        printf('<img %s>', $this->get_render_attribute_string('off_icon'));
                        $reviewLoop++;
                    }
                }
                ?>
            </div>
            <?php
            if ($reviewFieldLabel) {
                ?>
                <div <?php echo $this->get_render_attribute_string('review_label'); ?>>
                    <span class="review-label"><?php esc_html_e($reviewFieldLabel); ?></span>
                </div>
            <?php
            }
            if ( 'yes' === $review_description_onoff ) {
            ?>
            <a <?php echo $this->get_render_attribute_string('modal-opener'); ?>>
                <?php echo $des_title; ?>
            </a>
            <script type="text/html">
                <?php echo $this->get_settings('review_description'); ?>
            </script>
            <?php
            }
            ?>
        </div>
        <?php
        jvbpd_elements_tools()->restore_preview_post();
    }

    private static function getFields()
    {
        $output = array();
        if (!class_exists('\LavaAuthorReivew\Pages\Dashboard')) {
            return $output;
        }
        $reviewFields = \LavaAuthorReivew\Pages\Dashboard::getFields();
        foreach ($reviewFields as $reviewField) {
            $output[$reviewField['slug']] = $reviewField['label'];
        }

        return $output;
    }

    private function getReviewData($review)
    {
        $output = 0;
        $data = get_post_meta(get_the_ID(), '_lvar_review_key', true);
        if (is_array($data) && isset($data[$review])) {
            $output = $data[$review];
        }
        return intVal($output);
    }
}
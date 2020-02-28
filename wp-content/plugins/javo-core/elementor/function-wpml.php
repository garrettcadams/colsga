<?php

if(!function_exists('jvbpd_core_widgets_to_translate_wpml')){
    add_filter('wpml_elementor_widgets_to_translate', 'jvbpd_core_widgets_to_translate_wpml');
    function jvbpd_core_widgets_to_translate_wpml($widgets=Array()) {
        $widgetsFields = Array(
            'jvbpd-absolute-images' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'caption',
                        'type' => esc_html__("Absolute image caption", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'list_title',
                        'type' => esc_html__("Absolute image list title", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'list_content',
                        'type' => esc_html__("Absolute image list content", 'jvfrmtd'),
                        'editor_type' => 'VISUAL',
                    ),
                ),
            ),
            'jvbpd-bp-buddypress-list' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'widget_title',
                        'type' => esc_html__("Buddypress title", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                ),
            ),
            'jvbpd-carousel-category' => Array(
                'fields' => Array(),
                'integration' => 'WPML_JavoCore_Carousel_Category_List',
            ),
            'jvbpd-category-block' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'block_title',
                        'type' => esc_html__("Category block title", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'block_des',
                        'type' => esc_html__("Category block description", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                ),
            ),
            'jvbpd-favorites-button' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'txt_icon_button_label_normal',
                        'type' => esc_html__("Favorite button label", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'txt_icon_button_label_hover',
                        'type' => esc_html__("Favorite button hover label", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                ),
            ),
            'jvbpd-float-btns' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'ad_title',
                        'type' => esc_html__("Float button title", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'ad_code',
                        'type' => esc_html__("Float button description", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                ),
            ),
            'jvbpd-user-menu-widget' => Array(
                'fields' => Array(),
                'integration' => 'WPML_JavoCore_Header_User_Menu',
            ),
            'animation-headline' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'before_text',
                        'type' => esc_html__("Animation headline before text", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'highlighted_text',
                        'type' => esc_html__("Animation headline highlighted text", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'rotating_text',
                        'type' => esc_html__("Animation headline rotating text", 'jvfrmtd'),
                        'editor_type' => 'VISUAL',
                    ),
                    Array(
                        'field' => 'after_text',
                        'type' => esc_html__("Animation headline after text", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                ),
            ),
            'jv-heading' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'heading_text',
                        'type' => esc_html__("JV Heading text", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'sub_heading_text',
                        'type' => esc_html__("JV Heading sub heading text", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                ),
            ),
            'image-block' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'caption',
                        'type' => esc_html__("Image block caption", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                ),
            ),
            'jvbpd-image-marker' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'caption',
                        'type' => esc_html__("Image marker caption", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                ),
                'integration' => 'WPML_JavoCore_Image_Marker_list',
            ),
            'jvbpd-button' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'txt_icon_button_label_normal',
                        'type' => esc_html__("JV button label", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'txt_icon_button_label_hover',
                        'type' => esc_html__("JV button hover label", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                ),
            ),
            'jvbpd-modal-popup' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'des_title',
                        'type' => esc_html__("Modal popup title", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'review_description',
                        'type' => esc_html__("Modal popup description", 'jvfrmtd'),
                        'editor_type' => 'VISUAL',
                    ),
                ),
            ),
            'jvbpd-post-box-block' => Array(
                'fields' => Array(),
                'integration' => 'WPML_JavoCore_PostBox_Block_List',
            ),
            'jvbpd-price-table' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'title',
                        'type' => esc_html__("Price table title", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'subtitle',
                        'type' => esc_html__("Price table sub title", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'price',
                        'type' => esc_html__("Price table price", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'price_suffix',
                        'type' => esc_html__("Price table price suffix", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'button_before',
                        'type' => esc_html__("Price Table Text Before Action Button", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'button_text',
                        'type' => esc_html__("Price table price button text", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'button_after',
                        'type' => esc_html__("Price table Text After Action Button", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                ),
                'integration' => 'WPML_JavoCore_PriceTable_Features_List',
            ),
            'jvbpd-search-form-button' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'txt_icon_button_label_normal',
                        'type' => esc_html__("Search form button label", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'txt_icon_button_label_hover',
                        'type' => esc_html__("Search form button hover label", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                ),
            ),
            'jvbpd-search-form-listing' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'txt_icon_button_label_normal',
                        'type' => esc_html__("Search form advanced button label", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'txt_icon_button_label_hover',
                        'type' => esc_html__("Search form advanced button hover label", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                ),
            ),
            'jvbpd_slides' => Array(
                'fields' => Array(),
                'integration' => 'WPML_JavoCore_Slider_Slides',
            ),
            'jv_tabs' => Array(
                'fields' => Array(),
                'integration' => 'WPML_JavoCore_JV_Tabs',
            ),
            'jvbpd-team-members' => Array(
                'fields' => Array(),
                'integration' => 'WPML_JavoCore_TeamMebers',
            ),
            'jvbpd-banner-spot' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'ad_title',
                        'type' => esc_html__("Banner spot title", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'ad_code',
                        'type' => esc_html__("Banner spot description", 'jvfrmtd'),
                        'editor_type' => 'VISUAL',
                    ),
                ),
            ),
            'jvbpd-map-rating-btn' => Array(
                'fields' => Array(),
                'integration' => 'WPML_JavoCore_Map_Filter_Buttons',
            ),
            'jvbpd-map-list-filters' => Array(
                'fields' => Array(),
                'integration' => 'WPML_JavoCore_Map_List_Filters',
            ),
            'jvbpd-map-list-filter-total-count' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'suffix_label',
                        'type' => esc_html__("Map total count suffix label", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                ),
            ),
            'jvbpd-map-list-reset-filter' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'label',
                        'type' => esc_html__("Map reset button label", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                ),
            ),
            'jvbpd-map-list-sort-dropdown' => Array(
                'fields' => Array(),
                'integration' => 'WPML_JavoCore_Map_Sort_Dropdown',
            ),
            'jvbpd-map-list-toggle' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'map_text',
                        'type' => esc_html__("Map/List toggle map text", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'list_text',
                        'type' => esc_html__("Map/List toggle list text", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                ),
            ),
            'jvbpd-single-author-reviews' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'des_title',
                        'type' => esc_html__("Single author review title", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'review_description',
                        'type' => esc_html__("Single author review description", 'jvfrmtd'),
                        'editor_type' => 'VISUAL',
                    ),
                ),
            ),
            'jvbpd-single-btn-meta' => Array(
                'fields' => Array(),
                'integration' => 'WPML_JavoCore_Single_Header_buttons',
            ),
            'jvbpd-single-address' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'showmap_link_txt',
                        'type' => esc_html__("Single address map link text", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                ),
            ),
            'jvbpd-single-cf7' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'cf7_button_title',
                        'type' => esc_html__("Single ContactForm7 button title", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                ),
            ),
            'jvbpd-single-get-direction' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'button_label',
                        'type' => esc_html__("Single get direction button label", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                ),
            ),
            'jvbpd-single-tel1' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'tel_pre_txt',
                        'type' => esc_html__("Single tell pre text", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                ),
            ),
            'social-share-buttons' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'expand_title',
                        'type' => esc_html__("Single social share button title", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                ),
            ),
            'jvbpd-single-spyscroll' => Array(
                'fields' => Array(),
                'integration' => 'WPML_JavoCore_Single_ScrollSpy',
            ),
            'jvbpd-single-title-line' => Array(
                'fields' => Array(),
                'integration' => 'WPML_JavoCore_Single_TitleLine',
            ),
            'jvbpd-page-block' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'header_title',
                        'type' => esc_html__("Post block header title", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'subtitle',
                        'type' => esc_html__("Post block header sub title", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                ),
            ),
            'jvbpd-archive-meta' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'empty_msg',
                        'type' => esc_html__("Empty Message", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'field_label',
                        'type' => esc_html__("Field label", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'prefix_text',
                        'type' => esc_html__("Prefix text", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'suffix_text',
                        'type' => esc_html__("Suffix text", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                ),
            ),
            'jvbpd-submit-listing-field' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'empty_msg',
                        'type' => esc_html__("Empty Message", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'field_label',
                        'type' => esc_html__("Field label", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                ),
            ),
            'jvbpd-single-listing-base-meta' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'empty_msg',
                        'type' => esc_html__("Empty Message", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'field_label',
                        'type' => esc_html__("Field label", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'prefix_text',
                        'type' => esc_html__("Prefix text", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'suffix_text',
                        'type' => esc_html__("Suffix text", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                ),
            ),
            'jvbpd-block-card' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'title',
                        'type' => esc_html__("Block card title", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'description',
                        'type' => esc_html__("Block card description", 'jvfrmtd'),
                        'editor_type' => 'VISUAL',
                    ),
                    Array(
                        'field' => 'button',
                        'type' => esc_html__("Block card button text", 'jvfrmtd'),
                        'editor_type' => 'VISUAL',
                    ),
                ),
            ),
            'jvbpd_testimonial' => Array(
                'fields' => Array(),
                'integration' => 'WPML_JavoCore_Testimoonial_Options',
            ),
            'jvbpd_testimonial_wide' => Array(
                'fields' => Array(),
                'integration' => 'WPML_JavoCore_Testimoonial_Options',
            ),
            'jvbpd_featured_block' => Array(
                'fields' => Array(),
                'integration' => 'WPML_JavoCore_Testimoonial_Options',
            ),
            'jvbpd_members' => Array(
                'fields' => Array(),
                'integration' => 'WPML_JavoCore_Testimoonial_Options',
            ),
            'jvbpd_login' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'button_text',
                        'type' => esc_html__("Login Button text", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'user_label',
                        'type' => esc_html__("Login username label", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'password_label',
                        'type' => esc_html__("Login password placeholder", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'password_placeholder',
                        'type' => esc_html__("Login password placeholder", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                ),
            ),
            'jvbpd_signup' => Array(
                'fields' => Array(
                    Array(
                        'field' => 'button_text',
                        'type' => esc_html__("Signup Button text", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'user_label',
                        'type' => esc_html__("Signup username label", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'password_label',
                        'type' => esc_html__("Signup password placeholder", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                    Array(
                        'field' => 'password_placeholder',
                        'type' => esc_html__("Signup password placeholder", 'jvfrmtd'),
                        'editor_type' => 'LINE',
                    ),
                ),
            ),
        );

        $output = Array();
        foreach($widgetsParams as $widgetName => $widgetFields) {
            $args = Array(
                'conditions' => Array( 'widgetType' => $widgetName ),
                'fields' => $widgetFields['fields'],
            );
            if(isset($widgetFields['integration'])) {
                $args['integration-class'] = $widgetFields['integration'];
            }
            $output[$widgetName] = $args;
        }
        return wp_parse_args($output, $widgets);
    }
}



<?php

if(!class_exists('WPML_JavoCore_Carousel_Category_List')){
    class WPML_JavoCore_Carousel_Category_List extends WPML_Elementor_Module_With_Items{
        public function get_items_field() { return 'list'; }
        public function get_fields(){ return Array('list_title'); }
        protected function get_title( $field ) {
            switch($field) {
                case 'list_title' : return esc_html__( 'Carousel Category Title', 'jvfrmtd' );
                default: return '';
            }
        }
        protected function get_editor_type( $field ) {
            switch($field) {
                case 'list_title' : return 'LINE';
                default: return '';
            }
        }
    }
}

if(!class_exists('WPML_JavoCore_Header_User_Menu')){
    class WPML_JavoCore_Header_User_Menu extends WPML_Elementor_Module_With_Items{
        public function get_items_field() { return 'user_menus'; }
        public function get_fields(){ return Array('my_menu_text'); }
        protected function get_title( $field ) {
            switch($field) {
                case 'my_menu_text' :
                    return esc_html__( 'Header user menu "My menu" text', 'jvfrmtd' );
                case 'notification_text' :
                    return esc_html__( 'Header user menu "Notification" text', 'jvfrmtd' );
                case 'add_new_text' :
                    return esc_html__( 'Header user menu "Add new" text', 'jvfrmtd' );

                default: return '';
            }
        }
        protected function get_editor_type( $field ) {
            switch($field) {
                case 'my_menu_text' : return 'LINE';
                case 'notification_text' : return 'LINE';
                case 'add_new_text' : return 'LINE';
                default: return '';
            }
        }
    }
}

if(!class_exists('WPML_JavoCore_Image_Marker_list')){
    class WPML_JavoCore_Image_Marker_list extends WPML_Elementor_Module_With_Items{
        public function get_items_field() { return 'list'; }
        public function get_fields(){
            return Array(
                'caption',
                'list_title',
                'list_content',
            );
        }
        protected function get_title( $field ) {
            switch($field) {
                case 'caption' :
                    return esc_html__( 'Image marker list caption', 'jvfrmtd' );
                case 'list_title' :
                    return esc_html__( 'Image marker list title', 'jvfrmtd' );
                case 'list_content' :
                    return esc_html__( 'Image marker list content', 'jvfrmtd' );

                default: return '';
            }
        }
        protected function get_editor_type( $field ) {
            switch($field) {
                case 'caption' : return 'LINE';
                case 'list_title' : return 'LINE';
                case 'list_content' : return 'VISUAL';
                default: return '';
            }
        }
    }
}

if(!class_exists('WPML_JavoCore_PostBox_Block_List')){
    class WPML_JavoCore_PostBox_Block_List extends WPML_Elementor_Module_With_Items{
        public function get_items_field() { return 'block-list'; }
        public function get_fields(){ return Array('list_title'); }
        protected function get_title( $field ) {
            switch($field) {
                case 'list_title' : return esc_html__( 'PostBox Block list title', 'jvfrmtd' );
                default: return '';
            }
        }
        protected function get_editor_type( $field ) {
            switch($field) {
                case 'list_title' : return 'LINE';
                default: return '';
            }
        }
    }
}

if(!class_exists('WPML_JavoCore_PriceTable_Features_List')){
    class WPML_JavoCore_PriceTable_Features_List extends WPML_Elementor_Module_With_Items{
        public function get_items_field() { return 'features_list'; }
        public function get_fields(){ return Array('item_text'); }
        protected function get_title( $field ) {
            switch($field) {
                case 'item_text' : return esc_html__( 'Price table features list item text', 'jvfrmtd' );
                default: return '';
            }
        }
        protected function get_editor_type( $field ) {
            switch($field) {
                case 'item_text' : return 'LINE';
                default: return '';
            }
        }
    }
}

if(!class_exists('WPML_JavoCore_Slider_Slides')){
    class WPML_JavoCore_Slider_Slides extends WPML_Elementor_Module_With_Items{
        public function get_items_field() { return 'slides'; }
        public function get_fields(){
            return Array(
                'heading',
                'description',
                'button_text',
            );
        }
        protected function get_title( $field ) {
            switch($field) {
                case 'heading' : return esc_html__( 'JV Slider slide heading', 'jvfrmtd' );
                case 'description' : return esc_html__( 'JV Slider slide description', 'jvfrmtd' );
                case 'button_text' : return esc_html__( 'JV Slider slide button text', 'jvfrmtd' );
                default: return '';
            }
        }
        protected function get_editor_type( $field ) {
            switch($field) {
                case 'heading' : return 'LINE';
                case 'description' : return 'VISUAL';
                case 'button_text' : return 'LINE';
                default: return '';
            }
        }
    }
}

if(!class_exists('WPML_JavoCore_JV_Tabs')){
    class WPML_JavoCore_JV_Tabs extends WPML_Elementor_Module_With_Items{
        public function get_items_field() { return 'tabs'; }
        public function get_fields(){
            return Array(
                'tab_title',
                'tab_content',
            );
        }
        protected function get_title( $field ) {
            switch($field) {
                case 'tab_title' : return esc_html__( 'JV Tabs title', 'jvfrmtd' );
                case 'tab_content' : return esc_html__( 'JV Tabs content', 'jvfrmtd' );
                default: return '';
            }
        }
        protected function get_editor_type( $field ) {
            switch($field) {
                case 'tab_title' : return 'LINE';
                case 'tab_content' : return 'VISUAL';
                default: return '';
            }
        }
    }
}

if(!class_exists('WPML_JavoCore_TeamMebers')){
    class WPML_JavoCore_TeamMebers extends WPML_Elementor_Module_With_Items{
        public function get_items_field() { return 'team_members'; }
        public function get_fields(){
            return Array(
                'member_name',
                'member_position',
                'member_details',
            );
        }
        protected function get_title( $field ) {
            switch($field) {
                case 'member_name' : return esc_html__( 'Team member name', 'jvfrmtd' );
                case 'member_position' : return esc_html__( 'Team member position', 'jvfrmtd' );
                case 'member_details' : return esc_html__( 'Team member detail', 'jvfrmtd' );
                default: return '';
            }
        }
        protected function get_editor_type( $field ) {
            switch($field) {
                case 'member_name' : return 'LINE';
                case 'member_position' : return 'LINE';
                case 'member_details' : return 'VISUAL';
                default: return '';
            }
        }
    }
}

if(!class_exists('WPML_JavoCore_Map_Filter_Buttons')){
    class WPML_JavoCore_Map_Filter_Buttons extends WPML_Elementor_Module_With_Items{
        public function get_items_field() { return 'filters'; }
        public function get_fields(){ return Array('filter_title'); }
        protected function get_title( $field ) {
            switch($field) {
                case 'filter_title' : return esc_html__( 'Map filter button title', 'jvfrmtd' );
                default: return '';
            }
        }
        protected function get_editor_type( $field ) {
            switch($field) {
                case 'filter_title' : return 'LINE';
                default: return '';
            }
        }
    }
}

if(!class_exists('WPML_JavoCore_Map_List_Filters')){
    class WPML_JavoCore_Map_List_Filters extends WPML_Elementor_Module_With_Items{
        public function get_items_field() { return 'filters'; }
        public function get_fields(){
            return Array(
                'filter_title',
                'filter_placeholder',
                'price_unit',
            );
        }
        protected function get_title( $field ) {
            switch($field) {
                case 'filter_title' : return esc_html__( 'Map list filter title', 'jvfrmtd' );
                case 'filter_placeholder' : return esc_html__( 'Map list filter placeholder', 'jvfrmtd' );
                case 'price_unit' : return esc_html__( 'Map list filter unit', 'jvfrmtd' );
                default: return '';
            }
        }
        protected function get_editor_type( $field ) {
            switch($field) {
                case 'filter_title' : return 'LINE';
                case 'filter_placeholder' : return 'LINE';
                case 'price_unit' : return 'LINE';
                default: return '';
            }
        }
    }
}

if(!class_exists('WPML_JavoCore_Map_Sort_Dropdown')){
    class WPML_JavoCore_Map_Sort_Dropdown extends WPML_Elementor_Module_With_Items{
        public function get_items_field() { return 'sort'; }
        public function get_fields(){ return Array('sort_label'); }
        protected function get_title( $field ) {
            switch($field) {
                case 'sort_label' : return esc_html__( 'Map sort dropdown item label', 'jvfrmtd' );
                default: return '';
            }
        }
        protected function get_editor_type( $field ) {
            switch($field) {
                case 'sort_label' : return 'LINE';
                default: return '';
            }
        }
    }
}

if(!class_exists('WPML_JavoCore_Single_Header_buttons')){
    class WPML_JavoCore_Single_Header_buttons extends WPML_Elementor_Module_With_Items{
        public function get_items_field() { return 'single_btns'; }
        public function get_fields(){ return Array('list_title'); }
        protected function get_title( $field ) {
            switch($field) {
                case 'list_title' : return esc_html__( 'Single header button title', 'jvfrmtd' );
                default: return '';
            }
        }
        protected function get_editor_type( $field ) {
            switch($field) {
                case 'list_title' : return 'LINE';
                default: return '';
            }
        }
    }
}

if(!class_exists('WPML_JavoCore_Single_ScrollSpy')){
    class WPML_JavoCore_Single_ScrollSpy extends WPML_Elementor_Module_With_Items{
        public function get_items_field() { return 'list'; }
        public function get_fields(){ return Array('list_title'); }
        protected function get_title( $field ) {
            switch($field) {
                case 'list_title' : return esc_html__( 'Single scrollspy list title', 'jvfrmtd' );
                default: return '';
            }
        }
        protected function get_editor_type( $field ) {
            switch($field) {
                case 'list_title' : return 'LINE';
                default: return '';
            }
        }
    }
}

if(!class_exists('WPML_JavoCore_Single_TitleLine')){
    class WPML_JavoCore_Single_TitleLine extends WPML_Elementor_Module_With_Items{
        public function get_items_field() { return 'header_switchers'; }
        public function get_fields(){ return Array('list_title'); }
        protected function get_title( $field ) {
            switch($field) {
                case 'list_title' : return esc_html__( 'Single title line list title', 'jvfrmtd' );
                default: return '';
            }
        }
        protected function get_editor_type( $field ) {
            switch($field) {
                case 'list_title' : return 'LINE';
                default: return '';
            }
        }
    }
}

if(!class_exists('WPML_JavoCore_Testimoonial_Options')){
    class WPML_JavoCore_Testimoonial_Options extends WPML_Elementor_Module_With_Items{
        public function get_items_field() { return 'testimonial_option_1'; }
        public function get_fields(){
            return Array(
                'title_1',
                'designation_1',
                'testimoni_content_1',
            );
        }
        protected function get_title( $field ) {
            switch($field) {
                case 'title_1' : return esc_html__( 'Testimoonial title', 'jvfrmtd' );
                case 'designation_1' : return esc_html__( 'Testimoonial description', 'jvfrmtd' );
                case 'testimoni_content_1' : return esc_html__( 'Testimoonial content', 'jvfrmtd' );
                default: return '';
            }
        }
        protected function get_editor_type( $field ) {
            return 'VISUAL';
        }
    }
}
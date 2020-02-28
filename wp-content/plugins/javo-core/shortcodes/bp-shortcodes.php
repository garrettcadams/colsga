<?php

define( 'Jvbpd_Core_PLUGIN_DIR', plugin_dir_path(  dirname( __FILE__ ) ) );

if ( ! function_exists('jvbpd_get_img_overlay') ) {
    function jvbpd_get_img_overlay() {
        global $lynk_config;

        if (isset($lynk_config['image_overlay'])) {
            return $lynk_config['image_overlay'];
        }
        return '';
    }
}

if ( ! function_exists( 'jvbpd_get_online_status' ) ) :
    function jvbpd_get_online_status($user_id) {
        $output = '';
        if (jvbpd_is_user_online($user_id)) {
            $output .= '<span class="jvbpd-online-status high-bg"></span>';
        } else {
            $output .= '<span class="jvbpd-online-status"></span>';
        }
        return $output;
    }
endif;

/* Get User online */
if (!function_exists('jvbpd_is_user_online')):
    /**
     * Check if a Buddypress member is online or not
     * @global object $wpdb
     * @param integer $user_id
     * @param integer $time
     * @return boolean
     */
    function jvbpd_is_user_online($user_id, $time=5)
    {
        global $wpdb;
        $sql = $wpdb->prepare( "
			SELECT u.user_login FROM $wpdb->users u JOIN $wpdb->usermeta um ON um.user_id = u.ID
			WHERE u.ID = %d
			AND um.meta_key = 'last_activity'
			AND DATE_ADD( um.meta_value, INTERVAL %d MINUTE ) >= UTC_TIMESTAMP()", $user_id, $time);
        $user_login = $wpdb->get_var( $sql );
        if(isset($user_login) && $user_login !=""){
            return true;
        }
        else {return false;}
    }
endif;


/**
 * Buddypress Activity Stream.
 */
add_shortcode( 'jvlynk_activity_stream', 'jvbpd_activity_stream_func' );
function jvbpd_activity_stream_func($atts, $content = null) {
	$output = '';
	require( trailingslashit( Jvbpd_Core_PLUGIN_DIR ) . 'shortcodes/buddypress/jvlynk_activity_stream.php' );
	return $output;
}

/**
 * Buddypress Activity page.
 */
add_shortcode( 'jvlynk_activity_page', 'jvbpd_activity_page_func' );
function jvbpd_activity_page_func( $atts, $content = null ) {
	$output = '';
	require( trailingslashit( Jvbpd_Core_PLUGIN_DIR ) . 'shortcodes/buddypress/jvlynk_activity_page.php' );
	return $output;
}

/**
 * Buddypress Groups carousel.
 */
add_shortcode( 'jvlynk_groups_carousel', 'jvbpd_groups_carousel_func' );
function jvbpd_groups_carousel_func($atts, $content = null) {
	$output = '';
	require( trailingslashit( Jvbpd_Core_PLUGIN_DIR ) . 'shortcodes/buddypress/jvlynk_groups_carousel.php' );
	return $output;
}


/**
 * Buddypress Groups carousel.
 */
add_shortcode( 'jvlynk_groups_masonry', 'jvbpd_groups_masonry_func' );
function jvbpd_groups_masonry_func($atts, $content = null) {
	$output = '';
	require( trailingslashit( Jvbpd_Core_PLUGIN_DIR ) . 'shortcodes/buddypress/jvlynk_groups_masonry.php' );
	return $output;
}


/**
 * Buddypress Members carousel.
 */
add_shortcode( 'jvlynk_members_carousel', 'jvbpd_members_carousel_func' );
function jvbpd_members_carousel_func($atts, $content = null) {
	$output = '';
	require( trailingslashit( Jvbpd_Core_PLUGIN_DIR ) . 'shortcodes/buddypress/jvlynk_members_carousel.php' );
	return $output;
}


/**
 * Buddypress Members carousel.
 */
add_shortcode( 'jvlynk_members_masonry', 'jvbpd_members_masonry_func' );
function jvbpd_members_masonry_func($atts, $content = null) {
	$output = '';
	require( trailingslashit( Jvbpd_Core_PLUGIN_DIR ) . 'shortcodes/buddypress/jvlynk_members_masonry.php' );
	return $output;
}

function jvbpd_vc_manipulate_shortcodes(){

	$lynk_member_types = Array( 'All' => 'all' );

    // Buddypress Groups Carousel
    vc_map(
        array(
            "name" => __("Groups Carousel", 'jvfrmtd'),
            "base" => "jvlynk_groups_carousel",
            "class" => "",
            "category" => __('BuddyPress', 'jvfrmtd'),
            "icon" => "bp-icon",
            "params" => array(
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Type", 'jvfrmtd'),
                    "param_name" => "type",
                    "value" => array(
                        'Active' => 'active',
                        'Newest' => 'newest',
                        'Popular' => 'popular',
                        'Alphabetical' => 'alphabetical',
                        'Most Forum Topics' => 'most-forum-topics',
                        'Most Forum Posts' => 'most-forum-posts',
                        'Random' => 'random'
                    ),
                    "description" => __("The type of groups to display.", 'jvfrmtd')
                ),
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Number of groups", 'jvfrmtd'),
                    "param_name" => "number",
                    "value" => 12,
                    "description" => __("How many groups to get.", 'jvfrmtd')
                ),
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Minimum Items", 'jvfrmtd'),
                    "param_name" => "min_items",
                    "value" => 1,
                    "description" => __("Minimum number of items to show on the screen", 'jvfrmtd')
                ),
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Maximum Items", 'jvfrmtd'),
                    "param_name" => "max_items",
                    "value" => 6,
                    "description" => __("Maximum number of items to show on the screen", 'jvfrmtd')
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Image Type", 'jvfrmtd'),
                    "param_name" => "image_size",
                    "value" => array(
                        'Full' => 'full',
                        'Thumbnail' => 'thumb'
                    ),
                    "description" => __("The size to get from buddypress", 'jvfrmtd')
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Auto play", 'jvfrmtd'),
                    "param_name" => "autoplay",
                    "value" => array(
                        'No' => '',
                        'Yes' => 'yes'
                    ),
                    "description" => __("If the carousel should play automatically", 'jvfrmtd')
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Avatar type", 'jvfrmtd'),
                    "param_name" => "rounded",
                    "value" => array(
                        'Rounded' => 'rounded',
                        'Square' => 'square'
                    ),
                    "description" => __("Rounded or square avatar", 'jvfrmtd')
                ),
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Image Width", 'jvfrmtd'),
                    "param_name" => "item_width",
                    "value" => 150,
                    "description" => __("The size of the group image", 'jvfrmtd')
                ),
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Class", 'jvfrmtd'),
                    "param_name" => "class",
                    "value" => '',
                    "description" => __("A class to add to the element for CSS referrences.", 'jvfrmtd')
                ),

            )
        )
    );

    // Buddypress Groups Masonry
    vc_map(
        array(
            "name" => __("Groups Masonry", 'jvfrmtd'),
            "base" => "jvlynk_groups_masonry",
            "class" => "",
            "category" => __('BuddyPress'), 'jvfrmtd',
            "icon" => "bp-icon",
            "params" => array(
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Type", 'jvfrmtd'),
                    "param_name" => "type",
                    "value" => array(
                        'Active' => 'active',
                        'Newest' => 'newest',
                        'Popular' => 'popular',
                        'Alphabetical' => 'alphabetical',
                        'Most Forum Topics' => 'most-forum-topics',
                        'Most Forum Posts' => 'most-forum-posts',
                        'Random' => 'random'
                    ),
                    "description" => __("The type of groups to display.", 'jvfrmtd')
                ),
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Number of groups", 'jvfrmtd'),
                    "param_name" => "number",
                    "value" => 12,
                    "description" => __("How many groups to get.", 'jvfrmtd')
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Avatar type", 'jvfrmtd'),
                    "param_name" => "rounded",
                    "value" => array(
                        'Rounded' => 'rounded',
                        'Square' => 'square'
                    ),
                    "description" => __("Rounded or square avatar", 'jvfrmtd')
                ),
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Class", 'jvfrmtd'),
                    "param_name" => "class",
                    "value" => '',
                    "description" => __("A class to add to the element for CSS referrences.", 'jvfrmtd')
                ),

            )
        )
    );

    //Activity Stream
    vc_map(
        array(
            "name" => __("Activity Stream", 'jvfrmtd'),
            "base" => "jvlynk_activity_stream",
            "class" => "",
            "category" => __('BuddyPress', 'jvfrmtd'),
            "icon" => "jvbpd-bp-icon",
            "params" => array(
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Display", 'jvfrmtd'),
                    "param_name" => "show",
                    "value" => array(
                        'All' => false,
                        'Blogs' => 'blogs',
                        'Groups' => 'groups',
                        'Friends' => 'friends',
                        'Profile' => 'profile',
                        'Status' => 'status'
                    ),
                    "description" => __("The type of activity to show. It adds the 'object' parameter as in https://codex.buddypress.org/developer/loops-reference/the-activity-stream-loop/", 'jvfrmtd')
                ),
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Filter action", 'jvfrmtd'),
                    "param_name" => "filter_action",
                    "value" => '',
                    "description" => __("Example: activity_update<br> See action parameter from the filters section from https://codex.buddypress.org/developer/loops-reference/the-activity-stream-loop/", 'jvfrmtd')
                ),
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Number", 'jvfrmtd'),
                    "param_name" => "number",
                    "value" => '6',
                    "description" => __("How many activity streams to show", 'jvfrmtd')
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Show post update form", 'jvfrmtd'),
                    "param_name" => "post_form",
                    "value" => array(
                        'No' => 'no',
                        'Yes' => 'yes'
                    ),
                    "description" => __("Shows the form to post a new update", 'jvfrmtd')
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Bottom button", 'jvfrmtd'),
                    "param_name" => "show_button",
                    "value" => array(
                        'Yes' => 'yes',
                        'No' => 'no'
                    ),
                    "description" => __("Show a button with link to the activity page", 'jvfrmtd')
                ),
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Activity Button Label", 'jvfrmtd'),
                    "param_name" => "button_label",
                    "value" => 'View All Activity',
                    "dependency" => array(
                        "element" => "show_button",
                        "value" => "yes"
                    ),
                    "description" => __("Button text", 'jvfrmtd')
                ),
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Activity Button Link", 'jvfrmtd'),
                    "param_name" => "button_link",
                    "value" => '/activity',
                    "dependency" => array(
                        "element" => "show_button",
                        "value" => "yes"
                    ),
                    "description" => __("Put here the link to your activity page", 'jvfrmtd')
                )
            )
        )
    );

    //Activity Page
    vc_map(
        array(
            "name" => __("Activity Page", 'jvfrmtd'),
            "base" => "jvlynk_activity_page",
            "class" => "",
            "category" => __('BuddyPress', 'jvfrmtd'),
            "icon" => "jvbpd-bp-icon",
            "show_settings_on_create" => false
        )
    );


	 vc_map(
        array(
            "name" => __("Members Carousel", 'jvfrmtd'),
            "base" => "jvlynk_members_carousel",
            "class" => "",
            "category" => __('BuddyPress', 'jvfrmtd'),
            "icon" => "jvbpd-bp-icon",
            "params" => array(
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Member Type", 'jvfrmtd'),
                    "param_name" => "member_type",
                    "value" => $lynk_member_types,
                    "description" => __("The type of members to display.", 'jvfrmtd')
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Filter", 'jvfrmtd'),
                    "param_name" => "type",
                    "value" => array(
                        'Active' => 'active',
                        'Newest' => 'newest',
                        'Popular' => 'popular',
                        'Online' => 'online',
                        'Alphabetical' => 'alphabetical',
                        'Random' => 'random'
                    ),
                    "description" => __("Filter the members by.", 'jvfrmtd')
                ),
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Number of members", 'jvfrmtd'),
                    "param_name" => "number",
                    "value" => 12,
                    "description" => __("How many members to get.", 'jvfrmtd')
                ),
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Minimum Items", 'jvfrmtd'),
                    "param_name" => "min_items",
                    "value" => 1,
                    "description" => __("Minimum number of items to show on the screen", 'jvfrmtd')
                ),
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Maximum Items", 'jvfrmtd'),
                    "param_name" => "max_items",
                    "value" => 6,
                    "description" => __("Maximum number of items to show on the screen", 'jvfrmtd')
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Image Type", 'jvfrmtd'),
                    "param_name" => "image_size",
                    "value" => array(
                        'Full' => 'full',
                        'Thumbnail' => 'thumb'
                    ),
                    "description" => __("The size to get from buddypress", 'jvfrmtd')
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Avatar type", 'jvfrmtd'),
                    "param_name" => "rounded",
                    "value" => array(
                        'Rounded' => 'rounded',
                        'Square' => 'square'
                    ),
                    "description" => __("Rounded or square avatar", 'jvfrmtd')
                ),
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Image Width", 'jvfrmtd'),
                    "param_name" => "item_width",
                    "value" => 150,
                    "description" => __("The size of the member image", 'jvfrmtd')
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Auto play", 'jvfrmtd'),
                    "param_name" => "autoplay",
                    "value" => array(
                        'No' => '',
                        'Yes' => 'yes'
                    ),
                    "description" => __("If the carousel should play automatically", 'jvfrmtd')
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Online status", 'jvfrmtd'),
                    "param_name" => "online",
                    "value" => array(
                        'Show' => 'show',
                        'Hide' => 'noshow'
                    ),
                    "description" => __("Show online status", 'jvfrmtd')
                ),
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Class", 'jvfrmtd'),
                    "param_name" => "class",
                    "value" => '',
                    "description" => __("A class to add to the element for CSS referrences.", 'jvfrmtd')
                ),

            )
        )
    );



    // Buddypress Members Masonry

    vc_map(
        array(
            "name" => __("Members Masonry", 'jvfrmtd'),
            "base" => "jvlynk_members_masonry",
            "class" => "",
            "category" => __('BuddyPress', 'jvfrmtd'),
            "icon" => "jvbpd-bp-icon",
            "params" => array(
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Member Type", 'jvfrmtd'),
                    "param_name" => "member_type",
                    "value" => $lynk_member_types,
                    "description" => __("The type of members to display.", 'jvfrmtd')
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Filter", 'jvfrmtd'),
                    "param_name" => "type",
                    "value" => array(
                        'Active' => 'active',
                        'Newest' => 'newest',
                        'Popular' => 'popular',
                        'Online' => 'online',
                        'Alphabetical' => 'alphabetical',
                        'Random' => 'random'
                    ),
                    "description" => __("Filter the members by.", 'jvfrmtd')
                ),
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Number of members", 'jvfrmtd'),
                    "param_name" => "number",
                    "value" => 12,
                    "description" => __("How many members to get.", 'jvfrmtd')
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Avatar type", 'jvfrmtd'),
                    "param_name" => "rounded",
                    "value" => array(
                        'Rounded' => 'rounded',
                        'Square' => 'square'
                    ),
                    "description" => __("Rounded or square avatar", 'jvfrmtd')
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Online status", 'jvfrmtd'),
                    "param_name" => "online",
                    "value" => array(
                        'Show' => 'show',
                        'Hide' => 'noshow'
                    ),
                    "description" => __("Show online status", 'jvfrmtd')
                ),
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Class", 'jvfrmtd'),
                    "param_name" => "class",
                    "value" => '',
                    "description" => __("A class to add to the element for CSS referrences.", 'jvfrmtd')
                ),

            )
        )
    );

}
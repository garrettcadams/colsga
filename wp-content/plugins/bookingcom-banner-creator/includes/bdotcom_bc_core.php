<?php
/**
 * CORE SCRIPT
 * ----------------------------------------------------------------------------
 */

//Update plugin version on DB
/*
register_activation_hook( BDOTCOM_BC_PLUGIN_FILE, 'bdotcom_bc_install' );
function bdotcom_bc_install( ) {
//this install defaults values
$bdotcom_bc_options = array(
'plugin_ver' => BDOTCOM_BC_PLUGIN_VERSION //plugin version 
);
update_option( 'bdotcom_bc_options', $bdotcom_bc_options );
}*/

/* Localization and internazionalization */
add_action( 'plugins_loaded', 'bdotcom_bc_load_plugin_textdomain' );
function bdotcom_bc_load_plugin_textdomain( ) {
                load_plugin_textdomain( BDOTCOM_BC_TEXT_DOMAIN, false, dirname( BDOTCOM_BC_PLUGIN_FILE ) . '/languages/' );
}

/* Create custom post type */
add_action( 'init', 'bdotcom_bc_post_type' );
function bdotcom_bc_post_type( )
{
            // set dashicons class for RVM post icon
            $menu_icon = BDOTCOM_BC_DASHICON_CLASS;
            /* // fallback for menu icon in case wp vesrsion previous then 3.8 ( dashicons era )
            if ( version_compare( BDOTCOM_BC_WP_VERSION, '3.8', '<' ) ) {
                        $menu_icon = BDOTCOM_BC_IMG_PLUGIN_DIR . '/map-icon-16x16.png';
            } //version_compare(BDOTCOM_BC_WP_VERSION, '3.8', '<')*/
            
            register_post_type( 'bdotcom_bm', array(
                         'labels' => array(
                                    'name' => __( BDOTCOM_BC_PLUGIN_NAME ),
                                    'singular_name' => __( BDOTCOM_BC_PLUGIN_NAME . ' Singular Name', BDOTCOM_BC_TEXT_DOMAIN ),
                                    'add_new' => __( 'Add New Banner', BDOTCOM_BC_TEXT_DOMAIN ),
                                    'add_new_item' => __( 'Add a Responsive Banner', BDOTCOM_BC_TEXT_DOMAIN ),
                                    'edit_item' => __( 'Edit Banner', BDOTCOM_BC_TEXT_DOMAIN ),
                                    'new_item' => __( 'New Banner', BDOTCOM_BC_TEXT_DOMAIN ),
                                    'view_item' => __( 'View This Banner', BDOTCOM_BC_TEXT_DOMAIN ),
                                    'search_items' => __( 'Search Banner', BDOTCOM_BC_TEXT_DOMAIN ),
                                    'not_found' => __( 'No Banner Found', BDOTCOM_BC_TEXT_DOMAIN ),
                                    'not_found_in_trash' => __( 'No Banner Found in Trash', BDOTCOM_BC_TEXT_DOMAIN ),
                                    'parent_item_colon' => __( 'Parent Banner Colon', BDOTCOM_BC_TEXT_DOMAIN ),
                                    'menu_name' => __( 'B.com Banner', BDOTCOM_BC_TEXT_DOMAIN ) 
                        ),
                        'description' => __( '{plugin_custompost_descr}', BDOTCOM_BC_TEXT_DOMAIN ),
                        'public' => true,
                        'has_archive' => true,
                        'menu_position' => 65, //After plugin menu 
                        'menu_icon' => $menu_icon,
                        'supports' => array(
                                     'title'
                        ) 
            ) );
            // Retrieve all default options from DB
           /*$bdotcom_bc_options = bdotcom_bc_retrieve_options(); 
            $old_version = !empty( $bdotcom_bc_options[ 'ver' ] ) ? $bdotcom_bc_options[ 'ver' ] : '' ;
            // Update current plugin version or create it if do not exist
             if ( empty( $old_version ) || version_compare( BDOTCOM_BC_PLUGIN_VERSION , $old_version, '>' ) ) {
                      
                        // Alter just the version field of multidimensiona array
                        $bdotcom_bc_options['ver'] = BDOTCOM_BC_PLUGIN_VERSION ;                        
                        update_option( 'bdotcom_bc_options', $bdotcom_bc_options );
            } //!empty ( $options['ver'] ) || version_compare( BDOTCOM_BC_PLUGIN_VERSION, 1.0, '>' )*/
}

add_action( 'add_meta_boxes', 'bdotcom_bc_meta_boxes_create' );
function bdotcom_bc_meta_boxes_create( )
{
            add_meta_box( 'bdotcom_bc_meta', __( 'Settings For ' . get_the_title(), BDOTCOM_BC_TEXT_DOMAIN ), 'bdotcom_bc_mb_function', 'bdotcom_bm', 'normal', 'high' );
}

// Save data into DB
add_action( 'save_post', 'bdotcom_bc_save_meta' );
function bdotcom_bc_save_meta( $post_id ) {
                if( isset( $_POST[ 'bdotcom_bc_mbe_post_id' ] ) ) { 
                                $array_fields = bdotcom_bc_fields_array();
                                foreach ( $array_fields as $field ) {
                                               if ( $field[ 0 ] == 'bdotcom_bc_mbe_button' )  { // if we have a checkbox and is not isset means is unchecked
                                                                if ( !isset( $_POST[ $field[ 0 ] ] ) ) { $field_value = 'no'; }                                                
                                                                else { $field_value = 'yes'; }                                                
                                                } //$field[ 0 ] == 'bdotcom_bc_mbe_button' && !isset( $field[ 0 ] )
                                                
                                                elseif ( ($field[ 0 ] == 'bdotcom_bc_mbe_button_border_width' && !is_numeric(  $_POST[ $field[ 0 ] ] ) ) ) {
                                                    
                                                                                $field_value = '';
                                                                } //$field[ 0 ] == 'bdotcom_bc_mbe_button_border_width' && bdotcom_bc_check_valid_units( 'bdotcom_bc_mbe_button_border_width' )
                                                                
                                                               elseif(  $field[ 0 ] == 'bdotcom_bc_mbe_aid' &&  !is_numeric( $_POST[ $field[ 0 ] ] )  ) {
                                                                                   $field_value = '';
                                                                }
                                                               
                                                               elseif(  $field[ 0 ] == 'bdotcom_bc_mbe_copy'   ) {
                                                                                   $field_value = wp_slash( $_POST[ $field[ 0 ] ]  );//add slashes to quote.
                                                                }                                                              
                                                              
                                                                else {
                                                                                //$field_value = wp_slash( strip_tags( $_POST[ $field[ 0 ] ] ) );
                                                                                $field_value = wp_slash( strip_tags( $_POST[ $field[ 0 ] ] ) );//strips  from HTML, XML, and PHP tags.   
                                                                }
                                                                $field_value =  $field_value ;                    
                                                                
                                                                              
                                                update_post_meta( $post_id, '_' . $field[ 0 ], $field_value );
                                } //$array_fields as $field
                }
}

?>
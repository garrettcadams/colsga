<?php
// Initialize css for plugin initialization
add_action( 'init', 'bdotcom_bc_add_styles' );
function bdotcom_bc_add_styles( )
{
            wp_register_style( 'bdotcom_bc_general_css', BDOTCOM_BC_CSS_PLUGIN_DIR . '/bdotcom_bc_general.css', '', '1.1' );    
            wp_register_style( 'bdotcom_bc_admin_css', BDOTCOM_BC_CSS_PLUGIN_DIR . '/bdotcom_bc_admin.css', '', '1.1' );
            //wp_register_style( 'bdotcom_bc_public_css', BDOTCOM_BC_CSS_PLUGIN_DIR . '/bdotcom_bc_public.css', '', '1.0' );
}

// Make the style available just for plugin settings page
add_action( 'admin_enqueue_scripts', 'bdotcom_bc_add_settings_styles' );
function bdotcom_bc_add_settings_styles( $hook )
{
            if ( 'post.php' == $hook || 'post-new.php' == $hook ) {
                        wp_enqueue_style( 'wp-color-picker' ); // default WP colour picker
                        wp_enqueue_style( 'bdotcom_bc_general_css' );
                        wp_enqueue_style( 'bdotcom_bc_admin_css' );                        
            } //'post.php' == $hook || 'post-new.php' == $hook
}

// Make the style available for public pages after main theme style
add_action( 'wp_enqueue_scripts', 'bdotcom_bc_add_style', 11 );
function bdotcom_bc_add_style( )
{
            wp_enqueue_style( 'bdotcom_bc_general_css' );    
}

//Register script to WP
add_action( 'init', 'bdotcom_bc_add_scripts' );
function bdotcom_bc_add_scripts( )
{         
            wp_register_script( 'bdotcom_bc_admin_js', BDOTCOM_BC_JS_PLUGIN_DIR . '/bdotcom_bc_admin.js', array(
                         'jquery' // dependency from jquery
            ), '', true ); // true load the script in the footer          
            
            //Localize in javascript bdotcom_bc_admin_js
            wp_localize_script( 'bdotcom_bc_admin_js', 'objectL10n', array(
                        //set the path for javascript files
                        'bdotcom_bc_images_js_path' => BDOTCOM_BC_IMG_PLUGIN_DIR //path for images to be called from javascript  
            ) );
}
// Make the script available just for plugin post pages
add_action( 'admin_enqueue_scripts', 'bdotcom_bc_add_settings_scripts' );
function bdotcom_bc_add_settings_scripts( $hook )
{
            if ( 'post.php' == $hook || 'post-new.php' == $hook ) {
                        wp_enqueue_script( 'jquery' );
                        wp_enqueue_script( 'wp-color-picker' );
                        wp_enqueue_media(); //for media uploader
                        wp_enqueue_script( 'bdotcom_bc_admin_js' );
                        //wp_enqueue_script( 'bdotcom_bc_general_js' );

            } //'post.php' == $hook || 'post-new.php' == $hook
}
// Make the scripts available for public pages
/*add_action( 'wp_enqueue_scripts', 'bdotcom_bc_add_pub_scripts' );
function bdotcom_bc_add_pub_scripts( )
{
            wp_enqueue_script( 'jquery' );
}*/

?>
<?php
/**
     * Plugin Name: Booking.com Banner Creator
     * Plugin URI: http://www.booking.com/general.html?tmpl=docs/partners_affiliate_examples
     * Description: With the Booking.com Banner Creator plug-in, you can make attractive, profitable banners for your website and start earning commission. If you’re not an Affiliate Partner yet, you can still implement the plugin. To get the most out of the plugin and earn commission, you’ll need to <a href="http://www.booking.com/content/affiliates.html" target="_blank">sign up for the Booking.com Affiliate Partner Programme.</a>
     * Version: 1.4.2
     * Author: Strategic Partnerships Department at Booking.com
     * Author URI: http://www.booking.com/general.html?tmpl=docs/partners_affiliate_examples
     * Text Domain: bookingcom-banner-creator
     * Domain Path: /languages
     * License: GPLv2 or later
     */
     
     
     /* Booking.com Banner Creator is free software: you can redistribute it and/or modify
            it under the terms of the GNU General Public License as published by
            the Free Software Foundation, either version 2 of the License, or
            any later version.
             
            Booking.com Banner Creator is distributed in the hope that it will be useful,
            but WITHOUT ANY WARRANTY; without even the implied warranty of
            MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
            GNU General Public License for more details.
             
            You should have received a copy of the GNU General Public License
            along with Booking.com Banner Machine. If not, see http://www.gnu.org/licenses/gpl-2.0.html.
    */
    
    
    
/*Define constants and paths*/
define( 'BDOTCOM_BC_TEXT_DOMAIN' , 'bookingcom-banner-creator' ) ; //If changed, please change even the .po and .mo files with new name
define( 'BDOTCOM_BC_PLUGIN_NAME' , 'Booking.com Banner Creator' ) ;
define( 'BDOTCOM_BC_PLUGIN_WIDGET_DESCR' , 'Booking.com Banners' ) ;
define( 'BDOTCOM_BC_PLUGIN_VERSION' , '1.4.2' ) ;

define( 'BDOTCOM_BC_PLUGIN_FILE' , plugin_basename( __FILE__ ) ) ;    
define( 'BDOTCOM_BC_PLUGIN_DIR_PATH' , plugin_dir_path( __FILE__ ) ) ;
define( 'BDOTCOM_BC_PLUGIN_DIR_URL' , plugin_dir_url( __FILE__ ) ) ;
define( 'BDOTCOM_BC_JS_PLUGIN_DIR', BDOTCOM_BC_PLUGIN_DIR_URL.'js' ) ;
define( 'BDOTCOM_BC_CSS_PLUGIN_DIR', BDOTCOM_BC_PLUGIN_DIR_URL.'css' ) ;
define( 'BDOTCOM_BC_IMG_PLUGIN_DIR', BDOTCOM_BC_PLUGIN_DIR_URL.'images' ) ;
define( 'BDOTCOM_BC_INC_PLUGIN_DIR', BDOTCOM_BC_PLUGIN_DIR_PATH.'includes' ) ;
define( 'BDOTCOM_BC_WP_VERSION' , get_bloginfo( 'version' ) ) ;
define( 'BDOTCOM_BC_ID_CLASS_PREFIX' , 'bdotcom_bc_' ) ;
define( 'BDOTCOM_BC_DASHICON_CLASS' , 'dashicons-layout' ) ;
define( 'BDOTCOM_BC_DEFAULT_THUMBNAIL' , '_thumbnail' ) ;




//Default fallback values
define( 'BDOTCOM_BC_DEFAULT_AID' , 906594 ) ;
define( 'BDOTCOM_BC_DEFAULT_TARGET_AID' , 304142 ) ;// booking.com default aid
define( 'BDOTCOM_BC_DEFAULT_COPY_COLOUR' , '#FFF' ) ;//banner copy colour
define( 'BDOTCOM_BC_DEFAULT_LOGO_VAR' , 'light' ) ;// booking.com  logo variant
define( 'BDOTCOM_BC_DEFAULT_BUTTON' , 'yes' ) ;// call to action
define( 'BDOTCOM_BC_DEFAULT_BUTTON_COPY_COLOUR' ,  "#FFF") ;// button copy
define( 'BDOTCOM_BC_DEFAULT_COPY_BACKGROUND_COLOUR' ,  "#000") ;// button background colour
define( 'BDOTCOM_BC_DEFAULT_BUTTON_BG' , '#0896ff' ) ;// button bg color
define( 'BDOTCOM_BC_DEFAULT_BUTTON_BORDER_COLOUR' , '#FFF' ) ;// button border color
define( 'BDOTCOM_BC_DEFAULT_BUTTON_BORDER_WIDTH' , 0 ) ;// button border width
define( 'BDOTCOM_BC_DEFAULT_THEME' , 'https://r.bstatic.com/data/sp_aff/906594/bdotcom_hotel_theme_1.jpg' ) ;// banner image
define( 'BDOTCOM_BC_DEFAULT_EDIT_CSS' , '.i.e: .bdotcom_bc_mbe_banner { font-family: Verdana; } ' ) ;// default css edit example
define( 'BDOTCOM_BC_DEFAULT_LINK' , '//www.booking.com/' ) ;// banner link
define( 'BDOTCOM_BC_DEFAULT_LABEL' , 'wp-banner-widget-' ) ;// banner LABEL

include BDOTCOM_BC_INC_PLUGIN_DIR . '/bdotcom_bc_general_functions.php';
include BDOTCOM_BC_INC_PLUGIN_DIR . '/bdotcom_bc_style_and_script.php' ;
include BDOTCOM_BC_INC_PLUGIN_DIR . '/bdotcom_bc_ajax.php' ;
include BDOTCOM_BC_INC_PLUGIN_DIR . '/bdotcom_bc_core.php' ;
include BDOTCOM_BC_INC_PLUGIN_DIR . '/bdotcom_bc_forms.php' ;
include BDOTCOM_BC_INC_PLUGIN_DIR . '/bdotcom_bc_shortcode.php' ;
include BDOTCOM_BC_INC_PLUGIN_DIR . '/bdotcom_bc_widget.php' ;

?>
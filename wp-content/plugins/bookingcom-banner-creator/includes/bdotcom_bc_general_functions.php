<?php
/**
 * GENERAL FUNCTIONS
 * ----------------------------------------------------------------------------
 */
 
 //check units for input fields
 function bdotcom_bc_check_valid_units ( $unit ) {
     if( !preg_match( '/^[0-9]+\.?[0-9]*(px|%|rem|em)$/', $unit ) ) {
                    return false ;
     }  else {   
                    return true ; 
     }
 }
 
 //return array of image paths for default themes
 function bdotcom_bc_default_image_paths() {
                $bdotcom_bc_default_image_paths = array();
                $bdotcom_bc_default_image_paths['beach_theme_1'] = array("beach_theme_1", __( 'beach theme 1', BDOTCOM_BC_TEXT_DOMAIN ), "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_beach_theme_1.jpg", "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_beach_theme_1". BDOTCOM_BC_DEFAULT_THUMBNAIL .".jpg");
                $bdotcom_bc_default_image_paths['beach_theme_2'] = array("beach_theme_2", __( 'beach theme 2', BDOTCOM_BC_TEXT_DOMAIN ), "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_beach_theme_2.jpg", "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_beach_theme_2". BDOTCOM_BC_DEFAULT_THUMBNAIL .".jpg");
                $bdotcom_bc_default_image_paths['beach_theme_3'] = array("beach_theme_3", __( 'beach theme 3', BDOTCOM_BC_TEXT_DOMAIN ), "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_beach_theme_3.jpg", "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_beach_theme_3". BDOTCOM_BC_DEFAULT_THUMBNAIL .".jpg");
                $bdotcom_bc_default_image_paths['city_theme_1'] = array("city_theme_1", __( 'city theme 1', BDOTCOM_BC_TEXT_DOMAIN ), "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_city_theme_1.jpg", "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_city_theme_1". BDOTCOM_BC_DEFAULT_THUMBNAIL .".jpg");
                $bdotcom_bc_default_image_paths['city_theme_2'] = array("city_theme_2", __( 'city theme 2', BDOTCOM_BC_TEXT_DOMAIN ), "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_city_theme_2.jpg", "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_city_theme_2". BDOTCOM_BC_DEFAULT_THUMBNAIL .".jpg");
                $bdotcom_bc_default_image_paths['city_theme_3'] = array("city_theme_3", __( 'city theme 3', BDOTCOM_BC_TEXT_DOMAIN ), "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_city_theme_3.jpg", "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_city_theme_3". BDOTCOM_BC_DEFAULT_THUMBNAIL .".jpg");
                $bdotcom_bc_default_image_paths['city_theme_4'] = array("city_theme_4", __( 'city theme 4', BDOTCOM_BC_TEXT_DOMAIN ), "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_city_theme_4.jpg", "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_city_theme_4". BDOTCOM_BC_DEFAULT_THUMBNAIL .".jpg");
                $bdotcom_bc_default_image_paths['hotel_theme_1'] = array("hotel_theme_1", __( 'hotel theme 1', BDOTCOM_BC_TEXT_DOMAIN ), "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_hotel_theme_1.jpg", "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_hotel_theme_1". BDOTCOM_BC_DEFAULT_THUMBNAIL .".jpg");
                $bdotcom_bc_default_image_paths['hotel_theme_2'] = array("hotel_theme_2", __( 'hotel theme 2', BDOTCOM_BC_TEXT_DOMAIN ), "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_hotel_theme_2.jpg", "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_hotel_theme_2". BDOTCOM_BC_DEFAULT_THUMBNAIL .".jpg");
                $bdotcom_bc_default_image_paths['hotel_theme_3'] = array("hotel_theme_3", __( 'hotel theme 3', BDOTCOM_BC_TEXT_DOMAIN ), "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_hotel_theme_3.jpg", "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_hotel_theme_3". BDOTCOM_BC_DEFAULT_THUMBNAIL .".jpg");
                $bdotcom_bc_default_image_paths['inspirational_theme_1'] = array("inspirational_theme_1", __( 'inspirational theme 1', BDOTCOM_BC_TEXT_DOMAIN ), "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_inspirational_theme_1.jpg", "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_inspirational_theme_1". BDOTCOM_BC_DEFAULT_THUMBNAIL .".jpg");
                $bdotcom_bc_default_image_paths['inspirational_theme_2'] = array("inspirational_theme_2", __( 'inspirational theme 2', BDOTCOM_BC_TEXT_DOMAIN ), "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_inspirational_theme_2.jpg", "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_inspirational_theme_2". BDOTCOM_BC_DEFAULT_THUMBNAIL .".jpg");
                $bdotcom_bc_default_image_paths['inspirational_theme_3'] = array("inspirational_theme_3", __( 'inspirational theme 3', BDOTCOM_BC_TEXT_DOMAIN ), "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_inspirational_theme_3.jpg", "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_inspirational_theme_3". BDOTCOM_BC_DEFAULT_THUMBNAIL .".jpg");
                $bdotcom_bc_default_image_paths['inspirational_theme_4'] = array("inspirational_theme_4", __( 'inspirational theme 1', BDOTCOM_BC_TEXT_DOMAIN ), "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_inspirational_theme_4.jpg", "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_inspirational_theme_4". BDOTCOM_BC_DEFAULT_THUMBNAIL .".jpg");
                $bdotcom_bc_default_image_paths['mountain_theme_1'] = array("mountain_theme_1", __( 'mountain theme 1', BDOTCOM_BC_TEXT_DOMAIN ), "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_mountain_theme_1.jpg", "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_mountain_theme_1". BDOTCOM_BC_DEFAULT_THUMBNAIL .".jpg");
                $bdotcom_bc_default_image_paths['mountain_theme_2'] = array("mountain_theme_2", __( 'mountain theme 2', BDOTCOM_BC_TEXT_DOMAIN ), "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_mountain_theme_2.jpg", "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_mountain_theme_2". BDOTCOM_BC_DEFAULT_THUMBNAIL .".jpg");
                $bdotcom_bc_default_image_paths['mountain_theme_3'] = array("mountain_theme_3", __( 'mountain theme 3', BDOTCOM_BC_TEXT_DOMAIN ), "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_mountain_theme_3.jpg", "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_mountain_theme_3". BDOTCOM_BC_DEFAULT_THUMBNAIL .".jpg");
                $bdotcom_bc_default_image_paths['mountain_theme_4'] = array("mountain_theme_4", __( 'mountain theme 4', BDOTCOM_BC_TEXT_DOMAIN ), "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_mountain_theme_4.jpg", "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_mountain_theme_4". BDOTCOM_BC_DEFAULT_THUMBNAIL .".jpg");
                $bdotcom_bc_default_image_paths['nature_theme_1'] = array("nature_theme_1", __( 'nature theme 1', BDOTCOM_BC_TEXT_DOMAIN ), "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_nature_theme_1.jpg", "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_nature_theme_1". BDOTCOM_BC_DEFAULT_THUMBNAIL .".jpg");
                $bdotcom_bc_default_image_paths['nature_theme_2'] = array("nature_theme_2", __( 'nature theme 2', BDOTCOM_BC_TEXT_DOMAIN ), "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_nature_theme_2.jpg", "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_nature_theme_2". BDOTCOM_BC_DEFAULT_THUMBNAIL .".jpg");
                $bdotcom_bc_default_image_paths['nature_theme_3'] = array("nature_theme_3", __( 'nature theme 3', BDOTCOM_BC_TEXT_DOMAIN ), "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_nature_theme_3.jpg", "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_nature_theme_3". BDOTCOM_BC_DEFAULT_THUMBNAIL .".jpg");
                
                
                return $bdotcom_bc_default_image_paths;
}

 //get all options saved into DB
 function bdotcom_bc_retrieve_all_options( ) {
                $bdotcom_bc_retrieve_all_options = get_option( 'bdotcom_bc_retrieve_all_options' );
                return $bdotcom_bc_retrieve_all_options;
}
 
 //Manage dynamic values in different placement i.e.: in Ajax calls
 function bdotcom_bc_variables( $filename,  $bdotcom_bc_post_id) {
                //Initialize output
                $output = '';               
                
                // use isset just for checkbox field with  0/1 values: if no value sent this will lead to warnings  of  no defined index for the variable
                if( $filename == 'bdotcom_bc_ajax.php' ) {// In case the include file is inside the file managing ajax calls 
                            $bdotcom_bc_mbe_post_id_VALUE =  $_REQUEST[ 'bdotcom_bc_mbe_post_id' ]  ;
                            $bdotcom_bc_mbe_aid_VALUE =  $_REQUEST[ 'bdotcom_bc_mbe_aid' ] ;
                            //$bdotcom_bc_mbe_logo_var_VALUE = $_REQUEST[ 'bdotcom_bc_mbe_logo_var' ]  ;
                            $bdotcom_bc_mbe_button_VALUE = isset( $_REQUEST[ 'bdotcom_bc_mbe_button' ] ) ;
                            $bdotcom_bc_mbe_button_copy_VALUE =  $_REQUEST[ 'bdotcom_bc_mbe_button_copy' ]  ;
                            $bdotcom_bc_mbe_button_copy_colour_VALUE =  $_REQUEST[ 'bdotcom_bc_mbe_button_copy_colour' ] ;
                            $bdotcom_bc_mbe_button_bg_VALUE = $_REQUEST[ 'bdotcom_bc_mbe_button_bg' ] ;
                            $bdotcom_bc_mbe_button_border_colour_VALUE =   $_REQUEST[ 'bdotcom_bc_mbe_button_border_colour' ] ;
                            $bdotcom_bc_mbe_button_border_width_VALUE =  $_REQUEST[ 'bdotcom_bc_mbe_button_border_width' ] ;
                            $bdotcom_bc_mbe_themes_VALUE = $_REQUEST[ 'bdotcom_bc_mbe_themes' ] ;
                            $bdotcom_bc_mbe_img_path_VALUE = $_REQUEST[ 'bdotcom_bc_mbe_img_path' ] ;
                            $bdotcom_bc_mbe_copy_VALUE = $_REQUEST[ 'bdotcom_bc_mbe_copy' ] ; 
                            $bdotcom_bc_copy_wrapper_colour_VALUE = $_REQUEST[ 'bdotcom_bc_copy_wrapper_colour' ] ;
                            $bdotcom_bc_copy_colour_VALUE = $_REQUEST[ 'bdotcom_bc_mbe_copy_colour' ] ;
                            $bdotcom_bc_mbe_edit_css_VALUE = $_REQUEST[ 'bdotcom_bc_mbe_edit_css' ] ;
                            $bdotcom_bc_mbe_banner_link_VALUE = $_REQUEST[ 'bdotcom_bc_mbe_banner_link' ] ;
                            $bdotcom_bc_mbe_label_VALUE = $_REQUEST[ 'bdotcom_bc_mbe_label' ] ;
                            
                            
                }  else { // case banner is created with parameters saved into DB                            

                                            $bdotcom_bc_mbe_aid_VALUE = get_post_meta( $bdotcom_bc_post_id, '_bdotcom_bc_mbe_aid', true );
                                            $bdotcom_bc_mbe_logo_var_VALUE = get_post_meta( $bdotcom_bc_post_id, '_bdotcom_bc_mbe_logo_var', true );
                                            $bdotcom_bc_mbe_button_VALUE = get_post_meta( $bdotcom_bc_post_id, '_bdotcom_bc_mbe_button', true );
                                            $bdotcom_bc_mbe_button_copy_VALUE = get_post_meta( $bdotcom_bc_post_id, '_bdotcom_bc_mbe_button_copy', true );
                                            $bdotcom_bc_mbe_button_copy_colour_VALUE = get_post_meta( $bdotcom_bc_post_id, '_bdotcom_bc_mbe_button_copy_colour', true );
                                            $bdotcom_bc_mbe_button_bg_VALUE = get_post_meta( $bdotcom_bc_post_id, '_bdotcom_bc_mbe_button_bg', true );
                                            $bdotcom_bc_mbe_button_border_colour_VALUE  = get_post_meta( $bdotcom_bc_post_id, '_bdotcom_bc_mbe_button_border_colour', true );
                                            $bdotcom_bc_mbe_button_border_width_VALUE = get_post_meta( $bdotcom_bc_post_id, '_bdotcom_bc_mbe_button_border_width', true );
                                            $bdotcom_bc_mbe_themes_VALUE = get_post_meta( $bdotcom_bc_post_id, '_bdotcom_bc_mbe_themes', true );
                                            $bdotcom_bc_mbe_img_path_VALUE = get_post_meta( $bdotcom_bc_post_id, '_bdotcom_bc_mbe_img_path', true );
                                            $bdotcom_bc_mbe_copy_VALUE = get_post_meta( $bdotcom_bc_post_id, '_bdotcom_bc_mbe_copy', true );
                                            $bdotcom_bc_copy_colour_VALUE = get_post_meta( $bdotcom_bc_post_id, '_bdotcom_bc_mbe_copy_colour', true );
                                            //$bdotcom_bc_copy_wrapper_bg_color_VALUE = get_post_meta( $bdotcom_bc_post_id, 'bdotcom_bc_copy_wrapper_bg_color', true );
                                            $bdotcom_bc_mbe_edit_css_VALUE = get_post_meta( $bdotcom_bc_post_id, '_bdotcom_bc_mbe_edit_css', true );
                                            $bdotcom_bc_mbe_banner_link_VALUE = get_post_meta( $bdotcom_bc_post_id, '_bdotcom_bc_mbe_banner_link', true );
                                            $bdotcom_bc_mbe_label_VALUE = get_post_meta( $bdotcom_bc_post_id, '_bdotcom_bc_mbe_label', true );
                                                                      
                                            $bdotcom_bc_mbe_post_id_VALUE =  $bdotcom_bc_post_id  ;
                            
                }                
              
                //Initialize variables
                $bdotcom_bc_mbe_post_id = $bdotcom_bc_mbe_post_id_VALUE ;
                $bdotcom_bc_mbe_aid =  ( !empty( $bdotcom_bc_mbe_aid_VALUE ) && is_numeric( $bdotcom_bc_mbe_aid_VALUE  ) ) ?  $bdotcom_bc_mbe_aid_VALUE : BDOTCOM_BC_DEFAULT_AID ;
                $bdotcom_bc_mbe_logo =  '/booking_logotype_white_300x50.png';
                $bdotcom_bc_mbe_button = ( !empty( $bdotcom_bc_mbe_button_VALUE  ) && ( $bdotcom_bc_mbe_button_VALUE == 'yes'  || $bdotcom_bc_mbe_button_VALUE == 1 || $bdotcom_bc_mbe_button_VALUE == 'on' ) ) ? true : false ;
                $bdotcom_bc_mbe_button_copy =  !empty( $bdotcom_bc_mbe_button_copy_VALUE ) ? $bdotcom_bc_mbe_button_copy_VALUE : __( "Book now" , BDOTCOM_BC_TEXT_DOMAIN );
                $bdotcom_bc_mbe_button_copy_colour =  !empty( $bdotcom_bc_mbe_button_copy_colour_VALUE ) ? $bdotcom_bc_mbe_button_copy_colour_VALUE : BDOTCOM_BC_DEFAULT_BUTTON_COPY_COLOUR;
                $bdotcom_bc_copy_wrapper_bg_color =  !empty( $bdotcom_bc_copy_wrapper_bg_color_VALUE ) ? $bdotcom_bc_copy_wrapper_bg_color_VALUE : BDOTCOM_BC_DEFAULT_COPY_BACKGROUND_COLOUR;
                $bdotcom_bc_mbe_button_bg = !empty( $bdotcom_bc_mbe_button_bg_VALUE ) ? $bdotcom_bc_mbe_button_bg_VALUE : BDOTCOM_BC_DEFAULT_BUTTON_BG ;
                $bdotcom_bc_mbe_button_border_colour = !empty( $bdotcom_bc_mbe_button_border_colour_VALUE ) ? $bdotcom_bc_mbe_button_border_colour_VALUE : BDOTCOM_BC_DEFAULT_BUTTON_BORDER_COLOUR ;
                $bdotcom_bc_mbe_button_border_width = ( !empty( $bdotcom_bc_mbe_button_border_width_VALUE ) && is_numeric( $bdotcom_bc_mbe_button_border_width_VALUE ) ) ? $bdotcom_bc_mbe_button_border_width_VALUE : BDOTCOM_BC_DEFAULT_BUTTON_BORDER_WIDTH ;
                $bdotcom_bc_mbe_themes = !empty( $bdotcom_bc_mbe_themes_VALUE ) ?  $bdotcom_bc_mbe_themes_VALUE : BDOTCOM_BC_DEFAULT_THEME ;   
                $bdotcom_bc_mbe_img_path =  !empty(  $bdotcom_bc_mbe_img_path_VALUE  ) ? $bdotcom_bc_mbe_img_path_VALUE : '' ;
                $bdotcom_bc_mbe_copy =  !empty(  $bdotcom_bc_mbe_copy_VALUE  ) ? $bdotcom_bc_mbe_copy_VALUE : '<h1>' . __( "Search hotels and more..." , BDOTCOM_BC_TEXT_DOMAIN ) . '</h1>' ;
                $bdotcom_bc_copy_colour =  !empty(  $bdotcom_bc_copy_colour_VALUE ) ? $bdotcom_bc_copy_colour_VALUE : BDOTCOM_BC_DEFAULT_COPY_COLOUR ;
                $bdotcom_bc_mbe_edit_css =  !empty(  $bdotcom_bc_mbe_edit_css_VALUE ) ? $bdotcom_bc_mbe_edit_css_VALUE : '' ;
                $bdotcom_bc_mbe_aff_aid = BDOTCOM_BC_DEFAULT_LINK . '?aid=' . $bdotcom_bc_mbe_aid ;
                $bdotcom_bc_mbe_banner_link =  !empty(  $bdotcom_bc_mbe_banner_link_VALUE ) ? $bdotcom_bc_mbe_banner_link_VALUE . '?aid=' . $bdotcom_bc_mbe_aid  : $bdotcom_bc_mbe_aff_aid ;    
               $bdotcom_bc_fallback_label = BDOTCOM_BC_DEFAULT_LABEL . 'generic'; // Create default label structure if no custom label created by user
                $bdotcom_bc_mbe_label  = !empty( $bdotcom_bc_mbe_label_VALUE ) ?  BDOTCOM_BC_DEFAULT_LABEL . preg_replace( '/\s+/',  '-' , $bdotcom_bc_mbe_label_VALUE )  : $bdotcom_bc_fallback_label ;
                //Create the banner                                                                              

               // Case we have custom image from the partner and the image has been selected from Media Library
               if ( !empty( $bdotcom_bc_mbe_img_path ) && $bdotcom_bc_mbe_themes == 'custom_theme' ) {
                    
                                $bdotcom_bc_banner_image = $bdotcom_bc_mbe_img_path ;
                }
               
                // Case user chooses a default theme
               elseif ( $bdotcom_bc_mbe_themes != BDOTCOM_BC_DEFAULT_THEME && $bdotcom_bc_mbe_themes != 'custom_theme' ) {
                                $bdotcom_bc_default_image_paths_array = bdotcom_bc_default_image_paths();
                                foreach( $bdotcom_bc_default_image_paths_array as $bdotcom_bc_default_image_item ) {
                                                if( $bdotcom_bc_mbe_themes == $bdotcom_bc_default_image_item[0] ) {
                                                            $bdotcom_bc_banner_image = $bdotcom_bc_default_image_item[2] ;
                                                }
                                }
                                                            
                                
                }
                
                else {// default image when no other image is chosen
                                $bdotcom_bc_banner_image = BDOTCOM_BC_DEFAULT_THEME ;
                }

                /************ START :  BANNER STYLE ****************/               
                           
                $output .= '<style>';
                $output .= '#bdotcom_bc_mbe_banner_' .  $bdotcom_bc_mbe_post_id . ' { background-image: url("' .  $bdotcom_bc_banner_image . '");}';
                $output .= '#bdotcom_bc_mbe_banner_' .  $bdotcom_bc_mbe_post_id . ' .bdotcom_bc_copy_wrapper .bdotcom_bc_copy,
                 #bdotcom_bc_mbe_banner_' .  $bdotcom_bc_mbe_post_id . ' .bdotcom_bc_copy_wrapper .bdotcom_bc_copy h1,
                 #bdotcom_bc_mbe_banner_' .  $bdotcom_bc_mbe_post_id . ' .bdotcom_bc_copy_wrapper .bdotcom_bc_copy h2,
                 #bdotcom_bc_mbe_banner_' .  $bdotcom_bc_mbe_post_id . ' .bdotcom_bc_copy_wrapper .bdotcom_bc_copy h3,
                 #bdotcom_bc_mbe_banner_' .  $bdotcom_bc_mbe_post_id . ' .bdotcom_bc_copy_wrapper .bdotcom_bc_copy h4,
                 #bdotcom_bc_mbe_banner_' .  $bdotcom_bc_mbe_post_id . ' .bdotcom_bc_copy_wrapper .bdotcom_bc_copy h5,
                 #bdotcom_bc_mbe_banner_' .  $bdotcom_bc_mbe_post_id . ' .bdotcom_bc_copy_wrapper .bdotcom_bc_copy h6,
                 #bdotcom_bc_mbe_banner_' .  $bdotcom_bc_mbe_post_id . ' .bdotcom_bc_copy_wrapper .bdotcom_bc_copy p,
                 #bdotcom_bc_mbe_banner_' .  $bdotcom_bc_mbe_post_id . ' .bdotcom_bc_copy_wrapper .bdotcom_bc_copy div { color: ' .  $bdotcom_bc_copy_colour . ';}';
                                  
 
                if( $bdotcom_bc_mbe_button ) {
                            $output .= '#bdotcom_bc_mbe_banner_' .  $bdotcom_bc_mbe_post_id . ' .bdotcom_bc_mbe_button {' ; 
                            if( !empty( $bdotcom_bc_mbe_button_border_width ) && is_numeric( $bdotcom_bc_mbe_button_border_width ) ) {
                                            $output .= 'border:' . $bdotcom_bc_mbe_button_border_width . 'px ' . $bdotcom_bc_mbe_button_border_colour . ' solid;' ;                      
                            }//   if( !empty( $bdotcom_bc_mbe_button_border_width ) && bdotcom_bc_check_valid_units( $bdotcom_bc_mbe_button_border_width ) )
                         $output .= 'color:' . $bdotcom_bc_mbe_button_copy_colour . ' ;' ;
                $output .= 'background:' . $bdotcom_bc_mbe_button_bg . ' ; }' ;               
                }// if( $bdotcom_bc_mbe_button )
                $output .= $bdotcom_bc_mbe_edit_css ;// custom css to refine the banner if needed
                $output .= '</style>';
                /************ END : BANNER STYLE ****************/
                
                // Create banner link
                
                $bdotcom_bc_mbe_banner_link = $bdotcom_bc_mbe_banner_link . ';label=' . $bdotcom_bc_mbe_label . '-' . $bdotcom_bc_mbe_aid ;
                
                $output .= '<div id="bdotcom_bc_mbe_banner_' .  $bdotcom_bc_mbe_post_id . '" class="bdotcom_bc_mbe_banner" role="banner" data-ver="' . BDOTCOM_BC_PLUGIN_VERSION . '">';
                $output .= '<a href="' . $bdotcom_bc_mbe_banner_link . '" target="_blank" class="bdotcom_bc_banner_link" rel="nofollow"><img class="bdotcom_bc_spacer" src="' . BDOTCOM_BC_IMG_PLUGIN_DIR . '/spacer.gif" /></a>';//spacer for linking purpose
                //Adjust html in case some tags are non properly formatted
                $output .= '<div class="bdotcom_bc_copy_wrapper"><div class="bdotcom_bc_copy">' . force_balance_tags( wp_unslash( $bdotcom_bc_mbe_copy ) ) . '</div>' ;
                $output .= '<img class="bdotcom_bc_logo" src="' . BDOTCOM_BC_IMG_PLUGIN_DIR . $bdotcom_bc_mbe_logo .  '" />';//logo                                                     
                $output .= '</div>';//.bdotcom_bc_copy_wrapper
                if( $bdotcom_bc_mbe_button ) {
                                $output .= '<div class="bdotcom_bc_mbe_button">' . $bdotcom_bc_mbe_button_copy . '</div>';//.bdotcom_bc_mbe_button
                }                           
                $output .= '</div>';//.bdotcom_bc_mbe_banner
                return $output;

}
 
?>
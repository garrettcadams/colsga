<?php
/**
 * CORE SCRIPT
 * ----------------------------------------------------------------------------
 */
  
// Fields input arrays 
function bdotcom_bc_fields_array()
{
            $fields = array( );
            /*0  'field name', '
             *  1 input type',  
             * 2 'field label', 
             * 3 'field bonus expl.', '
             *  4 input maxlenght', 
             *  5 'input size', 
             *  6 'required', 
             * 7 'which section belongs to?', '
             * 8 'class' 
             * 9 'default value' 
             * */  
             
            $fields[ 'bdotcom_bc_mbe_aid' ] = array(
                         'bdotcom_bc_mbe_aid',
                        'text',
                        __( 'Your affiliate ID', BDOTCOM_BC_TEXT_DOMAIN ),
                        __( 'Your affiliate ID is a unique number that allows Booking.com to track commission. If you are not an affiliate yet, <a href="http://ww.booking.com/affiliate-program/index.html" target="_blank">check our affiliate programme</a> and get an affiliate ID. It\'s easy and fast. Start earning money, <a href="https://secure.booking.com/affiliate-program/register.html" target="_blank">sign up now!</a>', BDOTCOM_BC_TEXT_DOMAIN ),
                        10,
                        10,
                        0,
                        'main',
                        '',
                         BDOTCOM_BC_DEFAULT_AID
            ); 
            /*$fields[ 'bdotcom_bc_mbe_logo_var' ] = array(
                         'bdotcom_bc_mbe_logo_var',
                        'radio',
                        __( 'Logo Variation', BDOTCOM_BC_TEXT_DOMAIN ),
                        '',
                        '',
                        '',
                        1,
                        'main',
                        '',
                         BDOTCOM_BC_DEFAULT_LOGO_VAR
            );*/
            $fields[ 'bdotcom_bc_mbe_button' ] = array(
                         'bdotcom_bc_mbe_button',
                        'checkbox',
                        __( 'Need For a Button?', BDOTCOM_BC_TEXT_DOMAIN ),
                        '',
                        '',
                        '',
                        1,
                        'main',
                        '',
                        BDOTCOM_BC_DEFAULT_BUTTON
            );
            $fields[ 'bdotcom_bc_mbe_button_copy' ] = array(
                         'bdotcom_bc_mbe_button_copy',
                        'text',
                        __( 'Button Copy', BDOTCOM_BC_TEXT_DOMAIN ),
                        '',
                        '',
                        '',
                        1,
                        'main',
                        '',
                        __( "Book now" , BDOTCOM_BC_TEXT_DOMAIN )
            );
            $fields[ 'bdotcom_bc_mbe_button_copy_colour' ] = array(
                         'bdotcom_bc_mbe_button_copy_colour',
                        'text',
                        __( 'Button Copy Colour', BDOTCOM_BC_TEXT_DOMAIN ),
                        '',
                        '',
                        '',
                        1,
                        'main',
                        'bdotcom_bc_wp_color_picker',
                        BDOTCOM_BC_DEFAULT_BUTTON_COPY_COLOUR
            );
            $fields[ 'bdotcom_bc_mbe_button_bg' ] = array(
                         'bdotcom_bc_mbe_button_bg',
                        'text',
                        __( 'Button Background', BDOTCOM_BC_TEXT_DOMAIN ),
                        '',
                        '',
                        '',
                        1,
                        'main',
                        'bdotcom_bc_wp_color_picker',
                        BDOTCOM_BC_DEFAULT_BUTTON_BG
            );
            $fields[ 'bdotcom_bc_mbe_button_border_colour' ] = array(
                         'bdotcom_bc_mbe_button_border_colour',
                        'text',
                        __( 'Button Border Colour', BDOTCOM_BC_TEXT_DOMAIN ),
                        '',
                        '',
                        '',
                        1,
                        'main',
                        'bdotcom_bc_wp_color_picker',
                        BDOTCOM_BC_DEFAULT_BUTTON_BORDER_COLOUR
            );   
            $fields[ 'bdotcom_bc_mbe_button_border_width' ] = array(
                         'bdotcom_bc_mbe_button_border_width',
                        'text',
                        __( 'Button Border Width', BDOTCOM_BC_TEXT_DOMAIN ),
                        //__('( px, %, rem, em accepted )', BDOTCOM_BC_TEXT_DOMAIN),
                        'px',
                        '',
                        3,
                        1,
                        'main',
                        '',
                        BDOTCOM_BC_DEFAULT_BUTTON_BORDER_WIDTH
            );
            $fields[ 'bdotcom_bc_mbe_themes' ] = array(
                         'bdotcom_bc_mbe_themes',
                        'hidden',
                        __( 'Theme', BDOTCOM_BC_TEXT_DOMAIN ),
                        '',
                        '',
                        "",
                        1,
                        'main',
                        '',
                        BDOTCOM_BC_DEFAULT_THEME
            );
            $fields[ 'bdotcom_bc_mbe_img_path' ] = array(
                         'bdotcom_bc_mbe_img_path',
                        'text',
                        __( 'Banner Image ( 1920px wide suggested )', BDOTCOM_BC_TEXT_DOMAIN ),
                        __( 'Choose Your Image', BDOTCOM_BC_TEXT_DOMAIN ),
                        '',
                        '',
                        1,
                        'main',
                        'bdotcom_bc_mbe_hide_field',
                        ''
            );
            $fields[ 'bdotcom_bc_mbe_copy' ] = array(
                         'bdotcom_bc_mbe_copy',
                        'textarea',
                        __( 'Banner Copy', BDOTCOM_BC_TEXT_DOMAIN ),
                        '',
                        '',
                        "",
                        1,
                        'main',
                        'bdotcom_bc_textarea',
                        '<h1>' . __( "Search hotels and more..." , BDOTCOM_BC_TEXT_DOMAIN ) . '</h1>'
            );
            $fields[ 'bdotcom_bc_mbe_copy_colour' ] = array(
                         'bdotcom_bc_mbe_copy_colour',
                        'text',
                        __( 'Copy Colour', BDOTCOM_BC_TEXT_DOMAIN ),
                        '',
                        '',
                        '',
                        1,
                        'main',
                        'bdotcom_bc_wp_color_picker',
                        BDOTCOM_BC_DEFAULT_BUTTON_COPY_COLOUR
            );            
            /*$fields[ 'bdotcom_bc_mbe_copy_bg_color' ] = array(
                         'bdotcom_bc_mbe_copy_bg_color',
                        'text',
                        __( 'Copy Background Colour', BDOTCOM_BC_TEXT_DOMAIN ),
                        '',
                        '',
                        '',
                        1,
                        'main',
                        'bdotcom_bc_wp_color_picker',
                        BDOTCOM_BC_DEFAULT_COPY_BACKGROUND_COLOUR
            );    */      
            $fields[ 'bdotcom_bc_mbe_edit_css' ] = array(
                         'bdotcom_bc_mbe_edit_css',
                        'textarea',
                        __( 'Add Your Style', BDOTCOM_BC_TEXT_DOMAIN ),
                        __( 'Use this area to add your CSS to the banner. In the example, the font is set to Verdana - but you\'re free to change it as you wish.', BDOTCOM_BC_TEXT_DOMAIN ),
                        '',
                        "",
                        1,
                        'main',
                        'bdotcom_bc_textarea',
                        BDOTCOM_BC_DEFAULT_EDIT_CSS
            );   
            $fields[ 'bdotcom_bc_mbe_banner_link' ] = array(
                         'bdotcom_bc_mbe_banner_link',
                        'text',
                        __( 'Banner Link', BDOTCOM_BC_TEXT_DOMAIN ),
                        __( 'Leave blank to link to your landing page on Booking.com. Your affiliate ID will be automatically added to the link - make sure you add it in the &ldquo;affiliate ID&rdquo; field at the top of this page.', BDOTCOM_BC_TEXT_DOMAIN ),
                        "",
                        "",
                        1,
                        'main',
                        '',
                        __( 'i.e.: http://www.booking.com/city/nl/amsterdam.en-gb.html', BDOTCOM_BC_TEXT_DOMAIN )
            );
            $fields[ 'bdotcom_bc_mbe_label' ] = array(
                         'bdotcom_bc_mbe_label',
                        'text',
                        __( 'Banner label', BDOTCOM_BC_TEXT_DOMAIN ),
                         __( 'Customise your label for reservation tracking. When blank, the label will be set as the page title by default.', BDOTCOM_BC_TEXT_DOMAIN ),
                        '',
                        '',
                        1,
                        'main',
                        '',
                        'my-page-to-track'
            );
                   
            return $fields;
}

function bdotcom_bc_mb_function( $post )
{
            
            $output = ''; //initialize output
            // Get form fields
            $array_fields = bdotcom_bc_fields_array();
            
            //$bdotcom_bc_{plugin_field} = get_post_meta( $post->ID, '_bdotcom_bc_mbe_{plugin_field}', true );
            
            //Get current screen page
            //$bdotcom_bc_screen = get_current_screen();
            
            /*if ( $bdotcom_bc_screen->action == 'add' ) { //if new post or editing an existing one without any value in DB for active tab
                        $bdotcom_bc_tab_active_default = 'bdotcom_bc_main_settings';
            } //$screen->action == 'add' || empty($bdotcom_bc_tab_active)*/
            
            /*$output = '$bdotcom_bc_screen->id : ' . $bdotcom_bc_screen->id .'<br>' ;
            $output .= '$bdotcom_bc_screen->action : ' . $bdotcom_bc_screen->action .'<br>' ;
            $output .= '$bdotcom_bc_screen->base : ' . $bdotcom_bc_screen->base .'<br>' ;*/
            
            $output .= '<div id="bdotcom_bc_main_settings">';
            $display_field = '';
            $bdotcom_bc_mbe_button = get_post_meta( $post->ID, '_bdotcom_bc_mbe_button', true ); //to display or not the button preferencies
            $bdotcom_bc_mbe_themes = get_post_meta( $post->ID, '_bdotcom_bc_mbe_themes', true ); //to display or not the image loader
            $bdotcom_bc_mbe_img_path = get_post_meta( $post->ID, '_bdotcom_bc_mbe_img_path', true ); //to display or not the image loader
            //$output .= '$bdotcom_bc_mbe_themes :' . $bdotcom_bc_mbe_themes . '<br>';
            
            foreach ( $array_fields as $field ) {
            $field_value  = get_post_meta( $post->ID, '_' . $field[ 0 ], true );
             //$output .= '$field_value :' . $field_value . '<br>';
                        if( empty( $field_value ) ) { $field_value = ''; }
                        if( empty( $field_value ) && ( $field[ 0 ] != 'bdotcom_bc_mbe_aid'  && $field[ 0 ] != 'bdotcom_bc_mbe_banner_link' 
                        && $field[ 0 ] != 'bdotcom_bc_mbe_copy' && $field[ 0 ] != 'bdotcom_bc_mbe_edit_css' && $field[ 0 ] != 'bdotcom_bc_mbe_label' ) ) { $field_value = $field[ 9 ] ; }// default values   ))
                        if( !empty( $field[ 4 ] ) ) { $bdotcom_bc_maxlength = 'maxlength="' . $field[ 4 ] . '"' ; } else { $bdotcom_bc_maxlength = '' ; }
                        if( !empty( $field[ 5 ] ) ) { $bdotcom_bc_mbe_size = 'size="' . $field[ 5 ] . '"' ; } else { $bdotcom_bc_mbe_size = '' ; }
                        if( !empty( $field[ 9 ] ) ) { $bdotcom_bc_mbe_placeholder = 'placeholder="' . esc_attr( $field[ 9 ] ) . '"' ; } else { $bdotcom_bc_mbe_placeholder = '' ; }
                        if ( $field[ 7 ] == 'main' ) {
                                    // echo the fields                                        
                                    if ( $field[ 1 ] == 'textarea' ) {
                                                    if( $field[ 0 ] == 'bdotcom_bc_mbe_copy' ) {
                                                                    $output .= '<p class="' . $field[ 0 ] . '">' ;         
                                                                    $output .= '<label for="' . $field[ 0 ] . '">' . $field[ 2 ] . '</label>';
                                                                    $output .= '<textarea ' . $bdotcom_bc_mbe_placeholder . '  name="' . $field[ 0 ] . '" id="' . $field[ 0 ] . '" class="' .  $field[ 8 ] . '" cols="20" rows="5">' ;
                                                                    //$output .= esc_attr( $field_value ) ;
                                                                    $output .=  force_balance_tags( wp_unslash( $field_value ) ) ;
                                                                    $output .= '</textarea>&nbsp;<span class="bdotcom_bc_mbe_bonus_text">' . $field[ 3 ]  . '</span>';  
                                                                    $output .= '</p>'; 
                                                    }
                                                    if( $field[ 0 ] == 'bdotcom_bc_mbe_edit_css' ) {
                                                                    $output .= '<p class="' . $field[ 0 ] . '">' ;         
                                                                    $output .= '<label for="' . $field[ 0 ] . '">' . $field[ 2 ] . '</label>';
                                                                    $output .= '<textarea ' . $bdotcom_bc_mbe_placeholder . '  name="' . $field[ 0 ] . '" id="' . $field[ 0 ] . '" class="' .  $field[ 8 ] . '"cols="20" rows="5">' ;
                                                                    //$output .= esc_attr( $field_value ) ;
                                                                    $output .= $field_value ;
                                                                    $output .= '</textarea>&nbsp;<span class="bdotcom_bc_mbe_bonus_text">' . $field[ 3 ] . '</span>';  
                                                                    $output .= '</p>'; 
                                                    }                                       
                                    } // if( $field[ 1 ] == 'textarea' )                                    
                                    if ( $field[ 1 ] == 'radio' ) {
                                                    if( $field[ 0 ] == 'bdotcom_bc_mbe_logo_var' ) {
                                                                    $output .= '<p>' ;         
                                                                    $output .= '<label for="' . $field[ 0 ] . '">' . $field[ 2 ] . '</label>';
                                                                    $output .= '<span class="'. $field[ 0 ] .'">' .  __( 'Light', BDOTCOM_BC_TEXT_DOMAIN );
                                                                    $output .= '<input class="' . $field[ 0 ] . '" type="' . $field[ 1 ] . '" name="' . $field[ 0 ] . '" value="light" ' . checked( 'light', $field_value, false ) .  ' /></span>&nbsp;' ;
                                                                    $output .= '<span class="'. $field[ 0 ] .'">' .  __( 'Dark', BDOTCOM_BC_TEXT_DOMAIN ) ;
                                                                    $output .= '<input class="' . $field[ 0 ] . '" type="' . $field[ 1 ] . '" name="' . $field[ 0 ] . '" value="dark" ' . checked( 'dark', $field_value, false )  . ' /></span>' ;
                                                                    $output .= '</p>'; 
                                                    } 
                                    } // if( $field[ 1 ] == 'radio' )                                 
                                    if ( $field[ 1 ] == 'text' ) {
                                        switch(  $field[ 0 ] ) {
                                                        case  'bdotcom_bc_mbe_aid':
                                                        $output .= '<p class="' . $field[ 0 ] . '">' ;         
                                                        $output .= '<label for="' . $field[ 0 ] . '">' . $field[ 2 ] . '</label>';
                                                        $output .= '<input ' . $bdotcom_bc_maxlength . $bdotcom_bc_mbe_size . $bdotcom_bc_mbe_placeholder . '  id="' . $field[ 0 ] . '" class="' . $field[ 8 ] . '" type="' . $field[ 1 ] . '" name="' . $field[ 0 ] . '"  value="' . $field_value  . '" />&nbsp;<span class="bdotcom_bc_mbe_bonus_text">' . $field[ 3 ] . '</span>';
                                                        $output .= '</p>';
                                                        break;
                                                        
                                                        case 'bdotcom_bc_mbe_button_copy':
                                                        //Open bdotcom_bc_mbe_button_block
                                                         if( empty(  $bdotcom_bc_mbe_button ) || $bdotcom_bc_mbe_button === 'yes' )   { $display_field = 'bdotcom_bc_mbe_display_field' ; } else {  $display_field = 'bdotcom_bc_mbe_hide_field' ; }
                                                        $output .= '<div id="bdotcom_bc_mbe_button_block" class="' . $display_field . '">' ;                                                                                                                                                                                       
                                                        $output .= '<p class="' . $field[ 0 ] . '">' ;         
                                                        $output .= '<label for="' . $field[ 0 ] . '">' . $field[ 2 ] . '</label>';
                                                        $output .= '<input  ' . $bdotcom_bc_maxlength . $bdotcom_bc_mbe_size . $bdotcom_bc_mbe_placeholder . '  id="' . $field[ 0 ] . '" class="' . $field[ 8 ] . '" type="' . $field[ 1 ] . '" name="' . $field[ 0 ] . '"  value="' . $field_value  . '" />&nbsp;<span class="bdotcom_bc_mbe_bonus_text">' . $field[ 3 ] . '</span>';
                                                        $output .= '</p>';
                                                        break;
                                                        
                                                        case 'bdotcom_bc_mbe_button_border_width':
                                                        $output .= '<p class="' . $field[ 0 ] . '">' ;         
                                                        $output .= '<label for="' . $field[ 0 ] . '">' . $field[ 2 ] . '</label>';
                                                        $output .= '<input ' . $bdotcom_bc_mbe_placeholder . '  size="' . $field[ 5 ] . '" id="' . $field[ 0 ] . '" class="' . $field[ 8 ] . '" type="' . $field[ 1 ] . '" name="' . $field[ 0 ] . '"  value="' . $field_value  . '" />&nbsp;' . $field[ 3 ] ;
                                                        $output .= '</p>'; 
                                                        $output .= '</div>'; //close bdotcom_bc_mbe_button_block as bdotcom_bc_mbe_button_border_width is last field
                                                        break;
                                                        
                                                        case 'bdotcom_bc_mbe_banner_link':
                                                        $output .= '<p class="' . $field[ 0 ] . '">' ;         
                                                        $output .= '<label for="' . $field[ 0 ] . '">' . $field[ 2 ] . '</label>';
                                                        $output .= '<input  ' . $bdotcom_bc_maxlength . $bdotcom_bc_mbe_size . $bdotcom_bc_mbe_placeholder . '   id="' . $field[ 0 ] . '" class="' . $field[ 8 ] . '" type="' . $field[ 1 ] . '" name="' . $field[ 0 ] . '"  value="' . esc_url( $field_value )  . '" />&nbsp;<span class="bdotcom_bc_mbe_bonus_text">' . $field[ 3 ] . '</span>';
                                                        $output .= '</p>';                                                          
                                                        break;
                                                        
                                                        case 'bdotcom_bc_mbe_img_path':
                                                        $output .= '<p id="'.  $field[ 0 ] . '_wrapper" class="' .  $field[ 8 ] . '">' ; 
                                                        $output .= '<span class="'.  $field[ 0 ] . '_uploader"><label for="' . $field[ 0 ] . '">' . $field[ 2 ] . '</label><input  ' . $bdotcom_bc_maxlength . $bdotcom_bc_mbe_size . $bdotcom_bc_mbe_placeholder . '  id="' . $field[ 0 ] . '" name="' . $field[ 0 ] . '"  value="' . $field_value  . '"  type="text" />
                                                        <input id="bdotcom_bc_mbe_img_uploader_button" class="bdotcom_bc_mbe_img_uploader_button button-primary" name="bdotcom_bc_mbe_img_uploader_button" type="submit" value="'.  $field[ 3 ] .'" /></span>';
                                                        $output .= '</p>';
                                                        break;

                                                        case 'bdotcom_bc_mbe_label':
                                                        $output .= '<p class="' . $field[ 0 ] . '">' ;         
                                                        $output .= '<label for="' . $field[ 0 ] . '">' . $field[ 2 ] . '</label>';
                                                        $output .= BDOTCOM_BC_DEFAULT_LABEL . '<input ' . $bdotcom_bc_mbe_placeholder . '  size="' . $field[ 5 ] . '" id="' . $field[ 0 ] . '" class="' . $field[ 8 ] . '" type="' . $field[ 1 ] . '" name="' . $field[ 0 ] . '"  value="' . $field_value  . '" />&nbsp;<span class="bdotcom_bc_mbe_bonus_text">' .  $field[ 3 ] . '</span>';
                                                        $output .= '</p>'; 
                                                        break;
                                                            
                                                        default:
                                                        $output .= '<p class="' . $field[ 0 ] . '">' ;         
                                                        $output .= '<label for="' . $field[ 0 ] . '">' . $field[ 2 ] . '</label>';
                                                        $output .= '<input  ' . $bdotcom_bc_maxlength . $bdotcom_bc_mbe_size . $bdotcom_bc_mbe_placeholder . '    id="' . $field[ 0 ] . '" class="' . $field[ 8 ] . '" type="' . $field[ 1 ] . '" name="' . $field[ 0 ] . '"  value="' . $field_value  . '" ' . $bdotcom_bc_mbe_size . ' />&nbsp;<span class="bdotcom_bc_mbe_bonus_text">' . $field[ 3 ] . '</span>';
                                                        $output .= '</p>';
                                        

                                        }

                                                                                                     
                                    } // if( $field[ 1 ] == 'text' )
                                    if ( $field[ 1 ] == 'hidden' ) {
                                                    if( $field[ 0 ] == 'bdotcom_bc_mbe_themes' ) {
                                                                    $output .= '<p class="' . $field[ 0 ] . '">' ;         
                                                                    $output .= '<label for="' . $field[ 0 ] . '">' . $field[ 2 ] . '</label>';
                                                                    $output .= '<input ' . $bdotcom_bc_mbe_placeholder . '  size="' . $field[ 5 ] . '" id="' . $field[ 0 ] . '" class="' . $field[ 8 ] . '" type="' . $field[ 1 ] . '" name="' . $field[ 0 ] . '"  value="' . $field_value  . '" /></span>';
                                                                    /*Display the Your Theme button only if user did not choose the custom theme*/
                                                                    $output .= '<input type="button" id="bdotcom_bc_show_defaults_themes" class="button-primary"  value="' . __( 'Default Themes', BDOTCOM_BC_TEXT_DOMAIN ) . '" />&nbsp';
                                                                    $output .= '<input type="button" id="bdotcom_bc_custom_theme" class="button-primary"  value="' . __( 'Your Theme', BDOTCOM_BC_TEXT_DOMAIN ) . '" />';                       
                                                                    $output .= '</p>';
                                                                    $output .= '<span id="bdotcom_bc_theme_preview">';
                                                                    if( !empty( $bdotcom_bc_mbe_themes ) && $bdotcom_bc_mbe_themes === 'custom_theme' )   { 
                                                                        $output .= '<img src="' . $bdotcom_bc_mbe_img_path . '" class="bdotcom_bc_thumbnail_displayed">' ;
                                                                    }
                                                                    else {
                                                                        $bdotcom_bc_default_image_paths_array = bdotcom_bc_default_image_paths();                                                      
                                                                        foreach( $bdotcom_bc_default_image_paths_array as $bdotcom_bc_default_image_item ) {                                                                            
                                                                                        if( $bdotcom_bc_mbe_themes == $bdotcom_bc_default_image_item[0] ) {
                                                                                                    $bdotcom_bc_banner_image = $bdotcom_bc_default_image_item[3] ;
                                                                                        } else {
                                                                                            $bdotcom_bc_banner_image = "https://r.bstatic.com/data/sp_aff/". BDOTCOM_BC_DEFAULT_AID ."/bdotcom_hotel_theme_1". BDOTCOM_BC_DEFAULT_THUMBNAIL .".jpg";
                                                                                        }

                                                                        }
                                                                        $output .= '<img src="' . $bdotcom_bc_banner_image . '" class="bdotcom_bc_thumbnail_displayed">' ;
                                                                    }
                                                                    $output .= '</span>';

                                                                    $output .= '<div id="bdotcom_bc_default_themes_box" class="bdotcom_bc_mbe_hide_field">';
                                                                    $output .= '<div id="bdotcom_bc_default_themes_box_black_overlay"></div>';
                                                                    $output .= '<div id="bdotcom_bc_default_themes_box_images">';
                                                                    $bdotcom_bc_default_image_paths_array = bdotcom_bc_default_image_paths();
                                                                    foreach( $bdotcom_bc_default_image_paths_array as $bdotcom_bc_default_image_item ) {
                                                                                     /*$bdotcom_bc_default_image_selected = ( empty( $bdotcom_bc_mbe_themes ) && ( $bdotcom_bc_default_image_item[0] == 'hotel_theme_1' )  ) ? "selected='selected'" : '';*/
                                                                                    $output .= '<img data-theme="' . $bdotcom_bc_default_image_item[0] . '" class="bdotcom_bc_thumbnail" id="' . $bdotcom_bc_default_image_item[0] . '" src="' . $bdotcom_bc_default_image_item[3] . '">' ;
                                                                    }
                                                                    $output .= '</div>';
                                                                    $output .= '</div>';

                                                    }
                                    } // $if( $field[ 1 ] == 'hidden')                                    
                                    if ( $field[ 1 ] == 'checkbox'  ) {
                                                    if( $field[ 0 ] == 'bdotcom_bc_mbe_button') {
                                                                    $output .= '<p>' ;         
                                                                    $output .= '<label for="' . $field[ 0 ] . '">' . $field[ 2 ] . '</label>';                                         
                                                                    $output .= '<input id="' . $field[ 0 ] . '" type="' . $field[ 1 ] . '" name="' . $field[ 0 ] . '"   ' . checked( 'yes', $field_value, false ) . ' />&nbsp;<span class="bdotcom_bc_mbe_bonus_text">' . $field[ 3 ] . '</span>';
                                                                    $output .= '</p>';
                                                    }      
                                    } //$field[1] == 'checkbox' 
                                    //$output .= $field[ 3 ];   
                        } //if( $field[ 7 ] == 'main' )                   
                                      
            } //foreach( $array_fields as $field )
            $output .= '<input type="hidden"  id="bdotcom_bc_mbe_post_id" name="bdotcom_bc_mbe_post_id" value="' .  $post->ID .  '" />' ;
            $output .= '<input type="button" id="bdotcom_bc_mbe_preview_button" class="button-primary"  value="' . __( 'Preview', BDOTCOM_BC_TEXT_DOMAIN ) . '" />';
            $output .= /*'window width: <span id="width_test"></span>*/ '<div id="bdotcom_bc_mbe_preview_wrapper"></div>'; //banner will be loaded here via ajax 
            $output .= '<div id="bdotcom_bc_shortcode" class="updated"><p>' . __( 'Use following shortcode to display the banner in posts/pages:', BDOTCOM_BC_TEXT_DOMAIN ) . ' <strong><span id="bdotcom_bc_shortcode_to_copy">[bdotcom_bm bannerid="' . $post->ID . '"]</span></strong> .</p></div>';               
            //$output .= '<div class="updated"><p>' . __( 'If you want to see the banner using the "View post" link of this page, please <a href="#" class="bdotcom_bc_shortcode_action_link">copy and paste</a> the shortcode into the editor and save the post', BDOTCOM_BC_TEXT_DOMAIN ) . '.</p></div>';
            
            $output .= '</div>';  
            // close id="bdotcom_bc_main_settings" ;
            
            /**************** End: Main settings *****************/

            //create nonce for ajax call    
            $output .= '<span id="bdotcom_bc_ajax_nonce" class="hidden" style="visibility: hidden;">' . wp_create_nonce( 'bdotcom_bc_ajax_nonce' ) . '</span>';            
            echo $output; // echo the fields
            
} // function bdotcom_bc_mb_function( $post )

// Adding custom columns to display maps list
//this works before 3.1
add_filter( 'manage_edit-bdotcom_bc_columns', 'add_new_bdotcom_bc_columns' );
function add_new_bdotcom_bc_columns( $columns )
{
            $new_columns[ 'cb' ]  = '<input type="checkbox" />';
            $new_columns[ 'title' ] = __( 'Banner', BDOTCOM_BC_TEXT_DOMAIN );
            $new_columns[ 'shortcode' ] = __( 'Shortcode', BDOTCOM_BC_TEXT_DOMAIN );
            $new_columns[ 'date' ] = __( 'Date', BDOTCOM_BC_TEXT_DOMAIN );
            return $new_columns;
}

//Populate shortcodes column
add_action( 'manage_bdotcom_bc_posts_custom_column', 'bdotcom_bc_custom_columns', 10, 2 );
function bdotcom_bc_custom_columns( $column, $post_id )
{            switch ( $column ) {
            case 'shortcode':
                        echo '[bdotcom_bm bannerid="' . $post_id . '"]';
                        break;
            } //$column
}

?>
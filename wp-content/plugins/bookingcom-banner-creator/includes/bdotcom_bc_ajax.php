<?php
/**
 * Ajax calls
  ----------------------------------------------------------------------------
 */
 
 
 /* Theme  Preview */
add_action( 'wp_ajax_bdotcom_bc_theme_preview', 'bdotcom_bc_ajax_theme_preview' );
function bdotcom_bc_ajax_theme_preview( ) {
                if ( isset( $_REQUEST[ 'bdotcom_bc_ajax_nonce' ] ) ) {
                         // Verify that the incoming request is coming with the security nonce
                        if ( wp_verify_nonce( $_REQUEST[ 'bdotcom_bc_ajax_nonce' ], 'bdotcom_bc_ajax_nonce' ) ) {                    
                                        $bdotcom_bc_mbe_themes = $_REQUEST[ 'bdotcom_bc_mbe_themes'];
                                        $output = '';
                                        /*$bdotcom_bc_default_image_paths_array = bdotcom_bc_default_image_paths();
                                        foreach( $bdotcom_bc_default_image_paths_array as $bdotcom_bc_default_image_item ) {
                                                        if( $bdotcom_bc_mbe_themes == $bdotcom_bc_default_image_item[0] ) {
                                                                    $bdotcom_bc_banner_image = $bdotcom_bc_default_image_item[2] ;
                                                        }
                                        }*/
                                        $output .= '<img class="bdotcom_bc_theme_preview_image" src="' . $bdotcom_bc_mbe_themes . '" alt="' .  __( 'Theme Image', BDOTCOM_BC_TEXT_DOMAIN ) . '" />';                          
                                        die( $output );
                        }//if ( wp_verify_nonce( $_REQUEST[ 'nonce' ], 'bdotcom_bc_ajax_nonce' ) )
                    
                } //if ( isset( $_REQUEST[ 'nonce' ] ) )
                else {
                        die( __( 'Issues with theme preview generation', BDOTCOM_BC_TEXT_DOMAIN ) );
                }
    
}
 
/* Banner Preview */
add_action( 'wp_ajax_bdotcom_bc_preview', 'bdotcom_bc_ajax_preview' );
function bdotcom_bc_ajax_preview( ) {
                if ( isset( $_REQUEST[ 'bdotcom_bc_ajax_nonce' ] ) ) {
                         // Verify that the incoming request is coming with the security nonce
                        if ( wp_verify_nonce( $_REQUEST[ 'bdotcom_bc_ajax_nonce' ], 'bdotcom_bc_ajax_nonce' ) ) {                    
                                   
                                         //retrieve all values from form fields using the function
                                         $output = bdotcom_bc_variables( basename(__FILE__), $_REQUEST[ 'bdotcom_bc_mbe_post_id' ] );
                                         //$output = bdotcom_bc_variables( basename(__FILE__) );
                                        
                                        //echo $output ;
                                        die( $output );
                        }//if ( wp_verify_nonce( $_REQUEST[ 'nonce' ], 'bdotcom_bc_ajax_nonce' ) )
                    
                } //if ( isset( $_REQUEST[ 'nonce' ] ) )
                else {
                        die( __( 'Issues with banner preview generation', BDOTCOM_BC_TEXT_DOMAIN ) );
                }
    
}
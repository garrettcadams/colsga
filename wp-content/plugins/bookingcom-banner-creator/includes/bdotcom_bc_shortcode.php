<?php
/**
 * SHORTCODE SECTION
 * ----------------------------------------------------------------------------
 */
add_shortcode( 'bdotcom_bm', 'bdotcom_bc_shortcode' );
function bdotcom_bc_shortcode( $attr ) {
      /* if( isset( $attr[ 'bannerid' ] ) && is_numeric( $attr[ 'bannerid' ] ) ) { // Check if mapid attr exists and if is numeric
        $bdotcom_bc_postid = get_post( $attr[ 'bannerid' ]) ;
        return  bdotcom_bc_variables( basename(__FILE__), $bdotcom_bc_postid );
        
       }*/
        //$bdotcom_bc_postid = get_post( $attr[ 'bannerid' ]) ;
        return  bdotcom_bc_variables( basename(__FILE__), $attr[ 'bannerid' ] );
      
    // $attr manages the shortcode parameter - [bdotcom_bm bannerid="xxxx"]   
   /* if( isset( $attr[ 'bannerid' ] ) && is_numeric( $attr[ 'bannerid' ] ) ) { // Check if mapid attr exists and if is numeric
        
        // Get the post id to check wheter exists     
        $postid = get_post( $attr[ 'bannerid' ]) ;
        
        echo $postid;
        
        // check if post exists and not permanently deleted
        if( !empty( $postid ) ) {     

              //retrieve all values from form fields using the function
                        $output =  bdotcom_bc_variables( basename(__FILE__), $postid );                          
                         return $output ;
        } //if( !empty( $postid ) )            

   } //if( isset( $attr ) ) 
    */
        
} //function bdotcom_ohom_shortcode( $attr )

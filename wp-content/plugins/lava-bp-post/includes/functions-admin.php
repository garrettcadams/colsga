<?php
/**
 *
 *
 * @param	Integer	Default choise page ID
 * @return	String	Select tag options
 */
if( ! function_exists( 'getOptionsPagesLists' ) ) :
	function getOptionsPagesLists( $default=0 ) {
		return lava_bpp()->admin->getOptionsPagesLists( $default );
	}
endif;

/**
 * Get manager setting options
 *
 * @param	String	Option Key name
 * @param	Mixed	Result value null, return
 * @return	Mixed	String or default value
 */
if( ! function_exists( 'lava_bpp_get_option' ) ) :

	function lava_bpp_get_option( $key, $default=false ) {
		lava_bpp()->admin->get_settings( $key, $default );
	}
endif;
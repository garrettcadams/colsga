<?php




/**
 *
 *
 * @param	Integer	Default choise page ID
 * @return	String	Select tag options
 */
if( ! function_exists( 'getOptionsPagesLists' ) ) :

	function getOptionsPagesLists( $default=0 ) {
		return lava_directory()->admin->getOptionsPagesLists( $default );
	}

endif;




/**
 * Get manager setting options
 *
 * @param	String	Option Key name
 * @param	Mixed	Result value null, return
 * @return	Mixed	String or default value
 */
if( ! function_exists( 'lava_directory_manager_get_option' ) ) :

	function lava_directory_manager_get_option( $key, $default=false )
	{
		global $lava_directory_manager_admin;
		if( empty( $lava_directory_manager_admin ) )
			$lava_directory_manager_admin	= new Lava_Directory_Manager_Admin;

		return $lava_directory_manager_admin->get_settings( $key, $default );
	}

endif;
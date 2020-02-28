<?php
class Lava_Directory_Manager_widgets extends Lava_Directory_Manager_Func
{
	private static $widgets_dir;

	public function __construct()
	{
		self::$widgets_dir = dirname( __FILE__ ) . '/widgets';
		$this->register_widgets(
			Array(
				'Lava_Contact_Single_Page'		=> 'widget-single-contact.php'
				, 'Lava_Directory_Recents'		=> 'widget-recents.php'
				, 'Lava_Directory_Featured'	=> 'widget-featureds.php'
			)
		);
	}

	public function register_widgets( $widgets=Array() )
	{
		if( !empty( $widgets ) ) : foreach( $widgets as $class_name => $file ) {
			if( file_exists( self::$widgets_dir . '/' . $file ) ) {
				require_once self::$widgets_dir . '/' . $file;
				register_widget( $class_name );
			}
		} endif;
	}
}
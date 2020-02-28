<?php
function wilcityRegisterWidgets() {
	$aFetchFiles = glob(plugin_dir_path(__FILE__) . '/app/*.php');

	foreach ($aFetchFiles as $fileDirectory){
		require_once $fileDirectory;

		$aFileName = explode('/', $fileDirectory);
		$fileName = end($aFileName);
		$fileName = str_replace('.php', '', $fileName);
		$className = 'WilcityWidgets\App\\' . $fileName;
		register_widget($className);
	}

}
    
add_action( 'widgets_init', 'wilcityRegisterWidgets' );

function wilcityWidgetsFrontendScripts() {
	wp_register_script( 'wilcity-widget-mailchimp', plugin_dir_url(__FILE__) . 'front-end/js/mailchimp.js', array('jquery'), '1.0', true );
}

add_action('wp_enqueue_scripts', 'wilcityWidgetsFrontendScripts');

function wilcityWidgetScripts($hook) {
	if( $hook != 'widgets.php' ){
		return false;
	}
	wp_enqueue_media();
	wp_enqueue_script( 'wilokeWidgetUploadPhoto', plugin_dir_url(__FILE__) . 'assets/js/widgetUploadImg.js', array('jquery'), '1.0', true );
	wp_enqueue_style( 'wilokeWidgetStyle', plugin_dir_url(__FILE__) . 'assets/css/style.css');
}

add_action('admin_enqueue_scripts', 'wilcityWidgetScripts');
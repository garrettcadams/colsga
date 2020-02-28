<?php
/*
 * Plugin Name: Wilcity Import Demos
 * Plugin URI: https://wilcity.com
 * Author: Wiloke
 * Author URL: https://wilcity.com
 * Version: 1.1.1
 */

require plugin_dir_path(__FILE__) . 'vendor/autoload.php';
define('WILCITY_IMPORT_DIR', plugin_dir_path(__FILE__));
define('WILCITY_IMPORT_URL', plugin_dir_url(__FILE__));

new \WILCITY_IMPORT\Register\RegisterImportMenu();

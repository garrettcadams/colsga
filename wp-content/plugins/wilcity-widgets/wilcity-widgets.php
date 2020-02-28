<?php
/*
 * Plugin Name: Wilcity Widgets
 * Plugin URI: https://wiloke.com
 * Author: Wiloke
 * Author URI: https://wiloke.com
 * Version: 1.0.8
 * Description: This tool allows customizing your Add Listing page
 * Text Domain: wilcity-widgets
 * Domain Path: /languages/
 */

define('WILCITY_WIDGET', '(Wilcity)');

require_once plugin_dir_path(__FILE__) . 'wilcity-register-widgets.php';
require_once plugin_dir_path(__FILE__) . 'mailchimp/func.mailchimp.php';
require_once plugin_dir_path(__FILE__) . 'instagram/func.instagram-settings.php';

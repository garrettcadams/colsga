<?php
/*
 * Plugin name: Wilcity Paid Claim
 * Plugin URI: https://wiloke.com
 * Author URI: https://wiloke.com
 * Author: Wiloke
 * Description: An extension of the Wilcity. That helps to enable Paid Claim Listing
 * Version: 1.1.2
 * Languages: languages/
 * Text Domain: wilcity-paid-claim
 */

define('WILCITY_PC_DOMAIN', 'wilcity-paid-claim');
define('WILCITY_PC_URL', plugin_dir_url(__FILE__));
define('WILCITY_PC_CONFIG_DIR', plugin_dir_path(__FILE__) . 'config/');
define('WILCITY_PC_DIR', plugin_dir_path(__FILE__));

require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

use WilcityPaidClaim\Register\RegisterClaimSubMenu;
use WilcityPaidClaim\Controllers\ClaimListingsController;
use WilcityPaidClaim\Controllers\AddMiddleware;

add_action('wiloke-listing-tools/run-extension', function(){
//	new AddMiddleware;
	new RegisterClaimSubMenu;
	new ClaimListingsController;
});


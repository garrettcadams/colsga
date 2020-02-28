<?php

namespace WilokeListingTools\Frontend;


use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Models\UserModel;

class EnqueueScripts {
	public function __construct() {
		add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));
		add_action('wp_head', array($this, 'printScript'));
	}

	public function printScript(){
		global $wiloke;
		$mapTheme = isset($wiloke->aThemeOptions['map_theme']) ? esc_js($wiloke->aThemeOptions['map_theme']) : 'blurWater';
		if ( $mapTheme == 'custom' ):
			$theme = isset($wiloke->aThemeOptions['map_custom_theme']) && !empty($wiloke->aThemeOptions['map_custom_theme']) ? $wiloke->aThemeOptions['map_custom_theme'] : '[]';
		?>
			<script style="text/javascript">
				window.WILCITY_CUSTOM_MAP = <?php echo $theme; ?>;
			</script>
		<?php
		endif;
	}

	public function enqueueScripts(){
		wp_localize_script('jquery-migrate', 'WILCITY_GLOBAL', array(
			'oStripe' => array(
				'publishableKey' => GetWilokeSubmission::getField('stripe_publishable_key'),
				'hasCustomerID'  => UserModel::getStripeID() ? 'yes' : 'no'
			),
			'oGeneral' => array(
				'brandName' => GetWilokeSubmission::getField('brandname')
			)
		));

	}
}
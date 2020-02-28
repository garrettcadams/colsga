<?php
use WilokeListingTools\Framework\Helpers\GetSettings;

add_shortcode('wilcity_google_adsense', 'wilcityGoogleAdSense');
function wilcityGoogleAdSense(){
	global $post, $wiloke;
	if ( !GetSettings::isPlanAvailableInListing($post->ID, 'toggle_google_ads') ){
		return '';
	}

	if ( empty($wiloke->aThemeOptions)  ){
		$aThemeOptions = Wiloke::getThemeOptions(true);
	}else{
		$aThemeOptions = $wiloke->aThemeOptions;
	}
	if ( !isset($aThemeOptions['google_adsense_client_id']) || !isset($aThemeOptions['google_adsense_slot_id']) || empty($aThemeOptions['google_adsense_client_id']) || empty($aThemeOptions['google_adsense_slot_id']) ){
		return '';
	}
	ob_start();
	?>
	<Adsense data-ad-client="<?php echo esc_attr($aThemeOptions['google_adsense_client_id']); ?>" data-ad-slot="<?php echo esc_attr($aThemeOptions['google_adsense_slot_id']); ?>">
	</Adsense>
	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}
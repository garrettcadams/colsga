<?php
use WilokeListingTools\Framework\Helpers\GetSettings;

add_shortcode('wilcity_in_article_google_adsense', 'wilcityInArticleGoogleAdSense');
function wilcityInArticleGoogleAdSense(){
	global $post, $wiloke;
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
	<in-article-adsense data-ad-client="<?php echo esc_attr($aThemeOptions['google_adsense_client_id']); ?>" data-ad-slot="<?php echo esc_attr($aThemeOptions['google_adsense_slot_id']); ?>">
	</in-article-adsense>
	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}
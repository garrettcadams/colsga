<?php
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\General;

global $post;

if ( !\WilokeListingTools\Frontend\User::isPostAuthor($post) ){
	return '';
}

$aHighlightBoxes = GetSettings::getOptions(General::getSingleListingSettingKey('highlightBoxes', $post->post_type));

if ( empty($aHighlightBoxes) || ($aHighlightBoxes['isEnable'] == 'no') || empty($aHighlightBoxes['aItems']) ){
    return '';
}
$aHighlightBoxes = General::unSlashDeep($aHighlightBoxes);
$aPostTypeKeys = General::getPostTypeKeys(true, false);

foreach ($aHighlightBoxes['aItems'] as $aItem) :
    if ( in_array($aItem['key'], $aPostTypeKeys) ){
	    $aItem['isPopup'] = 'no';
	    $linkTo = apply_filters('wilcity/add-new-event-url', '#', $post);
	    $aItem['linkTargetType'] = 'self';
    }else{
	    $linkTo = isset($aItem['linkTo']) && !empty($aItem['linkTo']) ? $aItem['linkTo'] : '#';
	    $aItem['linkTargetType'] = isset($aItem['linkTargetType']) ? '_'. $aItem['linkTargetType'] : '_self';
    }

?>
    <div class="<?php echo esc_attr($aHighlightBoxes['itemsPerRow']); ?>">
        <!-- icon-box-2_module__AWd3Y wil-text-center bg-color-primary -->
        <div class="icon-box-2_module__AWd3Y wil-text-center" style="background-color: <?php echo esc_attr($aItem['bgColor']); ?>">
            <a class="temporary-disable" href="<?php echo esc_url($linkTo); ?>" target="<?php echo esc_attr($aItem['linkTargetType']); ?>" <?php if ( isset($aItem['isPopup']) && ($aItem['isPopup'] == 'yes') ) : ?> @click.prevent="onOpenPopup('<?php echo esc_attr(str_replace(array('wilcity_', 'wilcity-'), array('', ''), $aItem['key'])); ?>', '<?php echo esc_attr($post->ID); ?>')" <?php endif; ?>>
                <div class="icon-box-2_icon__ZqobK"><i class="<?php echo esc_attr($aItem['icon']); ?>"></i></div>
                <h2 class="icon-box-2_title__2cgba"><?php echo esc_html($aItem['name']); ?></h2>
            </a>
        </div><!-- End / icon-box-2_module__AWd3Y wil-text-center bg-color-primary -->
    </div>
<?php endforeach; ?>

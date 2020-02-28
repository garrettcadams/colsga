<?php
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;

function wilcityGroupPropertiesSCParseOption($options){
	$aRawOptions = explode(',', $options);

	$aOptions = array();
	foreach ($aRawOptions as $rawOption){
		$aParsed = explode(':', trim($rawOption));
		$val = isset($aParsed[1]) ? $aParsed[1] : $aParsed[0];

		$aOptions[$aParsed[0]] = $val;
	}
	return $aOptions;
}

function wilcityGroupPropertiesSCValueEmpty($aValues){
    foreach ($aValues as $val){
        if ( !empty($val) ){
            return false;
        }
    }

    return true;
}

function wilcityGroupPropertiesSC($aAtts){
	$aAtts = shortcode_atts(
		array(
			'group_key'     => '',
			'heading'       => '',
			'description'   => '',
			'post_id'       => '',
			'item_wrapper' => 'col-md-4 col-lg-4'
		),
		$aAtts
	);
	if (empty($aAtts['group_key'])){
		return '';
	}
	if ( !empty($aAtts['post_id']) ){
		$postID = $aAtts['post_id'];
	}else{
		global $post;
		$postID = $post->ID;
	}

	$aSettings = GetSettings::getPostMeta($postID, $aAtts['group_key']);

	if ( empty($aSettings) || wilcityGroupPropertiesSCValueEmpty($aSettings) ){
		return '';
	}

	$aGroupSettings = GetSettings::getGroupSetting($aAtts['group_key'], get_post_type($postID));

	if ( empty($aGroupSettings)  ){
		return '';
	}

	ob_start();
	?>
    <?php if ( !empty($aAtts['heading']) ) : ?>
        <div class="col-md-12"><h4 class="<?php  echo esc_attr(apply_filters('wilcity/filter/class-prefix','wilcity-group-properties-heading')); ?>"><?php echo esc_html($aAtts['heading']); ?></h4></div>
    <?php endif; ?>

    <?php if ( !empty($aAtts['description']) ) : ?>
        <div class="col-md-12"><p class="<?php  echo esc_attr(apply_filters('wilcity/filter/class-prefix','wilcity-group-properties-desc')); ?>"><?php Wiloke::ksesHTML($aAtts['description']); ?></p></div>
    <?php endif; ?>
    <?php
	$order = 0;

	foreach ($aSettings as $selected) :
		if ( empty($selected) ){
			$order++;
			continue;
		}

		$aRawOptions = end($aGroupSettings['fields'][$order]);

		if ( $aRawOptions['value']['type'] == 'select2' ){
			$aOptions = wilcityGroupPropertiesSCParseOption($aRawOptions['value']['options']);
			$value = $aOptions[$selected];
		}else{
			$value = $selected;
		} ?>

		<div class="<?php echo esc_attr($aAtts['item_wrapper']); ?>">
			<div class="utility-meta-02_module__1VqhJ">
				<div class="utility-meta-02_left__3P9WL"><?php echo esc_html($aGroupSettings['fields'][$order][0]['value']); ?>:</div>
				<?php if ( is_array($value) ) : ?>
					<div class="utility-meta-02_right__OiUF-"><?php echo implode(', ', $value); ?></div>
				<?php else: ?>
					<div class="utility-meta-02_right__OiUF-"><?php Wiloke::ksesHTML($value); ?></div>
				<?php endif; ?>
			</div>
		</div>

		<?php

		$order++;
		
	endforeach; 

	$content = ob_get_clean();

	$content = apply_filters("wilcity_shortcode/wilcity_render_group_field/". $post->post_type ."/". $aAtts['group_key'], $content, $aSettings, $aGroupSettings, $aAtts);
	
    return $content;
}
add_shortcode('wilcity_group_properties', 'wilcityGroupPropertiesSC');
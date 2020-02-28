<?php
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\General;

function wilcityRenderTextField($aAtts){
	$aAtts = shortcode_atts(
		array(
			'post_id'     => '',
			'key'         => '',
			'is_mobile'   => '',
			'description' => '',
			'link_target' => '_self',
			'is_link'      => 'no',
			'link_name'   => '',
			'title'       => '',
			'extra_class' => '',
			'title_tag'   => 'strong'
		),
		$aAtts
	);

	if ( !empty($aAtts['post_id']) ){
		$post = get_post($aAtts['post_id']);
	}else{
		$post = \WILCITY_SC\SCHelpers::getPost();
	}

	if ( empty($aAtts['key']) || !class_exists('WilokeListingTools\Framework\Helpers\GetSettings') || empty($post)){
		return '';
	}

	if ( !GetSettings::isPlanAvailableInListing($post->ID, $aAtts['key']) ){
		return '';
	}

	$content = GetSettings::getPostMeta($post->ID, 'custom_'.$aAtts['key']);
	
	$content = apply_filters("wilcity_shortcode/wilcity_render_text_field/". $post->post_type ."/". $aAtts['key'], $content, $aAtts);

	if ( empty($content) ){
	    return '';
    }

	$content = do_shortcode($content);

	if ( $aAtts['is_link'] == 'yes' ){
		ob_start();
		$aAtts['link_name'] = !empty($aAtts['link_name']) ? $aAtts['link_name'] : $content;
		?>
		<a href="<?php echo esc_url($content); ?>" target="<?php echo esc_attr($aAtts['link_target']); ?>"><?php echo esc_html($aAtts['link_name']); ?></a>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
	}
	$content = nl2br($content);
	if ( !empty($aAtts['title']) ){
        $content = '<'.$aAtts['title_tag']. ' class="wilcity-text-sc-title">' . $aAtts['title'] . '</'.$aAtts['title_tag'].'>: ' . $content;
    }

    $class = $aAtts['key'];
    if ( !empty($aAtts['extra_class']) ){
	    $class .= ' ' . $aAtts['extra_class'];
    }

	$content = $content = '<div class="'.esc_attr($class).'">'.$content.'</div>';

	return $content;
}

add_shortcode('wilcity_render_text_field', 'wilcityRenderTextField');
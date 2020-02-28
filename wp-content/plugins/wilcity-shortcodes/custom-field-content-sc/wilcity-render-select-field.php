<?php
function wilcityRenderSelectField($aAtts){
	$aAtts['print_unchecked'] = isset($aAtts['print_unchecked']) ? $aAtts['print_unchecked'] : 'no';

	if ( isset($aAtts['post_id']) && !empty($aAtts['post_id']) ){
		$aAtts['postID'] = get_post($aAtts['post_id']);
	}else{
		if ( wp_doing_ajax() ){
			$aAtts['postID'] = $_POST['postID'];
		}
	}
	return wilcityRenderCheckboxField($aAtts);
}

add_shortcode('wilcity_render_select_field', 'wilcityRenderSelectField');
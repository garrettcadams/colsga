<?php
use WilokeListingTools\Framework\Helpers\GetSettings;

class WilokeHelpers{
	public static $aTermByPostID;

	public static function ngettext($singular, $two, $biggerThanTwo, $val){
		$val = abs($val);
		if ( $val <= 1 ){
			return $singular;
		}else if ( $val == 2 ){
			return $two;
		}else{
			return $biggerThanTwo;
		}
	}

	public static function pagination($wp_query){
		?>
		<?php  if ( !empty($wp_query->max_num_pages) ) : ?>
            <nav>
				<?php
				$big = 999999999; // need an unlikely integer
				echo paginate_links(array(
					'base'                  => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
					'format'                => '?paged=%#%',
					'current'               => max( 1, get_query_var('paged') ),
					'total'                 => $wp_query->max_num_pages,
					'before_page_number'    => '',
					'prev_next'             => true,
					'prev_text'             => '<i class="la la-angle-left"></i>',
					'next_text'             => '<i class="la la-angle-right"></i>',
					'type'                  => 'list'
				));
				?>
            </nav>
		<?php endif; ?>
		<?php
	}

	public static function getTermGradient($oTerm, $setDefault=true){
		$leftBg = GetSettings::getTermMeta($oTerm->term_id, 'left_gradient_bg');
		$rightBg  = GetSettings::getTermMeta($oTerm->term_id, 'right_gradient_bg');
		$tiltedDegrees  = GetSettings::getTermMeta($oTerm->term_id, 'gradient_tilted_degrees');

		if ( (empty($leftBg) || empty($rightBg) || empty($tiltedDegrees)) && $setDefault ){
			$leftBg = empty($leftBg) ? '#006bf7' : $leftBg;
			$rightBg = empty($rightBg) ? '#f06292' : $rightBg;
			$tiltedDegrees = empty($tiltedDegrees) ? -10 : $tiltedDegrees;
		}

		return array(
			'leftBg'    => $leftBg,
			'rightBg'   => $rightBg,
			'tiltedDegrees' => $tiltedDegrees
		);
	}

	/*
	 * @postID
	 * @taxonomy
	 */
	public static function getTermByPostID($postID, $taxonomy, $getLastTermOnly=true){
		if ( isset(self::$aTermByPostID[$taxonomy.$postID]) ){
			return self::$aTermByPostID[$taxonomy.$postID];
		}

		$lastTermPrefix = $getLastTermOnly ? 'yes' : 'no';

		$aTerms = wp_get_post_terms($postID, $taxonomy);
		if ( empty($aTerms) || is_wp_error($aTerms) ){
			self::$aTermByPostID[$taxonomy.$postID.$lastTermPrefix] = false;
		}else{
			if ( class_exists('WPSEO_Primary_Term') ){
				$oPrimaryCategory = new WPSEO_Primary_Term($taxonomy, $postID);
				$termID = $oPrimaryCategory->get_primary_term();
			}

			if ( !empty($termID) ){
				$aTerm = get_term($termID, $taxonomy);
			}else{
				$aTerm = $getLastTermOnly ? end($aTerms) : $aTerms;
			}
			self::$aTermByPostID[$taxonomy.$postID.$lastTermPrefix] = $aTerm;
		}

		return self::$aTermByPostID[$taxonomy.$postID.$lastTermPrefix];
	}

	public static function getVimeoThumbnail($vimeoID){
		$url = is_ssl() ?  'https://vimeo.com/api/v2/video/'.$vimeoID.'.php' : 'http://vimeo.com/api/v2/video/'.$vimeoID.'.php';
		$response = wp_remote_get(esc_url_raw($url));
		$aResponse = maybe_unserialize(wp_remote_retrieve_body($response));

		return array(
			'thumbnail_small'   => $aResponse[0]['thumbnail_small'],
			'thumbnail'         => $aResponse[0]['thumbnail_medium'],
			'thumbnail_large'   => $aResponse[0]['thumbnail_large'],
		);
	}

	/*
	 * @param $postID
	 * @param $tfKey: Theme Options Key
	 */
	public static function getFeaturedImg($postID, $size='large', $tfKey=''){
		$featuredImg = get_the_post_thumbnail_url($postID, $size);
		if ( !empty($featuredImg) ){
			return apply_filters('wilcity/featured_image_url', $featuredImg);
		}

		global $wiloke;
		if ( isset($wiloke->aThemeOptions[$tfKey]) && isset($wiloke->aThemeOptions[$tfKey]['id']) ){
			return wp_get_attachment_image_url($wiloke->aThemeOptions[$tfKey]['id'], $size);
		}

		return '';
	}

	public static function getImgPostMeta($postID,  $key, $tfKey='', $size='large'){
		$imgID = GetSettings::getPostMeta($postID, $key.'_id');

		if ( !empty($imgID) ){
			return wp_get_attachment_image_url($imgID, $size);
		}

		global $wiloke;

		if ( isset($wiloke->aThemeOptions[$tfKey]) && !empty($wiloke->aThemeOptions[$tfKey]) ){
			return wp_get_attachment_image_url($wiloke->aThemeOptions[$tfKey]['id'], $size);
		}
	}

	public static function getPostMeta($postID,  $key, $tfKey=''){
		$aData = GetSettings::getPostMeta($postID, $key);
		if ( !empty($aData) ){
			return $aData;
		}

		global $wiloke;
		if ( isset($wiloke->aThemeOptions[$tfKey]) ){
			return $wiloke->aThemeOptions[$tfKey];
		}

		return '';
	}

	public static function getAttachmentImg($postID,  $key, $tfKey='', $size='large'){
		$attachmentURL = wp_get_attachment_image_url($postID, $key, $size);

		if ( !empty($attachmentURL) ){
			return apply_filters('wilcity/attachment_image_url', $attachmentURL);
		}

		global $wiloke;
		if ( !isset($wiloke->aThemeOptions[$tfKey]) ){
			return '';
		}
		return wp_get_attachment_image_url($wiloke->aThemeOptions[$tfKey]['id'], $size);
	}

	public static function getTermOriginalIcon($oTerm){
		$icon = GetSettings::getTermMeta($oTerm->term_id, 'icon');
		if ( !empty($icon) ) {
			$iconColor = GetSettings::getTermMeta( $oTerm->term_id, 'icon_color' );
			return array(
				'type' => 'icon',
				'icon' => $icon,
				'color'=> $iconColor
			);
		}
		
		$icon = '';
		$icon_id = GetSettings::getTermMeta($oTerm->term_id, 'icon_img_id');
		if( empty($icon_id) ) {
			$icon_attributes = wp_get_attachment_image_src( $icon_id );
			if($icon_attributes) {
				$icon = $icon_attributes[0];
			}
		} 

		if( empty($icon) ) {
			$icon = GetSettings::getTermMeta($oTerm->term_id, 'icon_img');
		}

		if ( !empty($icon) ){
			return array(
				'type' => 'image',
				'url'  => $icon
			);
		}

		return false;
	}

	public static function getTermIcon($oTerm, $iconWrapper='', $hasLink=true, $query_arg = array()){
		$aIcon = self::getTermOriginalIcon($oTerm);
		$termLink = get_term_link($oTerm->term_id);

		if( !empty($query_arg) && is_array($query_arg) ) {
			$termLink = add_query_arg($query_arg, $termLink);
		}

		if ( isset($aIcon['type']) && $aIcon['type'] == 'image' ){
			if ( $hasLink ){
				return '<a href="'.esc_url($termLink).'"><div class="bg-transparent '.esc_attr($iconWrapper).'"><img src="'.esc_url($aIcon['url']).'" alt="'.esc_attr($oTerm->name).'"></div><div class="icon-box-1_text__3R39g">'.esc_html($oTerm->name).'</div></a>';
			}else{
				return '<img src="'.esc_url($aIcon['url']).'" alt="'.esc_attr($oTerm->name).'">';
			}
		}

		if ( empty($aIcon) ){
			$aIcon['icon'] = apply_filters('wilcity/'. $oTerm->taxonomy .'/icon', 'la la-file-picture-o');
			$aIcon['color'] = GetSettings::getTermMeta($oTerm->term_id, 'icon_color');
		}

		if ( $hasLink ){
			if ( !empty($aIcon['color']) ){
				return '<a href="'.esc_url($termLink).'"><div class="'.esc_attr($iconWrapper).'" style="background-color: '.esc_attr($aIcon['color']).'"><i class="'.esc_attr($aIcon['icon']).'"></i></div><div class="icon-box-1_text__3R39g">'.esc_html($oTerm->name).'</div></a>';
			}else{
				return '<a href="'.esc_url($termLink).'"><div class="'.esc_attr($iconWrapper).'"><i class="'.esc_attr($aIcon['icon']).'"></i></div><div class="icon-box-1_text__3R39g">'.esc_html($oTerm->name).'</div></a>';
			}

		}else{
			if ( !empty($iconColor) ){
				return '<div class="'.esc_attr($iconWrapper).'" style="background-color: '.esc_attr($aIcon['color']).'"><i class="'.esc_attr($aIcon['icon']).'"></i></div>';
			}else{
				return '<div class="'.esc_attr($iconWrapper).'"><i class="'.esc_attr($aIcon['icon']).'"></i></div>';
			}
		}
	}

	public static function getTermFeaturedImage($oTerm, $imgSize='large'){
		$featuredImgID = GetSettings::getTermMeta($oTerm->term_id, 'featured_image_id');
		if ( !empty($featuredImgID) ){
			$featuredImg = wp_get_attachment_image_url($featuredImgID, $imgSize);
		}

		if ( isset($featuredImg) && !empty($featuredImg) ){
			return $featuredImg;
		}

		$featuredImg = GetSettings::getTermMeta($oTerm->term_id, 'featured_image');

		if ( empty($featuredImg) ){
			$aThemeOptions = Wiloke::getThemeOptions();
			switch ($oTerm->taxonomy){
				case 'listing_location':
					if ( isset($aThemeOptions['listing_location_featured_image']) && isset($aThemeOptions['listing_location_featured_image']['id']) ){
						$featuredImg = wp_get_attachment_image_url($aThemeOptions['listing_location_featured_image']['id'], $imgSize);
					}
					break;
				case 'listing_cat':
					if ( isset($aThemeOptions['listing_cat_featured_image']) && isset($aThemeOptions['listing_cat_featured_image']['id']) ){
						$featuredImg = wp_get_attachment_image_url($aThemeOptions['listing_cat_featured_image']['id'], $imgSize);
					}
					break;
				case 'listing_tag':
					if ( isset($aThemeOptions['listing_tag_featured_image']) && isset($aThemeOptions['listing_tag_featured_image']['id']) ){
						$featuredImg = wp_get_attachment_image_url($aThemeOptions['listing_tag_featured_image']['id'], $imgSize);
					}
					break;
				default:
					if ( isset($aThemeOptions['listing_featured_image']) && isset($aThemeOptions['listing_featured_image']['id']) ){
						$featuredImg = wp_get_attachment_image_url($aThemeOptions['listing_featured_image']['id'], $imgSize);
					}
					break;
			}
		}

		return $featuredImg;
	}
}
<?php
/**
 * @package   The_Grid
 * @author    Themeone <themeone.master@gmail.com>
 * @copyright 2015 Themeone
 *
 * Skin: Kampala
 *
 */

// Exit if accessed directly
if (!defined('ABSPATH')) { 
	exit;
}

$tg_el = The_Grid_Elements();
$jv_el = JV_The_Grid_Elements(); // Get Javo Elements

$format = $tg_el->get_item_format();
$colors = $tg_el->get_colors();

$terms_args = array(
	'color' => 'color',
	'separator' => ', '
);

$media_args = array(
	'icons' => array(
		'image' => '<i class="tg-icon-add"></i>'
	)
);

if ($format == 'quote' || $format == 'link') {
	
	$bg_img = $tg_el->get_attachement_url();
	
	$output  = ($bg_img) ? '<div class="tg-item-image" style="background-image: url('.esc_url($bg_img).')"></div>' : null;
	$output .= $tg_el->get_content_wrapper_start();
		$output .= $tg_el->get_the_date();
		$output .= ($format == 'quote') ? $tg_el->get_the_quote_format() : $tg_el->get_the_link_format();
		$output .= '<div class="tg-item-footer">';
			$output .= '<i class="tg-'.$format.'-icon tg-icon-'.$format.'" style="color:'.$colors['content']['title'].'"></i>';
			$output .= $tg_el->get_the_likes_number();
		$output .= '</div>';
	$output .= $tg_el->get_content_wrapper_end();
	
	return $output;
		
} else {
	
	$output = null;

	$social = $tg_el->get_social_share_links();
	$social_button  = null;
	if (!empty($social)) {
		$social_button = '<div class="tg-item-share-holder">';
			$social_button .= '<div class="triangle-up-left" style="border-color:'.$colors['overlay']['background'].'"></div>';
			$social_button .= '<i class="tg-icon-reply" style="color:'.$colors['overlay']['title'].'"></i>';
			$social_button .= '<div class="tg-share-icons">';
				foreach ($social as $url) {
					$social_button .= $url;
				}
			$social_button .= '</div>';
		$social_button .= '</div>';
	}
	
	$decoration  = '<div class="tg-item-decoration">';
		$decoration .= '<div class="triangle-down-right" style="border-color:'.$colors['content']['background'].'"></div>';
	$decoration .= '</div>';
	
	$media_content = $tg_el->get_media();
	$media_button  = $tg_el->get_media_button($media_args);
	$link_button   = $tg_el->get_link_button();

	if ($media_content) {
		$output .= $tg_el->get_media_wrapper_start();
			$output .= $media_content;
			$output .= ($media_button) ? $tg_el->get_center_wrapper_start() : null;
				$output .= ($media_button && in_array($format, array('video', 'audio'))) ? $tg_el->get_overlay().$media_button : null;
				$output .= ($link_button && !in_array($format, array('video', 'audio'))) ? $tg_el->get_overlay().$link_button : null;
			$output .= ($media_button) ? $tg_el->get_center_wrapper_end() : null;
			$output .= ($media_button) ? $decoration : null;
			$output .= ($media_button) ? $social_button : null;
			$output .= $jv_el->get_jv_rating_ave(); // Jv rating
		$output .= $tg_el->get_media_wrapper_end();
	}
	
	$output .= $tg_el->get_content_wrapper_start();
		$output .= $tg_el->get_the_date();
		$output .= $tg_el->get_the_title();
		$output .= '<div class="tg-jv-tax-wrap">';
			$output .= $jv_el->get_jv_category();  // Jv Category
			$output .= $jv_el->get_jv_location();  // Jv Location
		$output .= '</div>';
		$output .= $tg_el->get_the_excerpt();
		$output .= '<div class="tg-item-footer">';
			$output .= preg_replace('/(<a\b[^><]*)>/i', '$1 style="color:'.$colors['content']['title'].'">', $tg_el->get_the_comments_number());
			$output .= $tg_el->get_the_likes_number();
		$output .= '</div>';
	$output .= $tg_el->get_content_wrapper_end();

	return $output;

}
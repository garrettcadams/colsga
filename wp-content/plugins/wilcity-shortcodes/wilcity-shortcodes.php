<?php
/*
 * Plugin Name: WilCity Shortcodes
 * Author: wiloke
 * Plugin URI: https://wiloke.com
 * Author URI: https://wiloke.com
 * Version: 1.2.2
 * Text Domain: wilcity-shortcodes
 * Domain Path: /languages/
 */

add_action('wiloke-listing-tools/run-extension', function(){
	define('WILCITY_SC_VERSION', '1.2.2');
	define('WILCITY_SC_CATEGORY', 'Wilcity');
	define('WILCITY_SC_DOMAIN', 'wilcity-shortcodes');
	define('WILCITY_SC_URL', plugin_dir_url(__FILE__));
	define('WILCITY_SC_DIR', plugin_dir_path(__FILE__));

	require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

	add_action( 'plugins_loaded', 'wilcity_sc_load_textdomain' );
	function wilcity_sc_load_textdomain() {
		load_plugin_textdomain( 'wilcity-shortcodes', false, basename(dirname(__FILE__)) . '/languages' );
	}

	function wilcitySCElClass($aAtts){
		$aClasses = array();
		if ( defined('KC_VERSION') ){
			$aClasses =  apply_filters( 'kc-el-class', $aAtts );
		}

		if ( function_exists('vc_shortcode_custom_css_class') ){
			$aAtts['css'] = isset($aAtts['css']) ? $aAtts['css'] : '';
			$aClasses = array(vc_shortcode_custom_css_class($aAtts['css'], ' '));
		}
		return $aClasses;
	}

	add_filter('wilcity-el-class', 'wilcitySCElClass');

	function wiloke_kc_process_tab_title($matches){

		if( !empty( $matches[0] ) ){
			global $wilcityHasActivatedTab;

			if ( empty($wilcityHasActivatedTab) ){
				$tabStatus = 'active';
				$wilcityHasActivatedTab = 'yes';
			}else{
				$tabStatus = '';
			}
			$atts = json_decode($matches[0]);

			$tab_atts = shortcode_parse_atts( $matches[0] );

			$title = ''; $adv_title = '';$tab_id='';
			if ( isset( $tab_atts['title'] ) ){
				$title = $tab_atts['title'];
			}

			if ( isset( $tab_atts['tab_id'] ) ){

				$tab_id = strtolower(trim($tab_atts['title']));

	            $tab_id = str_replace(array('&', 'amp;'), array('', ''), $tab_id);

                $tab_id = preg_replace_callback('/\s+/', function($aMatched){
                    return '-';
				}, $tab_id);

				$tab_atts['tab_id'] = $tab_id;
			}

			if( isset( $tab_atts['advanced'] ) && $tab_atts['advanced'] === 'yes' ){

				if( isset( $tab_atts['adv_title'] ) && !empty( $tab_atts['adv_title'] ) )
					$adv_title = base64_decode( $tab_atts['adv_title'] );

				$icon=$icon_class=$image=$image_id=$image_url=$image_thumbnail=$image_medium=$image_large=$image_full='';

				if( isset( $tab_atts['adv_icon'] ) && !empty( $tab_atts['adv_icon'] ) ){
					$icon_class = $tab_atts['adv_icon'];
					$icon = '<i class="'.$tab_atts['adv_icon'].'"></i>';
				}

				if( isset( $tab_atts['adv_image'] ) && !empty( $tab_atts['adv_image'] ) ){
					$image_id = $tab_atts['adv_image'];
					$image_url = wp_get_attachment_image_src( $image_id, 'full' );
					$image_medium = wp_get_attachment_image_src( $image_id, 'medium' );
					$image_large = wp_get_attachment_image_src( $image_id, 'large' );
					$image_thumbnail = wp_get_attachment_image_src( $image_id, 'thumbnail' );

					if( !empty( $image_url ) && isset( $image_url[0] ) ){
						$image_url = $image_url[0];
						$image_full = $image_url;
					}
					if( !empty( $image_medium ) && isset( $image_medium[0] ) )
						$image_medium = $image_medium[0];

					if( !empty( $image_large ) && isset( $image_large[0] ) )
						$image_large = $image_large[0];

					if( !empty( $image_thumbnail ) && isset( $image_thumbnail[0] ) )
						$image_thumbnail = $image_thumbnail[0];
					if( !empty( $image_url ) )
						$image = '<img src="'.$image_url.'" alt="" />';
				}

				$adv_title = str_replace( array( '{title}', '{icon}', '{icon_class}', '{image}', '{image_id}', '{image_url}', '{image_thumbnail}', '{image_medium}', '{image_large}', '{image_full}', '{tab_id}' ), array( $title, $icon, $icon_class, $image, $image_id, $image_url, $image_thumbnail, $image_medium, $image_large, $image_full, $tab_id ), $adv_title );

				echo '<li class="'.esc_attr($tabStatus).'">'.$adv_title.'</li>';

			}else{
				if( isset( $tab_atts['icon_option'] ) && $tab_atts['icon_option']  == 'yes' ){
					if(empty($tab_atts['icon']))
						$tab_atts['icon'] = 'fa-leaf';
					$title = '<i class="'.$tab_atts['icon'].'"></i> '.$title;
				}
				echo '<li><a class="'.esc_attr($tabStatus).'" href="#'.(isset($tab_atts['tab_id']) ? $tab_atts['tab_id'] : '').'" data-prevent="scroll">'.$title.'</a></li>';
			}

		}
		return $matches[0];
	}

	function wilcitySCSearchTerms($s, $taxonomy){
		global $wpdb;

		$termTaxonomyTbl = $wpdb->term_taxonomy;
		$termsTbl        = $wpdb->terms;

		$sql = "SELECT $termsTbl.term_id, $termsTbl.name FROM $termsTbl LEFT JOIN $termTaxonomyTbl ON ($termsTbl.term_id = $termTaxonomyTbl.term_id) WHERE $termTaxonomyTbl.taxonomy='".esc_sql($taxonomy)."' AND $termsTbl.name LIKE '%".esc_sql($s)."%' ORDER BY $termsTbl.term_id DESC LIMIT 100";

		$aRawTerms = $wpdb->get_results($sql);

		if ( empty($aRawTerms) ){
			return false;
		}

		$aTerms = array();
		foreach ($aRawTerms as $oTerm){
			$aTerms[] = $oTerm->term_id.':'.$oTerm->name;
		}

		return $aTerms;
	}
    
    if (!function_exists('wilcityiCarePlusAdvancedGridTemplatePath')) {
        function wilcityiCarePlusAdvancedGridTemplatePath()
        {
            global $kc;
            if (!function_exists('kc_add_map')) {
                return false;
            }
            $kc->set_template_path(plugin_dir_path(__FILE__).'kingcomposer-sc/');
        }
        
        add_action('init', 'wilcityiCarePlusAdvancedGridTemplatePath', 99);
    }
    
    if (!function_exists('kc_modify_listing_location_children_query')) {
        add_filter('kc_autocomplete_listing_location_children', 'kc_modify_listing_location_children_query');
        
        function kc_modify_listing_location_children_query($data)
        {
            $aTerms = wilcitySCSearchTerms($_POST['s'], 'listing_location');
            if (!$aTerms) {
                return false;
            }
            
            return ['Select Terms' => $aTerms];
        }
    }
    
    if (!function_exists('kc_modify_listing_cat_children_query')) {
        add_filter('kc_autocomplete_listing_cat_children', 'kc_modify_listing_cat_children_query');
        
        function kc_modify_listing_cat_children_query($data)
        {
            $aTerms = wilcitySCSearchTerms($_POST['s'], 'listing_cat');
            if (!$aTerms) {
                return false;
            }
            
            return ['Select Terms' => $aTerms];
        }
    }
    
    if ( !function_exists('kc_modify_listing_cats_query') ){
		add_filter( 'kc_autocomplete_listing_cats', 'kc_modify_listing_cats_query' );

		function kc_modify_listing_cats_query( $data ){
			$aTerms = wilcitySCSearchTerms($_POST['s'], 'listing_cat');
			if ( !$aTerms ){
				return false;
			}

			return array('Select Terms'=>$aTerms);
		}
	}

	if ( !function_exists('kc_modify_listing_locations_query') ){
		add_filter( 'kc_autocomplete_listing_locations', 'kc_modify_listing_locations_query' );

		function kc_modify_listing_locations_query( $data ){
			$aTerms = wilcitySCSearchTerms($_POST['s'], 'listing_location');
			if ( !$aTerms ){
				return false;
			}

			return array('Select Terms'=>$aTerms);
		}
	}

	if ( !function_exists('kc_modify_listing_tags_query') ){
		add_filter( 'kc_autocomplete_listing_tags', 'kc_modify_listing_tags_query' );

		function kc_modify_listing_tags_query( $data ){
			$aTerms = wilcitySCSearchTerms($_POST['s'], 'listing_tag');
			if ( !$aTerms ){
				return false;
			}

			return array('Select Terms'=>$aTerms);
		}
	}

	add_filter( 'kc_autocomplete_listing_ids', 'wilcityModifyListingIDsQuery' );
	// filter id: kc_autocomplete_{field-name}
	// in this case the field-name is "post-ids"

	function wilcityModifyListingIDsQuery( $aData ){
		$query = new WP_Query(
			array(
				'post_type' => $aData['post_type'],
				'posts_per_page' => 20,
				's' => $aData['s'],
				'post_status' => 'publish'
			)
		);
		$aListings = array();
		if ( $query->have_posts() ){
			while ($query->have_posts()){
				$query->the_post();
				$aListings[] = $query->post->ID.':'.$query->post->post_title;
			}
		}
		return array('Select Listings'=>$aListings);
	}

	function woSCConfiguration() {
		if (function_exists('kc_add_map'))
		{
			$aConfigurations = wilcityShortcodesRepository()->get('config:shortcodes');
			global $kc;

			$aSCConfiguration = array();

			foreach ($aConfigurations as $key => $aScItem){
				$aScItem['params']['general'] = array_merge($aScItem['params']['general'], array(
					array(
						'name'  => 'extra_class',
						'label' => 'Extra Class',
						'type'  => 'text',
						'admin_label' => true
					)
				));
				$aSCConfiguration[$key] = $aScItem;
			}

			$kc->add_map($aSCConfiguration);
		}
	}

	add_action('init', 'woSCConfiguration', 99 );

	require_once plugin_dir_path(__FILE__) . 'WilcityShortcodeRepository.php';

	function wilcityIncludeCoreFiles(){
		foreach (glob(plugin_dir_path(__FILE__) . 'default-sc/wilcity-*.php') as $filename) {
			include $filename;
		}

		foreach (glob(plugin_dir_path(__FILE__) . 'core/wilcity_*.php') as $filename) {
			include $filename;
		}

		foreach (glob(plugin_dir_path(__FILE__) . 'custom-field-content-sc/wilcity-*.php') as $filename) {
			include $filename;
		}
	}

	wilcityIncludeCoreFiles();

	function wilcityShortcodesRepository(){
		return new WilcityShortcodeRepository();
	}

	function wilcityFilterPostIDForListingTaxonomyPage($postID){
		$pageID = \WilokeListingTools\Framework\Helpers\GetSettings::isTaxonomyUsingCustomPage();
		if ( empty($pageID) ){
			return $postID;
		}
		return $pageID;
	}

	function wilcityAllowKCRenderOnTaxonomyPage($allow){
		return \WilokeListingTools\Framework\Helpers\GetSettings::isTaxonomyUsingCustomPage() ? true : $allow;
	}

	function wilcityFilterKCRawContentOnTaxonomyPage($post){
		if ( $pageID = \WilokeListingTools\Framework\Helpers\GetSettings::isTaxonomyUsingCustomPage() ){
			$post = get_post($pageID);
		}

		return $post;
	}

	add_filter('kc_get_dynamic_css', 'wilcityFilterPostIDForListingTaxonomyPage');
	add_filter('kc_allows', 'wilcityAllowKCRenderOnTaxonomyPage');
	add_filter('kc_raw_post', 'wilcityFilterKCRawContentOnTaxonomyPage');

	function wilcityAllowUsingKCIfIsTaxonomyPage($return ){
		return \WilokeListingTools\Framework\Helpers\GetSettings::isTaxonomyUsingCustomPage() ? true : $return;
	}

	add_filter('kc_is_using', 'wilcityAllowUsingKCIfIsTaxonomyPage');

	add_action('init', 'woSCSetSCTemplatePath', 99 );
	function woSCSetSCTemplatePath(){
		global $kc;
		if (!function_exists('kc_add_map')){
			return false;
		}
		$kc->set_template_path( WILCITY_SC_DIR . 'kingcomposer-sc/' );
	}

	require_once plugin_dir_path(__FILE__) . 'wilcity-general-shortcodes.php';
});

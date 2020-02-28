<?php

namespace WilokeListingTools\Register;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\Submission;

class RegisterPostTypes{
	public function __construct()
	{
		add_action('init', array($this, 'register'));
//		add_filter('save_post_listing', array($this, 'updateLatLongForEachListing'), 10, 2);
//		add_action('init', array($this, 'updateLatitudeLongitudeForEachListing'));
		add_action('init', array($this, 'addMoreListingPostTypes'), 1);
		add_filter('posts_where', array($this, 'editListTableQuery'), 10, 2);
		add_filter('display_post_states', array($this, 'addPostState'), 10, 2);
	}

	public function addPostState($aPostStates, $post){
		$aPostTypes = Submission::getSupportedPostTypes();
		if ( in_array($post->post_type, $aPostTypes) ){
			$aCustomStatus = wilokeListingToolsRepository()->get('posttypes:post_statuses');

			if ( isset($aCustomStatus[$post->post_status]) ){
				$aPostStates[] = $aCustomStatus[$post->post_status]['label'];
			}
		}

		return $aPostStates;
	}

	/**
	 * Adding custom post statuses for the listing type
	 * @since 1.0
	 */
	public function addMoreListingPostTypes(){
		foreach (wilokeListingToolsRepository()->get('posttypes:post_statuses') as $postStatus => $aConfig){
			register_post_status($postStatus, $aConfig);
		}
	}

	public function updateLatitudeLongitudeForEachListing(){
		if ( !get_option('wiloke_listgo_updated_latitude_and_longitude') ){
			$query = new \WP_Query(
				array(
					'post_type' => 'listing',
					'posts_per_page' => -1,
					'post_status' => 'publish'
				)
			);

			if ( $query->have_posts() ){
				while ($query->have_posts()){
					$query->the_post();
					$aSettings = get_post_meta($query->post->ID, 'listing_settings', true);
					if ( isset($aSettings['map']) && isset($aSettings['map']['latlong']) ){
						update_post_meta($query->post->ID, 'listgo_listing_latlong', $aSettings['map']['latlong']);
					}
				}
				wp_reset_postdata();
			}

			update_option('wiloke_listgo_updated_latitude_and_longitude', true);
		}
	}

	public function updateLatLongForEachListing($postID){
		if ( !current_user_can('edit_posts') ){
			return false;
		}

		if ( isset($_POST['listing_settings']) && isset($_POST['listing_settings']['map']['latlong']) ){
			update_post_meta($postID, 'listgo_listing_latlong', $_POST['listing_settings']['map']['latlong']);
		}
	}

	public function editListTableQuery($where, $q){
		if( is_admin()
		    && $q->is_main_query()
		    && !filter_input( INPUT_GET, 'post_status' )
		    && ( $oScreen = get_current_screen() ) instanceof \WP_Screen
		    && ('edit-listing' === $oScreen->id)
		    && ($oScreen->post_type === 'listing')
		){
			global $wpdb;
			$where .=" AND {$wpdb->posts}.post_status NOT IN ('expired', 'processing', 'temporary_close')";
		}

		return $where;
	}

	public function register()
	{
		$aPostTypePatterns = wilokeListingToolsRepository()->get('posttypes:post_types');
		$aCustomPostTypes = GetSettings::getOptions(wilokeListingToolsRepository()->get('addlisting:customPostTypesKey'));

		$aTaxonomies = wilokeListingToolsRepository()->get('posttypes:taxonomies');
		$aRegisteredPostType = array();

		if ( empty($aCustomPostTypes) ){
			foreach ( $aPostTypePatterns as $postType => $aArgs ){
				register_post_type($postType, $aArgs);
			}
		}else{
			foreach ($aCustomPostTypes as $aPostType){
				switch ($aPostType['key']){
					case 'event':
						if ( $aPostType['name'] !== 'Event' ){
							$aPostTypePatterns['event']['rest_base'] = $aPostType['key'] . 's';
							$aPostTypePatterns['event']['rewrite']['slug'] = $aPostType['slug'];

							$aPostTypePatterns['event'] = apply_filters('wilcity/filter/register-post-types/event', $aPostTypePatterns['event']);
						}

						$aPostTypePatterns['event']['labels']['name_admin_bar'] = $aPostType['name'];
						$aPostTypePatterns['event']['has_archive'] = true;
						$aPostTypePatterns['event']['labels']['name'] = $aPostType['name'];
						$aPostTypePatterns['event']['labels']['menu_name'] = $aPostType['name'];
						$aPostTypePatterns['event']['labels']['singular_name'] = $aPostType['singular_name'];
						$aPostTypePatterns['event']['labels']['all_items'] = $aPostType['name'];
						register_post_type('event', $aPostTypePatterns['event']);
						break;
					default:
						$aGeneratePostType = $aPostTypePatterns['listing'];
						foreach ($aPostTypePatterns['listing']['labels'] as $key => $label){
							if ( in_array($key, array('menu_name', 'name_admin_bar', 'name', 'all_items')) ){
								$aGeneratePostType['labels'][$key] = $aPostType['name'];
							}else{
								$aGeneratePostType['labels'][$key] =  isset($aPostType[$key]) ? $aPostType[$key] : $label;
							}
						}
						$aGeneratePostType['rest_base'] = $aPostType['key'] . 's';
						$aGeneratePostType['has_archive'] = true;
						$aGeneratePostType['rewrite']['slug'] = $aPostType['slug'];
						$aGeneratePostType = apply_filters('wilcity/filter/register-post-types/listings', $aGeneratePostType);
						$postTypeKey = apply_filters('wilcity/filter/register-post-types/post-type-key', $aPostType['key']);
						register_post_type($postTypeKey, $aGeneratePostType);

						if ( $aPostType['key'] !== 'listing' ){
							$aTaxonomies['listing_location']['post_types'][] = $aPostType['key'];
							$aTaxonomies['listing_cat']['post_types'][] = $aPostType['key'];
							$aTaxonomies['listing_tag']['post_types'][] = $aPostType['key'];
						}
						break;
				}

				$aRegisteredPostType[] = $aPostType['key'];
			}
		}
		foreach ($aPostTypePatterns as $postType => $aArgs){
			if ( empty($aRegisteredPostType) || !in_array($postType, $aRegisteredPostType) ){
				register_post_type($postType, $aArgs);
			}
		}

		$aThemeOptions = class_exists('\Wiloke') ? \Wiloke::getThemeOptions(true) : '';
		foreach ( $aTaxonomies as $tax => $aArgs ){
			if ( !empty($aThemeOptions) && isset($aThemeOptions[$tax.'_slug']) && !empty($aThemeOptions[$tax.'_slug']) ){
				$aArgs['rewrite']['slug'] = $aThemeOptions[$tax.'_slug'];
			}
			register_taxonomy($tax, $aArgs['post_types'], $aArgs);
		}
	}
}

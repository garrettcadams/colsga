<?php
namespace WilokeListingTools\Register;


use WilokeListingTools\Framework\Helpers\FileSystem;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\Inc;
use WilokeListingTools\Framework\Helpers\SetSettings;

class RegisterSubmenuQuickSearchForm {
	use ListingToolsGeneralConfig;

	public $postCachingFileName = 'search-posts-caching.txt';
	public $termCachingFileName = 'search-terms-caching.txt';

	public $slug = 'quick-search-form';
	public $aPostTypeSettings = array();

	public function __construct() {
		add_action('admin_menu', array($this, 'register'));
		add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
		add_action('wp_ajax_wiloke_save_quick_search_form', array($this, 'saveSettings'));
		add_action('wp_insert_post', array($this, 'updateCache'), 10, 2);
		add_action('post_updated', array($this, 'updateCache'), 10, 2);
		add_action('edited_listing_cat', array($this, 'updateTerm'), 10, 1);
		add_action('delete_listing_cat', array($this, 'deleteTerm'), 10, 1);
		add_action('created_listing_cat', array($this, 'updateTerm'), 10, 1);
		add_action('before_delete_post', array($this, 'deletePost'), 10, 1);
		add_action('wilcity/wilcity-import/successfully', array($this, 'setDefaultSettings'));
	}

	public function termSkeleton($oTerm){
		return array(
			'termName'  => 	$oTerm->name,
			'ID'        => $oTerm->term_id,
			'termLink'  => get_term_link($oTerm->term_id),
			'oIcon'     => \WilokeHelpers::getTermOriginalIcon($oTerm)
		);
	}

	public function deleteTerm($termID){
		if ( !is_admin() ){
			return false;
		}

		$aCache = FileSystem::fileGetContents($this->termCachingFileName);
		$aCache = json_decode($aCache, true);
		if ( !empty($aCache) ){
			if ( isset($aCache[$termID]) ){
				unset($aCache[$termID]);
			}
			FileSystem::filePutContents($this->termCachingFileName, json_encode($aCache));
		}
	}

	public function updateTerm($termID){
		if ( !is_admin() ){
			return false;
		}

		$oTerm = get_term_by('id', $termID, 'listing_cat');

		$aCache = FileSystem::fileGetContents($this->termCachingFileName);
		$aCache = json_decode($aCache, true);
		if ( empty($aCache) ){
			$aCache[$oTerm->term_id] = $this->termSkeleton($oTerm);
		}else{
			if ( in_array($oTerm->term_id, $aCache) ){
				$aCache[$oTerm->term_id] = $this->termSkeleton($oTerm);
			}else{
				$aCache[$oTerm->term_id] = $this->termSkeleton($oTerm);
			}
		}
		FileSystem::filePutContents($this->termCachingFileName, json_encode($aCache));
	}

	public function postSkeleton($post){
		$oPostTypeObject = get_post_type_object( $post->post_type );

		$aPostTypeSetting = $this->getPostTypeSetting($post->post_type);

		$aPost = array(
			'postTitle' => get_the_title($post->ID),
			'postType'  => $post->post_type,
			'postLink'  => get_post_permalink($post->ID),
			'thumbnail' => get_the_post_thumbnail_url($post->ID, 'thumbnail'),
			'thumbnailLarge' => get_the_post_thumbnail_url($post->ID, 'large'),
			'logo'      => GetSettings::getPostMeta($post->ID, 'logo'),
			'name'      => $oPostTypeObject->labels->name,
			'singularName' => $oPostTypeObject->labels->singular_name,
			'groupIcon'   => isset($aPostTypeSetting['icon']) ? $aPostTypeSetting['icon'] : '',
			'groupIconColor' => isset($aPostTypeSetting['addListingLabelBg']) ? $aPostTypeSetting['addListingLabelBg'] : ''
		);

		$oTerm = GetSettings::getLastPostTerm($post->ID, 'listing_cat');
		if ( $oTerm ){
			$aPost['oIcon'] = \WilokeHelpers::getTermOriginalIcon($oTerm);
		}else{
			$aPost['oIcon'] = false;
		}
		return $aPost;
	}

	public function deletePost($postID){
		$aOptions = GetSettings::getOptions('quick_search_form_settings');
		$oAfter = get_post($postID);

		if ( empty($aOptions) || !isset($aOptions['post_types']) ){
			return false;
		}

		if ( !in_array($oAfter->post_type, $aOptions['post_types']) ){
			return false;
		}

		$aCacheData = FileSystem::fileGetContents($this->postCachingFileName);
		if ( empty($aCacheData) ){
			return false;
		}

		$aCacheData = json_decode($aCacheData, true);
		if ( !empty($aCacheData) ){
			unset($aCacheData[$postID]);
			FileSystem::filePutContents($this->postCachingFileName, json_encode($aCacheData));
		}
	}

	public function updateCache($postID, $oAfter){
		$aOptions = GetSettings::getOptions('quick_search_form_settings');

		if ( empty($aOptions) || !isset($aOptions['post_types']) ){
			return false;
		}

		if ( !in_array($oAfter->post_type, $aOptions['post_types']) ){
			return false;
		}

		$aCacheData = FileSystem::fileGetContents($this->postCachingFileName);

		if ( $oAfter->post_status == 'publish' ){
			$aCacheData = json_decode($aCacheData, true);
			$aPost = $this->postSkeleton($oAfter);
			$aCacheData[$postID] = $aPost;
			FileSystem::filePutContents($this->postCachingFileName, json_encode($aCacheData));
		}else{
			$aCacheData = json_decode($aCacheData, true);
			if ( !empty($aCacheData) ){
				unset($aCacheData[$postID]);
				FileSystem::filePutContents($this->postCachingFileName, json_encode($aCacheData));
			}
		}
	}

	public function getPostTypeSetting($postType){
		if ( isset($this->aPostTypeSettings[$postType]) ){
			return $this->aPostTypeSettings[$postType];
		}

		$aPostTypeSettings = GetSettings::getOptions(wilokeListingToolsRepository()->get('addlisting:customPostTypesKey'));

		foreach ($aPostTypeSettings as $aPostTypeSetting){
			if ( $aPostTypeSetting['key'] == $postType ){
				$this->aPostTypeSettings[$postType] = $aPostTypeSetting;
				return $this->aPostTypeSettings[$postType];
			}
		}
	}

	public function setDefaultSettings(){
		SetSettings::setOptions('quick_search_form_settings', array(
			'toggle_quick_search_form' => 'yes',
			'taxonomy_suggestion' => 'listing_cat',
			'taxonomy_suggestion_title' => 'Categories',
			'number_of_term_suggestions' => 6,
			'exclude_post_types' => array(),
			'suggestion_order_by' => 'count',
			'suggestion_order' => 'DESC'
		));
	}

	public function saveSettings(){
		if ( !current_user_can('edit_theme_options') ){
			wp_send_json_error(array(
				'msg' => esc_html__('You do not have permission to access this page', 'wiloke-listing-tools')
			));
		}
		$aSettings = array();
		foreach ($_POST['aFields'] as $aField){
			$aSettings[$aField['key']] = $aField['value'];
		}

		if ( $_POST['page'] == 1 ){
			SetSettings::setOptions('quick_search_form_settings', $aSettings);

			$aTerms = get_terms(array(
				'taxonomy'      => $aSettings['taxonomy_suggestion'],
				'count'         => $aSettings['number_of_term_suggestions'],
				'hide_empty'    => false
			));
			if ( !empty($aTerms) && !is_wp_error($aTerms) ){
				$aCacheTerms = array();
				foreach ($aTerms as $oTerm){
					$aCacheTerms[$oTerm->term_id] = $this->termSkeleton($oTerm);
				}

				FileSystem::filePutContents($this->termCachingFileName, json_encode($aCacheTerms));
			}
		}

		$query = new \WP_Query(array(
			'post_type'      => $aSettings['post_types'],
			'post_status'    => 'publish',
			'posts_per_page' => 100,
			'paged'          => abs($_POST['page']),
			'orderby'        => $aSettings['orderby'],
			'order'          => $aSettings['order']
		));

		$aPosts = array();
		if ( $query->have_posts() ){
			global $post;
			while ($query->have_posts()){
				$query->the_post();
				$aPosts[$post->ID] = $this->postSkeleton($post);
			}
			$aOldCache = array();

			if ( $_POST['page'] != 1 ){
				$content = FileSystem::fileGetContents('search-posts-caching.txt', true);
				if ( $content ){
					$aOldCache = json_decode($content, true);
				}

				if ( !empty($aOldCache) ){
					$aPosts = $aPosts + $aOldCache;
				}
			}
			$status = FileSystem::filePutContents('search-posts-caching.txt', json_encode($aPosts));

			if ( !$status ){
				wp_send_json_error(
					array(
						'msg' => 'Oops! Your server does not allow to write the file. Please set 755 permission tp wp-folder'
					)
				);
			}
			wp_reset_postdata();
		}else{
			if ( $_POST['page'] != 1 ){
				wp_send_json_success(array(
					'msg' => 'Congrats! the setting has been updated successfully.'
				));

			}else{
				wp_send_json_error(
					array(
						'msg' => 'There are no posts. Please create 1 post at least'
					)
				);
			}
		}

		wp_send_json_success(array(
			'continue' => 'yes'
		));
	}

	public function enqueueScripts($hook){
		if ( strpos($hook, $this->slug) === false ){
			return false;
		}
		$this->requiredScripts();
		$this->generalScripts();

		wp_enqueue_script('wiloke-quick-search-form', WILOKE_LISTING_TOOL_URL . 'admin/source/js/quick-search-form-script.js', array('jquery'), WILOKE_LISTING_TOOL_VERSION, true);

		$aValues = GetSettings::getOptions('quick_search_form_settings');
		$aSettings = wilokeListingToolsRepository()->get('quick-searchform');

		if ( !empty($aValues) ){
			foreach ($aSettings as $key => $aField){
				if ( isset($aValues[$aField['key']]) ){
					$aSettings[$key]['value'] = $aValues[$aField['key']];
				}
			}
		}

		wp_localize_script('wiloke-quick-search-form', 'WILOKE_QUICK_SEARCH_FORM',
			array(
				'aFields' => $aSettings
			)
		);
	}

	public function showQuickSearchForm(){
		Inc::file('quick-search-form:index');
	}

	public function register(){
		add_submenu_page($this->parentSlug, 'Quick Search Form', 'Quick Search Form', 'administrator', $this->slug, array($this, 'showQuickSearchForm'));
	}
}
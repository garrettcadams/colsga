<?php

namespace WilokeListingTools\Register;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\Inc;
use WilokeListingTools\Framework\Helpers\SetSettings;

class AddCustomPostType {
	use ListingToolsGeneralConfig;

	public $slug = 'add-post-type';

	public function __construct() {
		add_action('admin_menu', array($this, 'register'));
		add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
		add_action('wp_ajax_wiloke_save_custom_posttypes', array($this, 'saveCustomPostTypes'));
		add_action('admin_init', array($this, 'init'));
	}

	public function init(){
		$aCustomPostTypes = GetSettings::getOptions(wilokeListingToolsRepository()->get('addlisting:customPostTypesKey'));

		if ( empty($aCustomPostTypes) || !is_array($aCustomPostTypes) ){
			$this->setDefault();
		}
	}

	public function setDefault(){
		$aCustomPostTypes = array(
			array(
				'key'               => 'listing',
				'slug'              => 'listing',
				'singular_name'     => 'Listing',
				'name'              => 'Listings',
				'addListingLabel'   => 'Add Listing',
				'addListingLabelBg' => '#f06292',
				'deleteAble'        => 'no',
				'keyEditAble'       => 'no',
				'icon'              => ''
			),
			array(
				'key'               => 'event',
				'slug'              => 'event',
				'name'              => 'Events',
				'singular_name'     => 'Event',
				'addListingLabelBg' => '#3ece7e',
				'addListingLabel'   => 'Add Event',
				'deleteAble'        => 'no',
				'keyEditAble'       => 'no',
				'icon'              => ''
			)
		);

		SetSettings::setOptions(wilokeListingToolsRepository()->get('addlisting:customPostTypesKey'), $aCustomPostTypes);
	}

	public function getValue(){
		$aCustomPostTypes = GetSettings::getOptions(wilokeListingToolsRepository()->get('addlisting:customPostTypesKey'));
		if ( empty($aCustomPostTypes) ){
			$this->setDefault();
		}else{
			foreach ($aCustomPostTypes as $key => $aCustomPostType){
				$aCustomPostTypes[$key]['keyEditAble'] = 'no';
			}
		}
		return $aCustomPostTypes;
	}

	public function saveCustomPostTypes(){
		if ( !current_user_can('administrator') ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('You do not permission to access this page', 'wiloke-listing-tools')
				)
			);
		}

		if ( empty($_POST['data']) ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('There are no post types', 'wiloke-listing-tools')
				)
			);
		}

		$aPostTypes =  array();
		foreach ($_POST['data'] as $order => $aData){
			foreach ($aData as $k => $val){
				if ( empty($val) ){
					continue;
				}

				if ( $k == 'slug' || $k == 'key' ){
					$val = strtolower($val);
				}

				$aPostTypes[sanitize_text_field($order)][sanitize_text_field($k)] = sanitize_text_field($val);
			}
		}

		SetSettings::setOptions(wilokeListingToolsRepository()->get('addlisting:customPostTypesKey'), $aPostTypes);
		wp_send_json_success(array(
			'msg' => 'Congratulations! The post type has been added to your site. Now, From the admin sidebar, click on Settings -> Permalinks -> Re-save Post name to update the re-write rule. To setup the plans for this post type, please click on Listing Plans -> Add new -> Create some plans -> Then click on Wiloke Submission -> Add the plans to this post type field.'
		));
	}

	public function register(){
		add_submenu_page($this->parentSlug, 'Add Directory Type', 'Add Directory Type', 'edit_theme_options', $this->slug, array($this, 'settings'));
	}

	public function settings(){
		Inc::file('add-custom-posttype:index');
	}

	public function enqueueScripts($hook){
		if ( strpos($hook, $this->slug) === false ){
			return false;
		}

		$aCustomPostTypes = $this->getValue();
		$this->generalScripts();
		$this->requiredScripts();
		$this->draggable();

		wp_enqueue_script('spectrum');
		wp_enqueue_style('spectrum');

		wp_enqueue_script('add-custom-posttype', WILOKE_LISTING_TOOL_URL . 'admin/source/js/add-custom-posttype.js', array('jquery'), WILOKE_LISTING_TOOL_VERSION, true);
		wp_localize_script('add-custom-posttype', 'WILOKE_CUSTOM_POSTTYPES', $aCustomPostTypes);
	}
}
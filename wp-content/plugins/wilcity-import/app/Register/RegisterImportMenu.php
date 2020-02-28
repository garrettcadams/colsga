<?php
namespace WILCITY_IMPORT\Register;

use Elementor\Controls_Stack;
use Elementor\Core\Settings\Page\Model;
use Elementor\Plugin;
use WilokeListingTools\AlterTable\AlterTableViewStatistic;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\SetSettings;

class RegisterImportMenu {
	public $slug = 'wilcity-welcome';
	public $firstTimeSetup = 'wilcity_is_firstime_setup';
	protected $importDir = 'dummy';
	protected $ds = '/';

	public $aHomePages = array(
		'home-static' => 'Home Static',
		'home-slider' => 'Home Slider',
		'home-video'  => 'Home Video',
		'home-light'  => 'Home Light',
	);

	public $aPageBuilders = array(
		'kc' => 'King Composer',
		'elementor' => 'Elementor',
		'vc' => 'Visual Composer'
	);

	protected $pageBuilder = '';

	protected $isPassedMemoryCheck = false;

	protected $aMenuCollection = array('Discover', 'Company', 'wilMenu');
	protected $menuName = 'wilMenu';
	protected $menuLocation = 'wilcity_menu';

	public function __construct() {
		add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
		add_action('init', array($this, 'register'));
		add_action('admin_init', array($this, 'redirectToSetup'));
		add_action('wp_ajax_wilcity_importing_demo', array($this, 'runSetup'));
	}

	protected function checkMemory(){
		if ( $this->isPassedMemoryCheck ){
			return true;
		}

		$postMaxUploadSize = ini_get('post_max_size');
		$postMaxUploadSize = str_replace('M', '', $postMaxUploadSize);
		$postMaxUploadSize = absint($postMaxUploadSize);
		if ( $postMaxUploadSize < 5 ){
			return false;
		}

		$uploadMaxFileSize = ini_get('upload_max_filesize');
		$uploadMaxFileSize = str_replace('M', '', $uploadMaxFileSize);
		$uploadMaxFileSize = absint($uploadMaxFileSize);
		if ( $uploadMaxFileSize < 5 ){
			return false;
		}

		$this->isPassedMemoryCheck = true;
		return true;
	}

	private function searchPageByTemplate($templateName){
		global $wpdb;
		$pageID = $wpdb->get_var(
			$wpdb->prepare("SELECT post_id from $wpdb->postmeta WHERE meta_value=%s AND meta_key='_wp_page_template'", $templateName)
		);

		return $pageID;
	}

	private function setupWilokeSubmission($pageBuilder){
		$aConfigs = wilokeListingToolsRepository()->get('submission-pages');

		$aResponse = array();
		$aWilokeSubmission = GetWilokeSubmission::getAll();
		$hasUpdated = false;

		foreach ($aConfigs as $aPage){
			$check = isset($aWilokeSubmission[$aPage['key']]) ? $aWilokeSubmission[$aPage['key']] : '';
			if ( !empty($check) ){
				if ( get_post_status($check) == 'publish' ){
					continue;
				}
			}

			$postID = $this->searchPageByTemplate($aPage['template']);
			if ( empty($postID) ){
				$postID = wp_insert_post(array(
					'post_title'    => $aPage['title'],
					'post_content'  => $pageBuilder == 'kc' ? $aPage['content'] : '',
					'post_status'   => 'publish',
					'post_type'     => 'page'
				));

				if ( empty($postID) || is_wp_error($postID) ){
					$aResponse[] = array(
						'status' => 'error',
						'msg'    => 'We could not create '.$aPage['title']
					);
				}else{
					if ( !empty($aPage['template']) ){
						update_post_meta($postID,'_wp_page_template', $aPage['template']);
					}
					$aWilokeSubmission[$aPage['key']] = $postID;
					$hasUpdated = true;
				}
			}else{
				$aWilokeSubmission[$aPage['key']] = $postID;
				$hasUpdated = true;
			}

		}

		if ( $hasUpdated ){
			$aConfigs = wilokeListingToolsRepository()->get('wiloke-submission:configuration', true)->sub('fields');

			foreach ($aConfigs  as $aField){
				if ( isset($aField['name']) && isset($aField['id']) && isset($aField['default']) ){
					$fieldKey = str_replace('wilcity_submission_', '', $aField['id']);

					if ( !isset($aWilokeSubmission[$fieldKey]) ){
						$aWilokeSubmission[$fieldKey] = $aField['default'];
					}
				}
			}
			unset($aWilokeSubmission['wiloke_submission_toggle']);
			update_option('wiloke_submission_configuration', maybe_serialize($aWilokeSubmission));
		}
	}

	public function canUnZip(){
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		$fields = array( 'action', '_wp_http_referer', '_wpnonce' );
		$canUnZip = false;
		if ( false !== ( $creds = request_filesystem_credentials( '', '', false, false, $fields ) ) ) {

			if ( ! WP_Filesystem( $creds ) ) {
				request_filesystem_credentials( wp_nonce_url(admin_url('admin.php?page='.$this->slug)), '', true, false, $fields ); // Setup WP_Filesystem.
			}else{
				$canUnZip = true;
			}
		}

		return $canUnZip;
	}

	protected function _importXML($file, $isAttachment=true){
		if ( !class_exists('\Wiloke_Import') ){
			require_once plugin_dir_path(__FILE__) . 'wordpress-importer/wordpress-importer.php';
		}

		$oWPImport = new \Wiloke_Import;
		try {
			ob_start();
			$oWPImport->fetch_attachments = $isAttachment;
			$oWPImport->import($file);
			$res = ob_get_contents();
			ob_clean();
		} catch (\Exception $e) {
			wp_send_json_error(array(
				'msg' => 'There was an error while importing. Please click Import button again to continue importing the demo'
			));
		}

		return ($res !== 404);
	}

	public function installedMsg($name, $isSuccess=true){
		return !$isSuccess ? sprintf(esc_html__('We could not found %s plugin. Maybe the plugin has been removed'), $name) : sprintf(esc_html__('%s plugin has been installed'), $name);
	}

	/**
	 * Retrieve the download URL for a WP repo package.
	 *
	 * @since 2.5.0
	 *
	 * @param string $slug Plugin slug.
	 * @return string Plugin download URL.
	 */
	protected function _getWPRepoDownloadUrl( $slug ) {
		$source = '';
		$api    = $this->_getPluginsAPI( $slug );
		if ( !is_wp_error($api) || false !== $api && isset( $api->download_link ) ) {
			$source = $api->download_link;
		}

		return $source;
	}

	protected function _unZipFile($package, $isLive=false){
		WP_Filesystem();
		$status = unzip_file( $package,  ABSPATH.'/wp-content/plugins');
		if ( $isLive ){
			@unlink($package);
		}
		if ( $status ){
			return true;
		}

		return false;
	}

	protected function _installPlugin($slug){
		$downloadLink = $this->_getWPRepoDownloadUrl($slug);
		if ( empty($downloadLink) ){
			return false;
		}
		$package = download_url( $downloadLink, 18000 );
		if (is_wp_error($package)){
			return false;
		}

		return $this->_unZipFile($package, true);
	}

	protected function _installFromWilokeServer($downloadLink){
		if ( strpos($downloadLink, 'https://goo') !== false ){
			$package = download_url( $downloadLink, 18000 );
			if (is_wp_error($package)){
				return false;
			}
			return $this->_unZipFile($package, true);
		}

		return false;
	}

	protected function getAvailableWidgets(){
		global $wp_registered_widget_controls;
		$widget_controls = $wp_registered_widget_controls;
		$available_widgets = array();
		foreach ( $widget_controls as $widget ) {
			if ( ! empty( $widget['id_base'] ) && ! isset( $available_widgets[$widget['id_base']] ) ) { // no dupes
				$available_widgets[$widget['id_base']]['id_base'] = $widget['id_base'];
				$available_widgets[$widget['id_base']]['name'] = $widget['name'];
			}
		}
		return apply_filters( 'wie_available_widgets', $available_widgets );
	}

	protected function importingWidgets($data){
		global $wp_registered_sidebars;
		$data = json_decode($data);

		// Have valid data?
		// If no data or could not decode
		if ( empty($data) || ! is_object($data) ) {
			return true;
		}

		// Hook before import
		do_action( 'wie_before_import' );
		$data = apply_filters( 'wie_import_data', $data );

		// Get all available widgets site supports
		$available_widgets = $this->getAvailableWidgets();

		// Get all existing widget instances
		$widget_instances = array();
		foreach ( $available_widgets as $widget_data ) {
			$widget_instances[$widget_data['id_base']] = get_option( 'widget_' . $widget_data['id_base'] );
		}

		// Begin results
		$results = array();
		$widget_message_type = 'success';
		$widget_message = '';

		// Loop import data's sidebars
		foreach ( $data as $sidebar_id => $widgets ) {

			// Skip inactive widgets
			// (should not be in export file)
			if ( 'wp_inactive_widgets' == $sidebar_id ) {
				continue;
			}

			// Check if sidebar is available on this site
			// Otherwise add widgets to inactive, and say so
			if ( isset( $wp_registered_sidebars[$sidebar_id] ) ) {
				$sidebar_available = true;
				$use_sidebar_id = $sidebar_id;
				$sidebar_message_type = 'success';
				$sidebar_message = '';
			} else {
				$sidebar_available = false;
				$use_sidebar_id = 'wp_inactive_widgets'; // add to inactive if sidebar does not exist in theme
				$sidebar_message_type = 'error';
				$sidebar_message = esc_html__( 'Widget area does not exist in theme (using Inactive)', 'wiloke' );
			}

			// Result for sidebar
			$results[$sidebar_id]['name'] = ! empty( $wp_registered_sidebars[$sidebar_id]['name'] ) ? $wp_registered_sidebars[$sidebar_id]['name'] : $sidebar_id; // sidebar name if theme supports it; otherwise ID
			$results[$sidebar_id]['message_type'] = $sidebar_message_type;
			$results[$sidebar_id]['message'] = $sidebar_message;
			$results[$sidebar_id]['widgets'] = array();

			// Loop widgets
			foreach ( $widgets as $widget_instance_id => $widget ) {
				$fail = false;

				// Get id_base (remove -# from end) and instance ID number
				$id_base = preg_replace( '/-[0-9]+$/', '', $widget_instance_id );
				$instance_id_number = str_replace( $id_base . '-', '', $widget_instance_id );

				// Does site support this widget?
				if ( ! $fail && ! isset( $available_widgets[$id_base] ) ) {
					$fail = true;
					$widget_message_type = 'error';
					$widget_message = esc_html__( 'Site does not support widget', 'wiloke' ); // explain why widget not imported
				}
				$widget = apply_filters( 'wie_widget_settings', $widget ); // object
				$widget = json_decode( wp_json_encode( $widget ), true );

				$widget = apply_filters( 'wie_widget_settings_array', $widget );

				// Does widget with identical settings already exist in same sidebar?
				if ( ! $fail && isset( $widget_instances[$id_base] ) ) {

					// Get existing widgets in this sidebar
					$sidebars_widgets = get_option( 'sidebars_widgets' );
					$sidebar_widgets = isset( $sidebars_widgets[$use_sidebar_id] ) ? $sidebars_widgets[$use_sidebar_id] : array(); // check Inactive if that's where will go

					// Loop widgets with ID base
					$single_widget_instances = ! empty( $widget_instances[$id_base] ) ? $widget_instances[$id_base] : array();
					foreach ( $single_widget_instances as $check_id => $check_widget ) {

						// Is widget in same sidebar and has identical settings?
						if ( in_array( "$id_base-$check_id", $sidebar_widgets ) && (array) $widget == $check_widget ) {
							$fail = true;
							$widget_message_type = 'warning';
							$widget_message = esc_html__( 'Widget already exists', 'wiloke' ); // explain why widget not imported
							break;
						}
					}

				}

				// No failure
				if ( ! $fail ) {
					// Add widget instance
					$single_widget_instances = get_option( 'widget_' . $id_base ); // all instances for that widget ID base, get fresh every time
					$single_widget_instances = ! empty( $single_widget_instances ) ? maybe_unserialize($single_widget_instances) : array( '_multiwidget' => 1 ); // start fresh if have to

					$single_widget_instances[] = $widget; // add it

					// Get the key it was given
					end( $single_widget_instances );
					$new_instance_id_number = key( $single_widget_instances );

					// If key is 0, make it 1
					// When 0, an issue can occur where adding a widget causes data from other widget to load, and the widget doesn't stick (reload wipes it)
					if ( '0' === strval( $new_instance_id_number ) ) {
						$new_instance_id_number = 1;
						$single_widget_instances[$new_instance_id_number] = $single_widget_instances[0];
						unset( $single_widget_instances[0] );
					}

					// Move _multiwidget to end of array for uniformity
					if ( isset( $single_widget_instances['_multiwidget'] ) ) {
						$multiwidget = $single_widget_instances['_multiwidget'];
						unset( $single_widget_instances['_multiwidget'] );
						$single_widget_instances['_multiwidget'] = $multiwidget;
					}

					// Update option with new widget
					update_option( 'widget_' . $id_base, $single_widget_instances );

					// Assign widget instance to sidebar
					$sidebars_widgets = get_option( 'sidebars_widgets' ); // which sidebars have which widgets, get fresh every time

					// Avoid rarely fatal error when the option is an empty string
					// https://github.com/churchthemes/widget-importer-exporter/pull/11
					if ( ! $sidebars_widgets ) {
						$sidebars_widgets = array();
					}

					$new_instance_id = $id_base . '-' . $new_instance_id_number; // use ID number from new widget instance
					$sidebars_widgets[$use_sidebar_id][] = $new_instance_id; // add new instance to sidebar
					update_option( 'sidebars_widgets', $sidebars_widgets ); // save the amended data

					// After widget import action
					$after_widget_import = array(
						'sidebar'           => $use_sidebar_id,
						'sidebar_old'       => $sidebar_id,
						'widget'            => $widget,
						'widget_type'       => $id_base,
						'widget_id'         => $new_instance_id,
						'widget_id_old'     => $widget_instance_id,
						'widget_id_num'     => $new_instance_id_number,
						'widget_id_num_old' => $instance_id_number
					);
					do_action( 'wie_after_widget_import', $after_widget_import );

					// Success message
					if ( $sidebar_available ) {
						$widget_message_type = 'success';
						$widget_message = esc_html__( 'Imported', 'wiloke' );
					} else {
						$widget_message_type = 'warning';
						$widget_message = esc_html__( 'Imported to Inactive', 'wiloke' );
					}

				}

				// Result for widget instance
				$results[$sidebar_id]['widgets'][$widget_instance_id]['name'] = isset( $available_widgets[$id_base]['name'] ) ? $available_widgets[$id_base]['name'] : $id_base; // widget name or ID if name not available (not supported by site)
				$results[$sidebar_id]['widgets'][$widget_instance_id]['title'] = ! empty( $widget['title'] ) ? $widget['title'] : esc_html__( 'No Title', 'wiloke' ); // show "No Title" if widget instance is untitled
				$results[$sidebar_id]['widgets'][$widget_instance_id]['message_type'] = $widget_message_type;
				$results[$sidebar_id]['widgets'][$widget_instance_id]['message'] = $widget_message;
			}

		}

		return apply_filters( 'wie_import_results', $results );
	}

	protected function cleanHelloWorld(){
		global $wpdb;
		$postsTbl = $wpdb->prefix . 'posts';
		$postID = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM $postsTbl WHERE post_title=%s",
				'Hello World'
			)
		);
		wp_delete_post($postID, true);
	}

	protected function reUpdateTermParent($aData){
		$aData = json_decode($aData, true);
		if ( empty($aData) ){
			return false;
		}

		foreach ( $aData as $taxonomyName => $aInfo ){
			foreach ( $aInfo as $parentSlug => $aChildrenSlug ){
				$oParentTerm = get_term_by('slug', $parentSlug, $taxonomyName);

				if ( empty($oParentTerm) || is_wp_error($oParentTerm) ){
					continue;
				}

				foreach ($aChildrenSlug as $childSlug){
					$oChildInfo = get_term_by('slug', $childSlug, $taxonomyName);
					if ( empty($oChildInfo) || is_wp_error($oChildInfo) ){
						continue;
					}

					wp_update_term($oChildInfo->term_id, $taxonomyName, array(
						'parent' => $oParentTerm->term_id
					));
				}
			}
		}

		return true;
	}

	protected function setupVCRoles(){
		$settings = '{"administrator":"{\\\"post_types\\\":{\\\"_state\\\":\\\"custom\\\",\\\"post\\\":\\\"0\\\",\\\"page\\\":\\\"1\\\",\\\"listing\\\":\\\"0\\\",\\\"testimonial\\\":\\\"0\\\",\\\"event\\\":\\\"0\\\",\\\"pricing\\\":\\\"0\\\",\\\"event-pricing\\\":\\\"0\\\",\\\"discount\\\":\\\"0\\\",\\\"review\\\":\\\"0\\\",\\\"wiloke-menu\\\":\\\"1\\\",\\\"wiloke-menu-item\\\":\\\"1\\\"},\\\"backend_editor\\\":{\\\"_state\\\":\\\"1\\\",\\\"disabled_ce_editor\\\":\\\"0\\\"},\\\"frontend_editor\\\":{\\\"_state\\\":\\\"1\\\"},\\\"post_settings\\\":{\\\"_state\\\":\\\"1\\\"},\\\"settings\\\":{\\\"_state\\\":\\\"1\\\"},\\\"templates\\\":{\\\"_state\\\":\\\"1\\\"},\\\"shortcodes\\\":{\\\"_state\\\":\\\"1\\\"},\\\"grid_builder\\\":{\\\"_state\\\":\\\"1\\\"},\\\"presets\\\":{\\\"_state\\\":\\\"1\\\"},\\\"dragndrop\\\":{\\\"_state\\\":\\\"1\\\"}}","editor":"{\\\"post_types\\\":{\\\"_state\\\":\\\"1\\\"},\\\"backend_editor\\\":{\\\"_state\\\":\\\"1\\\",\\\"disabled_ce_editor\\\":\\\"0\\\"},\\\"frontend_editor\\\":{\\\"_state\\\":\\\"1\\\"},\\\"post_settings\\\":{\\\"_state\\\":\\\"1\\\"},\\\"templates\\\":{\\\"_state\\\":\\\"1\\\"},\\\"shortcodes\\\":{\\\"_state\\\":\\\"1\\\"},\\\"grid_builder\\\":{\\\"_state\\\":\\\"1\\\"},\\\"presets\\\":{\\\"_state\\\":\\\"1\\\"},\\\"dragndrop\\\":{\\\"_state\\\":\\\"1\\\"}}","author":"{\\\"post_types\\\":{\\\"_state\\\":\\\"1\\\"},\\\"backend_editor\\\":{\\\"_state\\\":\\\"1\\\",\\\"disabled_ce_editor\\\":\\\"0\\\"},\\\"frontend_editor\\\":{\\\"_state\\\":\\\"1\\\"},\\\"post_settings\\\":{\\\"_state\\\":\\\"1\\\"},\\\"templates\\\":{\\\"_state\\\":\\\"1\\\"},\\\"shortcodes\\\":{\\\"_state\\\":\\\"1\\\"},\\\"grid_builder\\\":{\\\"_state\\\":\\\"1\\\"},\\\"presets\\\":{\\\"_state\\\":\\\"1\\\"},\\\"dragndrop\\\":{\\\"_state\\\":\\\"1\\\"}}","contributor":"{\\\"post_types\\\":{\\\"_state\\\":\\\"1\\\"},\\\"backend_editor\\\":{\\\"_state\\\":\\\"1\\\",\\\"disabled_ce_editor\\\":\\\"0\\\"},\\\"frontend_editor\\\":{\\\"_state\\\":\\\"1\\\"},\\\"post_settings\\\":{\\\"_state\\\":\\\"1\\\"},\\\"templates\\\":{\\\"_state\\\":\\\"1\\\"},\\\"shortcodes\\\":{\\\"_state\\\":\\\"1\\\"},\\\"grid_builder\\\":{\\\"_state\\\":\\\"1\\\"},\\\"presets\\\":{\\\"_state\\\":\\\"1\\\"},\\\"dragndrop\\\":{\\\"_state\\\":\\\"1\\\"}}","shop_manager":"{\\\"post_types\\\":{\\\"_state\\\":\\\"1\\\"},\\\"backend_editor\\\":{\\\"_state\\\":\\\"1\\\",\\\"disabled_ce_editor\\\":\\\"0\\\"},\\\"frontend_editor\\\":{\\\"_state\\\":\\\"1\\\"},\\\"post_settings\\\":{\\\"_state\\\":\\\"1\\\"},\\\"templates\\\":{\\\"_state\\\":\\\"1\\\"},\\\"shortcodes\\\":{\\\"_state\\\":\\\"1\\\"},\\\"grid_builder\\\":{\\\"_state\\\":\\\"1\\\"},\\\"presets\\\":{\\\"_state\\\":\\\"1\\\"},\\\"dragndrop\\\":{\\\"_state\\\":\\\"1\\\"}}"}';
		if ( function_exists('vc_path_dir') ){
			require_once vc_path_dir( 'SETTINGS_DIR', 'class-vc-roles.php' );
			$vc_roles = new \Vc_Roles();
			$aSettings = json_decode(stripslashes($settings), true);
			$data = $vc_roles->save($aSettings);
		}
	}

	protected function setupMenu(){
		if ( get_option('wiloke_tmp_setup_menu') ) {
			return true;
		}
		update_option('wiloke_tmp_setup_menu', true);

		global $wpdb;
		$termTbl = $wpdb->prefix . 'terms';
		$termID = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT term_id from $termTbl WHERE name=%s",
				$this->menuName
			)
		);

		if ( empty($termID) ){
			return false;
		}
		$termID = absint($termID);

		set_theme_mod( 'nav_menu_locations', array($this->menuLocation=>$termID) );
	}

	protected function createMapPage(){
		$query = new \WP_Query(
			array(
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'meta_key'       => '_wp_page_template',
				'meta_value'     => 'templates/listing-map.php'
			)
		);

		$pageID = null;
		if ( $query->have_posts() ){
			while ($query->have_posts()){
				$query->the_post();
				$pageID = $query->post->ID;
			}
			return $pageID;
		}

		$pageID = wp_insert_post(
			array(
				'post_title'    => 'Map Template',
				'post_type'     => 'page',
				'post_status'   => 'publish'
			)
		);

		if(!is_wp_error($pageID)){
			update_post_meta($pageID, 'page_template', 'templates/listing-map.php');
			return $pageID;
		}

		return '';
	}

	protected function setFrontPage(){
		global $wpdb;
		$postName = $wpdb->_real_escape(trim($_POST['homepage']));

		$aPosts = $wpdb->get_results("SELECT ID, post_content FROM $wpdb->posts WHERE post_type='page' AND post_status='publish' AND post_name LIKE '%".$postName."%' ORDER BY ID DESC LIMIT 20");

		if ( $this->pageBuilder == 'elementor' ){
			$sign = 'elements';
		}else{
			$sign = $this->pageBuilder;
		}
		if ( !empty($aPosts) ){
			foreach ($aPosts as $oPost){
				if ( strpos($oPost->post_content, $sign)  !== false ){
					return $oPost->ID;
				}
			}
		}

		return '';
	}

	protected function importWilcityOptions(){
		$file = WILCITY_IMPORT_DIR . $this->importDir . $this->ds . 'wilcity-options.txt';

		$content = file_get_contents($file);
		$aContent = json_decode($content, true);

		foreach ($aContent as $key => $content){
			SetSettings::setOptions($key, maybe_unserialize($content));
		}
	}

	public function getFiles($key, $dummyDir='', $extension='xml'){
		$aKCXML = get_option($key);
		$dummyDir = empty($dummyDir) ? WILCITY_IMPORT_DIR . $this->importDir . $this->ds : $dummyDir;
		if ( empty($aKCXML) ){
			$aKCXML = glob($dummyDir.'*.'.$extension);
			natsort($aKCXML);
			update_option($key, $aKCXML);
		}

		return $aKCXML;
	}

	public function testEL(){
		$this->importElementor();
	}

	private function importElementor(){
		$dummyDir = WILCITY_IMPORT_DIR . $this->importDir . $this->ds;
		$elDir = $dummyDir.'elementor/';
		$aELFiles = $this->getFiles('wilcity_elementor_dummy', $elDir, 'json');
		$aELInstalled = get_option('el_installed_demo');

		$aELInstalled = empty($aELInstalled) ? array() : $aELInstalled;

		$instWilcityELImport = new WilcityElemtorImport();

		foreach ($aELFiles as $file){
			if ( empty($aELInstalled) || !in_array($file, $aELInstalled) ){
				$status = $instWilcityELImport->run($file);
				array_push($aELInstalled, $file);

				$aParseFile = explode('/', $file);
				$fileName = end($aParseFile);
				$msg = $status ? sprintf('%s has been imported.', $fileName) : sprintf('Could not import %s', $fileName);

				update_option('el_installed_demo', $aELInstalled);
				wp_send_json_success(
					array(
						'msg'   => $msg,
						'item_error'=>!$status
					)
				);
			}
		}

		return true;
	}

	private function importVC(){
		$dummyDir = WILCITY_IMPORT_DIR . $this->importDir . $this->ds;
		$aVCXML = get_option('wilcity_kc_xml');

		if ( empty($aVCXML) ){
			$aVCXML = glob($dummyDir.'vc/*.xml');
			natsort($aVCXML);
			update_option('wilcity_vc_xml', $aVCXML);
		}
		$vcDir = $dummyDir.'vc/';
		$aVCXML = $this->getFiles('wilcity_vc_xml', $vcDir);

		$aVCInstalled = get_option('vc_installed_demo');
		$aVCInstalled = empty($aVCInstalled) ? array() : $aVCInstalled;

		foreach ($aVCXML as $file){
			if ( empty($aVCInstalled) || !in_array($file, $aVCInstalled) ){
				array_push($aVCInstalled, $file);
				$aParseFile = explode('/', $file);
				$status = $this->_importXML($file);

				$fileName = end($aParseFile);
				$msg = $status ? sprintf('%s has been imported', $fileName) : sprintf('Could not import %s', $fileName);

				update_option('vc_installed_demo', $aVCInstalled);
				wp_send_json_success(
					array(
						'msg'   => $msg,
						'item_error'=>!$status
					)
				);
			}
		}

		return true;
	}

	private function importKC(){
		$dummyDir = WILCITY_IMPORT_DIR . $this->importDir . $this->ds;
		$aKCXML = get_option('wilcity_kc_xml');

		if ( empty($aKCXML) ){
			$aKCXML = glob($dummyDir.'kc/*.xml');
			natsort($aKCXML);
			update_option('wilcity_kc_xml', $aKCXML);
		}
		$kcDir = $dummyDir.'kc/';
		$aKCXML = $this->getFiles('wilcity_kc_xml', $kcDir);

		$aKCInstalled = get_option('kc_installed_demo');
		$aKCInstalled = empty($aKCInstalled) ? array() : $aKCInstalled;

		foreach ($aKCXML as $file){
			if ( empty($aKCInstalled) || !in_array($file, $aKCInstalled) ){
				array_push($aKCInstalled, $file);
				$aParseFile = explode('/', $file);
				$status = $this->_importXML($file);

				$fileName = end($aParseFile);
				$msg = $status ? sprintf('%s has been imported', $fileName) : sprintf('Could not import %s', $fileName);

				update_option('kc_installed_demo', $aKCInstalled);
				wp_send_json_success(
					array(
						'msg'   => $msg,
						'item_error'=>!$status
					)
				);
			}
		}

		return true;
	}

	private function importHeroSearchFormSettings(){
		if ( get_option('wiloke_tmp_hero_search_form') ){
			return true;
		}

		update_option('wiloke_tmp_hero_search_form', true);
		$data = file_get_contents(WILCITY_IMPORT_DIR . 'dummy/hero-search.json');
		$aData = json_decode($data, true);

		foreach ($aData as $postType => $aSettings){
			SetSettings::setOptions(General::getHeroSearchFieldsKey($postType), $aSettings['heroSearchFields']);
		}
	}

	private function deleteAllOptions(){
		delete_option('wiloke_permanent_imported_tax_options');
		delete_option('wiloke_tmp_setup_wiloke_submission');
		delete_option('wiloke_tmp_demos_file');
		delete_option('wiloke_tmp_saving_plugin_installation');
		delete_option('wiloke_tmp_installed_demo');
		delete_option('wiloke_tmp_setup_menu');
		delete_option('wiloke_tpm_widgets');
		delete_option('wiloke_themeoptions');
		delete_option('wiloke_tmp_clean_menu');
		delete_option('wiloke_tmp_delete_hello_world');
		delete_option('wiloke_tmp_imported_wilcity_options');
		delete_option('wilcity_elementor_dummy');
		delete_option('wilcity_kc_xml');
		delete_option('wilcity_vc_xml');
		delete_option('wiloke_tmp_hero_search_form');
		delete_option('el_installed_demo');
		delete_option('kc_installed_demo');
		delete_option('vc_installed_demo');
	}

	protected function cleanMenu(){
		global $wpdb;
		$termsTbl = $wpdb->prefix . 'terms';
		$taxonomyTbl = $wpdb->prefix . 'term_taxonomy';
		$aTermIDs = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT $termsTbl.term_id FROM $termsTbl INNER JOIN $taxonomyTbl ON ($termsTbl.term_id = $taxonomyTbl.term_id) WHERE $termsTbl.name IN ('".implode('\',\'', $this->aMenuCollection)."') AND $taxonomyTbl.taxonomy=%s",
				'nav_menu'
			),
			ARRAY_A
		);

		if ( !empty($aTermIDs) ){
			$aTermsCollection = array();
			foreach ( $aTermIDs as $oTerm ){
				$aTermsCollection[] = $oTerm['term_id'];
				wp_delete_term(intval($oTerm['term_id']), 'nav_menu');
			}

			$args = array(
				'post_type' => 'nav_menu_item',
				'tax_query' => array(
					array(
						'taxonomy'  => 'nav_menu',
						'field'     => 'id',
						'terms'     => $aTermsCollection
					)
				)
			);

			$oQuery = new \WP_Query( $args );
			if ( $oQuery->have_posts() ){
				while ($oQuery->have_posts()){
					$oQuery->the_post();
					wp_delete_post($oQuery->post->ID, true);
				}
			}
		}
	}

	protected function resetDefaultData(){
		global $wpdb;
		$tableName = $wpdb->prefix . \WilokeListingTools\AlterTable\AlterTableViewStatistic::$tblName;
		$wpdb->query("DELETE FROM $tableName");

		$query = new \WP_Query(
			array(
				'post_type' => array('listing', 'event'),
				'posts_per_page' => 100
			)
		);

		if ( $query->have_posts() ){
			while ($query->have_posts()){
				$query->the_post();
				SetSettings::deletePostMeta($query->post->ID, 'count_favorites');
				SetSettings::deletePostMeta($query->post->ID, 'count_shared');
			}
		}
	}

	public function runSetup(){
		try{
		    if ( !current_user_can('administrator') ){
		        return false;
            }

			$this->pageBuilder = $_POST['pagebuilder'];

			if ( !current_user_can('administrator') ){
				wp_send_json_error(
					array(
						'msg' => 'You are not allowed to access this page.'
					)
				);
			}

			if ( !get_option('wiloke_tmp_clean_menu') ){
				update_option('wiloke_tmp_clean_menu', true);
				$this->cleanMenu();
			}

			if ( !get_option('wiloke_tmp_delete_hello_world') ){
				update_option('wiloke_tmp_delete_hello_world', true);
				$this->cleanHelloWorld();
			}

			parse_str($_POST['data'], $aData);
			$canUnzip = isset($_POST['canUnzip']) ? $_POST['canUnzip'] : false;
			if ( !$canUnzip || $canUnzip === 'false'){
				$canUnzip = $this->canUnZip();
			}

			if ( !$canUnzip ){
				wp_send_json_error(
					array(
						'msg' => 'We could not install plugins because your server does not UnZip function. Please click on FAQ tab and refer to Manually Install Plugins',
						'item_error'=>true
					)
				);
			}

			if ( !get_option('wiloke_tmp_imported_wilcity_options') ){
				$this->importWilcityOptions();

				update_option('wiloke_tmp_imported_wilcity_options', 'yes');

				do_action('wilcity/wiloke-listing-tools/import-demo/setup-search-form', 'listing');
				do_action('wilcity/wiloke-listing-tools/import-demo/setup-search-form', 'event');
				do_action('wilcity/wiloke-listing-tools/import-demo/setup-sidebar-search-form', 'event');

				wp_send_json_success(
					array(
						'msg'   => 'The Wilcity Options have been imported'
					)
				);
			}

			$this->importHeroSearchFormSettings();

			$dummyDir = WILCITY_IMPORT_DIR . $this->importDir . $this->ds;
			$aXML = get_option('wiloke_tmp_demos_file');
			$aDemoInstalled = get_option('wiloke_tmp_installed_demo');
			$aDemoInstalled = empty($aDemoInstalled) ? array() : $aDemoInstalled;

			$isImportEverything = $_POST['mode'] == 'everything';

			if ( empty($aXML) ){
				$aXML = glob($dummyDir.'*.xml');
				natsort($aXML);
				update_option('wiloke_tmp_demos_file', $aXML);
			}

			if ( $_POST['mode'] == 'homes' ){
				$status = false;
				switch ($_POST['pagebuilder']){
					case 'elementor':
						$status = $this->importElementor();
						break;
					case 'vc':
						$status = $this->importVC();
						break;
					default:
						$status = $this->importKC();
						break;
				}

				if ( $postID = $this->setFrontPage() ){
					update_option('show_on_front', 'page');
					update_option('page_on_front', $postID);
				}

				$this->setupMenu();

				$this->deleteAllOptions();
				if ( $status ){
					wp_send_json_success(
						array(
							'msg'   => 'Congratulations! The demo have been imported successfully!',
							'done'  => true
						)
					);
				}
			}

			if ( count($aXML) === count($aDemoInstalled) ){
				if ( $isImportEverything ){
					switch ($_POST['pagebuilder']){
						case 'elementor':
							$status = $this->importElementor();
							break;
						case 'vc':
							$status = $this->importVC();
							break;
						default:
							$status = $this->importKC();
							break;
					}

					if ( !get_option('wiloke_permanent_imported_themeoptions') && $_POST['mode'] != 'homes' ){
						$themeOptionsDir = $dummyDir . 'themeoptions.json';
						update_option('wiloke_backup_themeoptions', json_encode(get_option('wiloke_themeoptions')));
						$aThemeOptions = json_decode(file_get_contents($themeOptionsDir), true);
						$aThemeOptions['listing_search_page']     = 'map';
						$aThemeOptions['header_search_map_page']  = $this->createMapPage();
						update_option('wiloke_themeoptions', $aThemeOptions);
						update_option('wiloke_permanent_imported_themeoptions', true);
						wp_send_json_success(
							array(
								'msg'   => esc_html__('The theme options data has been imported.', 'wiloke')
							)
						);
					}

					if ( !get_option('wiloke_permanent_imported_tax_options') && $_POST['mode'] != 'homes' ) {
						$aTaxOptions = glob($dummyDir.'term-*.json');
						if ( !empty($aTaxOptions) ){
							foreach ( $aTaxOptions as $fileDir ){
								$aTaxData = file_get_contents($fileDir);
								if ( !empty($aTaxData) ){
									$aTaxData = json_decode($aTaxData, true);

									if ( empty($aTaxData) ){
										$aTaxData = json_decode(stripslashes($aTaxData), true);
									}

									if ( !empty($aTaxData) ){
										foreach ( $aTaxData['data'] as $termSlug => $aRawTermOptions ){
											foreach ($aRawTermOptions as $oTermMetaOptions){
												$oTerm = get_term_by('slug', $termSlug, $aTaxData['taxonomy']);
												if ( !empty($oTerm) && !is_wp_error($oTerm) ){
													update_term_meta($oTerm->term_id, $oTermMetaOptions['meta_key'], $oTermMetaOptions['meta_value']);
												}
											}
										}
									}
								}
							}
							update_option('wiloke_permanent_imported_tax_options', true);
							wp_send_json_success(
								array(
									'msg'   => 'Taxonomies options have been imported.'
								)
							);
						}
					}

					if ( !get_option('wiloke_permanet_reupdate_term_parent') ){
						update_option('wiloke_permanet_reupdate_term_parent', true);
						$termParent = $dummyDir . 'reupdatetermparent.json';
						if ( !empty($termParent) ){
							$aData = file_get_contents($termParent);
							$status = $this->reUpdateTermParent($aData);
							if ( $status ){
								wp_send_json_success(
									array(
										'msg'   => 'Terms\'s Parent have been updated.'
									)
								);
							}else{
								wp_send_json_error(
									array(
										'msg'   => 'Failed update Terms\'s Parent'
									)
								);
							}
						}
					}

					if ( !get_option('wiloke_tpm_widgets') ) {
						$widgetsDir = $dummyDir . 'widgets.wie';
						$this->importingWidgets(file_get_contents($widgetsDir));
						update_option('wiloke_tpm_widgets', true);
						wp_send_json_success(
							array(
								'msg'   => 'Widgets have been imported.'
							)
						);
					}

					$this->deleteAllOptions();
				}

				if ( $postID = $this->setFrontPage() ){
					update_option('show_on_front', 'page');
					update_option('page_on_front', $postID);
				}

				$aMenus = get_theme_mod('nav_menu_locations');
				if ( empty($aMenus) || !isset($aMenus['wilcity_menu']) ){
					$this->setupMenu();
				}

				$this->setupWilokeSubmission($_POST['pagebuilder']);
				$this->resetDefaultData();

				do_action('wilcity/wilcity-import/successfully');

				wp_send_json_success(
					array(
						'msg'   => 'Congratulations! The demo have been imported successfully!',
						'done'  => true
					)
				);
			}else{
				foreach ( $aXML as $file ){
					if ( empty($aDemoInstalled) || !in_array($file, $aDemoInstalled) ){
						array_push($aDemoInstalled, $file);
						$aParseFile = explode('/', $file);
						$status = $this->_importXML($file);

						$fileName = end($aParseFile);
						$msg = $status ? sprintf('%s has been imported', $fileName) : sprintf('Could not import %s', $fileName);

						update_option('wiloke_tmp_installed_demo', $aDemoInstalled);
						wp_send_json_success(
							array(
								'msg'   => $msg,
								'item_error'=>!$status
							)
						);
					}
				}
			}
		}catch (\Exception $oE){
			wp_send_json_error(
				array(
					'msg' => $oE->getMessage()
				)
			);
		}
	}

	public function register(){
		add_action('admin_menu', array($this, 'registerMenu'));
	}
	/**
	 * Try to grab information from WordPress API.
	 *
	 * @since 2.5.0
	 *
	 * @param string $slug Plugin slug.
	 * @return object Plugins_api response object on success, WP_Error on failure.
	 */
	protected function _getPluginsAPI( $slug ) {
		static $api = array(); // Cache received responses.

		if ( ! isset( $api[ $slug ] ) ) {
			if ( ! function_exists( 'plugins_api' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
			}

			$response = plugins_api( 'plugin_information', array( 'slug' => $slug, 'fields' => array( 'sections' => false ) ) );

			$api[ $slug ] = false;

			if ( is_wp_error( $response ) ) {
				return $response;
			} else {
				$api[ $slug ] = $response;
			}
		}

		return $api[ $slug ];
	}

	public function registerMenu(){
		add_menu_page('Wilcity Import', 'Wilcity Import', 'administrator', $this->slug, array($this, 'setupArea'),'dashicons-nametag', 100);
	}

	public function setupArea(){
		?>
        <div id="wiloke-submission-wrapper">
            <div class="form ui">
                <h1 class="dividing header">Welcome To Wilcity</h1>
                <div class="ui message info">
                    <p>First of all, thanks for using Wiloke Theme! The below are some useful information that explain how to setup the theme and how to set up the theme's elements. If You have any beyond question, don't hesitate to open a ticket at - <a href="http://help.wilcity.com/" target="_blank">www.help.wilcity.com</p>
                    <p>
                        Don't forget to follow our fan page. We announce Wiloke's news via
                        <a href="https://www.facebook.com/wilokewp/" target="_blank">Facebook chanel</a> and <a href="https://twitter.com/wilokethemes" target="_blank">Twitter chanel</a>
                    </p>
                </div>
            </div>

            <div class="ui message danger">
                <p>
                    Wilcity is compatible with 3 Page Builders: King Composer, Elementor, WPBakery Page Builder, You can which one that is family with you. <span  style="color: red;">If you select 1 Page Builder, we recommend disabling the rest. For example, If you want to use King Composer, We recommend disabling Elementor, Wilcity Elementor Addon, WPBakery Page Builder, Wilcity WPBakery Addon and else. To disable a plugin, please click on Plugins menu from the sidebar.</span></p>
            </div>

            <div class="ui top attached tabular menu">
                <a class="active item" data-tab="setup"><?php esc_html_e('Import Demo', 'wiloke'); ?></a>
            </div>

            <div class="ui bottom attached active tab segment" data-tab="setup">
                <div class="ui icon message">
                    <i class="pied piper alternate icon"></i>
                    <div class="content">
                        <div class="header">
							<?php esc_html_e('Before importing demos, please pay attention two things: '); ?>
                        </div>

                        <div class="ui segment">
                            <p>Hover on Appearance -> Install Plugins -> Make sure that all required plugins are activated. If you do not see Install Plugins under the Appearance, it means you did it.</p>
                        </div>
                        <div class="ui segment">
                            <p>It may takes up to almost 5 minutes, so please patient. But if it takes more than 5 minutes, please access to your WordPress folder - Using FileZilla or the same tools - Opening <strong>wp-config.php</strong> -> Put <strong>define("FS_METHOD", "direct")</strong> before <strong>That's all, stop editing! Happy blogging.</strong> text -> Back to the page and click <strong>Install demo & Setup required plugins</strong> again.</p>
                        </div>
                    </div>
                </div>

                <div class="ui segment">
                    <h3 class="header ui">Page Builder</h3>
                    <div class="form ui">
                        <select name="pagebuilder" id="pagebuilder" class="field ui dropdown">
							<?php
							foreach ($this->aPageBuilders as $key => $val) {
								echo '<option value="'.$key.'">'.$val.'</option>';
							}
							?>
                        </select>
                    </div>
                </div>

                <div class="ui segment">
                    <h3 class="header ui">Select Home Page</h3>
                    <div class="form ui">
                        <select name="homepage" id="homepage" class="field ui dropdown">
							<?php
							foreach ($this->aHomePages as $key => $val) {
								echo '<option value="'.$key.'">'.$val.'</option>';
							}
							?>
                        </select>
                    </div>
                </div>

                <div class="ui segment">
                    <h3 class="header ui">Importing Home Pages Only</h3>
                    <p class="message ui warning">We recommend using this feature if you migrated from another theme to Wilcity. It will import the home pages only.</p>
                    <form id="wilcity-import-homes" class="wiloke-setup form ui" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="POST">
                        <input type="hidden" name="method" value="home_page">
                        <div class="field">
                            <button type="submit" class="ui green basic button">Import</button>
                        </div>
                        <div class="message ui success notification available">
                            <p class="system-running">System is running ...</p>
                            <ul class="list"></ul>
                        </div>
                    </form>
                </div>

                <div class="ui segment">
                    <h3 class="header ui">Importing everything</h3>
                    <p class="message ui warning">This import will download everything from Wilcity Server to your site.</p>
                    <p class="message ui warning">If you see a message like this <span>0-media_Part_009_of_18.xml has been imported</span> then button keeps Loading Status more 5 minutes, please refresh your site then click Import button again to continue importing the demo.</p>
					<?php
					$postMaxFilesize = ini_get('post_max_size');
					$postMaxFilesize = str_replace('M', '', $postMaxFilesize);

					$maxUploadFilesize = ini_get('upload_max_filesize');
					$maxUploadFilesize = str_replace('M', '', $maxUploadFilesize);

					$postMaxFilezie = ini_get('post_max_size');
					$postMaxFilezie = str_replace('M', '', $postMaxFilezie);

					$maxExecutionTime = ini_get('max_execution_time');
					$maxExecutionTime = str_replace('M', '', $maxExecutionTime);

					$memoryLimit = ini_get('memory_limit');
					$memoryLimit = str_replace('M', '', $memoryLimit);

					$configFalse = false;
					if ( $postMaxFilesize < 64 ){
						$configFalse = true;
						?>
                        <p class="message ui warning">Your post_max_size is <?php echo $postMaxFilesize ?>. We recommend increasing it to 60 M</p>
						<?php
					}

					if ( $postMaxFilezie < 64 ){
						$configFalse = true;
						?>
                        <p class="message ui warning">Your post_max_size is <?php echo $memoryLimit ?>. We recommend increasing it to 128M</p>
						<?php
					}

					if ( $maxUploadFilesize < 64 ){
						$configFalse = true;
						?>
                        <p class="message ui warning">Your upload_max_filesize is <?php echo $maxUploadFilesize ?>. We recommend increasing it to 60 M</p>
						<?php
					}

					if ( $maxExecutionTime < 64 ){
						$configFalse = true;
						?>
                        <p class="message ui warning">Your max_execution_time is <?php echo $maxExecutionTime ?>. We recommend increasing it to 120</p>
						<?php
					}

					if ( $memoryLimit < 128 ){
						$configFalse = true;
						?>
                        <p class="message ui warning">Your memory_limit is <?php echo $memoryLimit ?>. We recommend increasing it to 128M</p>
						<?php
					}

					if ( $configFalse ){
						?>
                        <p class="message ui warning">To change server configuration, please read <a href="https://documentation.wilcity.com/knowledgebase/wordpress-and-wilcity-server-requirements/" target="_blank">WordPress and Wilcity Server Requirements</a></p>
						<?php
					}

					?>
                    <form id="wilcity-import-everything" class="wiloke-setup form ui" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="POST">
                        <input type="hidden" name="method" value="everything">
                        <div class="field">
                            <button type="submit" class="ui green basic button">Import</button>
                        </div>
                        <div class="message ui success notification available">
                            <p class="system-running">System is running ...</p>
                            <ul class="list"></ul>
                        </div>
                    </form>
                </div>
            </div>
        </div>
		<?php
	}

	/**
	 * Run after the plugin is activated
	 * @since 1.0
	 */
	public function redirectToSetup(){
		if ( get_option($this->firstTimeSetup) ){
			return false;
		}

		update_option($this->firstTimeSetup, true);
		wp_redirect(admin_url('admin.php?page='.rawurldecode($this->slug)));
		exit();
	}

	/**
	 * Enqueue Scripts
	 * @since 1.0
	 */
	public function enqueueScripts($hook){
		if ( !defined('WILOKE_LISTING_TOOL_URL') ){
			return false;
		}

		if ( (strpos($hook, $this->slug) !== false) ){
			wp_register_style('semantic-ui', WILOKE_LISTING_TOOL_URL . 'admin/assets/semantic-ui/form.min.css');
			wp_enqueue_style('semantic-ui');
			wp_register_script('semantic-ui', WILOKE_LISTING_TOOL_URL . 'admin/assets/semantic-ui/semantic.min.js', array('jquery'), null, true);
			wp_enqueue_script('semantic-ui');

			wp_enqueue_script('wilcity-import', WILCITY_IMPORT_URL . 'source/js/script.js', array('jquery'), null, true);

		}
	}
}
<?php
/*
Plugin Name: Upload Larger Plugins
Version: 1.4.1
Plugin URI: https://wordpress.org/plugins/upload-larger-plugins
Description: Allow plugins larger than the PHP-defined limit to be uploaded.
Author: David Anderson
Donate: https://david.dw-perspective.org.uk/donate
Author URI: https://david.dw-perspective.org.uk
License: MIT
*/

if (!defined('ABSPATH')) die('No direct access');

// Globals
define('UPLOADLARGERPLUGINS_VERSION', '1.4.1');
define('UPLOADLARGERPLUGINS_SLUG', "upload-larger-plugins");
define('UPLOADLARGERPLUGINS_DIR', dirname(realpath(__FILE__)));
define('UPLOADLARGERPLUGINS_URL', plugins_url('', __FILE__));

$simba_upload_larger_plugins = new Simba_Upload_Larger_Plugins();

class Simba_Upload_Larger_Plugins {

	private $upload_dir;
	private $upload_basedir;

	public function __construct() {
		#add_filter('plugin_action_links', array($this, 'action_links'), 10, 2 );
		add_action('install_plugins_upload', array($this, 'install_plugins_upload'), 9, 1);
		add_action('install_plugins_pre_upload', array($this, 'install_plugins_pre_upload'));
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
		add_action('plugins_loaded', array($this, 'load_translations'));
		add_action('admin_head', array($this, 'admin_head'));
		add_action('wp_ajax_ulp_plupload_action', array($this, 'ulp_plupload_action'));
		add_action('admin_init', array($this, 'admin_init'));
		// This filter only exists on WP 3.7+. We used to use it then... but then WP 4.6.1 broke our method, so we've reverted to the pre-WP-3.7 method
// 		add_filter('upgrader_pre_download', array($this, 'upgrader_pre_download'), 10, 3);
	}

	public function admin_init() {

		if (empty($_GET['plugincksha1']) || empty($_GET['overridebd']) || empty($_GET['package']) || !current_user_can('install_plugins')) return;

		require(ABSPATH.WPINC.'/version.php');
		
		/*
		Old note:
		
		The rest of the code's purpose is to work-around the lack of the upgrader_pre_download filter before WP 3.7
		The below would work on >= 3.7 too; but there, we use a more elegant/direct method.
		
		New situation:
		WP 4.6.1 - https://build.trac.wordpress.org/changeset/38466 - introduced a change which prevents upgrader_pre_download from working. So, this way is back.
		*/

// 		if (version_compare($wp_version, '3.7', '>=')) return;

		$upgrader = new stdClass;
		$upgrader->strings = array('download_failed' => __('Error when trying to find uploaded file', 'uploadlargerplugins'));
		$try_file = $this->upgrader_pre_download(false, $_GET['package'], $upgrader);

		// The File_Upload_Upgrader object eventually gets constructed with this (where $urlholder = 'package', and $uploads = wp_upload_dir())
		//File_Upload_Upgrader::filename = $_GET[$urlholder];
		//File_Upload_Upgrader::package = $uploads['basedir'] . '/' . $this->filename;
		if ( ! ( ( $uploads = wp_upload_dir() ) && false === $uploads['error'] ) ) return;
		if (is_string($try_file) && file_exists($try_file)) {
			$upload_dir = untrailingslashit(get_temp_dir());
// 			if (!is_writable($upload_dir)) return;
			$this->upload_basedir = $upload_dir;
			add_filter('upload_dir', array($this, 'upload_dir'));
			add_action('upgrader_process_complete', array($this, 'upgrader_process_complete'));
		}
	}

	// Only hooked on WP < 3.7
	public function upgrader_process_complete() {
		remove_filter('upload_dir', array($this, 'upload_dir'));
	}

	public function upgrader_pre_download($result, $package, $upgrader) {

		if (empty($_GET['plugincksha1']) || empty($_GET['overridebd'])) return $result;
		$upload_dir = untrailingslashit(get_temp_dir());

		# Sanity checks
		if ($upload_dir != $_GET['overridebd']) return new WP_Error('download_failed', $upgrader->strings['download_failed']);
		$try_file = $upload_dir.'/'.basename($package);

		if (!file_exists($try_file) || sha1_file($try_file) != $_GET['plugincksha1']) return new WP_Error('download_failed', $upgrader->strings['download_failed']);

		return $try_file;
	}

	private function is_our_page_and_authorised() {
		if (!current_user_can('install_plugins')) return false;
		
		require(ABSPATH.WPINC.'/version.php');
		
		global $pagenow;
		// On WP 4.6, there is no longer an upload 'tab' - it's a slide-down instead

		return ($pagenow != 'plugin-install.php' || (version_compare($wp_version, '4.5.9999', '<') && (!isset($_REQUEST['tab']) || 'upload' != $_REQUEST['tab']))) ? false : true;
		
	}
	
	public function admin_enqueue_scripts() {

		if (!$this->is_our_page_and_authorised()) return;
	
		wp_enqueue_script('ulp-admin-ui', UPLOADLARGERPLUGINS_URL.'/admin.js', array('jquery', 'plupload-all'), '1');

		wp_localize_script('ulp-admin-ui', 'ulplion', array(
			'notarchive' => __('This file does not appear to be a zip file.', 'uploadlargerplugins'),
			'notarchive2' => '<p>'.__('This file does not appear to be a zip file.', 'uploadlargerplugins').'</p>',
			'uploaderror' => __('Upload error:','uploadlargerplugins'),
			'makesure' => __('(make sure that you were trying to upload a zip file','uploadlargerplugins'),
			'uploaderr' => __('Upload error', 'uploadlargerplugins'),
			'jsonnotunderstood' => __('Error: the server sent us a response (JSON) which we did not understand.', 'uploadlargerplugins'),
			'error' => __('Error:','uploadlargerplugins')
		));

	}

	public function load_translations() {
		// Tell WordPress where to find the translations
		load_plugin_textdomain('uploadlargerplugins', false, basename(dirname(__FILE__)).'/languages/');
	}

	public function upload_dir($uploads) {
		if (!empty($this->upload_dir)) $uploads['path'] = $this->upload_dir;
		if (!empty($this->upload_basedir)) $uploads['basedir'] = $this->upload_basedir;
		return $uploads;
	}

	public function ulp_plupload_action() {
		// check ajax nonce

		@set_time_limit(900);

		if (!current_user_can('install_plugins')) return;
		check_ajax_referer('uploadlargerplugins-uploader');

		$upload_dir = untrailingslashit(get_temp_dir());
		if (!is_writable($upload_dir)) exit;
		$this->upload_dir = $upload_dir;

		add_filter('upload_dir', array($this, 'upload_dir'));
		// handle file upload

		$farray = array('test_form' => true, 'action' => 'ulp_plupload_action');

		$farray['test_type'] = false;
		$farray['ext'] = 'zip';
		$farray['type'] = 'application/zip';

// 		if (isset($_POST['chunks'])) {
// 
// 		} else {
// 			# Over-write - that's OK.
// 			$farray['unique_filename_callback'] = array($this, 'unique_filename_callback');
// 		}

		$status = wp_handle_upload(
			$_FILES['async-upload'],
			$farray
		);
		remove_filter('upload_dir', array($this, 'upload_dir'));

		if (isset($status['error'])) {
			echo json_encode(array('e' => $status['error']));
			exit;
		}

		# Should be a no-op
		$name = basename($_POST['name']);

		// If this was the chunk, then we should instead be concatenating onto the final file
		if (isset($_POST['chunks']) && isset($_POST['chunk']) && preg_match('/^[0-9]+$/',$_POST['chunk'])) {
			# A random element is added, because otherwise it is theoretically possible for another user to upload into a shared temporary directory in between the upload and install, and over-write
			$final_file = $name;
			rename($status['file'], $upload_dir.'/'.$final_file.'.'.$_POST['chunk'].'.zip.tmp');
			$status['file'] = $upload_dir.'/'.$final_file.'.'.$_POST['chunk'].'.zip.tmp';

			// Final chunk? If so, then stich it all back together
			if ($_POST['chunk'] == $_POST['chunks']-1) {
				if ($wh = fopen($upload_dir.'/'.$final_file, 'wb')) {
					for ($i=0 ; $i<$_POST['chunks']; $i++) {
						$rf = $upload_dir.'/'.$final_file.'.'.$i.'.zip.tmp';
						if ($rh = fopen($rf, 'rb')) {
							while ($line = fread($rh, 32768)) fwrite($wh, $line);
							fclose($rh);
							@unlink($rf);
						}
					}
					fclose($wh);
					$status['file'] = $upload_dir.'/'.$final_file;
				}
			}

		}

		$response = array();
		if (!isset($_POST['chunks']) || (isset($_POST['chunk']) && $_POST['chunk'] == $_POST['chunks']-1)) {
			$file = basename($status['file']);
			if (!preg_match('/\.zip$/i', $file, $matches)) {
				@unlink($status['file']);
				echo json_encode(array('e' => sprintf(__('Error: %s', 'uploadlargerplugins'), __('This file does not appear to be a zip file.', 'uploadlargerplugins'))));
				exit;
			}
		}

		// send the redirect URL
		$response['m'] = admin_url('update.php?action=upload-plugin&overridebd='.urlencode(dirname($status['file'])).'&plugincksha1='.sha1_file($status['file']).'&_wpnonce='.wp_create_nonce( 'plugin-upload' ).'&package='.urlencode(basename($status['file'])));
		echo json_encode($response);
		exit;
	}

	public function admin_head() {

		if (!$this->is_our_page_and_authorised()) return;

 		$chunk_size = min(wp_max_upload_size()-1024, 1024*1024*2-1024);

		# The multiple_queues argument is ignored in plupload 2.x (WP3.9+) - https://make.wordpress.org/core/2014/04/11/plupload-2-x-in-wordpress-3-9/
		# max_file_size is also in filters as of plupload 2.x, but in its default position is still supported for backwards-compatibility. Likewise, our use of filters.extensions below is supported by a backwards-compatibility option (the current way is filters.mime-types.extensions

		$plupload_init = array(
			'runtimes' => 'html5,flash,silverlight,html4',
			'browse_button' => 'plupload-browse-button',
			'container' => 'plupload-upload-ui',
			'drop_element' => 'drag-drop-area',
			'file_data_name' => 'async-upload',
			'multiple_queues' => false,
			'max_file_count' => 1,
			'max_file_size' => '100Gb',
			'chunk_size' => $chunk_size.'b',
			'url' => admin_url('admin-ajax.php'),
			'filters' => array(array('title' => __('Allowed Files'), 'extensions' => 'zip')),
			'multipart' => true,
			'multi_selection' => false,
			'urlstream_upload' => true,
			// additional post data to send to our ajax hook
			'multipart_params' => array(
				'_ajax_nonce' => wp_create_nonce('uploadlargerplugins-uploader'),
				'action' => 'ulp_plupload_action'
			)
		);
// 			'flash_swf_url' => includes_url('js/plupload/plupload.flash.swf'),
// 			'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),

		# WP 3.9 updated to plupload 2.0 - https://core.trac.wordpress.org/ticket/25663
		if (is_file(ABSPATH.'wp-includes/js/plupload/Moxie.swf')) {
			$plupload_init['flash_swf_url'] = includes_url('js/plupload/Moxie.swf');
		} else {
			$plupload_init['flash_swf_url'] = includes_url('js/plupload/plupload.flash.swf');
		}

		if (is_file(ABSPATH.'wp-includes/js/plupload/Moxie.xap')) {
			$plupload_init['silverlight_xap_url'] = includes_url('js/plupload/Moxie.xap');
		} else {
			$plupload_init['silverlight_xap_url'] = includes_url('js/plupload/plupload.silverlight.swf');
		}

		?><script type="text/javascript">
			var ulp_plupload_config=<?php echo json_encode($plupload_init); ?>;
		</script>
		<style type="text/css">
		.drag-drop #drag-drop-area {
			border: 4px dashed #ddd;
			height: 200px;
		}
		#filelist  {
			width: 100%;
		}
		#filelist .file {
			padding: 5px;
			background: #ececec;
			border: solid 1px #ccc;
			margin: 4px 0;
		}
		#filelist .fileprogress {
			width: 0%;
			background: #f6a828;
			height: 5px;
		}
		</style>
		<?php

	}

	public function install_plugins_pre_upload() {
		// Unhook the default uploader (works on WP < 4.6 only)
		remove_action('install_plugins_upload', 'install_plugins_upload');
	}

	public function install_plugins_upload( $page = 1 ) {
	?>
	
	<div class="upload-plugin">
	
		<?php
		
		require(ABSPATH.WPINC.'/version.php');
		
		if (version_compare($wp_version, '4.5.9999', '<')) { ?>
		
			<!-- Upload form from Upload Larger Plugins -->
			<h4><?php _e('Install a plugin in .zip format'); ?></h4>
			
		<?php } ?>
		
		<p class="install-help" style="text-align:left; margin-bottom: 6px;">
			<?php _e('If you have a plugin in a .zip format, you may install it by uploading it here.'); ?>
		</p>

		<?php
		
		
		
		if (version_compare($wp_version, '3.3', '<')) {
			echo '<em>'.sprintf(__('This feature requires %s version %s or later', 'uploadlargerplugins'), 'WordPress', '3.3').'</em>';
		} else {
			?>
			<div id="plupload-upload-ui" class="drag-drop" style="width: 70%;">
				<div id="drag-drop-area">
					<div class="drag-drop-inside">
					<p class="drag-drop-info"><?php _e('Drop plugin zip here', 'uploadlargerplugins'); ?></p>
					<p><?php _ex('or', 'Uploader: Drop plugin zip here - or - Select File'); ?></p>
					<p class="drag-drop-buttons"><input id="plupload-browse-button" type="button" value="<?php echo esc_attr(__('Select File', 'uploadlargerplugins')); ?>" class="button" /></p>
					</div>
				</div>
				<div id="filelist">
				</div>
			</div>
			<?php 
		}
		?>

	</div>

	<?php
	/*
<div style="display:none;">
		<form method="post" enctype="multipart/form-data" class="wp-upload-form" action="<?php echo self_admin_url('update.php?action=upload-plugin'); ?>">
			<?php wp_nonce_field( 'plugin-upload'); ?>
			<input type="file" id="pluginzip" name="pluginzip" />
			<?php submit_button( __( 'Install Now' ), 'button', 'install-plugin-submit', false ); ?>
		</form>
		</div>
*/
	}

	public function action_links($links, $file) {
		if ($file == UPLOADLARGERPLUGINS_SLUG."/".basename(__FILE__)) {
			array_unshift( $links, 
				'<a href="options-general.php?page=upload_larger_plugins">'.__('Settings', 'uploadlargerplugins').'</a>'
			);
		}
		return $links;
	}

}

<?php
if (!function_exists ('add_action')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

class jvbpd_Export {

	function __construct() {
		add_action('admin_menu', array(&$this, 'export_menu'));
	}

	function export_menu() {
		if(isset($_REQUEST['export'])){
			$this->init_jvbpd_Export();
		}
		//Add the javo options page to the Themes' menu
		add_menu_page('Javo Theme', esc_html__('Javo Export', 'jvfrmtd'), 'manage_options', 'lynk_options_export_page', array(&$this, 'lynk_generate_export_page'));
	}


	function init_jvbpd_Export() {
		if(isset($_REQUEST['export_option'])) {
			if( method_exists($this, 'export_' . $_REQUEST['export_option']) ) {
				call_user_func(array($this, 'export_' . $_REQUEST['export_option'] ));
			}
		}
	}

	public function export_custom_sidebars(){
		$custom_sidebars = get_option( 'lynk_sidebars' );
		$output = base64_encode(serialize($custom_sidebars));
		$this->save_as_txt_file("custom_sidebars.txt", $output);
	}

	public function export_tsettings(){
		$jvbpd_theme_settings = get_option("jvbpd_themes_settings");
		$output = base64_encode(serialize($jvbpd_theme_settings));
		//die;
		$this->save_as_txt_file("jvbpd_themes_settings.txt", $output);
	}

	public function export_widgets(){
		$this->data = array();
		$this->data['sidebars'] = $this->export_sidebars();
		$this->data['widgets'] 	= $this->export_wp_widgets();
		$output = base64_encode(serialize($this->data));
		$this->save_as_txt_file("widgets.txt", $output);
	}

	public function export_wp_widgets(){

		global $wp_registered_widgets;
		$all_jvbpd_widgets = array();

		foreach ($wp_registered_widgets as $jvbpd_widget_id => $widget_params)
			$all_jvbpd_widgets[] = $widget_params['callback'][0]->id_base;

		foreach ($all_jvbpd_widgets as $jvbpd_widget_id) {
			$jvbpd_widget_data = get_option( 'widget_' . $jvbpd_widget_id );
			if ( !empty($jvbpd_widget_data) )
				$widget_datas[ $jvbpd_widget_id ] = $jvbpd_widget_data;
		}
		unset($all_jvbpd_widgets);
		return $widget_datas;

	}

	public function export_sidebars(){
		$jvbpd_sidebars = get_option("sidebars_widgets");
		$jvbpd_sidebars = $this->exclude_sidebar_keys($jvbpd_sidebars);
		return $jvbpd_sidebars;
	}

	private function exclude_sidebar_keys( $keys = array() ){
		if ( ! is_array($keys) )
			return $keys;

		unset($keys['wp_inactive_widgets']);
		unset($keys['array_version']);
		return $keys;
	}

	public function export_menus(){
		global $wpdb;

		$this->data = array();
		$locations = get_nav_menu_locations();

		$terms_table = $wpdb->prefix . "terms";
		foreach ((array)$locations as $location => $menu_id) {
			$menu_slug = $wpdb->get_results("SELECT * FROM $terms_table where term_id={$menu_id}", ARRAY_A);
			$this->data[ $location ] = $menu_slug[0]['slug'];
		}
		$output = base64_encode(serialize( $this->data ));
		$this->save_as_txt_file("menus.txt", $output);
	}

	public function export_setting_pages(){
		$jvbpd_static_page = get_option("page_on_front");
		$jvbpd_post_page = get_option("page_for_posts");
		$jvbpd_show_on_front = get_option("show_on_front");
		/*
		$jvbpd_settings_pages = array(
			'show_on_front' => 'page', //$jvbpd_show_on_front,
			'page_on_front' => '33320', // $jvbpd_static_page,
			'page_for_posts' => '36613', // $jvbpd_post_page
		); */
		$jvbpd_settings_pages = array(
			'show_on_front' => $jvbpd_show_on_front,
			'page_on_front' => $jvbpd_static_page,
			'page_for_posts' => $jvbpd_post_page
		);
		$output = base64_encode(serialize($jvbpd_settings_pages));
		$this->save_as_txt_file("settingpages.txt", $output);
	}

	function save_as_txt_file($file_name, $output){
		header("Content-type: application/text",true,200);
		header("Content-Disposition: attachment; filename=$file_name");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $output;
		exit;
	}

	function lynk_generate_export_page() {
		?>
		<div class="wrapper">
				<div class="content">
					<table class="form-table">
						<tbody>
							<tr><td scope="row" width="150"><h2><?php esc_html_e('Export', 'jvfrmtd'); ?></h2></td></tr>
							<tr valign="middle">

								<td>
		    						<form method="post" action="">
									<input type="hidden" name="export_option" value="widgets" />
									<input type="submit" value="Export Widgets" name="export" />
		    						</form>
		    						<br />
		    						<form method="post" action="">
									<input type="hidden" name="export_option" value="custom_sidebars" />
									<input type="submit" value="Export Custom Sidebars" name="export" />
		    						</form>
		    						<br />
		    						<form method="post" action="">
									<input type="hidden" name="export_option" value="tsettings" />
									<input type="submit" value="Export Theme Settings" name="export" />
		    						</form>
		    						<br />
		    						<form method="post" action="">
									<input type="hidden" name="export_option" value="menus" />
									<input type="submit" value="Export Menus" name="export" />
		    						</form>
		    						<br />
		    						<form method="post" action="">
									<input type="hidden" name="export_option" value="setting_pages" />
									<input type="submit" value="Export Setting Pages" name="export" />
		    						</form>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
		</div>

<?php
	}
}
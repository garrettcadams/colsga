<?php
class Lava_Directory_Manager_Addons {

	const DEBUG_MODE = false;

	const urlAPI = 'www.lava-code.com/directory/';

	const LicenseKeyFormat = '%1$s_license_key';
	const LicenseAccountFormat = '%1$s_license_account';
	const ActvatedAddonsKey = 'lava_activated_addons';
	const AddonsTransient = 'lava_directory_lastest_addons_cache';
	const AddonsSchedule = 'lava_directory_version_check_schedule';

	public $parent;

	private $optionGroup = false;

	public $addons = Array();

	public static $addonInst	= false;

	public function __construct() {
		$this->parent = lava_directory();

		$this->post_type = $this->parent->core->getSlug();

		$this->optionGroup	= 'lava_' . $this->post_type . '_addons';

		add_filter( "lava_{$this->post_type}_admin_tab"	, Array( $this, 'add_addons_tab' ) );
		add_action( 'lava_directory_manager_admin_addons_after', Array( $this, 'addons_page' ) );

		// Ajax
		add_action( "wp_ajax_lava_{$this->post_type}_register_licensekey", Array( $this, 'register_licensekey' ) );
		add_action( "wp_ajax_lava_{$this->post_type}_deactive_licensekey", Array( $this, 'deactive_licensekey' ) );
		add_action( "wp_ajax_lava_{$this->post_type}_update_check", Array( $this, 'plugin_update_complete' ) );

		add_action( $this->parent->getHookName( 'Register' ), Array( $this, 'update_check_trigger' ) );
		add_action( $this->parent->getHookName( 'Unregister' ), Array( $this, 'update_check_untrigger' ) );

		// Wordpress Plugin Check
		add_filter( 'transient_update_plugins', array( $this, 'check_for_update' ) );
		add_filter( 'site_transient_update_plugins', array( $this, 'check_for_update' ) );

		// Plugin Update Complete Hook
		add_action( $this->parent->getHookName( 'Addons_check' ), Array( $this,  'plugin_update_complete') );
		add_action( 'upgrader_process_complete', Array( $this, 'wp_plugin_update_complete' ), 10, 2 );

		// add_action( 'admin_init', Array( $this, 'custom_update_comment' ) );

		if( self::DEBUG_MODE ) {
			$this->getLavaAddons( true );
		}
	}

	public function getLicenseKey( $addon='', $default=false ){
		return get_option( sprintf( self::LicenseKeyFormat, $addon ), $default );
	}

	public function getLicenseAccount( $addon='', $default=false ){
		return get_option( sprintf( self::LicenseAccountFormat, $addon ), $default );
	}

	public function setLicenseKey( $addon='', $strKey=null ){
		return update_option( sprintf( self::LicenseKeyFormat, $addon ), sanitize_key( $strKey ) );
	}

	public function setLicenseAccount( $addon='', $strKey=null ){
		return update_option( sprintf( self::LicenseAccountFormat, $addon ), $strKey );
	}

	public function getActivatedAddons( $default=false ){
		return (Array) get_option( self::ActvatedAddonsKey, $default );
	}

	public function add_addons_tab( $args )
	{
		return wp_parse_args(
			Array(
				'addons'		=> Array(
					'label'		=> __( "Activated Addons", 'Lavocode' ),
					'group'	=> $this->optionGroup,
					'file'		=> dirname( __FILE__ ) . '/admin/admin-addons-page.php'
				)
			), $args
		);
	}

	public function addons_page() {
		$this->parent->core->load_admin_template(
			'admin-addons.php',
			Array(
				'objThis'		=> $this,
				'arrAddons'	=> $this->getAddons()
			)
		);
	}

	public function register_licensekey()
	{
		$arrOutput	= Array( 'state' => '' );

		if(
			!empty( $_POST[ 'addon' ] ) &&
			!empty( $_POST[ 'email' ] ) &&
			!empty( $_POST[ 'license_key' ] )
		) {
			$license_actAddons = $this->getActivatedAddons();
			$licenseResponse =  $this->getRemotePost( Array(
				'action' => 'license_exists',
				'addon' => $_POST[ 'addon' ],
				'email' => $_POST[ 'email' ],
				'license_key' => $_POST[ 'license_key' ],
			) );

			if( $licenseResponse[ 'connect' ] ) {
				if( !empty( $licenseResponse[ 'result' ]->result ) ){
					if( ! in_Array( $_POST[ 'addon' ], $license_actAddons ) )
						$license_actAddons[] = $_POST[ 'addon' ];
					$arrOutput[ 'state' ] = 'OK';
				}else{
					if( false !== ( $arrOrder = array_search( $_POST[ 'addon' ], $license_actAddons ) ) ) {
						unset( $license_actAddons[ $arrOrder ] );
					}
				}
			}
			update_option( self::ActvatedAddonsKey, $license_actAddons );

			// Register License
			$this->setLicenseAccount( $_POST[ 'addon' ], $_POST[ 'email' ] );
			$this->setLicenseKey( $_POST[ 'addon' ], $_POST[ 'license_key' ] );

			if( $arrOutput[ 'state' ] == 'OK' ) {
				$this->getLavaAddons( true );
			}
		}
		wp_send_json( $arrOutput );
	}

	public function deactive_licensekey()
	{
		if( !empty( $_POST[ 'addon'] ) ) {
			$license_actAddons = $this->getActivatedAddons();
			if( false !== ( $arrOrder = array_search( $_POST[ 'addon' ], $license_actAddons ) ) ) {
				unset( $license_actAddons[ $arrOrder ] );
			}

			update_option( self::ActvatedAddonsKey, $license_actAddons );

			$this->setLicenseAccount( $_POST[ 'addon' ] );
			$this->setLicenseKey( $_POST[ 'addon' ]  );
			$this->getLavaAddons( true );
		}
		wp_send_json( Array() );
	}

	public function update_check_trigger(){
		wp_schedule_event( time(), 'daily', $this->parent->getHookName( 'Addons_check' ) );
	}

	public function update_check_untrigger(){
		wp_clear_scheduled_hook( $this->parent->getHookName( 'Addons_check' ) );
	}

	public function getRemotePost( $args=Array() )
	{
		$arrOutput				= Array( 'connect' => false );

		$lavaResponser		= wp_remote_post(
			esc_url( self::urlAPI ),
			Array(
				'method'			=> 'POST',
				'timeout'			=> 15,
				'user-agent'		=> sprintf(
					'%1$s/%2$s;%3$s',
					$this->parent->getName(),
					$this->parent->getVersion(),
					get_bloginfo('url')
				),
				'body' => wp_parse_args( Array( 'site' => home_url( '/' ) ), $args )
			)
		);

		$lavaResponse				= wp_remote_retrieve_body( $lavaResponser );

		if( is_wp_error( $lavaResponser ) ){
			$arrOutput[ 'result' ]	= sprintf( "%s : %s", __( "Error", 'Lavacode' ), $lavaResponser->get_error_message() );
		}else if( is_wp_error( $lavaResponser) ){
			$arrOutput[ 'result' ]	= sprintf( "%s : %s", __( "Error", 'Lavacode' ), $lavaResponse->get_error_message() );
		}else if( empty( $lavaResponse ) ){
			$arrOutput[ 'result' ]	= __( "Error", 'Lavacode' );
		}else{
			$arrOutput[ 'connect' ]	= true;
			$arrOutput[ 'result' ]	= json_decode( $lavaResponse );
		}
		return $arrOutput;
	}

	public function getLavaAddons( $refresh=false ) {

		if( !$refresh  ) {
			if( $cache_addons = get_site_transient( self::AddonsTransient ) ) {
				$this->addons = $cache_addons;
				return false;
			}
		}

		$this->addons = null;
		$response = $this->getRemotePost( Array( 'action' => 'getAddons' ) );

		if( ! $response[ 'connect' ] || empty( $response[ 'result' ] ) )
			return;

		if( 'OK' === $response[ 'result' ]->state ) {
			$this->addons = $response[ 'result' ]->result;

			if( !empty( $this->addons ) ) {
				foreach( $this->addons as $addon ) {
					$this->parseAddon( $addon );
				}
			}
			delete_site_transient( self::AddonsTransient );
			set_site_transient( self::AddonsTransient, $this->addons, intVal( 60 * 60 * 24 ) );
		}else{
			if( ! $strMessage = $response[ 'result' ]->result )
				$strMessage		= __( "Server Error", 'Lavacode' );
			echo $strMessage;
		}
	}

	public function parseAddon(  &$addon ) {

		$addon->active = false;
		$addon->version = false;
		$addon->describe = false;

		if( false !== ( $pluginDATA = $this->getAddonsDATA( "{$addon->slug}/{$addon->slug}.php" ) ) ){
			$addon->active = true;
			$addon->version = $pluginDATA[ 'Version' ];
			$addon->describe = $pluginDATA[ 'Description' ];
		}

		if( !$addon->active )
			return false;

		$addon->license = $this->getLicenseKey( $addon->name );
		$addon->license_account = $this->getLicenseAccount( $addon->name );
		$addon->license_active	= in_Array( $addon->name, $this->getActivatedAddons() );

		$urlResponse =  $this->getRemotePost(
			Array(
				'action' => 'getPackageLink',
				'addon' => $addon->name,
				'email' => $addon->license_account,
				'license_key' => $addon->license,
			)
		);

		$addon->package_url = false;
		if( !empty( $urlResponse[ 'result' ]->result->url ) )
			$addon->package_url = $urlResponse[ 'result' ]->result->url;

	}

	public function getAddons() {
		$this->getLavaAddons();
		return $this->addons;
	}

	public function getAddonsDATA( $slug=false ) {
		$arrReturn		= false;
		if( function_exists( 'is_plugin_active' ) && is_plugin_active( $slug ) ) {
			$arrReturn	= get_plugin_data( $this->parent->getPluginDir() . $slug );
		}
		return $arrReturn;
	}

	public function custom_update_comment() {

		if( empty( $this->addons ) )
			$this->getLavaAddons();

		if( !empty( $this->addons ) ) : foreach( $this->addons as $addon ) {
			$addonName	= $addon->slug . '/' . $addon->slug . '.php';
			add_action( 'in_plugin_update_message-' . $addonName, Array( $this, 'lava_addon_update_message' ), 10, 2 );
		} endif;
	}

	public function lava_addon_update_message( $plugData, $data ) {

		if( empty( $plugData[ 'PluginURI' ] ) )
			return;

		echo join( false,
			Array(
				'<style>',
					"#{$data->slug}-update .update-message em,",
					"#{$data->slug}-update .update-message .thickbox,",
					"#{$data->slug}-update .update-message .thickbox{ display:none; visibility:hidden }",
				'</style>'
			)
		);
		echo join( false,
			Array(
				sprintf( '<a href="%s">', $plugData[ 'PluginURI' ] ),
					__( "Download from plugin site", 'Lavacode' ),
				'</a>',
			)
		);
	}

	public function check_for_update( $data )
	{

		if( empty( $this->addons ) )
			$this->getLavaAddons();

		if( !empty( $this->addons ) ) : foreach( $this->addons as $objAddon ) {

			if( ! $objAddon->active )
				continue;

			if( version_compare( $objAddon->version,$objAddon->lastest_version, '>=' ) )
				continue;

			if( ! $package_url = $objAddon->package_url )
				continue;

			$addonSlug = sprintf( '%1$s/%1$s.php', $objAddon->slug );
			$addonMeta = new stdClass();
			$addonMeta->new_version = $objAddon->lastest_version;
			$addonMeta->slug = $objAddon->name;
			$addonMeta->package = $package_url;
			$data->response[ $addonSlug ]	= $addonMeta;
		} endif;

		return $data;
	}

	public function wp_plugin_update_complete( $obj, $option ) { $this->getLavaAddons( true ); }

	public function plugin_update_complete() {
		$this->getLavaAddons( true );

		if( defined( 'DOING_AJAX' ) && DOING_AJAX )
			die;
	}
}
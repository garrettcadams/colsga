<?php
/**
 * Settings API
 *
 *
 */

namespace Awps\Api;

/**
 * Settings API Class
 */
class Settings
{
	/**
	 * Settings array
	 * @var private array
	 */
	public $settings = array();

	/**
	 * Sections array
	 * @var private array
	 */
	public $sections = array();

	/**
	 * Fields array
	 * @var private array
	 */
	public $fields = array();

	/**
	 * Script path
	 * @var string
	 */
	public $script_path;

	/**
	 * Enqueues array
	 * @var private array
	 */
	public $enqueues = array();

	/**
	 * Admin pages array to enqueue scripts
	 * @var private array
	 */
	public $enqueue_on_pages = array();

	/**
	 * Admin pages array
	 * @var private array
	 */
	public $admin_pages = array();

	/**
	 * Admin subpages array
	 * @var private array
	 */
	public $admin_subpages = array();

	/**
	 * Constructor
	 */
	public function __construct()
	{}

	public function register() {

		add_action( 'admin_enqueue_scripts', array( $this, 'webpack_enqueue_scripts' ) );

		$this->defines();
		$this->loadWalker();
		$this->loadPlugins();
		$this->loadTSettings();
	}

	/**
	 * Notice the mix() function in wp_enqueue_...
	 * It provides the path to a versioned asset by Laravel Mix using querystring-based
	 * cache-busting (This means, the file name won't change, but the md5. Look here for
	 * more information: https://github.com/JeffreyWay/laravel-mix/issues/920 )
	 */
	public function webpack_enqueue_scripts()
	{
		// JS
		//wp_enqueue_script( 'mainfest', t_mix('js/manifest.js'), array(), '1.0.0', true );
		//wp_enqueue_script( 'vendor', t_mix('js/vendor.js'), array(), '1.0.0', true );
	}

	/**
	 * Dinamically enqueue styles and scripts in admin area
	 *
	 * @param  array  $scripts file paths or wp related keywords of embedded files
	 * @param  array  $pages    pages id where to load scripts
	 */
	public function admin_enqueue( $scripts = array(), $pages = array() )
	{
		if ( empty( $scripts ) )
			return;

		$i = 0;
		foreach ( $scripts as $key => $value ) :
			foreach ( $value as $val ):
				$this->enqueues[ $i ] = $this->enqueue_script( $val, $key );
				$i++;
			endforeach;
		endforeach;

		if ( !empty( $pages ) ) :
			$this->enqueue_on_pages = $pages;
		endif;

		return $this;
	}

	/**
	 * Call the right WP functions based on the file or string passed
	 *
	 * @param  array $script  file path or wp related keyword of embedded file
	 * @param  var $type      style | script
	 * @return variable functions
	 */
	private function enqueue_script( $script, $type ) {
		if ( $script === 'media_uplaoder' )
			return 'wp_enqueue_media';

		return ( $type === 'style' ) ? array( 'wp_enqueue_style' => $script ) : array( 'wp_enqueue_script' => $script );
	}

	/**
	 * Injects user's defined pages array into $admin_pages array
	 *
	 * @param  var $pages      array of user's defined pages
	 */
	public function addPages( $pages )
	{
		$this->admin_pages = $pages;

		return $this;
	}

	public function withSubPage( $title = null )
	{
		if ( empty( $this->admin_pages ) ) {
			return $this;
		}

		$adminPage = $this->admin_pages[0];

		$subpage = array(
			array(
				'parent_slug' => $adminPage['menu_slug'],
				'page_title' => $adminPage['page_title'],
				'menu_title' => ($title) ? $title : $adminPage['menu_title'],
				'capability' => $adminPage['capability'],
				'menu_slug' => $adminPage['menu_slug'],
				'callback' => $adminPage['callback']
			)
		);

		$this->admin_subpages = $subpage;

		return $this;
	}

	/**
	 * Injects user's defined pages array into $admin_subpages array
	 *
	 * @param  var $pages      array of user's defined pages
	 */
	public function addSubPages( $pages )
	{
		$this->admin_subpages = ( count( $this->admin_subpages ) == 0 ) ? $pages : array_merge( $this->admin_subpages, $pages );

		return $this;
	}

	/**
	 * Injects user's defined settings array into $settings array
	 *
	 * @param  var $args      array of user's defined settings
	 */
	public function add_settings( $args )
	{
		$this->settings = $args;

		return $this;
	}

	/**
	 * Injects user's defined sections array into $sections array
	 *
	 * @param  var $args      array of user's defined sections
	 */
	public function add_sections( $args )
	{
		$this->sections = $args;

		return $this;
	}

	/**
	 * Injects user's defined fields array into $fields array
	 *
	 * @param  var $args      array of user's defined fields
	 */
	public function add_fields( $args )
	{
		$this->fields = $args;

		return $this;
	}


	public function defines() {
		$defines = Array(
			'JVBPD_APP_PATH' => get_template_directory(),
			'JVBPD_THEME_DIR' => get_template_directory_uri(),
			'JVBPD_IMG_DIR' => get_template_directory_uri() . '/assets/dist/images/',
		);
		foreach( $defines as $defineKey => $defineValue ) {
			if( !defined($defineKey) ) {
				define( $defineKey, $defineValue );
			}
		}
	}

	public function loadWalker() {
		$walkerPath = get_template_directory() . '/inc/Api/Callbacks/WalkerCallback.php';

		if( file_exists( $walkerPath ) ) {
		 	require_once( $walkerPath );
		}
	}

	public function loadTSettings() {
		$settingsPath = get_template_directory() . '/inc/Api/TSettings.php';
		if( file_exists( $settingsPath ) ) {
			require_once( $settingsPath );
		}
	}

	public function loadPlugins() {
		$pluginsPath = get_template_directory() . '/inc/Api/Plugins.php';
		if( file_exists( $pluginsPath ) ) {
			require_once( $pluginsPath );
		}
		$tgmpaPath = get_template_directory() . '/inc/Custom/TGMPA.php';
		if(file_exists($tgmpaPath)) {
			require_once($tgmpaPath);
		}
	}

}
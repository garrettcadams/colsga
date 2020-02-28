<?php
/**
 * @package Wiloke Framework
 * @category Core
 * @author WilokeTeam
 */

if ( !defined('ABSPATH') )
{
    exit; // Exit If accessed directly
}

if ( !class_exists('Wiloke') ) :
    /**
     * Main Wiloke Class
     * @class Wiloke
     * @version 1.0.2
     */

    class Wiloke{
        public $aConfigs;
        public static $aErrors;

	    /**
	     * Prefix
	     * @since 1.0
	     */
	    public static $prefix = 'wiloke_listgo_';

        /**
         * First time Installation theme theme
         * @since 1.0
         */
        public static $firsTimeInstallation = 'wiloke_first_time_theme_installation';

        /**
         * @var string
         */
        public $version = '1.1.7.5';

        /**
         * @var string
         * @since 1.0.1
         */
        public static $wilokeDesignPortfolioDemos = 'wiloke_design_portfolio_demos';

        /**
         * @var Wiloke The single instance of the class
         * @since 1.0
         */
        protected static $_instance = null;

        /**
         * @var $aThemeOptions
         */
        public $aThemeOptions = null;
        public static $cacheThemeOptions=null;
        public $aTranslation = null;

        /**
         * Caching User Data
         * @since 1.0
         */
        public static $aUsersData = array();

        /**
         * Main Wiloke Instance
         *
         * Ensures only one instance of Wiloke is loaded or can be loaded.
         *
         * @since 1.0
         * @var static
         * @see Wiloke()
         * @return Wiloke - Main Instance
         */
        public static function instance()
        {
            if ( is_null(self::$_instance) )
            {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        /**
         * An instance of WilokeLoader class
         *
         * @since 1.0
         * @access protected
         * @return object
         */
        protected $_loader;

        /**
         * An instance of WO_Ajax class
         *
         * @since 1.0
         * @access protected
         * @return object
         */
        protected $_ajax;


        /**
         * He knows everything about WOinstThemeOptions
         *
         * @since 1.0
         * @access protected
         * @return object
         */
        protected $instThemeOptions;

        /**
         * Register Sidebar
         *
         * @since 1.0
         * @access protected
         * @return object
         */
        protected $_registerSidebar;

        /**
         * An instance of WO_AdminGeneral
         *
         * @since 1.0
         * @access protected
         * @return object
         */
        protected $_adminGeneral;

        /**
         * An instance of WilokePublic
         *
         * @since 1.0
         * @access protected
         * @return object
         */
        protected $_public;
        protected $instSearchSystem;
        public $frontEnd;

        /*
         * WooCommerce
         * @since 1.1.2
         */
        public $woocommerce;

        /**
         * An instance of Mobile_Detect
         *
         * @since 1.0
         * @access protected
         * @return object
         */
        public static $public_path;

        /**
         * An instance of Mobile_Detect
         *
         * @since 1.0
         * @access protected
         * @return object
         */
        public static $public_url;

	    /**
	     * Caching Post Terms Here
	     * @since 1.0.1
	     */
	    public static $aPostTerms;

        /**
         * Caching post meta
         * @since 1.0.1
         */
        public static $aPostMeta;

        /**
         * Variable Caching
         * @since 1.0.3
         */
        public static $aVariableCaching = array();

        public static $aAllParentTerms = array();

        /**
         * Register autoload
         * @since 1.0.1
         */
        public static function autoload($name){
            if ( strpos($name, 'Wiloke') === false ){
                return;
            }

            $parseFileName = 'class.' . $name . '.php';

            if ( is_file( get_template_directory() . '/admin/inc/' . $parseFileName ) ) {
                include  get_template_directory() . '/admin/inc/' . $parseFileName;
            }elseif ( is_file( get_template_directory() . '/admin/public/' . $parseFileName ) ){
                include get_template_directory() . '/admin/public/' . $parseFileName;
            }
        }

        /**
         * Wiloke Constructor.
         */
        public function __construct()
        {
            self::$public_path = get_template_directory() . '/admin/public/';
            self::$public_url  = get_template_directory_uri() . '/admin/public/';

            do_action('wiloke_action_before_framework_init');

            $this->defineConstants();
            $this->configs();
            $this->includeModules();

            do_action('wiloke_action_after_framework_loaded');
            add_action('after_setup_theme', array($this, 'afterThemeLoaded'));
        }

	    /**
	     * Get Options
	     * @since 1.0.3
	     *
	     */
	    public static function getOption($name, $isReturnArray=true){
	        if ( isset(self::$aVariableCaching[$name]) ){
	            return self::$aVariableCaching[$name];
            }

		    $val = get_option($name);
            if ( empty($val) ){
		        return false;
            }

		    if ( is_array($val) ){
		        return $val;
            }

		    $val = $isReturnArray ? json_decode($val, true) : json_decode($val);
		    self::$aVariableCaching[$name] = $val;
		    return $val;
	    }

	    /**
         * Register hooks after theme loaded
         * @since 1.0
         */
        public function afterThemeLoaded()
        {
            $this->runModules();
            $this->generalHooks();
            $this->admin_hooks();
            $this->public_hooks();
            $this->run();
        }

	    public static function timeStampToMinutes($timeStamp){
		    return $timeStamp/60;
	    }

        public static function timeStampToHours($timeStamp){
            return self::timeStampToMinutes($timeStamp)/60;
        }

	    public static function timeStampToDays($timeStamp){
		    return self::timeStampToHours($timeStamp)/24;
	    }

        /**
         * Define Wiloke Constants
         */
        public function defineConstants()
        {
            $this->define('WILOKE_THEME_URI', trailingslashit(get_template_directory_uri()));
            $this->define('WILOKE_THEME_DIR', trailingslashit(get_template_directory()));

            $this->define('WILOKE_AD_REDUX_DIR', trailingslashit(WILOKE_THEME_DIR . 'admin/inc/redux-extensions'));
            $this->define('WILOKE_AD_REDUX_URI', trailingslashit(WILOKE_THEME_URI . 'admin/inc/redux-extensions'));

            $this->define('WILOKE_AD_SOURCE_URI', trailingslashit(get_template_directory_uri()) . 'admin/source/');
            $this->define('WILOKE_AD_ASSET_URI', trailingslashit(get_template_directory_uri()) . 'admin/asset/');

            $this->define('WILOKE_INC_DIR', trailingslashit(get_template_directory() . '/admin/inc/'));
            $this->define('WILOKE_PUBLIC_DIR', trailingslashit(get_template_directory() . '/admin/public/'));
            $this->define('WILOKE_TPL_BUILDER', trailingslashit(get_template_directory() . '/template-builder/'));
            $this->define('WILOKE_THEMESLUG', 'wilcity');
            $this->define('WILOKE_THEMENAME', 'Wilcity');
	        $this->define('WILCITY_WHITE_LABEL', apply_filters('wilcity/filter/wilcity-white-label', 'wilcity'));
	        $this->define('WILOKE_WHITE_LABEL', apply_filters('wilcity/filter/wiloke-white-label', 'wiloke'));

            $this->version = defined('WP_DEBUG_SCRIPT') && WP_DEBUG_SCRIPT ? uniqid('version_') : $this->version;
            $this->define('WILOKE_THEMEVERSION', $this->version);
        }

        /**
         * Includes
         */
        public function configs()
        {
            $aListOfConfigs = glob(get_template_directory().'/configs/*.php');

            foreach ( $aListOfConfigs as $file )
            {
	            $parsekey = explode('/', $file);
	            $fileName = end($parsekey);
	            $parsekey = str_replace(array('config.', '.php'), array('', ''), $fileName);
	            if ( $parsekey == 'translation' && is_file(get_stylesheet_directory() . '/configs/'.$fileName) ){
		            $aOverride = include get_stylesheet_directory() . '/configs/'.$fileName;
		            $aDefault  = include $file;
		            $this->aConfigs[$parsekey] = wp_parse_args($aOverride, $aDefault);
                }else{
		            $this->aConfigs[$parsekey] = include $file;
                }
            }
        }

        public function includeModules()
        {
            /**
             * Including Classes
             */
            do_action('wiloke_admin_hook_before_include_modules');
//	        include WILOKE_INC_DIR . 'visual-composer.php';
//	        include WILOKE_INC_DIR . 'king-composer.php';
            do_action('wiloke_admin_hook_after_include_modules');
        }

        /**
         * Initialize Modules
         * @since 1.0
         */
        public function runModules()
        {
	        $this->_loader = new WilokeLoader();
            if ( !$this->kindofrequest('admin') )
            {
                new WilokeFrontPage;
                new WilokeComment();
            }else{
	            new WilokeInstallPlugins;
	            new WilokeContactForm7;
            }

	        $this->instThemeOptions  = new WilokeThemeOptions();
	        new WilokeMetaboxes();
	        new WilokeNavMenu();
        }

        /**
         * Do you meet him ever?
         * @params $className
         */
        public function isClassExists($className, $autoMessage=true)
        {
            if ( !class_exists($className) )
            {
                if ( $this->kindofrequest('admin') )
                {
                    if ( $autoMessage )
                    {
                        $message = esc_html__('Sorry', 'wilcity') . $className . esc_html__('Class doesn\'t exist!', 'wilcity');
                    }else{
                        $message = true;
                    }
                }else{
                    $message = false;
                }


                throw new Exception($message);

            }else{
                return true;
            }
        }

        /**
         * Check this file whether it exists or not?
         */
        public function isFileExists($dir, $file)
        {
            if ( file_exists($dir.$file) )
            {
                return true;
            }else{
                $message = sprintf( __('The file with name %s doesn\'t exist. Please open a topic via support.wiloke.com to report this problem.', 'wilcity'), $file );
                self::$aErrors['error'][] = $message;
            }
        }

        /**
         * Define constant if not already set
         * @param string $name
         * @param string|bool $value
         */
        public function define($name, $value)
        {
            if ( !defined($name) )
            {
                define($name, $value);
            }
        }

        public static function display_number($count, $zero, $one, $more)
        {
            $count = absint($count);

            switch ($count)
            {
                case 0:
                    $count = $zero;
                    break;
                case 1:
                    $count = $count . ' ' . $one;
                    break;
                default:
                    $count = $count . ' ' . $more;
                    break;
            }

            return $count;
        }

        /**
         * What kind of request is that?
         * @param $needle
         * @return bool
         */
        public function kindofrequest($needle='admin')
        {
            switch ( $needle )
            {
                case 'admin':
                    return is_admin() ? true : false;
                    break;

                default:
                    if ( !empty($needle) )
                    {
                        global $pagenow;

                        if ( $pagenow === $needle )
                            return true;
                    }

                    return false;
                    break;
            }
        }

	    static public function truncateString($text, $max_characters)
	    {
		    $text = trim( $text );

		    if(function_exists('mb_strlen') && function_exists('mb_strrpos'))
		    {
			    if ( mb_strlen( $text ) > $max_characters ) {
				    $text = mb_substr( $text, 0, $max_characters + 1 );
				    $text = trim( mb_substr( $text, 0, mb_strrpos( $text, ' ' ) ) );
			    }
		    }else{
			    if ( strlen( $text ) > $max_characters ) {
				    $text = substr( $text, 0, $max_characters + 1 );
				    $text = trim( substr( $text, 0, strrpos( $text, ' ' ) ) );
			    }
		    }
		    return $text;
	    }

	    static public function ksesHTML($content, $isReturn=false)
	    {
		    $allowed_html = array(
			    'a' => array(
				    'href'  => array(),
				    'style' => array(
					    'color' => array()
				    ),
				    'title' => array(),
				    'target'=> array(),
				    'class' => array()
			    ),
			    'div'    => array('class'=>array()),
			    'h1'     => array('class'=>array()),
			    'h2'     => array('class'=>array()),
			    'h3'     => array('class'=>array()),
			    'h4'     => array('class'=>array()),
			    'h5'     => array('class'=>array()),
			    'h6'     => array('class'=>array()),
			    'br'     => array('class' => array()),
			    'p'      => array('class' => array(), 'style'=>array()),
			    'em'     => array('class' => array()),
			    'strong' => array('class' => array()),
			    'span'   => array('data-typer-targets'=>array(), 'class' => array()),
			    'i'      => array('class' => array()),
			    'ul'     => array('class' => array()),
			    'ol'     => array('class' => array()),
			    'li'     => array('class' => array()),
			    'code'   => array('class'=>array()),
			    'pre'    => array('class' => array()),
			    'iframe' => array('src'=>array(), 'width'=>array(), 'height'=>array(), 'class'=>array('embed-responsive-item')),
			    'img'    => array('src'=>array(), 'width'=>array(), 'height'=>array(), 'class'=>array(), 'alt'=>array()),
			    'embed'  => array('src'=>array(), 'width'=>array(), 'height'=>array(), 'class' => array()),
		    );

		    $content = str_replace('[wiloke_quotes]', '"', $content);

		    if ( !$isReturn ) {
			    echo wp_kses(wp_unslash($content), $allowed_html);
		    }else{
			    return wp_kses(wp_unslash($content), $allowed_html);
		    }
	    }

        /**
         * Truncate string
         */
        static public function contentLimit($limit=0, $post, $isFocusCutString=false, $content='', $isReturn = true, $dotted='')
        {
            if ( !empty($post->post_excerpt) && !$isFocusCutString ){
	            Wiloke::ksesHTML($post->post_excerpt . $dotted, $isReturn);
            }else{
                if ( strpos($post->post_content, '<!--more') !== false ){
                    if ( $isReturn ){
                        ob_start();
                        the_content();
                        $excerpt = ob_get_contents();
                        ob_end_clean();
                        return $excerpt;
                    }else{
                        the_content();
                        return '';
                    }
                }

	            if ( empty($limit) )
	            {
		            return null;
	            }

	            if ( empty($content) )
	            {
		            if ( !$isFocusCutString && !empty($post->post_excerpt) )
		            {
			            $content = $post->post_excerpt;
		            }else{
			            if ( isset($post->ID) )
			            {
				            $content = get_post_field('post_content', $post->ID);
			            }else{
				            $content = null;
			            }
		            }
	            }
	            $content = strip_shortcodes($content);
	            $content = strip_tags($content, '<script>,<style>');
	            $content = trim( preg_replace_callback('#<(s(cript|tyle)).*?</\1>#si', function(){
		            return '';
	            }, $content));

	            $content = str_replace('&nbsp;', '<br /><br />', $content);

	            $content = self::truncateString($content, $limit);

	            if ( $isReturn )
	            {
		            return strip_shortcodes($content . $dotted);
	            }else{
		            self::ksesHTML(strip_shortcodes($content . $dotted), false);
	            }
            }
        }

        static public function lazyLoad($src='', $cssClass='', $aAtributes=array(), $status = null, $isFocusRender = false)
        {
            $renderAttr = '';
            if ( !empty($aAtributes) )
            {
                foreach ( $aAtributes as $atts => $val )
                {
                    $renderAttr .= $atts . '=' . esc_attr($val) . ' ';
                }
            }

            if ( !$isFocusRender )
            {
                if ( $status === null )
                {
                    global $wiloke;
                    $status = !isset($wiloke->aThemeOptions['general_is_lazy_load']) || $wiloke->aThemeOptions['general_is_lazy_load'] ? true : false;
                }

                if ( $status ) :
                    $cssClass = trim($cssClass . ' lazy');
                    ?>
                    <img class="<?php echo esc_attr($cssClass); ?>" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" data-src="<?php echo esc_url($src); ?>" <?php echo esc_attr($renderAttr); ?> />
                    <noscript>
                        <img src="<?php echo esc_url($src); ?>" <?php echo esc_attr($renderAttr); ?>  />
                    </noscript>
                    <?php
                else :
                    ?>
                    <img src="<?php echo esc_url($src); ?>" <?php echo esc_attr($renderAttr); ?>  />
                    <?php
                endif;
            }else{
                ?>
                <img src="<?php echo esc_url($src); ?>" <?php echo esc_attr($renderAttr); ?>  />
                <?php
            }
        }


        /**
         * Collection of hooks related to admin
         * @since 1.0
         */
        public function admin_hooks()
        {
            if ( is_file( WILOKE_THEME_DIR . 'hooks/admin.php' ) ) {
                require WILOKE_THEME_DIR . 'hooks/admin.php';
            }
        }

        /**
         * We care everything related to front-end
         * @since 1.0
         */
        public function public_hooks()
        {
            if ( is_file( WILOKE_THEME_DIR . 'hooks/public.php' ) ) {
                require WILOKE_THEME_DIR . 'hooks/public.php';
            }
        }

        /**
         * General Hooks, in other words, he works the both admin and front-end
         * @since 1.0
         */
        public function generalHooks()
        {
            if ( !empty($this->instThemeOptions) )
            {
                $this->_loader->add_action('init', $this->instThemeOptions, 'get_option');
            }

            if ( !empty($this->_registerSidebar) )
            {
                $this->_loader->add_action('widgets_init', $this->_registerSidebar, 'register_widgets');
            }
        }

	    /**
	     * Safely Enqueue Google Fonts
         * @since 1.0.2
         * @param $aFonts array a list of google font
         * @return string
	     */
	    public static function safelyGenerateGoogleFont($aFonts){
		    $subsets   = 'latin,latin-ext';
            $fonts_url = add_query_arg(array(
                'family' => urlencode( implode( '|', $aFonts ) ),
                'subset' => urlencode( $subsets ),
            ), 'https://fonts.googleapis.com/css' );

		    return esc_url_raw($fonts_url);
        }

        public static function hexToRgba($color, $opacity = false){
            $default = 'rgb(0,0,0)';
            //Return default if no color provided
            if(empty($color)){
	            return $default;
            }

            //Sanitize $color if "#" is provided
            if ($color[0] == '#' ) {
                $color = substr( $color, 1 );
            }

            //Check if color has 6 or 3 characters and get values
            if (strlen($color) == 6) {
                $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
            } elseif ( strlen( $color ) == 3 ) {
                $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
            } else {
                return $default;
            }

            //Convert hexadec to rgb
            $rgb =  array_map('hexdec', $hex);

            //Check if opacity is set(rgba or rgb)
            if($opacity){
                if(abs($opacity) > 1) {
	                $opacity = 1.0;
                }
                $output = 'rgba('.implode(",",$rgb).','.$opacity.')';
            } else {
                $output = 'rgb('.implode(",",$rgb).')';
            }

            //Return rgb(a) color string
            return $output;
        }

        public static function getThemeOptions($isFocus=false){
	        if ( wp_doing_ajax() || $isFocus ){
	            if ( !empty(self::$cacheThemeOptions) ){
	                return self::$cacheThemeOptions;
                }

		        self::$cacheThemeOptions = get_option('wiloke_themeoptions');
	            return self::$cacheThemeOptions;
            }else{
	            global $wiloke;
	            return $wiloke->aThemeOptions;
            }
        }

        /**
         * List of actions and filters. We will run it soon
         * @since 1.0
         */
        public function run()
        {
            $this->_loader->run();
        }
    }

endif;

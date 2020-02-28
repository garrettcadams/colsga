<?php
/**
 * WO_FrontEnd Class
 *
 * @category Front end
 * @package Wiloke Framework
 * @author Wiloke Team
 * @version 1.0
 */
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\FileSystem;
use \WilokeListingTools\Controllers\DashboardController;
use WilokeListingTools\Framework\Helpers\Firebase as FirebaseHelps;

if ( !defined('ABSPATH') )
{

    exit;
}
use WilokeListingTools\Frontend\User as WilokeUser;
use WilokeListingTools\Framework\Store\Session;
use \WilokeListingTools\Framework\Helpers\FileSystem as WilcityFileSystem;
use WilokeListingTools\Register\WilokeSubmission;
use \WilokeListingTools\Framework\Helpers\SetSettings;

class WilokeFrontPage
{
	public $mainStyle = '';
	public $minifyStyle = 'wiloke_minify_theme_css';
    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'), 9999);
        add_action('wp_head', array($this, 'addFavicon'));
        add_action('wp_head', array($this, 'fbTags'));
        add_action('wp_head', array($this, 'googleReCaptcha'));
        add_action('wp_enqueue_scripts', array($this, 'loadFBSDK'));
    }

    public function googleReCaptcha(){
        if ( !wilcityIsLoginPage() || !\WilokeThemeOptions::isEnable('toggle_google_recaptcha')  ){
            return false;
        }

        $mode = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'login';
        if ( $mode == 'rp' ){
            return false;
        }

        if ( $mode == 'login' ){
            if ( WilokeThemeOptions::getOptionDetail('using_google_recaptcha_on') != 'both' ){
                return false;
            }
        }

        ?>
        <script type="text/javascript">
            var wilcityRunReCaptcha = function() {
	            grecaptcha.render('wilcity-render-google-repcatcha', {
		            'sitekey' : '<?php echo esc_attr(WilokeThemeOptions::getOptionDetail('recaptcha_site_key')); ?>'
	            });
            };
        </script>
        <?php
    }

    public function loadFBSDK(){
	    global $wiloke;
	    $alwaysIncludeFb = false;
	    if ( is_user_logged_in() ){
	        if ( !is_singular() ){
	            return false;
            }else{
	            global $post;
		        $aCoupon = class_exists('WilokeListingTools\Framework\Helpers\GetSettings') ? GetSettings::getPostMeta($post->ID, 'coupon') : '';
		        if ( empty($aCoupon) || ( empty($aCoupon['code']) && empty($aCoupon['redirect_to']) ) ){
			        return false;
		        }
		        $alwaysIncludeFb = true;
            }
	    }

	    if ( !$alwaysIncludeFb ){
		    $isIncludeFB = isset($wiloke->aThemeOptions['fb_toggle_login']) && $wiloke->aThemeOptions['fb_toggle_login'] == 'enable';

		    $isIncludeFB = apply_filters('wilcity/is-include-fb-skd', $isIncludeFB);
		    if ( !$isIncludeFB ){
			    return false;
		    }
        }

        $language = isset($wiloke->aThemeOptions['fb_api_language']) ? esc_js($wiloke->aThemeOptions['fb_api_language']) : '';

	    $sdkURL = "https://connect.facebook.net/".$language."/sdk.js";

	    ob_start();
	    ?>
        window.fbAsyncInit = function() {
            FB.init({
                appId      : '<?php echo esc_js($wiloke->aThemeOptions['fb_api_id']);?>',
                cookie     : true,  // enable cookies to allow the server to access
                xfbml      : true,  // parse social plugins on this page
                version    : 'v2.8' // use version 2.2
            });
        };
        // Load the SDK asynchronously
        (function(d, s, id) {
        let js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "<?php echo esc_url($sdkURL); ?>";
        fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
	    <?php
	    $script = ob_get_clean();
	    wp_add_inline_script('jquery-migrate', $script);
    }

    public function fbTags(){
        $aThemeOptions = Wiloke::getThemeOptions();
        if ( !class_exists('\WilokeListingTools\Framework\Helpers\General') ){
            return '';
        }
        global $post;

        $aListingTypes = \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false, false);

        if ( !is_singular($aListingTypes) ){
            return '';
        }

        if ( isset($aThemeOptions['toggle_fb_ogg_tag_to_listing']) && $aThemeOptions['toggle_fb_ogg_tag_to_listing'] == 'enable' ){
            ?>
            <meta property="og:title" content="<?php echo get_the_title($post->ID); ?>" />
            <meta property="og:url" content="<?php echo get_permalink($post->ID); ?>" />
            <meta property="og:image" content="<?php echo esc_url(GetSettings::getFeaturedImg($post->ID, 'full')); ?>" />
            <meta property="og:description" content="<?php echo esc_html(Wiloke::contentLimit($aThemeOptions['listing_excerpt_length'], $post, false, $post->post_content, true, '...')); ?>" />
            <?php
        }
    }

    public function addFavicon(){
    	global $wiloke;
    	if ( isset($wiloke->aThemeOptions['general_favicon']) && !empty($wiloke->aThemeOptions['general_favicon']) ){
    		?>
		    <link rel="shortcut icon" type="image/png" href="<?php echo esc_url($wiloke->aThemeOptions['general_favicon']['url']); ?>"/>
			<?php
	    }
    }

    public function addAsyncAttributes($tag, $handle){
		if ( strpos($tag, 'async') === false ){
			return $tag;
		}

	    return str_replace( ' src', ' async defer src', $tag );
    }

    public function dequeueScripts()
    {
        wp_dequeue_script('isotope');
        wp_dequeue_script('isotope-css');
    }

    public static function fontUrl($fonts)
    {
        $font_url = '';

        /*
        Translators: If there are characters in your language that are not supported
        by chosen font(s), translate this to 'off'. Do not translate into your own language.
         */
        if ( 'off' !== _x( 'on', 'Google font: on or off', 'wilcity' ) ) {
            $font_url = add_query_arg( 'family', urlencode( $fonts ), "//fonts.googleapis.com/css" );
        }
        return $font_url;
    }

    private function checkConditional($conditions, $scriptName){
        $aParseConditions = explode(',', $conditions);
        if ( !is_array($aParseConditions) ){
            return true;
        }

	    $isGoodConditional = true;
	    foreach ($aParseConditions as $condition){
		    $condition = trim($condition);
            if ( function_exists($condition) ){
	            if ( call_user_func($condition, $scriptName) ){
		            $isGoodConditional = true;
	            }else{
		            return false;
	            }
            }else{
	            $isGoodConditional = true;
            }
        }

        return $isGoodConditional;
    }

    /**
     * Enqueue scripts into front end
     */
    public function enqueueScripts()
    {
        global $wiloke;
	    $vendorURL = WILOKE_THEME_URI . 'assets/vendors/';
	    $cssURL    = WILOKE_THEME_URI . 'assets/production/css/';
	    $cssDir    = WILOKE_THEME_DIR . 'assets/production/css/';
	    $jsURL     = WILOKE_THEME_URI . 'assets/production/js/';
	    $fontURL   = WILOKE_THEME_URI . 'assets/fonts/';

		$aScripts = $wiloke->aConfigs['frontend']['scripts'];

	    // Enqueue Scripts
	    if ( isset($aScripts['js']) ){
	    	foreach ($aScripts['js'] as $name => $aJs){

			    if ( isset($aJs['conditional']) ) {
			    	if ( !$this->checkConditional($aJs['conditional'], $name) ){
			    	    continue;
                    }
				}

	    		if ( isset($aJs['isExternal']) && $aJs['isExternal'] ){
	    			wp_register_script($aJs[0], $aJs[1], array('jquery'), WILOKE_THEMEVERSION, true);
	    			wp_enqueue_script($aJs[0]);
			    }else{
	    			if ( isset($aJs['isVendor']) ){
					    wp_register_script($aJs[0], $vendorURL . $aJs[1], array('jquery'), WILOKE_THEMEVERSION, true);
					    wp_enqueue_script($aJs[0]);
				    }else if (isset($aJs['isWPLIB'])){
	    			    if ( function_exists($aJs[0]) ){
                            call_user_func($aJs[0]);
                        }else{
					        wp_enqueue_script($aJs[0]);
                        }
				    }else if(isset($aJs['isGoogleAPI'])){
				    	$googleAPI = isset($wiloke->aThemeOptions['general_google_api']) && !empty($wiloke->aThemeOptions['general_google_api']) ? $wiloke->aThemeOptions['general_google_api'] : '';
					    $url = isset($aJs[1]) ? $aJs[1] : 'https://maps.googleapis.com/maps/api/js?key=';
					    $url = apply_filters('wilcity/filter/scripts/google-map', $url);
					    $url = $url.$googleAPI;

					    if ( isset($wiloke->aThemeOptions['general_google_language']) && !empty($wiloke->aThemeOptions['general_google_language']) ){
						    $url .= '&language='.esc_js(trim($wiloke->aThemeOptions['general_google_language']));
					    }

					    wp_enqueue_script($aJs[0], $url);
				    }else{
					    wp_register_script($aJs[0], $jsURL . $aJs[1], array('jquery'), WILOKE_THEMEVERSION, true);
					    wp_enqueue_script($aJs[0]);
				    }
			    }
		    }
	    }

	    if ( isset($aScripts['css']) ){
		    foreach ($aScripts['css'] as $aCSS){
			    if ( isset($aCSS['conditional']) ) {
				    if ( !$this->checkConditional($aCSS['conditional'], $aCSS) ){
					    continue;
				    }
			    }

			    if ( isset($aCSS['isExternal']) && $aCSS['isExternal'] ){
				    wp_register_style($aCSS[0], $aCSS[1], array(), WILOKE_THEMEVERSION, false);
				    wp_enqueue_style($aCSS[0]);
			    }else{
				    if ( isset($aCSS['isVendor']) ){
					    wp_register_style($aCSS[0], $vendorURL . $aCSS[1], array(), WILOKE_THEMEVERSION);
					    wp_enqueue_style($aCSS[0]);
				    }else if (isset($aCSS['isWPLIB'])){
					    wp_enqueue_style($aCSS[0]);
				    }elseif( isset($aCSS['isGoogleFont']) ){
					    wp_enqueue_style($aCSS[0], self::fontUrl($aCSS[1]));
				    }else if(isset($aCSS['isFont'])){
					    wp_enqueue_style($aCSS[0], $fontURL . $aCSS[1], array(), WILOKE_THEMEVERSION);
				    }else{
					    wp_register_style($aCSS[0], $cssURL . $aCSS[1], array(), WILOKE_THEMEVERSION);
					    wp_enqueue_style($aCSS[0]);
				    }
			    }
		    }
	    }

	    if ( isset($wiloke->aThemeOptions['advanced_google_fonts']) && $wiloke->aThemeOptions['advanced_google_fonts']=='general' && class_exists('WilokeListingTools\Framework\Helpers\GetSettings') ){
		    if ( isset($wiloke->aThemeOptions['advanced_general_google_fonts']) && !empty($wiloke->aThemeOptions['advanced_general_google_fonts']) ){
			    wp_enqueue_style('wilcity-custom-google-font', esc_url($wiloke->aThemeOptions['advanced_general_google_fonts']));

			    $cssRules = $wiloke->aThemeOptions['advanced_general_google_fonts_css_rules'];

			    if ( !empty($cssRules) ){
				    $googleFont = GetSettings::getOptions('custom_google_font');
				    $fontTextFileName = 'fontText.css';
				    $fontTitleFileName = 'fontTitle.css';
				    if ( $googleFont == urlencode($cssRules) && WilcityFileSystem::isFileExists($fontTextFileName) ){
					    wp_enqueue_style('wilcity-google-font-text-rules', WilcityFileSystem::getFileURI($fontTextFileName));
					    wp_enqueue_style('wilcity-google-font-title-rules', WilcityFileSystem::getFileURI($fontTitleFileName));
				    }else{
					    ob_start();
					    include get_template_directory() . '/assets/production/css/fonts/fontText.css';
					    $fontText = ob_get_clean();
					    $fontText = str_replace('#googlefont', $cssRules, $fontText);

					    ob_start();
					    include get_template_directory() . '/assets/production/css/fonts/fontTitle.css';
					    $fontTitle = ob_get_clean();
					    $fontTitle = str_replace('#googlefont', $cssRules . ' !important;', $fontTitle);

					    if ( WilcityFileSystem::filePutContents($fontTextFileName, $fontText) ){
					    	WilcityFileSystem::filePutContents($fontTitleFileName, $fontTitle);

						    wp_enqueue_style('wilcity-custom-fontText', WilcityFileSystem::getFileURI($fontTextFileName));
						    wp_enqueue_style('wilcity-custom-fontTitle', WilcityFileSystem::getFileURI($fontTitleFileName));
					        \WilokeListingTools\Framework\Helpers\SetSettings::setOptions('custom_google_font', urlencode($cssRules));
					    }else{
						    wp_add_inline_style('wilcity-custom-fontText', $fontText);
						    wp_add_inline_style('wilcity-custom-fontTitle', $fontTitle);
					    }
				    }
			    }
		    }
	    }

	    wp_enqueue_script('comment-reply');
	    wp_enqueue_style(WILOKE_THEMESLUG, get_stylesheet_uri(), array(), WILOKE_THEMEVERSION );

	    if ( isset($wiloke->aThemeOptions['advanced_main_color']) && !empty($wiloke->aThemeOptions['advanced_main_color']) ){
	    	if ( $wiloke->aThemeOptions['advanced_main_color'] != 'custom' ){
			    wp_enqueue_style(WILCITY_WHITE_LABEL.'-custom-color', $cssURL . 'colors/'.$wiloke->aThemeOptions['advanced_main_color'].'.css', array(), WILOKE_THEMEVERSION );
		    }else{
	    		if ( class_exists('\WilokeListingTools\Framework\Helpers\FileSystem') ){
	    			$currentCustomColor = get_option('wilcity_current_custom_color');

					if ( WilcityFileSystem::isFileExists('custom-main-color.css') && $currentCustomColor == $wiloke->aThemeOptions['advanced_custom_main_color']['rgba'] ){
						wp_enqueue_style(WILCITY_WHITE_LABEL.'-custom-color', WilcityFileSystem::getFileURI('custom-main-color.css'), array(), WILOKE_THEMEVERSION );
					}else{
						if ( isset($wiloke->aThemeOptions['advanced_custom_main_color']) && isset($wiloke->aThemeOptions['advanced_custom_main_color']['rgba']) ) {
							if ( ! function_exists( 'WP_Filesystem' ) ) {
								require_once( ABSPATH . 'wp-admin/includes/file.php' );
							}
							WP_Filesystem();
							global $wp_filesystem;
							$defaultCSS = $wp_filesystem->get_contents( $cssDir . 'colors/default.css' );

							$parseCSS = str_replace( '#f06292', $wiloke->aThemeOptions['advanced_custom_main_color']['rgba'], $defaultCSS );

							$status = WilcityFileSystem::filePutContents( 'custom-main-color.css', $parseCSS );
							if ( $status ) {
								update_option('wilcity_current_custom_color', $wiloke->aThemeOptions['advanced_custom_main_color']['rgba']);
								wp_enqueue_style( WILCITY_WHITE_LABEL.'-custom-color', WilcityFileSystem::getFileURI( 'custom-main-color.css' ), array(), WILOKE_THEMEVERSION );
							} else {
								wp_add_inline_style( WILOKE_THEMESLUG, $parseCSS );
							}
						}
					}
			    }
		    }
	    }

	    if ( isset($wiloke->aThemeOptions['advanced_css_code']) && !empty($wiloke->aThemeOptions['advanced_css_code']) ){
		    wp_add_inline_style( WILOKE_THEMESLUG, $wiloke->aThemeOptions['advanced_css_code'] );
	    }

	    if ( isset($wiloke->aThemeOptions['advanced_js_code']) && !empty($wiloke->aThemeOptions['advanced_js_code']) )
	    {
		    wp_add_inline_script('jquery-migrate', $wiloke->aThemeOptions['advanced_js_code']);
	    }

    }
}
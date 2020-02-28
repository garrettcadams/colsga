<?php
use ElementorPro\Modules\ThemeBuilder\Module;

global $wiloke;

if ( !is_page_template('dashboard/index.php') && !is_page_template('templates/map.php') && ( wilcityHasCopyright() || wilcityIsHasFooterWidget() ) ) :
	if ( !defined('ELEMENTOR_PRO_VERSION') ){
        get_template_part('footer-widget');
    }else{
		$aCoreLocations = Module::instance()->get_locations_manager()->get_core_locations();
		if ( !isset($aCoreLocations['footer']) ){
			get_template_part('footer-widget');
        }else{
			$oConditionalManager = Module::instance()->get_conditions_manager();
			$footers = $oConditionalManager->get_documents_for_location( 'footer' );
            if ( empty($footers) ){
	            get_template_part('footer-widget');
            }
		}
    }
    ?>
	<div class="wil-scroll-top">
		<a href="#" title="<?php echo esc_attr__('To top', 'wilcity'); ?>">
			<i class="la la-angle-up"></i>
		</a>
	</div>
<?php endif; ?>

<div id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-popup-area')); ?>">
	<?php do_action('wilcity/before/close_wrapper') ?>
</div>

<div id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-wrapper-all-popup')); ?>">
	<?php do_action('wilcity/footer/vue-popup-wrapper'); ?>
</div>
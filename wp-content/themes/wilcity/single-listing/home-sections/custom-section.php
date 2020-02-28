<?php global $wilcityArgs;
$scKey = str_replace('wilcity_single_navigation_', '', $wilcityArgs['key']);

if ( empty($wilcityArgs['content']) ){
    return '';
}
$content = \WilokeListingTools\Frontend\SingleListing::parseCustomFieldSC($wilcityArgs['content'], $scKey);
$content = do_shortcode(stripslashes($content));
if ( !empty($typeClass) ){
    $scKey .= ' ' . $typeClass;
}
if ( !empty(trim($content)) ) :
?>
<div class="content-box_module__333d9 wilcity-single-listing-custom-content-box <?php echo esc_attr($scKey); ?>">
	<header class="content-box_header__xPnGx clearfix">
		<div class="wil-float-left">
			<h4 class="content-box_title__1gBHS"><i class="<?php echo esc_html($wilcityArgs['icon']); ?>"></i><span><?php echo esc_html($wilcityArgs['name']); ?></span></h4>
		</div>
	</header>
	<div class="content-box_body__3tSRB">
		<div class="row" data-col-xs-gap="10">
			<?php echo $content; ?>
		</div>
	</div>
</div>
<?php endif; ?>



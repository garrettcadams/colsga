<?php global $wilcityArgs;
$content = do_shortcode("[wilcity_google_adsense]");
if ( !empty($content) ) :
	?>
	<div class="content-box_module__333d9 wilcity-single-listing-ads-box">
		<?php if ( $wilcityArgs['isShowBoxTitle'] == 'yes' ) : ?>
		<header class="content-box_header__xPnGx clearfix">
			<div class="wil-float-left">
				<h4 class="content-box_title__1gBHS"><i class="<?php echo esc_html($wilcityArgs['icon']); ?>"></i><span><?php echo esc_html($wilcityArgs['name']); ?></span></h4>
			</div>
		</header>
		<?php endif; ?>
		<div class="content-box_body__3tSRB">
			<div class="row" data-col-xs-gap="10">
				<?php echo $content; ?>
			</div>
		</div>
	</div>
<?php endif; ?>
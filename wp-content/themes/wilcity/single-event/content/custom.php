<?php
global $post, $wilcityArgs;

$aParseSC = explode('|', $wilcityArgs['key']);

if ( !isset($aParseSC[1]) ){
   return '';
}

if ( isset($wilcityArgs['content']) && !empty($wilcityArgs['content']) ){
	$shortcode = \WilokeListingTools\Frontend\SingleListing::parseCustomFieldSC($wilcityArgs['content'], $aParseSC[0]);
}else{
	$shortcode = '[wilcity_render_'.$aParseSC[1].'_field key="'.$aParseSC[0].'" postID="'.$post->ID.'"]';
}

ob_start();
echo do_shortcode($shortcode);
$content = ob_get_contents();
ob_end_clean();
if ( empty($content) ){
    return '';
}

?>
<div class="content-box_module__333d9">
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
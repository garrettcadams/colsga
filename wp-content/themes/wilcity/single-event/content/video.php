<?php
global $post, $wilcityaVideo, $wilcityArgs;
$aRawVideos = \WilokeListingTools\Framework\Helpers\GetSettings::getPostMeta($post->ID, 'video_srcs');

if ( empty($aRawVideos) ){
	return '';
}
$aRawVideos = \WilokeListingTools\Frontend\SingleListing::parseVideos($aRawVideos, $post->ID);
?>
<div class="content-box_module__333d9">
	<header class="content-box_header__xPnGx clearfix">
		<div class="wil-float-left">
			<h4 class="content-box_title__1gBHS"><i class="<?php echo esc_html($wilcityArgs['icon']); ?>"></i><span><?php echo esc_html($wilcityArgs['name']); ?></span></h4>
		</div>
	</header>
	<div class="content-box_body__3tSRB">
		<div class="gallery_module__2AbLA">
			<div class="row" data-col-xs-gap="5" data-col-sm-gap="10">
				<?php
				foreach ($aRawVideos as $wilcityaVideo){
					get_template_part('single-listing/partials/video');
				}
				?>
			</div>
		</div>
	</div>
</div>

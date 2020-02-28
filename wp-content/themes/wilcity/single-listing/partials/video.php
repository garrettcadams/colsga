<?php global $wilcityaVideo;
if ( strpos($wilcityaVideo['src'], 'facebook.com') !== false ){
    $aParse = explode('/', $wilcityaVideo['src']);
    $total = count($aParse);
    if ( empty($aParse[$total-1]) || strpos($aParse[$total-1], '?t=') !== false  ){
        $src = $aParse[$total-2];
    }else{
	    $src = $aParse[$total-1];
    }
    $src = 'https://www.facebook.com/v2.5/plugins/video.php?href=https%3A%2F%2Fwww.facebook.com%2Fvideo.php%3Fv%3D'.$src;
}else{
    $src = $wilcityaVideo['src'];
}
?>
<div class="col-xs-12 col-sm-4">
	<!-- gallery-item_module__1wn6T -->
	<div class="gallery-item_module__1wn6T">
		<!-- video-popup_module__2P6ZG -->
		<div class="video-popup_module__2P6ZG video-popup_sm__11-9c">
			<div class="video-popup_media__dEwwq">
				<div class="video-popup_overlay__2lJoC"></div>
				<div class="video-popup_img__3zV5d bg-cover" style="background-image: url(<?php echo esc_url($wilcityaVideo['thumbnail']); ?>);"></div>
				<a class="video-popup_popup__17b-F js-video-popup" href="<?php echo esc_url(\WILCITY_SC\SCHelpers::cleanYoutubeUrl($src)); ?>">
					<i class="la la-play"></i>
				</a>
			</div>
		</div><!-- End / video-popup_module__2P6ZG -->
	</div><!-- End / gallery-item_module__1wn6T -->
</div>
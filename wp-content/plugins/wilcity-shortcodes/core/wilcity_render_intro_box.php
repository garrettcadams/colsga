<?php
function wilcity_render_intro_box($aAtts){
	$classes = !empty($aAtts['extra_class']) ? $aAtts['extra_class'] . ' row equal-height equal-height-center' : 'row equal-height equal-height-center';
?>
<div class="<?php echo esc_attr($classes); ?>">
	<div class="col-md-6 col-lg-6 ">
		<!-- video-popup_module__2P6ZG -->
		<?php if ( !empty($aAtts['video_intro']) ) : ?>
		<div class="video-popup_module__2P6ZG video-popup_round__2d8uy  video-popup-parallax-inner" data-hover-parallax-options="3d">
			<div class="video-popup_media__dEwwq">
				<div class="video-popup_overlay__2lJoC"></div>
				<div class="video-popup_img__3zV5d bg-cover" style="background-image: url('<?php echo esc_url($aAtts['bg_img']); ?>')"></div><a class="video-popup_popup__17b-F js-video-popup" href="<?php echo esc_url(\WILCITY_SC\SCHelpers::cleanYoutubeUrl($aAtts['video_intro'])); ?>"><i class="la la-play"></i></a>
			</div>
		</div><!-- End / video-popup_module__2P6ZG -->
		<?php else: ?>
		<div class="bg-cover" style="background-image: url(<?php echo esc_url($aAtts['bg_img']); ?>)">
			<img src="<?php echo esc_url($aAtts['bg_img']); ?>" alt="<?php esc_html_e('Image Background', 'wiloke-shortcodes'); ?>">
		</div>
		<?php endif; ?>
	</div>
	<div class="col-md-6 col-lg-6 ">
		<div>
			<?php echo do_shortcode($aAtts['intro']); ?>
		</div>
	</div>
</div>
<?php
}
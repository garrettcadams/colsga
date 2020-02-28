<?php
function wilcityRenderHeaderSlider(){
	global $wiloke, $wilcityGallerySettings;
?>
	<header class="listing-detail_header__18Cfs hasSlider">
		<!-- swiper__module swiper-container -->
		<div class="swiper__module swiper-container swiper--button-pill swiper--button-abs-outer swiper--button-mobile-disable" data-options='{"autoplay": <?php echo abs($wiloke->aThemeOptions['listing_slider_autoplay']); ?>, "slidesPerView":3,"spaceBetween":4,"breakpoints":{"640":{"slidesPerView":1,"spaceBetween":4},"992":{"slidesPerView":2,"spaceBetween":4},"1200":{"slidesPerView":3,"spaceBetween":4},"1400":{"slidesPerView":3,"spaceBetween":4}}}'>
			<div class="swiper-wrapper">
				<?php foreach ($wilcityGallerySettings as $id => $url): ?>
					<div class="listing-detail_itemOverlay__1F_RJ">
						<?php
						$imgUrl = wp_get_attachment_image_url($id, $wiloke->aThemeOptions['listing_slider_img_size']); ?>
						<div class="listing-detail_sliderItem__3k2pH bg-cover" style="background-image: url(<?php echo esc_attr($imgUrl); ?>);">
							<img src="<?php echo esc_url($imgUrl); ?>" alt="<?php echo esc_attr(get_the_title($id)); ?>">
						</div>
						<?php
						$caption = get_post_field('post_excerpt', $id);
						if ( !empty($caption) ) :
							?>
							<div class="listing-detail_caption__TrVbq"><?php echo esc_html($caption); ?></div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="swiper-button-custom">
				<div class="swiper-button-prev-custom"><i class='la la-angle-left'></i></div>
				<div class="swiper-button-next-custom"><i class='la la-angle-right'></i></div>
			</div>

			<div class="full-load">
				<div class="pill-loading_module__3LZ6v pos-a-center">
					<div class="pill-loading_loader__3LOnT"></div>
				</div>
			</div>
		</div><!-- End / swiper__module swiper-container -->
	</header>
<?php
}
add_shortcode('wilcity_render_header_slider', 'wilcityRenderHeaderSlider');
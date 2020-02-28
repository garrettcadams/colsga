<?php
function wilcity_sc_render_testimonials($atts){
	$atts = \WILCITY_SC\SCHelpers::mergeIsAppRenderingAttr($atts);
	if ( $atts['isApp'] ){
		echo '%SC%' . json_encode($atts) . '%SC%';
		return '';
	}

	if ( empty($atts['testimonials']) ){
		return '';
	}
	$wrap_class = 'swiper__module swiper-container swiper--button-pill swiper--button-abs-md swiper--button-abs-inner ' . $atts['extra_class'];
	$wrap_class = trim($wrap_class);
?>

	<!-- swiper__module swiper-container -->
	<?php if ( !empty($atts['autoplay']) ) : ?>
	<div class="<?php echo esc_attr($wrap_class); ?>" data-options='{"autoHeight": true, "loop":true, "autoplay":{"delay":"<?php echo absint($atts['autoplay'])*1000; ?>"}}'>
	<?php else: ?>
	<div class="<?php echo esc_attr($wrap_class); ?>" data-options='{"loop":true}'>
    <?php endif; ?>

		<div class="full-load">
			<!-- pill-loading_module__3LZ6v -->
			<div class="pill-loading_module__3LZ6v pos-a-center">
				<div class="pill-loading_loader__3LOnT"></div>
			</div><!-- End / pill-loading_module__3LZ6v -->
		</div>

		<div class="swiper-wrapper">
			<?php
			foreach ( $atts['testimonials'] as $oTestimonial ){
				wilcity_render_testimonial_item($atts['icon'], $oTestimonial);
			}
			?>
		</div>

		<div class="swiper-button-custom">
			<div class="swiper-button-prev-custom"><i class='la la-angle-left'></i></div>
			<div class="swiper-button-next-custom"><i class='la la-angle-right'></i></div>
		</div>
	</div>
	<?php
}
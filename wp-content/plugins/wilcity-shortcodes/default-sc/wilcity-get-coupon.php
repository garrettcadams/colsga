<?php
function wilcityGetCoupon($aAtts){
	$aAtts = shortcode_atts(
		array(
			'highlight'         => '',
			'title'             => '',
			'description'       => '',
			'code'              => '',
			'redirect_to'       => '',
			'expiry_date'       => ''
		),
		$aAtts
	);

    $mainClass = 'col-md-8';
	if ( empty($aAtts['highlight']) ){
		$mainClass = 'col-md-12 align-center';
	}
	$redirectTo = empty($aAtts['redirect_to']) ? '#' : esc_url($aAtts['redirect_to']);
	?>
	<div class="sale-off">
		<div class="row">
			<?php if ( !empty($aAtts['highlight']) ) : ?>
			<div class="col-sm-4">
				<a @click.prevent="getCoupon('<?php echo esc_attr($aAtts['code']); ?>', '<?php echo $redirectTo; ?>')" class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-get-counpon'));?>" href="<?php echo $redirectTo; ?>">
					<h4 class="color-secondary mt-15 mb-0 text-center"><?php echo esc_attr($aAtts['highlight']); ?> <i class="<?php echo esc_attr(apply_filters('wilcity/filter/coupon/highlight/icon', 'la la-angle-double-right')); ?>"></i></h4>
				</a>
			</div>
			<?php endif; ?>
			<div class="<?php echo esc_attr($mainClass); ?>">
				<a @click.prevent="getCoupon('<?php echo esc_attr($aAtts['code']); ?>', '<?php echo $redirectTo; ?>')" class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-get-counpon'));?>" href="<?php echo $redirectTo; ?>">
					<?php if ( !empty($aAtts['title']) ) : ?>
					<h4 class="color-quaternary mt-0 mb-0"><?php echo esc_html($aAtts['title']); ?></h4>
					<?php endif; ?>
					<?php if ( !empty($aAtts['description']) ) : ?>
					<div><?php echo esc_html($aAtts['description']); ?></div>
					<?php endif; ?>

					<?php if ( !empty($aAtts['expiry_date']) ) : ?>
                        <div><?php echo esc_html__('Expiry date: ', 'wilcity-shortcodes') . ' ' . esc_html($aAtts['expiry_date']); ?></div>
					<?php endif; ?>
				</a>
			</div>
		</div>
	</div>
	<?php
}

add_shortcode('wilcity_get_coupon', 'wilcityGetCoupon');


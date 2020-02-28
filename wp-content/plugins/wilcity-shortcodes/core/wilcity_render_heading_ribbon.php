<?php
function wilcityRenderHeadingRibbon($aAtts){
	$aAtts = wp_parse_args(
		$aAtts,
		array(
			'ribbon' => '',
			'ribbon_color' => '',
			'heading' => '',
			'heading_color' => '',
			'desc' => '',
			'desc_color'
		)
	);

	?>
	<div class="heading_module__156eJ heading_ribbon__2Jt9F wil-text-center">
		<?php if ( !empty($aAtts['ribbon']) ): ?>
			<?php if ( !empty($aAtts['ribbon_color']) ): ?>
				<h3 class="heading_mask__pcO5T" style="color: <?php echo esc_attr($aAtts['ribbon_color']); ?>"><span><?php Wiloke::ksesHTML($aAtts['ribbon']); ?></span></h3>
			<?php else: ?>
				<h3 class="heading_mask__pcO5T"><span><?php Wiloke::ksesHTML($aAtts['ribbon']); ?></span></h3>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( !empty($aAtts['heading']) ): ?>
			<?php if ( !empty($aAtts['heading_color']) ): ?>
				<h2 class="heading_title__1bzno" style="color: <?php echo esc_attr($aAtts['heading_color']); ?>"><span><?php Wiloke::ksesHTML($aAtts['heading']); ?></span></h2>
			<?php else: ?>
				<h2 class="heading_title__1bzno"><?php Wiloke::ksesHTML($aAtts['heading']); ?></h2>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( !empty($aAtts['desc']) ): ?>
			<?php if ( !empty($aAtts['desc_color']) ): ?>
				<p class="heading_content__2mtYE" style="color: <?php echo esc_attr($aAtts['desc_color']); ?>"><span><?php Wiloke::ksesHTML($aAtts['desc']); ?></span></p>
			<?php else: ?>
				<p class="heading_content__2mtYE"><?php Wiloke::ksesHTML($aAtts['desc']); ?></p>
			<?php endif; ?>
		<?php endif; ?>
    </div>
    <?php
}

<?php
function wilcityWidgetListStyle($post, $aAtts=array()){
	$aAtts = wp_parse_args($aAtts, array(
		'size' => 'thumbnail',
		'isShowComment' => true,
		'isShowRating'  => false
	));

	$thumbnail = \WilokeHelpers::getFeaturedImg($post->ID, $aAtts['size']);
	?>
	<div class="widget-post-item">
		<div class="widget-post-item__media bg-cover" style="background-image: url('<?php echo esc_url($thumbnail); ?>')">
            <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($post->post_title); ?>"/>
        </div>
		<div class="widget-post-item__body">
			<h2 class="widget-post-item__title"><a href="<?php echo esc_url(get_permalink($post->ID)); ?>"><?php echo get_the_title($post->ID); ?></a></h2>
			<div class="widget-post-item__meta">
				<?php if ( $aAtts['isShowComment'] ) : ?>
				<a href="<?php echo esc_url(get_permalink($post->ID)); ?>">
					<i class="la la-comments"></i>
					<?php
					$commentNumbers = get_comments_number($post->ID);
					if ( empty($commentNumbers) ) {
						esc_html_e( 'No Comment', 'wilcity-shortcodes' );
					}else{
						echo sprintf(_n('%s Comment', '%s Comments', $commentNumbers, 'wilcity-shortcodes'), $commentNumbers);
					}
					?>
				</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
}
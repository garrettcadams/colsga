<?php
function wilcityRenderFullWidthListItem($post, $size='wilcity_500x275'){
	$thumbnail = \WilokeHelpers::getFeaturedImg($post->ID, 'wilcity_500x275');
	?>
	<div class="widget-listing-item">
		<div class="widget-listing-item__media bg-cover" style="background-image: url('<?php echo esc_url($thumbnail); ?>')">
            <a class="pos-a-full" href="<?php echo get_permalink($post->ID); ?>">
                <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($post->post_title); ?>"/>
            </a>
        </div>
		<div class="widget-listing-item__body">
			<h2 class="widget-post-item__title"><a href="<?php echo esc_url(get_permalink($post->ID)); ?>"><?php echo get_the_title($post->ID); ?></a></h2>
			<div class="widget-listing-item__meta">
                <a href="<?php echo esc_url(get_permalink($post->ID)); ?>"><?php echo esc_html(get_the_date(get_option('date_format'), $post->ID)); ?></a>
            </div>
		</div>
	</div>
	<?php
}
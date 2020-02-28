<?php
function wilcityPostSliderItem($post, $size='thumbnail'){
	$thumbnail = \WilokeHelpers::getFeaturedImg($post->ID, $size);
	?>
    <div class="event_module__2zicF wil-shadow js-event">
        <header class="event_header__u3oXZ">
            <a href="<?php echo get_permalink($post->ID); ?>">
                <div class="event_img__1mVnG pos-a-full bg-cover" style="background-image: url('<?php echo esc_url($thumbnail); ?>')"><img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($post->post_title); ?>"/></div>
            </a>
        </header>
        <div class="event_body__BfZIC">
            <div class="event_calendar__2x4Hv"><span class="event_month__S8D_o color-primary"><?php echo get_the_date('m', $post->ID); ?></span><span class="event_date__2Z7TH"><?php echo get_the_date('d', $post->ID); ?></span>
            </div>
            <div class="event_content__2fB-4">
                <h2 class="event_title__3C2PA"><a href="<?php echo get_permalink($post->ID); ?>"><?php echo get_the_title($post->ID); ?></a></h2>
            </div>
        </div>
        <footer class="event_footer__1TsCF"><span class="event_by__23HUz"><a href="<?php echo get_author_posts_url($post->post_author); ?>" class="color-dark-2"><?php echo get_the_author(); ?></a></span>
            <div class="event_right__drLk5 pos-a-center-right"><span class="event_interested__2RxI-" data-tooltip="<?php esc_html_e('Interested', 'wilcity-shortcodes'); ?>" data-tooltip-placement="top"><i class="la la-star color-primary"></i></span>
            </div>
        </footer>
    </div>
	<?php
}
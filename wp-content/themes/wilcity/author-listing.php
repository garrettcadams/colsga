<?php
get_header();
?>
	<!-- Content-->
	<div id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-author-listing')); ?>" class="wil-content">
		<div class="author-hero_module__1u4Pt">
            <div class="author-hero_wrap__KG0cu">
                <?php get_template_part('author-listing/header-image'); ?>
                <?php get_template_part('author-listing/author-info'); ?>
            </div>
			<?php get_template_part('author-listing/navigation'); ?>
		</div>


        <section class="wil-section bg-color-gray-2 pt-30">
            <div class="container">
            <?php
                $mode = get_query_var('mode');
                if ( empty($mode) || $mode == 'about' ){
	                get_template_part('author-listing/about');
                }else if ( $mode == 'event' ){
	                get_template_part('author-listing/events');
                }else{
	                get_template_part('author-listing/listings');
                }
            ?>
            </div>
        </section>

	</div>
<?php
get_footer();

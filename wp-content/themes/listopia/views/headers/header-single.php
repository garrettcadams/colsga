<?php
if( is_single() ) {
    $jvbpdSingleHeadeStyle = "";
    if( has_post_thumbnail() ) {
        $jvbpdSingleHeadeStyle = sprintf( 'background-image:url(%1$s);', esc_url(get_the_post_thumbnail_url(null, 'full')));
    } ?>
    <div class="single-header-wrap header-<?php the_ID(); ?>">
        <div class="single-header-bg" style="<?php echo esc_attr($jvbpdSingleHeadeStyle);?>"></div>
        <div class="single-header-overlay"></div>
        <div class="single-header">
            <div class="header-title"><?php the_title( '<h1 class="entry-title">', '</h1>' ); ?></div>
            <?php
            if ( 'post' === get_post_type() ) : ?>
                <div class="entry-meta">
                    <?php Awps\Core\Tags::posted_on(); ?>
                </div><!-- .entry-meta -->
            <?php endif; ?>
        </div>
    </div>
    <?php
}
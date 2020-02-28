<?php if( have_posts() ) { ?>
    <div class="archive-header-wrap term-<?php the_ID(); ?>">
        <div class="archive-header">
            <div class="archive-title"><?php the_archive_title( '<h1 class="page-title">', '</h1>' ); ?></div>
            <div class="archive-description"><?php the_archive_description( '<div class="archive-description">', '</div>' ); ?></div>
        </div>
    </div>
<?php } ?>
<?php
/*
 * Template Name: Wilcity Page Builder
 */

get_header();
?>
<div class="wil-content">
    <?php if ( class_exists('Vc_Manager') && defined('WILCITY_VC_SC') ) : ?>
    <div class="container">
    <?php endif; ?>
    <?php
        if ( have_posts() ){
            while (have_posts()){
                the_post();
                the_content();
            }
        }
    ?>
    <?php if ( class_exists('Vc_Manager') && defined('WILCITY_VC_SC') ) : ?>
    </div>
    <?php endif; ?>
</div>
<?php
get_footer();
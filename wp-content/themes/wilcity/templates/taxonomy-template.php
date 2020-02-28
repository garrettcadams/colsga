<?php
/*
 * Template Name: Wilcity Taxonomy Template
 */
use WilokeListingTools\Framework\Helpers\GetSettings;

get_header();
global $wiloke, $wilcityGallerySettings;
$oTerm = get_queried_object();
$size = apply_filters('wilcity/taxonomy-template/image-cover-size', 'large');
$headerBg = GetSettings::getTermFeaturedImg($oTerm, $size);
$aGradientSettings = GetSettings::getTermGradients($oTerm);
$wilcityGallerySettings = GetSettings::getTermMeta($oTerm->term_id, 'gallery');

if ( empty($wilcityGallerySettings) ){
	$wilcityGallerySettings = array($headerBg);
} ?>

<section class="wil-image-slider_module__3RUE_ pos-r win-half">
    <?php if ( !empty($wilcityGallerySettings) ) : ?>
    <div class="pos-a-full">
        <div class="swiper__module swiper-container" data-options='{"slidesPerView":1,"effect":"fade","autoplay":{"delay":3000}}'>
            <div class="swiper-wrapper">
                <?php
                foreach ($wilcityGallerySettings as $id => $url):
                    $imgUrl = wp_get_attachment_image_url($id, $size);
                    $imgUrl = empty($imgUrl) ? $url : $imgUrl;
                ?>
                    <div class="bg-cover" style="background-image: url(<?php echo esc_url($imgUrl); ?>)">
                        <?php  if ( !empty($aGradientSettings) ) : ?>
                        <div class="gradient-color" style="background-image: linear-gradient(to top, <?php echo esc_attr($aGradientSettings['left']); ?> 7%, <?php echo esc_attr($aGradientSettings['right']); ?> 100%)"></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if ( count($wilcityGallerySettings) > 1 ) : ?>
                <div class="swiper-pagination-custom"></div>
            <?php endif; ?>
        </div><!-- End / swiper__module swiper-container -->
    </div>
    <?php endif; ?>
    <div class="wil-tb">
        <div class="wil-tb__cell">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 ">
                        <div class="heading_module__156eJ heading_style2__1Cs03 light">
                            <h1 class="heading_title__1bzno"><?php echo esc_html($oTerm->name); ?></h1>
                            <?php if ( $tagLine = GetSettings::getTermMeta($oTerm->term_id, 'tagline') ) : ?>
                            <div class="heading_content__2mtYE"><?php echo esc_html($tagLine); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if ( !empty($oTerm->description) ): ?>
    <section class="wil-section pt-30 pb-30">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-lg-12 ">
                    <div class="utility-description_module__1mldF">
                        <p><?php Wiloke::ksesHTML($oTerm->description); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<div class="wil-content">
    
    <?php if ( class_exists('Vc_Manager') && defined('WILCITY_VC_SC') ) :?>
        <div class="container">
    <?php endif; ?>
        <?php
        
            global $wilcityTaxonomyPageID, $kc;

            if( !empty($wilcityTaxonomyPageID) ) {

                $query = new WP_Query(array(
                    'post_type' => 'page',
                    'page_id'   => $wilcityTaxonomyPageID
                ));

                if ( $query->have_posts() ){
                    while ($query->have_posts()){
                        $query->the_post();

                        $content = get_post_field('post_content_filtered', $query->post->ID);
                    
                        if ( strpos($content, 'kc_') === false ){
                            the_content();
                        }else{
                            if ( class_exists('KingComposer') ){
                                echo $kc->do_shortcode($content);
                            }else{
                                echo do_shortcode($content);
                            }
                        }
                    }
                }

                wp_reset_postdata();

            } else if ( have_posts() ) {

                while ( have_posts() ) { the_post();
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
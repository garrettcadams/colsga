<?php
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Controllers\ReviewController;
use WilokeListingTools\Models\ReviewMetaModel;
use WilokeListingTools\Frontend\BusinessHours;
use WILCITY_SC\SCHelpers;

function wilcity_feature_slider_item($post, $imgSize='wilcity_380x215', $atts=array()){
    $featuredImgUrl = SCHelpers::getFeaturedImage($post->ID, $imgSize);
    $headerClass = 'listing_header__2pt4D';
    if ( \WilokeListingTools\Frontend\SingleListing::isClaimedListing($post->ID, true) ){
        $headerClass .= ' is-verified';
    }
?>
    <article class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-listing-slider')); ?> loading listing_module__2EnGq wil-shadow listing_style2__2PwZl listing_dark__1rJrd  js-listing-module">
        <div class="listing_firstWrap__36UOZ">
            <header class="<?php echo esc_attr($headerClass); ?>">
                <?php SCHelpers::renderAds($post, 'LISTINGS_SLIDER'); ?>
                <a href="<?php echo esc_url(get_permalink($post->ID)); ?>">
                    <?php if ( isset($atts['toggle_gradient']) && $atts['toggle_gradient'] == 'enable' ) : ?>
                    <span class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-slider-gradient-before')); ?>" style="background-image: linear-gradient(45deg, <?php echo esc_attr($atts['left_gradient']); ?> 0%,<?php echo esc_attr($atts['right_gradient']); ?> 100%); opacity: <?php echo esc_attr($atts['gradient_opacity']); ?>;"></span>
                    <?php endif; ?>

                    <?php if ( $atts['lazyLoad'] == 'yes' ) : ?>
                    <div class="listing_img__3pwlB pos-a-full bg-cover swiper-lazy" data-mobilebg="<?php echo esc_url($featuredImgUrl) ?>" data-background="<?php echo esc_url($featuredImgUrl); ?>">
                        <div class="swiper-lazy-preloader"></div>
                    </div>
                    <?php else: ?>
                        <div class="listing_img__3pwlB pos-a-full bg-cover swiper-lazy" style="background-image: url(<?php echo esc_url($featuredImgUrl); ?>)"></div>
                    <?php endif; ?>
					<?php SCHelpers::renderAverageReview($post); ?>
                	<?php if ( isset($atts['toggle_gradient']) && $atts['toggle_gradient'] == 'enable' ) : ?>
                    <span class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-slider-gradient-after')); ?>"></span>
	                <?php endif; ?>
                </a>
	            <?php SCHelpers::renderClaimedBadge($post->ID); ?>
            </header>
            <div class="listing_body__31ndf">
                <a class="listing_goo__3r7Tj" href="<?php echo esc_url(get_permalink($post->ID)); ?>">
                    <div class="listing_logo__PIZwf bg-cover" style="background-image: url(<?php echo esc_url(get_the_post_thumbnail_url($post->ID, 'wilcity_380x215')); ?>)"></div>
                </a>
				<?php SCHelpers::renderTitle($post); ?>
				<?php SCHelpers::renderExcerpt($post); ?>

                <div class="listing_meta__6BbCG">
					<?php SCHelpers::renderAddress($post); ?>
					<?php SCHelpers::renderPhone($post); ?>
                </div>
            </div>
        </div>
        <footer class="listing_footer__1PzMC">
            <div class="text-ellipsis">
                <div class="icon-box-1_module__uyg5F icon-box-1_style2__1EMOP five-text-ellipsis">
					<?php SCHelpers::renderFooterTaxonomy($post); ?>
					<?php SCHelpers::renderBusinessStatus($post, array(), true); ?>
                </div>
            </div>
            <div class="listing_footerRight__2398w">
				<?php SCHelpers::renderFavorite($post); ?>
            </div>
        </footer>

        <div class="listing-loading_module__2_Uwh wave-loading">
            <div class="shape-transparent">
                <div class="shape shape--left"></div>
                <div class="shape shape--right"></div>
            </div>
            <div class="shape shape--special">
                <div class="wave-loading"></div>
            </div>
            <div class="shape shape--1"></div>
            <div class="shape shape--2"></div>
            <div class="shape shape--3"></div>
            <div class="shape shape--4"></div>
        </div><!-- End / listing-loading_module__2_Uwh -->
    </article>
<?php
}

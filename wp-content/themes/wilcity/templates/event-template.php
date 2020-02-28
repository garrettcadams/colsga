<?php
/*
 * Template Name: Wilcity Event Template
 */
use WilokeListingTools\Framework\Helpers\GetSettings;

get_header();
global $wiloke;

    $imgSize = GetSettings::getPostMeta($post->ID, 'search_img_size');
    if ( !empty($imgSize) ){
        $aRequest['img_size'] = $imgSize;
    }else{
        $aRequest['img_size'] = 'wilcity_360x200';
    }

    $aRequest['templateID'] = $post->ID;

    $aRequest = wp_parse_args(
        $aRequest,
        array(
            'postType'      => 'event',
            'image_size'    => '',
            'order'         => '',
            'orderby'       => '',
            'templateID'    => ''
        )
    );

    if ( have_posts() ) : while ( have_posts() ) : the_post();
        $sidebarPos = GetSettings::getPostMeta($post->ID, 'sidebar');
	    $sidebarPos = empty($sidebarPos) ? 'right' : $sidebarPos;
	    switch ($sidebarPos){
            case 'right':
                $wrapperClass = 'col-md-8';
                $sidebarClass = 'col-md-4';
                break;
            case 'left':
	            $wrapperClass = 'col-md-8 col-md-push-4';
	            $sidebarClass = 'col-md-4 col-md-pull-8';
                break;
            default:
	            $wrapperClass = 'col-md-12';
	            $sidebarClass = '';
                break;
        }

        $aScreenSize['lg'] = GetSettings::getPostMeta($post->ID, 'maximum_posts_on_lg_screen');
        $aScreenSize['md'] = GetSettings::getPostMeta($post->ID, 'maximum_posts_on_md_screen');
        $aScreenSize['sm'] = GetSettings::getPostMeta($post->ID, 'maximum_posts_on_sm_screen');
        $postsPerPage = GetSettings::getPostMeta($post->ID, 'events_per_page');
?>
        <div id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-no-map')); ?>" class="wil-content">
            <section class="wil-section bg-color-gray-2 pt-30">
                <div class="container">
                    <div class="row">
                        <div class="<?php echo esc_attr($wrapperClass); ?> js-sticky">
                            <div id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-result-preloader')); ?>" class="full-load" :class="additionalPreloaderClass"><div class="pill-loading_module__3LZ6v pos-a-center"><div class="pill-loading_loader__3LOnT"></div></div></div>
		                    <?php
		                        do_action('wilcity/render-search', array('type'=>'event', 'aItemsPerRow'=>$aScreenSize, 'postsPerPage'=>$postsPerPage, 'img_size'=>$imgSize, 'templateID' => $aRequest['templateID']));
		                    ?>
                        </div>
                        <?php if ( !empty($sidebarClass) ) : ?>
                        <div v-if="!isMobile" class="<?php echo esc_attr($sidebarClass); ?> js-sticky">
                            <search-form v-on:searching="searching" type="event" is-map="no" posts-per-page="<?php echo esc_attr($wiloke->aThemeOptions['listing_posts_per_page']); ?>" raw-taxonomies="" s="" address="" raw-date-range="" lat-lng="" form-item-class="col-md-12" is-popup="no" raw-taxonomies-options="" image-size="<?php echo esc_attr($imgSize); ?>" template-id="<?php echo esc_attr($aRequest['templateID']); ?>" order-by="<?php echo esc_attr($aRequest['orderby']); ?>" order="<?php echo esc_attr($aRequest['order']); ?>"></search-form>
	                        <?php
	                        if ( is_active_sidebar('wilcity-sidebar-events') ){
		                        dynamic_sidebar('wilcity-sidebar-events');
	                        }
	                        ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>
<?php
    endwhile; endif; wp_reset_postdata();
get_footer();

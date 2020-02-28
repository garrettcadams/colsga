<?php
/*
 * Template Name: Wilcity Search Without Map
 */
use WilokeListingTools\Controllers\SearchFormController;
use WilokeListingTools\Framework\Helpers\GetSettings;
use \WilokeListingTools\Framework\Helpers\General;

get_header();
global $wiloke;
$search = $latLng = $address = $taxonomy = $taxID = '';
$aTaxonomies = array();
$aDateRange = array();
$aRequest = SearchFormController::parseRequestFromUrl();
$aRequest['postType'] = General::getDefaultPostTypeKey();
$aRequest['img_size'] = 'large';

if ( is_tax() ){
	$oTerm  = get_queried_object();
	$taxTitle   = $oTerm->name;
	$taxSlug    = $oTerm->slug;
	$taxonomy   = $oTerm->taxonomy;
	$taxID      = $oTerm->term_id;

	if ( $taxonomy == 'listing_cat' || $taxonomy == 'listing_tag' ){
		$aRequest[$taxonomy] = array($taxSlug);
		$aTaxonomies[$taxonomy] = array($taxSlug);
	}else{
		$aRequest[$taxonomy] = $taxSlug;
		$aTaxonomies[$taxonomy] = $taxSlug;
	}

	if ( !isset($_REQUEST['type']) && isset($taxID) ){
		$aBelongsTo = GetSettings::getTermMeta($taxID, 'belongs_to');
		if ( !empty($aBelongsTo) ){
			$aRequest['postType'] = $aBelongsTo[0];
		}
	}

	if ( isset($wiloke->aThemeOptions['taxonomy_image_size']) && !empty($wiloke->aThemeOptions['taxonomy_image_size']) ){
		$aRequest['img_size'] = $wiloke->aThemeOptions['taxonomy_image_size'];
	}
	$aTaxonomiesKeys = array('listing_cat', 'listing_location', 'listing_tag');

	foreach ($aTaxonomiesKeys as $order => $taxKey){
		if ( isset($_GET[$taxKey]) ){
			if ( $taxonomy == 'listing_cat' || $taxonomy == 'listing_tag' ){
				$aTaxonomies[$taxKey] = array($taxSlug);
			}else{
				$aTaxonomies[$taxKey] = $_GET[$taxKey];
			}
		}
	}

	global $wiloke;
	$headerBg = GetSettings::getTermFeaturedImg($oTerm);
	$overColor = WilokeThemeOptions::getColor('listing_overlay_color');
	$aGradientSettings = GetSettings::getTermGradients($oTerm);
	$wilcityGallerySettings = GetSettings::getTermMeta($oTerm->term_id, 'gallery');

	if ( empty($wilcityGallerySettings) ){
		$wilcityGallerySettings = array($headerBg);
	}

	?>
    <div class="wil-content">
		
        <div class="wil-section bg-cover" style="background-image: url(<?php echo esc_url($headerBg); ?>)">

            <div class="container">
				<?php if ( !empty($overColor) ) : ?>
                    <div class="wil-overlay" style="background-color: <?php echo esc_attr($overColor); ?>"></div>
				<?php else: ?>
                    <div class="wil-overlay"></div>
				<?php endif; ?>
                <div class="row">
                    <div class="col-md-12 ">
                        <div class="heading_module__156eJ light wil-text-center mb-0">
                            <h1 class="heading_title__1bzno"><?php echo esc_html($oTerm->name); ?></h1>
							<?php if ( !empty($oTerm->description) ) : ?>
                                <div class="heading_content__2mtYE">
									<?php Wiloke::ksesHTML($oTerm->description); ?>
                                </div>
							<?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

			<?php if ( !empty($wilcityGallerySettings) ) : ?>
				<div class="wil-image-slider_term-without-map">
					<div class="swiper__module swiper-container" data-options='{"slidesPerView":1,"effect":"fade","autoplay":{"delay":3000}}'>
						<div class="swiper-wrapper">
							<?php
							foreach ($wilcityGallerySettings as $id => $url):
								$imgUrl = wp_get_attachment_image_url($id, 'large');
								$imgUrl = empty($imgUrl) ? $url : $imgUrl;
							?>
								<div class="bg-cover" style="background-image: url(<?php echo esc_url($imgUrl); ?>)"></div>
							<?php endforeach; ?>
						</div>
					</div><!-- End / swiper__module swiper-container -->
				</div>

			<?php endif; ?>

        </div>
    </div>
	<?php
    $searchPageID = $wiloke->aThemeOptions['search_page'];
}else{
	if ( isset($aRequest['listing_cat']) ){
		$aTaxonomies['listing_cat'] = $aRequest['listing_cat'];
	}

	if ( isset($aRequest['listing_location']) ){
		$aTaxonomies['listing_location'] = $aRequest['listing_location'];
	}

	if ( isset($aRequest['listing_tag']) ){
		$aTaxonomies['listing_tag'] = $aRequest['listing_tag'];
	}

	$imgSize = GetSettings::getPostMeta($post->ID, 'search_img_size');

	if ( !empty($imgSize) ){
		$aRequest['img_size'] = $imgSize;
	}else{
		$aRequest['img_size'] = apply_filters('wilcity/filter/search-without-map/default-img-size', 'wilcity_360x200');
	}

	if ( isset($_REQUEST['type']) ){
	    $aRequest['postType'] = $_REQUEST['type'];
    }else if ( isset($_REQUEST['post_type']) ){
		$aRequest['postType'] = $_REQUEST['post_type'];
	}

	if ( isset($_REQUEST['order_by']) ){
		$aRequest['orderby'] = $_REQUEST['order_by'];
	}

	$aRequest['templateID'] = $post->ID;
	$searchPageID = $post->ID;
}
$aTaxonomiesOption = array();

if ( !empty($aTaxonomies) ){
	foreach ($aTaxonomies as $tax => $rawSlug){
		$slug = is_array($rawSlug) ? $rawSlug[0] : $rawSlug;
		$oTermInfo = get_term_by('slug', $slug, $tax);
		if ( !empty($oTermInfo) && !is_wp_error($oTermInfo) ){
			$aTaxonomiesOption[$tax] = array(
				array(
					'name' => $oTermInfo->name,
					'value'=> $slug
				)
			);

			$taxID = $oTermInfo->term_id;
		}
	}
}

if ( isset($_REQUEST['order_by']) ){
	$aRequest['orderby'] = $_REQUEST['order_by'];
}

if ( isset($aRequest['oAddress']) ){
    $address = $aRequest['oAddress']['address'];
	$latLng  = $aRequest['oAddress']['lat'] . ','.$aRequest['oAddress']['lng'];
}

if ( isset($_REQUEST['type']) && !empty($_REQUEST['type']) ){
	$aRequest['postType'] = $_REQUEST['type'];
}

if ( !empty($aRequest['date_range']) ){
	$aDateRange = $aRequest['date_range'];
}

$search = isset($aRequest['title']) ? $aRequest['title'] : '';

if ( isset($aRequest['title']) ){
	$aRequest['s'] = $aRequest['title'];
}

if ( !isset($aRequest['img_size']) ){
	$aRequest['img_size'] = '';
}

$aRequest['style'] = GetSettings::getPostMeta($searchPageID, 'style');
$aRequest['style'] = empty($aRequest['style']) ? 'grid' : $aRequest['style'];

$aRequest = wp_parse_args(
	$aRequest,
	array(
		'postType' => 'listing',
		'image_size' => '',
        'order' => '',
        'orderby' => '',
        'templateID' => ''
	)
);

$listSwitchClass = 'listing-bar_item__266Xo listing-bar_item__266Xo--switch js-list-button';
$gridSwitchClass = 'listing-bar_item__266Xo listing-bar_item__266Xo--switch js-grid-button';
if ( $aRequest['style'] == 'list' ){
    $listSwitchClass .= ' color-primary';
}else{
	$gridSwitchClass .= ' color-primary';
}

$class_hidden_bar = $aRequest['postType'] == 'event' ? 'hidden' : '';

?>
    <div id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-no-map')); ?>" class="wil-content">
		<?php do_action('wilcity/search-without-map/before-section'); ?>
        <section class="wil-section bg-color-gray-2 pt-0">
            <div class="listing-bar_module__2BCsi js-listing-bar-sticky js-sticky-for-md">
                <div class="container">
                    <div class="listing-bar_resuilt__R8pwY visible-hidden" :class="searchResultAdditionalClass">
						<?php esc_html_e('We found ', 'wilcity'); ?><span class="color-primary">{{foundPosts}} </span> {{resultText}}
						<?php if ( !isset($taxTitle) ) : ?>
                            <a id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-reset-search')); ?>" class="wil-btn wil-btn--border wil-btn--round wil-btn--xs" href="#" @click.prevent="resetSearchForm"><i class="color-primary la la-share"></i> <?php esc_html_e('Reset', 'wilcity'); ?></a>
						<?php else: ?>
                            <h2 style="font-size: 13px; display: inline"><a id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-reset-search')); ?>" class="wil-btn wil-btn--border wil-btn--round wil-btn--xs" href="#" @click.prevent="resetSearchForm"><i class="color-primary la la-share"></i> <?php echo esc_html($taxTitle); ?></a></h2>
						<?php endif; ?>
                    </div>
                    <div class="listing-bar_layout__TK3vH">
                        <a class="<?php echo esc_attr($gridSwitchClass . ' ' . $class_hidden_bar); ?>" @click.prevent="switchStyle('<?php echo $aRequest['style'] == 'grid2' ? 'grid2' : 'grid'; ?>')" href="#" data-tooltip="<?php echo esc_attr__('Grid Layout', 'wilcity'); ?>" data-tooltip-placement="bottom"><i class="la la-th-large"></i></a>
                        <a class="<?php echo esc_attr($listSwitchClass . ' ' . $class_hidden_bar); ?>" href="#" @click.prevent="switchStyle('list')" data-tooltip="<?php echo esc_attr__('List Layout', 'wilcity'); ?>" data-tooltip-placement="bottom"><i class="la la-list"></i></a>
                        <a class="listing-bar_item__266Xo js-map-button" href="#"><i class="la la-map-marker"></i><i class="la la-close"></i></a>
                        <a class="wil-btn js-listing-search-button wil-btn--primary wil-btn--round wil-btn--xs" href="#">
                            <i class="la la-search"></i> <?php esc_html_e('Search', 'wilcity'); ?>
                        </a>
                        <a class="wil-btn js-listing-search-button-mobile wil-btn--primary wil-btn--round wil-btn--xs" href="#" @click.prevent="toggleSearchFormPopup"><i class="la la-search"></i> <?php esc_html_e('Search', 'wilcity'); ?></a>
                    </div>
                </div>
            </div>

            <div class="container mt-30">
                <div class="row flex-sm">
                    <div v-if="!isMobile" class="wil-page-sidebar left js-sticky js-listing-search">
                        <div style="min-height: 300px">
                            <div class="full-load" :class="loadingMakeSearchFormLookBetter"><div class="pill-loading_module__3LZ6v pos-a-center"><div class="pill-loading_loader__3LOnT"></div></div></div>
                            <search-form v-on:searching="searching" type="<?php echo esc_attr($aRequest['postType']); ?>" is-map="no" posts-per-page="<?php echo esc_attr($wiloke->aThemeOptions['listing_posts_per_page']); ?>" raw-taxonomies='<?php echo esc_attr(json_encode($aTaxonomies)); ?>' s="<?php echo esc_attr($search); ?>" address="<?php echo esc_attr($address); ?>" raw-date-range='<?php echo esc_attr(json_encode($aDateRange)); ?>' lat-lng="<?php echo esc_attr($latLng); ?>" form-item-class="col-md-6 col-lg-6" is-popup="no" taxonomy="<?php echo esc_attr($taxonomy); ?>" cat-id="<?php echo esc_attr($taxID); ?>" image-size="<?php echo esc_attr($aRequest['img_size']); ?>" raw-taxonomies-options="<?php echo esc_attr(json_encode($aTaxonomiesOption)); ?>" order-by="<?php echo esc_attr($aRequest['orderby']); ?>" order="<?php echo esc_attr($aRequest['order']); ?>" template-id="<?php echo esc_attr($aRequest['templateID']); ?>" template-style="<?php echo esc_attr($aRequest['style']); ?>"></search-form>
                        </div>
						<?php
						do_action('wilcity/search-without-map-sidebar');
						if ( is_active_sidebar('wilcity-listing-taxonomy') ){
							dynamic_sidebar('wilcity-listing-taxonomy');
						}
						?>
                    </div>

                    <div class="wil-page-content js-sticky">
                        <div id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-result-preloader')); ?>" class="full-load" :class="additionalPreloaderClass"><div class="pill-loading_module__3LZ6v pos-a-center"><div class="pill-loading_loader__3LOnT"></div></div></div>
	                    <?php do_action('wilcity/render-search', $aRequest); ?>
                    </div>
                </div>
            </div>
        </section>
		<?php do_action('wilcity/search-without-map/after-section'); ?>
    </div>
<?php
get_footer();

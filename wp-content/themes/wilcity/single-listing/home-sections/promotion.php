<?php
use WilokeListingTools\Models\ReviewModel;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Controllers\ViewStatisticController;
use WilokeListingTools\Controllers\SharesStatisticController;
use WilokeListingTools\Controllers\FavoriteStatisticController;

global $post, $wilcityArgs;

if ( !User::isPostAuthor($post) || $post->post_status !== 'publish' ){
    return '';
}

$togglePromotion = \WilokeListingTools\Framework\Helpers\GetSettings::getOptions('toggle_promotion');
if ( $togglePromotion != 'enable' ){
	return '';
}

$averageReviews = GetSettings::getPostMeta($post->ID, 'average_reviews');
$averageReviews = empty($averageReviews) ? 0 : number_format($averageReviews, 1);

$aCompareViews = ViewStatisticController::compare($post->post_author, $post->ID);
$aCompareFavorites = FavoriteStatisticController::compare($post->ID);
$aCompareShares = SharesStatisticController::compare($post->ID);
$mode = ReviewModel::getReviewMode($post->post_type);

if ( $mode == 5 ){
    $dataRated = floatval($averageReviews)*2;
}else{
    $dataRated = $averageReviews;
}

?>
<!-- promo-item-2_module__2mwrO -->
<div class="content-box_module__333d9">
	<header class="content-box_header__xPnGx clearfix">
		<div class="wil-float-left">
			<h4 class="content-box_title__1gBHS"><i class="la la-bar-chart"></i><span><?php esc_html_e('Boost your listing', 'wilcity'); ?></span></h4>
		</div>
	</header>

	<div class="content-box_body__3tSRB">
		<div class="row">

			<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
				<div class="rated-small_module__1vw2B rated-small_style-3__1c0gb mb-15">
					<div class="rated-small_wrap__2Eetz" data-rated="<?php echo esc_attr($dataRated); ?>">
						<div class="rated-small_overallRating__oFmKR"><?php echo esc_html($averageReviews); ?></div>
						<div class="rated-small_ratingWrap__3lzhB">
							<div class="rated-small_maxRating__2D9mI"><?php echo esc_html($mode); ?></div>
						</div>
					</div>
				</div>
			</div>

            <single-compare-views :wrapper-class="'col-xs-6 col-sm-6 col-md-3 col-lg-3'" :place-holder-text="'<?php echo esc_attr__('View', 'wilcity'); ?>'"></single-compare-views>
            <single-compare-favorites :wrapper-class="'col-xs-6 col-sm-6 col-md-3 col-lg-3'" :place-holder-text="'<?php echo esc_attr__('Favorites', 'wilcity'); ?>'"></single-compare-favorites>
            <single-compare-shares :wrapper-class="'col-xs-6 col-sm-6 col-md-3 col-lg-3'" :place-holder-text="'<?php echo esc_attr__('Shares', 'wilcity'); ?>'"></single-compare-shares>

		</div>
		<div class="wil-divider wil-divider--forBox mb-15"></div>
		<div class="promo-item-2_module__2mwrO">
			<div class="promo-item-2_icon__2EU_c">
				<i class="la la-user la1"></i>
				<i class="la la-user la2"></i>
				<i class="la la-user la3"></i>
				<i class="color-primary la la-thumbs-o-up la4"></i>
			</div>
			<div class="promo-item-2_group__KUQtl">
				<h3 class="promo-item-2_title__Ghd11"><?php esc_html_e('Boost Your Listing today', 'wilcity'); ?></h3>
				<p class="promo-item-2_description__1KXY2"><?php esc_html_e('Reach more people visit your listing', 'wilcity'); ?></p>
			</div>
			<div class="promo-item-2_action__Gnojf">
				<a class="temporary-disable wil-btn wil-btn--primary wil-btn--sm wil-btn--round disable" @click.prevent="onOpenPromotionPopup('<?php echo esc_attr($post->ID); ?>')" href="#"><?php esc_html_e('Promote Listing', 'wilcity'); ?></a>
			</div>
		</div><!-- End / promo-item-2_module__2mwrO -->
	</div>
</div>

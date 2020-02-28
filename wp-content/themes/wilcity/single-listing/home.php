<?php
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Controllers\ReviewController;
use WilokeListingTools\Models\ReviewMetaModel;
use WilokeListingTools\Models\ReviewModel;
use WilokeListingTools\Frontend\SingleListing;
use WilokeListingTools\Framework\Helpers\General;

global $post, $wiloke, $wilcityArgs, $wilcitySingleSidebarPos;

$aContentsOrder = SingleListing::getNavOrder();
?>
<div id="single-home" class="single-tab-content <?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-js-toggle-group')); ?>" data-tab-key="home"  v-show="isSingleNavActivating('home')">
    <div class="listing-detail_row__2UU6R clearfix">
        <div class="wil-colLarge <?php echo esc_attr($wilcitySingleSidebarPos); ?>" data-additional-class="<?php echo esc_attr($wilcitySingleSidebarPos); ?>">
            <div class="mb-20">
                <div class="row" data-col-xs-gap="20">
                    <?php get_template_part('single-listing/top-block'); ?>
                </div>
            </div>
            <?php get_template_part('single-listing/home-sections/promotion'); ?>

            <?php
            $mode = ReviewController::getMode($post->post_type);
            if ( $mode ) :
                $aGeneralReviewData = ReviewMetaModel::getGeneralReviewData($post->ID);
                if ( !empty($aGeneralReviewData['total']) ) :
                    $aReviewDetails     = GetSettings::getOptions(General::getReviewKey('details', $post->post_type));
                    $aReviewQuality     = $mode;
                    $reviewsTotal       = $aGeneralReviewData['total'];
                    $reviewsAverage     = $aGeneralReviewData['average'];
                    $reviewQuality      = $aGeneralReviewData['quality'];
                    $aReviewDetails     = $aGeneralReviewData['aDetails'];
                    $isEnableReview     = true;
                    $isUserReviewed = ReviewModel::isUserReviewed($post->ID) ? 'yes' : 'no';
            ?>
                    <wiloke-average-rating quality="<?php echo esc_attr($reviewQuality); ?>" average="<?php echo esc_attr($reviewsAverage); ?>" mode="<?php echo esc_attr($mode); ?>" total="<?php echo esc_attr($reviewsTotal); ?>" raw-review-details-result="<?php echo esc_attr(json_encode($aReviewDetails)); ?>" post-title="<?php echo esc_attr__('Average Reviews', 'wilcity'); ?>" is-user-reviewed="<?php echo esc_attr($isUserReviewed); ?>" v-on:on-open-review-popup="onOpenReviewPopup"></wiloke-average-rating>
            <?php endif;
            endif; ?>

            <?php
            foreach ($aContentsOrder as $aContentSetting) {
                if ( $aContentSetting['isShowOnHome'] == 'no' ) {
                    continue;
                }

	            $wilcityArgs = $aContentSetting;

                if ( isset($aContentSetting['isCustomSection']) && $aContentSetting['isCustomSection'] == 'yes' ){
	                $fileName = 'custom-section';
                }elseif ( strpos($aContentSetting['key'], 'google_adsense') !== false ){
	                $fileName = 'google-adsense';
                }else{
	                $fileName = $aContentSetting['key'];
                }
                get_template_part( 'single-listing/home-sections/' . $fileName);
            }
            ?>
        </div>
        <div class="wil-colSmall <?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-js-toggle-group')); ?>" data-tab-key="home">
		    <?php
		    /*
			 * @hooked SingleListing:printContent
			 */
		    get_template_part('single-listing/sidebar');
		    ?>
        </div>
    </div>
</div>
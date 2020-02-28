<?php
global $post, $wilcityArgs;

use WilokeListingTools\Models\ReviewModel;
use WilokeListingTools\Models\ReviewMetaModel;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Controllers\ReviewController;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Frontend\User as WilokeUser;

$aGeneralReviewData = ReviewMetaModel::getGeneralReviewData($post->ID);

$aReviewSettings    = wilokeListingToolsRepository()->get('reviews');
$aReviewDetails     = GetSettings::getOptions(General::getReviewKey('details', $post->post_type));

$aAverageReviews    = array();
$totalReviews       = $aGeneralReviewData['total'];
$average            = $aGeneralReviewData['average'];
$mode               = ReviewController::getMode($post->post_type);
$reviewQuality      = ReviewMetaModel::getReviewQualityString($average, $post->post_type);
$totalReviewItems   = empty($aReviewDetails) ? 0 : count($aReviewDetails);
$isUserReviewed     = ReviewModel::isUserReviewed($post->ID) ? 'yes' : 'no';
$toggleReport       = GetSettings::getOptions('toggle_report');;

$canDoAnything = 'no';
if ( current_user_can('administrator') ){
	$canDoAnything = 'yes';
}
?>
<single-listing-reviews v-on:on-open-report-popup="onOpenReportPopup" is-user-reviewed="<?php echo esc_attr($isUserReviewed); ?>" is-allow-reported="<?php echo esc_attr($toggleReport); ?>" post-title="<?php echo esc_attr($post->post_title); ?>" raw-review-details-result="<?php echo esc_attr(json_encode($aGeneralReviewData['aDetails'])); ?>" mode="<?php echo esc_attr($mode); ?>" total-reviews="<?php echo esc_attr($totalReviews); ?>" average-review="<?php echo esc_attr($average); ?>" v-on:on-open-review-popup="onOpenReviewPopup" review-quality="<?php echo esc_attr($reviewQuality); ?>" can-do-anything="<?php echo esc_attr($canDoAnything); ?>" page-url="<?php echo esc_url(get_permalink($post->ID)); ?>" current-page="<?php echo isset($_GET['paged']) ? abs($_GET['paged']) : 1; ?>" posts-per-page="<?php echo isset($wilcityArgs['maximumItemsOnHome']) ? abs($wilcityArgs['maximumItemsOnHome']) : ''; ?>"></single-listing-reviews>

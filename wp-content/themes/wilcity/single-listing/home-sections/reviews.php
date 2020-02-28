<?php
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Controllers\ReviewController;
use WilokeListingTools\Models\ReviewMetaModel;
use WilokeListingTools\Models\ReviewModel;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;

global $post, $wiloke, $wilcityoReview, $wilcityaUserInfo, $wilcityReviewConfiguration, $wilcityParentPost, $wilcityArgs;
$wilcityParentPost = $post;

$oSomeReviews = ReviewModel::getReviews($post->ID, array(
	'postsPerPage' => $wilcityArgs['maximumItemsOnHome'],
    'page' => isset($_GET['page']) ? abs($_GET['page']) : 1
));

$aGeneralReviewData = ReviewMetaModel::getGeneralReviewData($post->ID);
$reviewsTotal       = $aGeneralReviewData['total'];
$wilcityaUserInfo['avatar']   = User::getAvatar();
$wilcityaUserInfo['position'] = User::getPosition();
$wilcityaUserInfo['displayName'] = User::getField('display_name');

$wilcityReviewConfiguration['enableReviewDiscussion'] = ReviewController::isEnabledDiscussion($post->post_type);
$isUserReviewed = ReviewModel::isUserReviewed($post->ID) ? 'yes' : 'no';
$toggleReview = ReviewController::isEnabledReview($post->post_type) ? 'enable' : 'disable';

?>
<review-statistic v-on:on-open-review-popup="onOpenReviewPopup" is-use-reviewed="<?php echo esc_attr($isUserReviewed); ?>" total-reviews="<?php echo esc_attr($reviewsTotal); ?>" post-title="<?php echo esc_attr($post->post_title); ?>" toggle-review="<?php echo esc_attr($toggleReview); ?>"></review-statistic>
<?php
if ( $oSomeReviews ) {
	while ($oSomeReviews->have_posts()){
	    $oSomeReviews->the_post();
		$wilcityoReview = $oSomeReviews->post;
		get_template_part('reviews/item');
	}
	wp_reset_postdata();
}

<?php
global $wiloke, $wilcityParentPost, $wilcityoReview, $wilcityReviewConfiguration, $wilcityaUserInfo;

use WilokeListingTools\Models\ReviewModel;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Controllers\ReviewController;
use \WilokeListingTools\Models\ReviewMetaModel;
use WilokeListingTools\Controllers\SharesStatisticController;

$score = ReviewMetaModel::getAverageReviewsItem($wilcityoReview->ID);
$authorPostsUrl = get_author_posts_url($wilcityoReview->post_author);

$dataRated = $mode = 0;
if ( !empty($wilcityParentPost) ){
	$mode = ReviewController::getMode($wilcityParentPost->post_type);
	if ( $mode == 5 ){
		$dataRated = floatval($score)*2;
	}else{
		$dataRated = $score;
	}
}

?>
<div class="comment-review_module__-Z5tr js-review-item-<?php echo esc_attr($wilcityoReview->ID); ?>">
    <div class="comment-review_header__1si3M">
        <div class="utility-box-1_module__MYXpX utility-box-1_boxLeft__3iS6b clearfix utility-box-1_sm__mopok  review-author-avatar">
            <a  href="<?php echo esc_url($authorPostsUrl) ?>" class="utility-box-1_avatar__DB9c_ rounded-circle" style="background-image: url('<?php echo esc_url(User::getAvatar($wilcityoReview->post_author)); ?>');">
                <img src="<?php echo esc_url(User::getAvatar($wilcityoReview->post_author)); ?>" alt="<?php echo esc_attr(User::getField('display_name', $wilcityoReview->ID)); ?>"/>
            </a>
            <div class="utility-box-1_body__8qd9j">
                <div class="utility-box-1_group__2ZPA2">
                    <h3 class="utility-box-1_title__1I925"><a href="<?php echo esc_url($authorPostsUrl)?>"><?php echo User::getField('display_name', $wilcityoReview->post_author); ?></a></h3>
                </div>
                <div class="utility-box-1_description__2VDJ6 wilcity-review-date-<?php echo esc_attr($wilcityoReview->ID); ?>"><?php echo get_the_date('M d, Y', $wilcityoReview->ID); ?></div>
            </div>
        </div>
        <div class="comment-review_abs__9mb1G pos-a-center-right">
			<?php if ( ReviewController::isSticky($wilcityoReview) ) : ?>
                <span class="comment-review_sticky__3iQ8y color-primary fs-20 d-inline-block v-middle"><i class="la la-thumb-tack"></i></span>
			<?php endif; ?>
            <div class="rated-small_module__1vw2B rated-small_style-1__2lG7u ml-20">
                <div class="rated-small_wrap__2Eetz wilcity-data-average-review-score-<?php echo esc_attr($wilcityoReview->ID); ?> <?php if (empty($score) ){ echo 'hidden'; } ?>" data-rated="<?php echo esc_attr($dataRated); ?>">
                    <div class="rated-small_overallRating__oFmKR wilcity-average-review-score-<?php echo esc_attr($wilcityoReview->ID); ?>"><?php echo esc_attr($score); ?></div>
                    <div class="rated-small_ratingWrap__3lzhB">
                        <div class="rated-small_maxRating__2D9mI"><?php echo esc_html(ReviewModel::getReviewMode($wilcityParentPost->post_type)); ?></div>
                        <div class="rated-small_ratingOverview__2kCI_ wilcity-review-quality-<?php echo esc_attr($wilcityoReview->ID); ?>"><?php echo esc_html(ReviewMetaModel::getReviewQualityString($score, $wilcityParentPost->post_type)); ?></div>
                    </div>
                </div>
            </div>
			<?php get_template_part('reviews/toolbar'); ?>
        </div>
    </div>
    <div class="comment-review_body__qhUqq">
        <div class="comment-review_content__1jFfZ">
			<?php if ( !isset($wilcityReviewConfiguration['turnOffTitle']) || !$wilcityReviewConfiguration['turnOffTitle'] ) : ?>
                <h3 class="comment-review_title__2WbAh wilcity-review-title-<?php echo esc_attr($wilcityoReview->ID); ?>"><a href="<?php echo esc_url($authorPostsUrl) ?>"><?php echo esc_html($wilcityoReview->post_title); ?></a></h3>
			<?php endif; ?>
            <div class="wilcity-review-content wilcity-text-show-less wilcity-review-content wilcity-review-content-<?php echo esc_attr($wilcityoReview->ID); ?>"><?php Wiloke::ksesHTML(wpautop($wilcityoReview->post_content)); ?></div>
            <a href="#" class="wilcity-expand-text wilcity-show-more color-primary"><?php esc_html_e('Show more', 'wilcity'); ?></a>
            <a href="#" class="wilcity-expand-text wilcity-show-less color-primary"><?php esc_html_e('Show less', 'wilcity'); ?></a>
        </div>
		<?php
		echo do_shortcode('[wilcity_gallery gallery_id="wilcity-review-gallery-'.esc_attr($wilcityoReview->ID).'" post_id="'.esc_attr($wilcityoReview->ID).'" max_photos="4"]');
		?>
        <div class="comment-review_meta__1chzm">
            <span data-count-liked="<?php echo esc_attr(ReviewMetaModel::countLiked($wilcityoReview->ID)); ?>" class="wilcity-count-liked-<?php echo esc_attr($wilcityoReview->ID); ?>"><?php echo esc_html(ReviewMetaModel::countLiked($wilcityoReview->ID) . ' ' . $wiloke->aConfigs['translation']['liked']); ?></span>
			<?php ReviewMetaModel::countDiscussion($wilcityoReview->ID); ?>
			<?php SharesStatisticController::renderShared($wilcityoReview->ID, true); ?>
        </div>
    </div>
    <footer class="comment-review_footer__3XR0_">
        <div class="comment-review_btnGroup__1PqPh">
			<?php $aLikeStatus = ReviewController::isLikedReview($wilcityoReview->ID); ?>
            <div class="comment-review_btn__32CMP">
                <a class="utility-meta_module__mfOnV wilcity-i-like-it <?php echo esc_attr($aLikeStatus['class']); ?>" href="#" data-id="<?php echo esc_attr($wilcityoReview->ID); ?>"><i class="la la-thumbs-up"></i><?php echo esc_html($aLikeStatus['is']); ?></a>
            </div>

			<?php if ( $wilcityReviewConfiguration['enableReviewDiscussion'] ) : ?>
                <div class="comment-review_btn__32CMP">
                    <a data-id="<?php echo esc_attr($wilcityoReview->ID); ?>" class="utility-meta_module__mfOnV wilcity-add-new-discussion" href="#"><i class="la la-comments-o"></i><?php esc_html_e('Comment', 'wilcity'); ?></a>
                </div>
			<?php endif; ?>

            <div class="comment-review_btn__32CMP">
                <a class="utility-meta_module__mfOnV" href="#" data-toggle-button="share" data-body-toggle="true"><i class="la la-share-square-o"></i><?php esc_html_e('Share', 'wilcity'); ?>
                </a>
                <div class="comment-review_shareContent__UGmyE" data-toggle-content="share">
					<?php echo do_shortcode('[wilcity_sharing_post post_id="'.esc_attr($wilcityoReview->ID).'"]'); ?>
                </div>
            </div>
        </div>

		<?php
		$oChildren = ReviewModel::getReviews($wilcityoReview->ID, array('postsPerPage'=>WILCITY_NUMBER_OF_DISCUSSIONS, 'page'=>1));

		$isAllowDiscussion = $wilcityReviewConfiguration['enableReviewDiscussion'] && $wilcityReviewConfiguration['enableReviewDiscussion'] ? 'yes' : 'no';
		$aDiscussions = array();
		$foundPosts = 0;

		if ( $oChildren ){
			while ($oChildren->have_posts()){
				$oChildren->the_post();
				$aDiscussions[] = ReviewController::getReviewInfo($oChildren->post, $wilcityoReview->ID, true);
			}
			wp_reset_postdata();
			$foundPosts = $oChildren->found_posts;
		}
		?>
        <wiloke-discussions max-discussions="<?php echo esc_attr($foundPosts); ?>" a-old-discussions="<?php echo esc_attr(json_encode($aDiscussions)); ?>" parent-id="<?php echo esc_attr($wilcityoReview->ID); ?>" o-user="<?php echo esc_attr(json_encode($wilcityaUserInfo)); ?>" toggle-review-discussion="<?php echo esc_attr($isAllowDiscussion); ?>"></wiloke-discussions>
    </footer>
</div><!-- End / comment-review_module__-Z5tr -->

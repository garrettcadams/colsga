<?php
global $wiloke, $wilcityoReview;
use WilokeListingTools\Models\ReviewModel;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Controllers\ReviewController;
use WilokeListingTools\Controllers\ShareController;
use \WilokeListingTools\Models\ReviewMetaModel;

$score = ReviewMetaModel::getAverageReviewsItem($wilcityoReview->ID);
$dataRated = ReviewMetaModel::getDataReviewItemRated($wilcityoReview->ID, $score);
?>
<div class="comment-review_module__-Z5tr wilcity-review-item-<?php echo esc_attr($wilcityoReview->ID); ?>">
    <div class="comment-review_header__1si3M">
        <!-- utility-box-1_module__MYXpX -->
        <div class="utility-box-1_module__MYXpX utility-box-1_boxLeft__3iS6b clearfix utility-box-1_sm__mopok  review-author-avatar">
            <div class="utility-box-1_avatar__DB9c_ rounded-circle" style="background-image: url('<?php echo esc_url(User::getAvatar($wilcityoReview->post_author)); ?>');"><img src="<?php echo esc_url(User::getAvatar($wilcityoReview->post_author)); ?>" alt="<?php echo esc_attr(User::getField('display_name', $wilcityoReview->post_author)); ?>"/></div>
            <div class="utility-box-1_body__8qd9j">
                <div class="utility-box-1_group__2ZPA2">
                    <h3 class="utility-box-1_title__1I925"><?php echo User::getField('display_name', $wilcityoReview->post_author); ?></h3>
                </div>
                <div class="utility-box-1_description__2VDJ6 wilcity-review-date-<?php echo esc_attr($wilcityoReview->ID); ?>"><?php echo get_the_date('M d, Y', $wilcityoReview->ID); ?></div>
            </div>
        </div><!-- End / utility-box-1_module__MYXpX -->

        <div class="comment-review_abs__9mb1G pos-a-center-right">
			<?php if ( ReviewController::isSticky($wilcityoReview) ) : ?>
                <span class="comment-review_sticky__3iQ8y color-primary fs-20 d-inline-block v-middle"><i class="la la-thumb-tack"></i></span>
			<?php endif; ?>
            <!-- rated-small_module__1vw2B -->
            <div class="rated-small_module__1vw2B rated-small_style-1__2lG7u ml-20">
                <div class="rated-small_wrap__2Eetz wilcity-data-average-review-score-<?php echo esc_attr($wilcityoReview->ID); ?> <?php if (empty($score) ){ echo 'hidden'; } ?>" data-rated="<?php echo esc_attr($dataRated); ?>">
                    <div class="rated-small_overallRating__oFmKR wilcity-average-review-score-<?php echo esc_attr($wilcityoReview->ID); ?>"><?php echo esc_attr($score); ?></div>
                    <div class="rated-small_ratingWrap__3lzhB">
                        <div class="rated-small_maxRating__2D9mI"><?php echo esc_html(ReviewModel::getReviewMode($wilcityoReview->post_type)); ?></div>
                        <div class="rated-small_ratingOverview__2kCI_ wilcity-review-quality-<?php echo esc_attr($wilcityoReview->ID); ?>"><?php echo esc_html(ReviewMetaModel::getReviewQualityString($score, $wilcityoReview->post_type)); ?></div>
                    </div>
                </div>
            </div><!-- End / rated-small_module__1vw2B -->

			<?php get_template_part('single-listing/partials/review-toolbar'); ?>

        </div>
    </div>
    <div class="comment-review_body__qhUqq">
        <div class="comment-review_content__1jFfZ">
            <h3 class="comment-review_title__2WbAh wilcity-review-title-<?php echo esc_attr($wilcityoReview->ID); ?>"><?php echo esc_html($wilcityoReview->post_title); ?></h3>
            <div class="wilcity-review-content wilcity-text-show-less wilcity-review-content-<?php echo esc_attr($wilcityoReview->ID); ?>"><?php Wiloke::ksesHTML(nl2br($wilcityoReview->post_content)); ?></div>
        </div>
		<?php
		echo do_shortcode('[wilcity_gallery gallery_id="wilcity-review-gallery-'.esc_attr($wilcityoReview->ID).'" review_id="'.esc_attr($wilcityoReview->ID).'" max_Photos="3"]');
		?>
        <div class="comment-review_meta__1chzm">
            <span class="wilcity-count-liked-<?php echo esc_attr($wilcityoReview->ID); ?>"><?php echo esc_html(ReviewMetaModel::countLiked($wilcityoReview->ID) . ' ' . $wiloke->aConfigs['translation']['liked']); ?></span>
			<?php ReviewMetaModel::countDiscussion($wilcityoReview->ID); ?>
			<?php ShareController::countShared($wilcityoReview->ID); ?>
        </div>
    </div>
    <footer class="comment-review_footer__3XR0_">
        <div class="comment-review_btnGroup__1PqPh">
			<?php $aLikeStatus = ReviewController::isLikedReview($wilcityoReview->ID); ?>
            <div class="comment-review_btn__32CMP">
                <a class="utility-meta_module__mfOnV wilcity-i-like-it <?php echo esc_attr($aLikeStatus['class']); ?>" href="#" data-id="<?php echo esc_attr($wilcityoReview->ID); ?>"><i class="la la-thumbs-up"></i><?php echo esc_html($aLikeStatus['is']); ?></a>
            </div>

			<?php if ( ReviewController::isEnabledDiscussion($wilcityoReview->post_type) ) : ?>
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

        <ul class="comment-review_commentlist__1LH_D list-none">
			<?php
			$oChildren = ReviewModel::getDiscussions($wilcityoReview->ID, -1);
			if ( $oChildren ){
				global $wilcityoDiscussion;
				foreach ($oChildren  as $wilcityoDiscussion){
					get_template_part('single-listing/partials/review-discussion');
				}
			}
			?>
        </ul>
		<?php if ( ReviewController::isEnabledDiscussion(get_post_type($parentID)) ) : ?>
			<?php get_template_part('single-listing/partials/write-review-discussion'); ?>
		<?php endif; ?>
    </footer>
</div><!-- End / comment-review_module__-Z5tr -->
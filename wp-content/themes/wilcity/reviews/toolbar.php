<?php
global $wilcityoReview, $post, $wilcityParentPost;
use WilokeListingTools\Frontend\User as WilokeUser;
use WilokeListingTools\Controllers\ReviewController;
use WilokeListingTools\Controllers\ReportController;
if ( !WilokeUser::isPostAuthor($wilcityParentPost, true) && !ReportController::isAllowReport() && !WilokeUser::isPostAuthor($post, true) ){
    return '';
}
?>
<!-- dropdown_module__J_Zpj -->
<div class="dropdown_module__J_Zpj ml-20">
	<div class="dropdown_threeDots__3fa2o" data-toggle-button="dropdown" data-body-toggle="true"><span class="dropdown_dot__3I1Rn"></span><span class="dropdown_dot__3I1Rn"></span><span class="dropdown_dot__3I1Rn"></span></div>

	<div class="dropdown_itemsWrap__2fuze" data-toggle-content="dropdown">
		<!-- list_module__1eis9 list-none -->
		<ul class="list_module__1eis9 list-none list_small__3fRoS list_abs__OP7Og arrow--top-right wilcity-review-toolbar-wrapper" data-id="<?php echo esc_attr($wilcityoReview->ID); ?>">
			<?php if (  WilokeUser::isPostAuthor($wilcityParentPost, true) ) : ?>
				<li class="list_item__3YghP">
                    <a @click.prevent="pinReviewToTop('<?php echo esc_attr($wilcityoReview->ID); ?>', '<?php echo esc_attr($wilcityoReview->post_parent); ?>')" class="list_link__2rDA1 text-ellipsis color-primary--hover wilcity-pin-review-to-top" href="#"><span class="list_icon__2YpTp"><i class="la la-thumb-tack"></i></span><span class="list_text__35R07"><?php !ReviewController::isSticky($wilcityoReview) ? esc_html_e('Pin to Top of Review', 'wilcity') : esc_html_e('Unpin this review', 'wilcity'); ?></span></a>
                </li>
			<?php endif; ?>

            <?php if ( ReportController::isAllowReport() ) : ?>
			<li class="list_item__3YghP">
                <report-popup-btn wrapper-class="list_link__2rDA1 text-ellipsis color-primary--hover" target-id="<?php echo esc_attr($wilcityoReview->ID); ?>">
                    <template slot="insideBtn">
                        <span class="list_icon__2YpTp"><i class="la la-flag-o"></i></span><span class="list_text__35R07"><?php esc_html_e('Report review', 'wilcity'); ?></span>
                    </template>
                </report-popup-btn>
            </li>
            <?php endif; ?>

			<?php if (  WilokeUser::isPostAuthor($wilcityoReview, false) && ( isset($wilcityParentPost->post_type) && $wilcityParentPost->post_type != 'event') ) : ?>
			<li class="list_item__3YghP">
                <review-popup-btn v-on:on-open-review-popup="onOpenReviewPopup" review-id="<?php echo esc_attr($post->ID); ?>" btn-name="<?php echo esc_attr__('Edit Review', 'wilcity'); ?>" icon="la la-edit" wrapper-class="list_link__2rDA1 text-ellipsis color-primary--hover wilcity-edit-review wilcity-edit-review-<?php echo esc_attr($wilcityoReview->ID); ?>"></review-popup-btn>
            </li>
            <?php endif; ?>

			<?php if (  WilokeUser::isPostAuthor($post, true) ) : ?>
            <li class="list_item__3YghP"><a class="list_link__2rDA1 text-ellipsis color-primary--hover wilcity-edit-review wilcity-delete-review-<?php echo esc_attr($wilcityoReview->ID); ?>" href="#" @click.prevent="onOpenDeleteReviewPopup('<?php echo esc_attr($wilcityoReview->ID); ?>')"><span class="list_icon__2YpTp"><i class="la la-trash"></i></span><span class="list_text__35R07"><?php esc_html_e('Delete', 'wilcity'); ?></span></a></li>
            <?php endif; ?>

		</ul><!-- End /  list_module__1eis9 list-none -->
	</div>
</div><!-- End / dropdown_module__J_Zpj -->

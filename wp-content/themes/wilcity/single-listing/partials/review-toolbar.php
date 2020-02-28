<?php global $wilcityoReview; ?>
<!-- dropdown_module__J_Zpj -->
<div class="dropdown_module__J_Zpj ml-20">
	<div class="dropdown_threeDots__3fa2o" data-toggle-button="dropdown" data-body-toggle="true"><span class="dropdown_dot__3I1Rn"></span><span class="dropdown_dot__3I1Rn"></span><span class="dropdown_dot__3I1Rn"></span></div>

	<div class="dropdown_itemsWrap__2fuze" data-toggle-content="dropdown">
		<!-- list_module__1eis9 list-none -->
		<ul class="list_module__1eis9 list-none list_small__3fRoS list_abs__OP7Og arrow--top-right wilcity-review-toolbar-wrapper" data-id="<?php echo esc_attr($wilcityoReview->ID); ?>">
            <?php if ( \WilokeListingTools\Frontend\User::can('edit_theme_options') ) : ?>
            <li class="list_item__3YghP"><a class="list_link__2rDA1 text-ellipsis color-primary--hover wilcity-pin-review-to-top" href="#"><span class="list_icon__2YpTp"><i class="la la-thumb-tack"></i></span><span class="list_text__35R07"><?php !\WilokeListingTools\Controllers\ReviewController::isSticky($wilcityoReview) ? esc_html_e('Pin to Top of Review', 'wilcity') : esc_html_e('Unpin this review', 'wilcity'); ?></span></a></li>
            <?php endif; ?>
			<li class="list_item__3YghP"><a class="list_link__2rDA1 text-ellipsis color-primary--hover" href="#"><span class="list_icon__2YpTp"><i class="la la-flag-o"></i></span><span class="list_text__35R07"><?php esc_html_e('Report review', 'wilcity'); ?></span></a></li>
			<li class="list_item__3YghP"><a class="list_link__2rDA1 text-ellipsis color-primary--hover wilcity-edit-review wilcity-edit-review-<?php echo esc_attr($wilcityoReview->ID); ?>" href="#" data-popup="write-a-review-popup"><span class="list_icon__2YpTp"><i class="la la-edit"></i></span><span class="list_text__35R07"><?php esc_html_e('Edit review', 'wilcity'); ?></span></a></li>
		</ul><!-- End /  list_module__1eis9 list-none -->
	</div>
</div><!-- End / dropdown_module__J_Zpj -->
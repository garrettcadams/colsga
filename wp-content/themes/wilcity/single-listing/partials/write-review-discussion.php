<?php global $wilcityaUserInfo, $wiloke, $wilcityoReview; ?>
<div class="comment-review_form__20wWm">
	<div class="utility-box-1_module__MYXpX utility-box-1_xs__3Nipt d-inline-block mr-10 wil-float-left">
		<div class="utility-box-1_avatar__DB9c_ rounded-circle" style="background-image: url(<?php echo esc_url($wilcityaUserInfo['avatar']); ?>);"><img src="<?php echo esc_url($wilcityaUserInfo['avatar']); ?>" alt="<?php echo esc_attr($wilcityaUserInfo['display_name']); ?>"/></div>
	</div>
	<div class="comment-review_comment__dJNqv">
		<!-- field_module__1H6kT -->
		<div class="field_module__1H6kT field_style4__2DBqx field-autoHeight js-field">
			<div class="field_wrap__Gv92k">
				<textarea data-parentid="<?php echo esc_attr($wilcityoReview->ID); ?>" class="field_field__3U_Rt wilcity-write-new-discussion-field wilcity-write-new-discussion-field-<?php echo esc_attr($wilcityoReview->ID); ?>" data-height-default="22"></textarea>
				<span class="field_label__2eCP7 text-ellipsis"><?php echo esc_html($wiloke->aConfigs['translation']['typeAMessage']); ?></span>
				<span class="bg-color-primary"></span>
				<div class="field_rightButton__1GGWz js-field-rightButton">
					<span class="field_iconButton__2p3sr bg-color-primary"><i class="la la-arrow-up"></i></span>
				</div>
			</div>
		</div><!-- End / field_module__1H6kT -->
	</div>
</div>
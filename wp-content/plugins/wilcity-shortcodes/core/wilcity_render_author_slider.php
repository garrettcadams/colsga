<?php
use WilokeListingTools\Frontend\User as WilokeUser;
use WilokeListingTools\Controllers\FollowController;

if ( !function_exists('wilcity_render_author_slider') ){
	function wilcity_render_author_slider($aAtts){
		$aRoleIn = explode(',', $aAtts['role__in']);

		$aUsers = get_users(array(
			'role__in' => $aRoleIn,
			'number'   => $aAtts['number'],
			'orderby'  => $aAtts['orderby']
		));
		if ( empty($aUsers) || is_wp_error($aUsers) ){
			return '';
		}
		?>
		<div id="<?php echo esc_attr(uniqid(apply_filters('wilcity/filter/id-prefix', 'wilcity-slider-'))); ?>" class="swiper__module swiper-container swiper--button-pill swiper--button-abs-outer swiper--button-mobile-disable" data-options='{"slidesPerView":5,"spaceBetween":30, "breakpoints":{"640":{"slidesPerView": 1, "spaceBetween": 10}, "992": {"slidesPerView": 3, "spaceBetween": 10}, "1200": {"slidesPerView": 4, "spaceBetween": 20}, "1400":{"slidesPerView": 4, "spaceBetween": 30}, "1600": {"slidesPerView": 5, "spaceBetween": 30}}}'>
			<div class="swiper-wrapper">
				<?php foreach ($aUsers as $oUser) : ?>
				<div class="content-box_module__333d9 content-box_follow__3MNT9 wil-text-center">
					<div class="content-box_body__3tSRB">
						<a href="<?php echo get_author_posts_url($oUser->ID); ?>">
							<div class="utility-box-1_module__MYXpX mt-20">
								<div class="utility-box-1_avatar__DB9c_ rounded-circle" style="background-image: url('<?php echo WilokeUser::getAvatar($oUser->ID); ?>')"><img src="<?php echo WilokeUser::getAvatar($oUser->ID); ?>" alt="<?php echo WilokeUser::getField('display_name', $oUser->ID); ?>"/></div>
								<div class="utility-box-1_body__8qd9j">
									<div class="utility-box-1_group__2ZPA2">
										<h3 class="utility-box-1_title__1I925"><?php echo WilokeUser::getField('display_name', $oUser->ID); ?></h3>
									</div>
									<div class="utility-box-1_description__2VDJ6"><?php echo date_i18n(get_option('date_format'), strtotime($oUser->user_registered)); ?></div>
								</div>
							</div>
						</a>
						<?php if ( FollowController::toggleFollow() ): ?>
						<div class="follow_module__17lY_ follow_style2__jlXHR mt-20">
							<div class="follow_item__3GAob">
								<div class="follow_content__2R1YP"><?php WilokeUser::renderFollower($oUser->ID); ?></div>
							</div>
							<div class="follow_item__3GAob">
								<div class="follow_content__2R1YP"><?php WilokeUser::renderFollowing($oUser->ID); ?></div>
							</div>
							<div class="follow_item__3GAob">
								<div class="follow_content__2R1YP">
                                    <a class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-toggle-follow')); ?> color-primary fs-12 font-secondary font-bold" data-textonly="true" data-authorid="<?php echo esc_attr($oUser->ID); ?>" data-current-status="<?php echo FollowController::isIamFollowing($oUser->ID) ? 'followingtext' : 'followtext'; ?>" data-followtext="<?php esc_html_e('Follow', 'wilcity-shortcodes'); ?>" data-followingtext="<?php esc_html_e('Following', 'wilcity-shortcodes'); ?>" href='<?php echo esc_url(get_author_posts_url($oUser->ID)); ?>'><i class='la la-refresh'></i> <?php echo FollowController::isIamFollowing($oUser->ID) ?  esc_html__('Following', 'wilcity-shortcodes') : esc_html__('Follow', 'wilcity-shortcodes'); ?></a>
                                </div>
							</div>
						</div>
						<?php endif; ?>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
            <div class="swiper-button-custom">
                <div class="swiper-button-prev-custom"><i class='la la-angle-left'></i></div>
                <div class="swiper-button-next-custom"><i class='la la-angle-right'></i></div>
            </div>
        </div>
		<?php
	}
}


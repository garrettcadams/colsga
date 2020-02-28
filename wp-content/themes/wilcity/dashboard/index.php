<?php
/*
 * Template Name: Wilcity Dashboard
 */

if ( !is_user_logged_in() ){
	wp_redirect(home_url('/'));
	die();
}
get_header();

global $wiloke;
use WilokeListingTools\Frontend\User as WilokeUser;
use WilokeListingTools\Controllers\FollowController;
use WilokeListingTools\Framework\Helpers\HTML;

$oUser = WilokeUser::getUserData();
$avatar = WilokeUser::getAvatar();
?>
<div id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-dashboard')); ?>" class="wil-dashboard wil-content">
	<section id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-dashboard-inner')); ?>" class="wil-section wil-dashboard-inner bg-color-gray-2 pd-0">
		<div class="dashboard-content_module__zpEtP clearfix">
			<div class="dashboard__sidebar js-sticky" data-margin-top="0">
				<!-- utility-box-1_module__MYXpX -->
				<div class="utility-box-1_module__MYXpX wil-text-center">
					<div v-cloak class="utility-box-1_avatar__DB9c_ rounded-circle" :style="{'background-image': 'url('+avatar+')'}"><img :src="avatar" :alt="displayName"/></div>
					<div class="utility-box-1_body__8qd9j">
						<div class="utility-box-1_group__2ZPA2">
							<h3 class="utility-box-1_title__1I925" v-html="displayName" v-cloak></h3>
						</div>
						<div class="utility-box-1_description__2VDJ6" v-html="position" v-cloak></div>
					</div>
				</div>
                <?php if ( isset($wiloke->aThemeOptions['user_toggle_follow']) && $wiloke->aThemeOptions['user_toggle_follow'] == 'enable' ) :
	                $isFollowing = FollowController::isIamFollowing(get_current_user_id());
	                $isFollowing = $isFollowing ? 'followingtext' : 'followtext';
	                $followings = FollowController::countFollowings();
	                $followers = FollowController::countFollowers();
                ?>
				<div class="follow_module__17lY_ mt-20">
					<div class="follow_item__3GAob">
						<div class="follow_content__2R1YP"><a href="#"><span class='color-primary'><?php echo HTML::reStyleText($followers); ?></span> <?php echo WilokeHelpers::ngettext(esc_html__('Follower', 'wilcity'), esc_html__('Follower', 'wilcity'), esc_html__('Followers', 'wilcity'), $followers); ?></a></div>
					</div>
					<div class="follow_item__3GAob">
						<div class="follow_content__2R1YP"><a  href="#" class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-toggle-follow')); ?>" data-authorid="<?php echo esc_attr(get_current_user_id()); ?>" data-followtext="<?php echo esc_attr__('Follow', 'wilcity'); ?>" data-followingtext="<?php echo esc_attr__('Following', 'wilcity'); ?>" data-current-status="<?php echo esc_attr($isFollowing); ?>"><span class='color-primary'><?php echo HTML::reStyleText($followings); ?></span> <?php echo WilokeHelpers::ngettext(esc_html__('Following', 'wilcity'), esc_html__('Followings', 'wilcity'), esc_html__('Followings', 'wilcity'), $followings); ?></a></div>
					</div>
				</div><!-- End /  follow_module__17lY_ -->
                <?php endif; ?>

				<?php include get_template_directory() . '/dashboard/navigation.php'; ?>
			</div>
            <div class="dashboard__content">
                <router-view></router-view>
            </div>
		</div>
	</section>
    <promotion-popup></promotion-popup>
</div>
<?php

get_footer('not-widgets');


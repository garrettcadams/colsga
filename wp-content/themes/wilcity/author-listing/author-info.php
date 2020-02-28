<?php
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Controllers\FollowController;
use WilokeListingTools\Framework\Helpers\General;
?>
<div class="author-hero_content__x740L">
	<div class="container">
		<?php
			$authorID       = get_query_var( 'author' );
			$displayName    = User::getField('display_name', $authorID);
			$position = User::getPosition($authorID);
		?>
		<!-- utility-box-1_module__MYXpX -->
		<div class="utility-box-1_module__MYXpX utility-box-1_round__29x6N ">
			<div class="utility-box-1_avatar__DB9c_ rounded-circle" style="background-image: url(<?php echo esc_url(User::getAvatar($authorID)) ?>);">
				<img src="<?php echo esc_url(User::getAvatar($authorID)); ?>" alt="<?php echo esc_attr($displayName); ?>"/>
			</div>
			<div class="utility-box-1_body__8qd9j">
				<div class="utility-box-1_group__2ZPA2">
					<h3 class="utility-box-1_title__1I925"><?php Wiloke::ksesHTML($displayName); ?></h3>
				</div>
				<?php if ( !empty($position) ) : ?>
				<div class="utility-box-1_description__2VDJ6"><?php Wiloke::ksesHTML($position); ?></div>
				<?php elseif ($authorID == User::getCurrentUserID()): ?>
                    <div class="utility-box-1_description__2VDJ6"><?php esc_html_e('Please go to Dashboard -> Profile -> Write something about yourself.', 'wilcity'); ?></div>
                <?php else: ?>
                    <div class="utility-box-1_description__2VDJ6"><?php esc_html_e('There is no information about this customer', 'wilcity'); ?></div>
                <?php endif; ?>
			</div>
		</div><!-- End / utility-box-1_module__MYXpX -->

		<?php if ( FollowController::toggleFollow() ) : ?>
		<div class="author-hero_right__20vEh">
            <?php
            User::renderFollower($authorID);
            User::renderFollowing($authorID);
            if ( $authorID != get_current_user_id() ) :
                $isIamFollowing = FollowController::isIamFollowing($authorID);
                if ( $isIamFollowing ){
                    $followText = __('Following', 'wilcity');
                    $followKey = 'followingtext';
                }else{
	                $followText =  __('Follow', 'wilcity');
	                $followKey = 'followtext';
                }
            ?>
                <a data-authorid="<?php echo esc_attr($authorID); ?>" class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-toggle-follow wil-btn wil-btn--sm wil-btn--round wil-btn--light')); ?>" href="#" data-followtext="<?php echo esc_attr__('Follow', 'wilcity'); ?>" data-current-status="<?php echo esc_attr($followKey); ?>" data-followingtext="<?php echo esc_attr__('Following', 'wilcity'); ?>">
                    <i class="la la-refresh"></i><?php echo esc_html($followText); ?>
                </a>
			<?php endif; ?>

        </div>
		<?php endif; ?>

	</div>
</div>

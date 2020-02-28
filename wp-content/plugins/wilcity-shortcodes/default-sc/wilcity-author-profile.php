<?php
use \WilokeListingTools\Frontend\User;
use \WilokeListingTools\Controllers\FollowController;
use WilokeListingTools\Framework\Helpers\HTML;

function wilcityAuthorProfile($aAtts){
	$aAtts = isset($aAtts['atts']) ? \WILCITY_SC\SCHelpers::decodeAtts($aAtts['atts']) : '';
	$aAtts = shortcode_atts(
		array(
			'user_id' => '',
			'name'    => '',
			'icon'    => ''
		),
		$aAtts
	);
	$postID = '';

	if ( empty($aAtts['user_id']) ){
		if ( is_singular() ){
			global $post;
			$aAtts['user_id'] = $post->post_author;
			$postID = $post->ID;
		}else{
			return '';
		}
	}

	$avatar = User::getAvatar($aAtts['user_id']);
	$displayName = User::getField('display_name', $aAtts['user_id']);
	$position = User::getPosition($aAtts['user_id']);
	$authorPostsUrl = get_author_posts_url($aAtts['user_id']);

	if ( !empty($aAtts['name']) ) :
	?>
	<div class="content-box_module__333d9">
		<?php wilcityRenderSidebarHeader($aAtts['name'], $aAtts['icon']); ?>
		<div class="content-box_body__3tSRB">
	<?php endif; ?>
		<div class="author-listing_module__3K7-I">
			<div class="utility-box-1_module__MYXpX utility-box-1_md__VsXoU utility-box-1_boxLeft__3iS6b clearfix  mb-20 mb-sm-15">
				<div class="utility-box-1_avatar__DB9c_ rounded-circle">
					<a class="clearfix" href="<?php echo esc_url($authorPostsUrl); ?>">
						<img style="display: block !important;" src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr($displayName); ?>">
					</a>
				</div>
				<div class="utility-box-1_body__8qd9j">
					<div class="utility-box-1_group__2ZPA2">
						<h3 class="utility-box-1_title__1I925"><a href="<?php echo esc_url($authorPostsUrl); ?>"><?php echo esc_html($displayName); ?></a></h3>
					</div>
					<?php if ( !empty($position) ) : ?>
						<div class="utility-box-1_description__2VDJ6"><a href="<?php echo esc_url($authorPostsUrl); ?>"><?php echo esc_html($position); ?></a></div>
					<?php endif; ?>
				</div>
			</div>
			<?php if ( FollowController::toggleFollow() ) :
				$followings = FollowController::countFollowings($aAtts['user_id']);
				$followers = FollowController::countFollowers($aAtts['user_id']);
				?>
				<div id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-follower-number-'.$aAtts['user_id'])); ?>" class="author-listing_follow__3RxQ6">
					<div class="follow_module__17lY_">
						<div class="follow_item__3GAob">
							<div class="follow_content__2R1YP">
								<span class="color-primary <?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-print-number')); ?>"><?php echo HTML::reStyleText($followers); ?></span> <?php echo \WilokeHelpers::ngettext(esc_html__('Follower', 'wilcity-shortcodes'), esc_html__('Followers', 'wilcity-shortcodes'), esc_html__('Followers', 'wilcity-shortcodes'), $followers); ?>
							</div>
						</div>
						<div class="follow_item__3GAob">
							<div class="follow_content__2R1YP">
								<span class="color-primary"><?php echo HTML::reStyleText($followings); ?></span> <?php esc_html_e('Following', 'wilcity-shortcodes'); ?>
							</div>
						</div>
						<?php if ( get_current_user_id() != $aAtts['user_id'] ) : ?>
							<div class="follow_item__3GAob">
								<div class="follow_content__2R1YP">
									<a class='<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-toggle-follow')); ?> color-primary fs-12 font-secondary font-bold' data-textonly="true" data-authorid="<?php echo esc_attr($aAtts['user_id']); ?>" data-current-status="<?php echo FollowController::isIamFollowing($aAtts['user_id']) ? 'followingtext' : 'followtext'; ?>" data-followtext="<?php esc_html_e('Follow', 'wilcity-shortcodes'); ?>" data-followingtext="<?php esc_html_e('Following', 'wilcity-shortcodes'); ?>" href='<?php echo esc_url(get_author_posts_url($postID)); ?>'><i class='la la-refresh'></i> <?php echo FollowController::isIamFollowing($postID) ?  esc_html__('Following', 'wilcity-shortcodes') : esc_html__('Follow', 'wilcity-shortcodes'); ?></a>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>

            <?php
            $aSocialNetworks = User::getSocialNetworks($aAtts['user_id']);
            if ( !empty($aSocialNetworks) ):
            ?>
            <div class="social-icon_module__HOrwr social-icon_style-2__17BFy mt-20">
                <?php foreach ($aSocialNetworks as $socialKey => $socialLink) : ?>
                    <a class="social-icon_item__3SLnb" href="<?php echo esc_url($socialLink); ?>"><i class="fa fa-<?php echo esc_attr($socialKey); ?>"></i></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
		</div>
	<?php if ( !empty($aAtts['name']) ) : ?>
		</div>
	</div>
	<?php
	endif;
}

add_shortcode('wilcity_author_profile', 'wilcityAuthorProfile');
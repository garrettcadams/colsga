<?php
global $wilcityoDiscussion;
use WilokeListingTools\Frontend\User;

$avatar = User::getAvatar($wilcityoDiscussion->post_author);
$displayName = User::getField('display_name',$wilcityoDiscussion->post_author);
$position = User::getPosition($wilcityoDiscussion->post_author);

$desc = '';
if ( !empty($position) ){
	$desc = $position . ' . ';
}
$desc .= get_the_date('M d, Y', $wilcityoDiscussion->ID);

?>
<li class="wilcity-review-discussion-wrapper comment-review_commentlistItem__2DILM">
	<!-- utility-box-1_module__MYXpX -->
	<div class="wilcity-discussion-<?php echo esc_attr($wilcityoDiscussion->ID); ?> utility-box-1_module__MYXpX utility-box-1_xs__3Nipt utility-box-1_boxLeft__3iS6b clearfix ">
		<div class="utility-box-1_avatar__DB9c_ rounded-circle" style="background-image: url(<?php echo esc_url($avatar); ?>);"><img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr($displayName); ?>"/></div>
		<div class="utility-box-1_body__8qd9j">
			<div class="utility-box-1_group__2ZPA2">
				<h3 class="utility-box-1_title__1I925"><?php echo esc_html($displayName); ?></h3>
				<div class="utility-box-1_content__3jEL7 wilcity-show-discussion-<?php echo esc_attr($wilcityoDiscussion->ID); ?>"><?php Wiloke::ksesHTML($wilcityoDiscussion->post_content); ?></div>
			</div>
			<div class="utility-box-1_description__2VDJ6"><?php Wiloke::ksesHTML($desc); ?></div>
		</div>
	</div><!-- End / utility-box-1_module__MYXpX -->

	<!-- dropdown_module__J_Zpj -->
    <?php get_template_part('single-listing/partials/discussion-toolbar'); ?>
</li>
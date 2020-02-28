<?php
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Controllers\EventController;
global $post;
?>
<div id="wilcity-render-comments">
    <?php
    global $wiloke, $wilcityoReview, $wilcityaUserInfo, $wilcityParentPost, $wilcityReviewConfiguration;
    $wilcityParentPost = $post;
    $query = new \WP_Query(
	    array(
		    'post_type'         => 'event_comment',
		    'posts_per_page'    => 4,
		    'orderby'           => 'menu_order post_date',
		    'post_status'       => 'publish',
		    'post_parent'       => $post->ID
	    )
    );

    if ( $query->have_posts() ):
        $wilcityaUserInfo['avatar']        = User::getAvatar();
        $wilcityaUserInfo['position']      = User::getPosition();
        $wilcityaUserInfo['display_name']  = User::getField('display_name');

	    $wilcityReviewConfiguration['turnOffScore'] = true;
	    $wilcityReviewConfiguration['turnOffTitle'] = true;
	    $wilcityReviewConfiguration['enableReviewDiscussion'] = EventController::isEnabledDiscussion();

        while ($query->have_posts()): $query->the_post();
	        $wilcityoReview = $query->post;
	        get_template_part('reviews/item');
	    endwhile;
    endif; wp_reset_postdata();
    ?>
</div>
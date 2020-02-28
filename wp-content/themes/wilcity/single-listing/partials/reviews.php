<?php
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Models\ReviewModel;
use WilokeListingTools\Controllers\ReviewController;
use WilokeListingTools\Controllers\ShareController;

global $wiloke, $wilcityoReview, $wilcityaUserInfo, $wilcityArgs;
$aSomeReviews = ReviewController::fetchSomeReviews(array(
	'postsPerPage' => $wilcityArgs['maximumItemsOnHome'],
	'page' => isset($_GET['page']) ? abs($_GET['page']) : 1
));

if ( $aSomeReviews ) {
	$wilcityaUserInfo['avatar']   = User::getAvatar();
	$wilcityaUserInfo['position'] = User::getPosition();
	$wilcityaUserInfo['display_name'] = User::getField('display_name');

	foreach ($aSomeReviews as $wilcityoReview){
        get_template_part('single-listing/partials/review-item');
	}
}
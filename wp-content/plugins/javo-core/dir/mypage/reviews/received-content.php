<?php
$arrQueriedReviews = Array();
if( function_exists( 'lv_directoryReview' ) ) {
	$arrQueriedReviews = lv_directoryReview()->core->getReviewQuery( get_current_user_id(), Array(
		'type' => 'received',
		'number' => 5,
	) );
}

if( !empty( $arrQueriedReviews ) ) {
	foreach( $arrQueriedReviews as $objReview ) {

		$objParent = get_post( $objReview->comment_post_ID );
		if( 0 < intVal( $objReview->user_id ) ) {
			$objWriter = new WP_User( $objReview->user_id );
		}else{
			$objWriter = new stdClass();
			$objWriter->display_name = $objParent->author_name;
		}

		$strImage = jvbpd_tso()->get( 'no_image', JVBPD_IMG_DIR.'/no-image.png' );
		if( has_post_thumbnail( $objParent ) ) {
			$intFeaturedID = get_post_thumbnail_id( $objParent->ID );
			$strImage = wp_get_attachment_thumb_url( $intFeaturedID );
		}

		printf(
			'<li class="list-group-item">
				<div class="listing-thumb">
					<img src="%1$s" class="rounded-circle">
				</div>
				<div class="listing-content">
					<h5 class="title"><a href="%8$s" target="_blank">%2$s</a></h5>
					<span class="listing-desc">%3$s</span>
					<span class="author"><a href="#"><i class="icon-user"></i> %4$s</a></span>
					<span class="rating"><i class="jvbpd-icon3-star"></i>%5$s / 5</span>
					<span class="status label label-rounded label-info">%6$s</span>
					<span class="time pull-right date"><i class=" jvbpd-icon3-clock"></i>%7$s</span>
				</div>
			</li><!-- .list-group-item -->',
			$strImage, $objParent->post_title, $objReview->comment_content, $objWriter->display_name,
			call_user_func_array( Array( lv_directoryReview()->core, 'get' ), Array( 'average', 0, $objParent->ID ) ), get_post_status( $objParent ),
			date( get_option( 'date_format' ), strtotime( $objParent->post_date ) ), get_permalink( $objParent )
		);


	}
}else{
	printf( "<li class='list-group-item text-center jv-mypage-not-found-dat'>%s</li>", esc_html__( "Not found any data", 'jvfrmtd' ) );
} ?>
<?php

namespace WilokeListingTools\Register;


class General {
	public function __construct() {
		add_action('wp_ajax_get_posts_by_post_types', array($this, 'getPosts'));
	}

	public function getPosts(){
		$aPostTypes = explode(',', $_GET['postTypes']);

		$query = new \WP_Query(array(
			'post_type' => $aPostTypes,
			'post_status' => 'publish',
			's' => $_GET['s']
		));

		if ( !$query->have_posts() ){
			wp_send_json_error();
		}

		$aResponses = array();

		while ($query->have_posts()){
			$query->the_post();
			$aResponses[] = array(
				'value' => $query->post->ID,
				'name'  => $query->post->post_title,
				'text'  => $query->post->post_title,
			);
		}
		wp_reset_postdata();

		echo json_encode(array(
			'success' => true,
			'results' => $aResponses
		));
		die();
	}
}
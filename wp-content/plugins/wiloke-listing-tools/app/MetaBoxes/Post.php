<?php

namespace WilokeListingTools\MetaBoxes;


class Post {
	public function __construct() {
		add_action('cmb2_admin_init', array($this, 'register'), 10);
//		add_action('save_post', array($this, 'saveSettings'), 10, 3);
	}

	public function saveSettings($postID, $post, $updated){
		if ( !current_user_can('edit_posts') ){
			return false;
		}

		if ( $post->post_type != 'post' ){
			return false;
		}

		if ( isset($_POST['wilcity_posts_belongs_to']) && !empty($_POST['wilcity_posts_belongs_to']) ){
			global $wpdb;
			$wpdb->update(
				$wpdb->posts,
				array(
					'post_parent' => abs($_POST['wilcity_posts_belongs_to'])
				),
				array(
					'ID' => $postID
				),
				array(
					'%d'
				),
				array(
					'%d'
				)
			);
		}
	}

	public static function getPostParent(){
		if ( isset($_GET['post']) && !empty($_GET['post']) ){
			return wp_get_post_parent_id($_GET['post']);
		}
		return '';
	}

	public function register(){
		$aPosts = wilokeListingToolsRepository()->get('post-metaboxes:postParent');
		new_cmb2_box($aPosts);
	}
}
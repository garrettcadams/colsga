<?php

if ( ! function_exists( 'get_contact_form_7_posts' ) ) :

  function get_contact_form_7_posts(){

  $args = array('post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1);

    $catlist=[];

    if( $categories = get_posts($args)){
    	foreach ( $categories as $category ) {
    		(int)$catlist[$category->ID] = $category->post_title;
    	}
    }
    else{
        (int)$catlist['0'] = esc_html__('No contect From 7 form found', 'jvfrmtd');
    }
  return $catlist;
  }

endif;

if ( ! function_exists( 'jvbpd_get_all_pages' ) ) :

  function jvbpd_get_all_pages(){

  $args = array('post_type' => 'page', 'posts_per_page' => -1);

    $catlist=[];

    if( $categories = get_posts($args)){
      foreach ( $categories as $category ) {
        (int)$catlist[$category->ID] = $category->post_title;
      }
    }
    else{
        (int)$catlist['0'] = esc_html__('No Pages Found!', 'jvfrmtd');
    }
  return $catlist;
  }

endif;


if ( ! function_exists( 'get_map_template_pages' ) ) :

  function get_map_template_pages(){

  $args = array('post_type' => 'page', 'posts_per_page' => -1);

    $catlist=[];

    if( $categories = get_posts($args)){
    	foreach ( $categories as $category ) {
    		(int)$catlist[$category->ID] = $category->post_title;
    	}
    }
    else{
        (int)$catlist['0'] = esc_html__('No Map ( Listing ) Pages. Please Create a Page.', 'jvfrmtd');
    }
  return $catlist;
  }

endif;

if ( ! function_exists( 'get_listing_list' ) ) :

  function get_listing_list(){

  $args = array('post_type' => 'lv_listing', 'posts_per_page' => -1);

    $catlist=[];

    if( $categories = get_posts($args)){
    	foreach ( $categories as $category ) {
    		(int)$catlist[$category->ID] = $category->post_title;
    	}
    }
    else{
        (int)$catlist['0'] = esc_html__('Not Found Data.', 'jvfrmtd');
    }
  return $catlist;
  }

endif;

// Get MailChimp Form List
if ( ! function_exists( 'get_mailchimp_forms' ) ) :

  function get_mailchimp_forms(){

  $args = array('post_type' => 'mc4wp-form', 'posts_per_page' => -1);

    $catlist=[];

    if( $categories = get_posts($args)){
    	foreach ( $categories as $category ) {
    		(int)$catlist[$category->ID] = $category->post_title;
    	}
    }
    else{
        (int)$catlist['0'] = esc_html__('No mailchimp form found', 'jvfrmtd');
    }
  return $catlist;
  }

endif;

// Get Category List
if ( ! function_exists( 'jvbpd_get_category' ) ) :
	function jvbpd_get_category($taxonomies, $args){
		$args = array(
		'orderby'=>'name'
		, 'taxonomy' => $taxonomies
		, 'parent'=>''
		, 'hide_empty'=>true
		, 'hierarchical' => true
		);
	$custom_terms = get_terms( $args );
	foreach($custom_terms as $term){
		//echo '<button class="button" data-filter=".' . $term->slug . '"> ' . $term->name . '</button>';
		//echo $term->name;
	}
	}
endif;



function lg_post_orderby_options(){
    $orderby = array(
        'ID' => 'Post Id',
        'author' => 'Post Author',
        'title' => 'Title',
        'date' => 'Date',
        'modified' => 'Last Modified Date',
        'parent' => 'Parent Id',
        'rand' => 'Random',
        'comment_count' => 'Comment Count',
        'menu_order' => 'Menu Order',
    );

    return $orderby;
}


function lg_post_type(){
	$args= array(
			'public'	=> 'true',
			'_builtin'	=> false
		);
	$post_types = get_post_types( $args, 'names', 'and' );
	$post_types = array( 'post'	=> 'post' ) + $post_types;
	return $post_types;
}

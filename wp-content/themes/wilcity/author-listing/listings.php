<?php
$postType = get_query_var('mode');
$postsPerPage = 9;
$query = new WP_Query(
	array(
		'post_type'         => $postType,
		'posts_per_page'    => $postsPerPage,
		'post_status'       => 'publish',
		'author__in'        => array(get_query_var('author'))
	)
);

$gridID = 'wilcity-search-results';
?>

<div id="wilcity-search-results" class="row js-listing-grid wilcity-grid">
	<?php
		if ( !function_exists('wilcity_render_grid_item') ){
			WilokeMessage::message(array(
				'status' => 'danger',
				'msgIcon'=> 'la la-bullhorn',
				'msg'    => esc_html__('Please go to Appearance -> Install Plugins -> Activate Wilcity Shortcodes plugin', 'wilcity')
			));
		}else{

			while ($query->have_posts()){
				$query->the_post();
				wilcity_render_grid_item($query->post, array(
					'img_size' => 'wilcity_360x200',
					'maximum_posts_on_lg_screen' => 'col-lg-4',
					'maximum_posts_on_md_screen' => 'col-md-4',
					'maximum_posts_on_sm_screen' => 'col-sm-6',
                    'style' => 'grid'
				));
			}

			wp_reset_postdata();
		}
	?>
</div>
<nav class="mt-20 mb-20"><ul class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-pagination pagination_module__1NBfW')); ?>" data-action="wilcity_load_more_listings" data-gridid="<?php echo esc_attr($gridID); ?>" data-post-type="<?php echo esc_attr($postType); ?>" data-totals="<?php echo esc_attr($query->found_posts); ?>" data-max-pages="<?php echo esc_attr($query->max_num_pages); ?>" data-posts-per-page="<?php echo esc_attr($postsPerPage); ?>" data-current-page="1" data-maximum_posts_on_lg_screen="<?php echo esc_attr('col-lg-4'); ?>" data-maximum_posts_on_md_screen="<?php echo esc_attr('col-md-4'); ?>" data-maximum_posts_on_sm_screen="<?php echo esc_attr('col-sm-6'); ?>" data-img_size="<?php echo esc_attr('wiloke_listgo_360x200'); ?>"></ul></nav>
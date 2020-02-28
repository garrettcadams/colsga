<?php
use \WilokeListingTools\Framework\Helpers\GetSettings;
function wilcity_render_grid_post($post){
	$featuredImg = GetSettings::getBlogFeaturedImage($post->ID, apply_filters('wilcity/post-grid/size', 'medium'));
	$aMetaData = apply_filters('wilcity/post-grid/metadata', array(
		'data' => array(
			'icon' => 'la la-calendar',
			'name' => 'date'
		),
		'category' => array(
			'icon' => 'la la-list-alt',
			'name' => 'category'
		),
		'comment' => array(
			'icon' => 'la la-comments',
			'name' => 'comment'
		)
	));
    $permalink = get_permalink($post->ID);
	?>
	<article class="post_module__3uT9W post_grid__2xFvJ border-box">
		<header class="post_header__2pWQ0">
			<a href="<?php echo esc_url($permalink); ?>">
				<div class="bg-cover" style="background-image: url(<?php echo esc_url($featuredImg); ?>);"><img class="d-none" src="<?php echo esc_url($featuredImg); ?>" alt="<?php echo esc_attr($post->post_title); ?>"></div>
			</a>
		</header>
		<div class="post_body__TYys6">
			<h2 class="post_title__2Jnhn"><a href="<?php echo esc_url($permalink); ?>"><?php echo get_the_title($post->ID); ?></a></h2>
			<div class="post_metaData__3b_38 color-primary-meta">
				<?php
				foreach ($aMetaData as $aMeta){
					$val = \WILCITY_SC\SCHelpers::getPostMetaData($post, $aMeta['name']);
					if ( empty($val) ){
						continue;
					}
					?>
					<span><a href="<?php echo esc_url($permalink); ?>"><i class="<?php echo esc_attr($aMeta['icon']); ?>"></i> <?php echo $val; ?></a></span>
					<?php
				}
				?>
			</div>
		</div>
	</article>
	<?php
}
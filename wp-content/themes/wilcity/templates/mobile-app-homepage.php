<?php
/*
 * Template Name: Wilcity Mobile App Homepage
 */

get_header();
?>
	<div class="wil-content">
		<?php
		if ( have_posts() ){
			while (have_posts()){
				the_post();
				the_content();
			}
		}
		?>
	</div>
<?php
get_footer();
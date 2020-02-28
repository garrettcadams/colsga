<?php
/*
 * Template Name: Wilcity Cancel
 */

get_header();
global $wiloke;
?>
	<div class="wil-content">
		<section class="wil-section bg-color-gray-2 pt-30">
			<div class="container">
				<div id="wilcity-cancel" class="row">
					<?php do_action('wilcity/wiloke-submission/cancel/content'); ?>
					<?php
					if ( have_posts() ){
						while (have_posts()){
							the_post();
							the_content();
						}
					}
					?>
				</div>
			</div>
		</section>
	</div>
<?php
get_footer();

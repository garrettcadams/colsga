<?php
get_header();

global $wiloke;

$bg = isset($wiloke->aThemeOptions['404_bg']) && isset($wiloke->aThemeOptions['404_bg']['id']) ? wp_get_attachment_image_url($wiloke->aThemeOptions['404_bg']['id'], 'large') : ''; ?>

<div class="wil-404 bg-cover <?php echo !empty($bg) ? 'wil-404-bg' : '' ?>" style="background-image:url(<?php echo esc_url($bg); ?>)">
	<div class="wil-tb">
		<div class="wil-tb__cell">
			<div class="container">
				<div class="row">
					<div class="col-sm-12">
				        <div class="wil-404-content wil-text-center">
				        	<h2><?php echo Wiloke::ksesHTML($wiloke->aThemeOptions['404_heading']); ?></h2>
				        	<p><?php Wiloke::ksesHTML($wiloke->aThemeOptions['404_description']); ?></p>
				        	<?php get_search_form(); ?>
			        	</div>
		        	</div>
	        	</div>
		    </div>
		</div>
	</div>
</div>

<?php get_footer();

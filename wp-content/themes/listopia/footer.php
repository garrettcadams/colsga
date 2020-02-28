<?php
/**
 * The template for displaying the footer
 *
 * @package WordPress
 * @subpackage Javo
 * @since Javo Themes 1.0
 */
?>
					<?php do_action( 'jvbpd_core/wrapper/after/render', get_page_template_slug() ); ?>
				</div><!-- content-page-wrapper -->
		</div><!-- /#wrapper -->
		<a href="#" class="scrollToTop">
			<div class='scroll icon'><i class="fa fa-arrow-up"></i></div>
		</a>
		<?php
		do_action('Javo/Footer/Render');
		do_action( 'jvbpd_body_after', get_page_template_slug() );
		wp_footer(); ?>
	</body>
</html>
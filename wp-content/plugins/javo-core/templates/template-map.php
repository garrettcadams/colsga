<?php
get_header();
do_action( 'lava_' . jvbpdCore()->getSlug() . '_map_container_before', $post );
?>

			<div id="javo-maps-listings-wrap" <?php post_class(); ?>>
				<?php do_action( 'jvbpd_' . jvbpdCore()->getSlug() . '_map_body' ); ?>
			</div>

			<fieldset>
				<input type="hidden" name="get_pos_trigger" value="<?php echo (boolean) esc_attr( $post->req_is_geolocation ); ?>">
				<input type="hidden" name="set_radius_value" value="<?php echo esc_attr( $post->lava_current_dis ); ?>">
			</fieldset>

			<script type="text/html" id="javo-map-not-found-data">
				<div class="error-page-wrap w-100"><div class="row">
					<div class="col-md-12 text-center" data-dismiss>
						<div class="error-template">
							<h2 class="text-center"><?php esc_html_e( "Sorry, No results", 'jvfrmtd' ); ?></h2>
						</div>
					</div>
				</div>
			</script>

			<?php do_action( 'lava_' . jvbpdCore()->getSlug() . '_map_container_after', $post ); ?>
			<?php do_action( 'jvbpd_core/wrapper/after/render', get_page_template_slug() ); ?>
		</div><!-- #content-wrapper ( in header.php ) -->
	</div><!-- #wrapper ( in header.php ) -->
<?php
get_template_part( 'includes/templates/modal', 'contact-us' );
do_action('Javo/Footer/Render');
do_action( 'jvbpd_body_after', get_page_template_slug() );
wp_footer(); ?>
</body></html>
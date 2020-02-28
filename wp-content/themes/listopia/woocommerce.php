<?php
/**
 * The template for displaying Woocommerce
 *
 * @package WordPress
 * @subpackage Javo Framework
 * @since Javo Themes 1.0
 */
if( !defined( 'ABSPATH' ) ){
	die;
}
get_header(); ?>

	<?php
	switch( apply_filters( 'jvbpd_sidebar_position', 'full', get_the_ID() ) ) {
		case "left":
			?>
			<div class="row">
				<?php get_sidebar();?>
				<div class="col-sm-9 main-content-wrap">
					<?php woocommerce_content(); ?>
				</div>
			</div>
			<?php
		break;
		case "full":
			?>
			<div class="row">
				<div class="col-sm-12 main-content-wrap">
					<?php woocommerce_content(); ?>
				</div>
			</div>
			<?php
		break;
		case "right":
		default:
			?>
			<div class="container">
			<div class="row">
				<div class="col-sm-9 main-content-wrap">
					<?php woocommerce_content(); ?>
				</div>
				<div class="col-sm-3 sidebar">
				<?php
				wp_reset_postdata();
				get_sidebar();?>
				</div>
			</div>
			</div>
		<?php
	}; ?>
<?php get_footer();?>
<?php
/**
 * The Header template for Javo Theme
 *
 * @package WordPress
 * @subpackage Javo
 * @since Javo Themes 1.0
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 *
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<?php
	if('enable' === jvbpd_tso()->get('preloader')) {
		?>
		<div id="preloader-wrap">
			<div class="loading">
				<div class="sk-double-bounce">
					<div class="sk-child sk-double-bounce1"></div>
					<div class="sk-child sk-double-bounce2"></div>
				</div>
			</div>
		</div>
		<?php
	} ?>

	<?php do_action( 'jvbpd_after_body_tag' );?>
	<?php if( defined('ICL_LANGUAGE_CODE') ){ ?>
		<input type="hidden" name="jvbpd_cur_lang" value="<?php echo esc_attr( ICL_LANGUAGE_CODE );?>">
	<?php }; ?>


	<?php do_action( 'Javo/Header/Render' ); // #wrapper ?>
		<div id="content-wrapper">
				<?php do_action( 'jvbpd_output_header', get_the_ID() ); ?>
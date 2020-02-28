<h1><?php esc_html_e( 'Import', 'jvbpd' ); ?></h1>

<?php
$importInstance = class_exists( 'Jvbpd_Import' ) ? Jvbpd_Import::getInstance() : false;
if( $importInstance ) {
	$importInstance->jvbpd_generate_import_page();
} ?>

<p class="jvbpd-wizard-actions step">
	<a href="<?php echo esc_url( $helper->get_next_step_link() ); ?>" class="button button-primary button-next button-large button-next"><?php esc_html_e( 'Next', 'jvbpd' ); ?></a>
</p>
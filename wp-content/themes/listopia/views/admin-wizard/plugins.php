<h1><?php esc_html_e( 'Plugins setup', 'jvbpd' ); ?></h1>

<?php
function_exists('tgmpa_load_bulk_installer') && tgmpa_load_bulk_installer();
// install plugins with TGM.
if ( ! class_exists( 'TGM_Plugin_Activation' ) || ! isset( $GLOBALS['tgmpa'] ) ) {
	die( 'Failed to find TGM' );
}
$url     = wp_nonce_url( add_query_arg( array( 'plugins' => 'go' ) ), 'envato-setup' );
$plugins = $helper->_get_plugins();

$method = '';
$fields = array_keys( $_POST );

if ( false === ( $creds = request_filesystem_credentials( esc_url_raw( $url ), $method, false, false, $fields ) ) ) {
	return true;
}

if ( ! WP_Filesystem( $creds ) ) {
	request_filesystem_credentials( esc_url_raw( $url ), $method, true, false, $fields );
	return true;
} ?>
<h1><?php esc_html_e( "Let's Install Required Plugins", 'jvbpd' ); ?></h1>
<form method="post">

	<?php
	$plugins = $helper->_get_plugins();
	if ( count( $plugins['all'] ) ) {
		?>
		<p><?php esc_html_e( 'Your website needs a few essential plugins. The following plugins will be installed or updated:', 'jvbpd' ); ?></p>
		<ul class="envato-wizard-plugins">
			<?php foreach ( $plugins['all'] as $plugin ) { ?>
				<li data-slug="<?php echo esc_attr( $plugin[ 'slug' ] ); ?>"><?php echo esc_html( $plugin['name'] ); ?>
					<span>
						<?php
						$keys = array();
						if ( isset( $plugins['install'][ $plugin[ 'slug' ] ] ) ) {
							$keys[] = 'Installation';
						}
						if ( isset( $plugins['update'][ $plugin[ 'slug' ] ] ) ) {
							$keys[] = 'Update';
						}
						if ( isset( $plugins['activate'][ $plugin[ 'slug' ] ] ) ) {
							$keys[] = 'Activation';
						}
						echo implode( ' and ', $keys ) . ' required';
						?>
					</span>
					<div class="spinner"></div>
				</li>
			<?php } ?>
		</ul>
		<?php
	} else {
		echo '<p><strong>' . esc_html_e( 'Good news! All plugins are already installed and up to date. Please continue.', 'jvbpd' ) . '</strong></p>';
	} ?>

	<p><?php esc_html_e( 'You can add and remove plugins later on from within WordPress. Click Continue to install', 'jvbpd' ); ?></p>
	<p><?php esc_html_e( "If it's failed, it's mostly from a plugin folder permission issue.", 'jvbpd' ); ?></p>


	<p class="jvbpd-wizard-actions step">
		<a href="<?php echo esc_url( $helper->get_next_step_link() ); ?>" id="jvbpd-wizard-plugins" class="button button-primary button-next button-large button-next"><?php esc_html_e( 'Continue', 'jvbpd' ); ?></a>
	</p>
</form>
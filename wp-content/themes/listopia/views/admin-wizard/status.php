<h2><?php esc_html_e( 'Check your server status', 'jvbpd' ); ?></h2>

<?php
$arrCheckLists = Array(
	'php_ver' => Array(
		'label' => esc_html__( "PHP Version", 'jvbpd' ),
		'desc' => esc_html__( "7.3.x", 'jvbpd' ),
		'value' => phpversion(),
		'state' => version_compare( '7.3.0', phpversion(), '<=' ),
	),
	'post_max' => Array(
		'label' => esc_html__( "Post Max Size", 'jvbpd' ),
		'desc' => esc_html__( "128M", 'jvbpd' ),
		'value' => ini_get( 'post_max_size' ),
		'state' =>
			intVal( apply_filters( 'jvbpd_memory_unit_conversion', '128M' ) ) <=
			intVal( apply_filters( 'jvbpd_memory_unit_conversion', ini_get( 'post_max_size' ) ) ),
	),
	'limit_memory' => Array(
		'label' => esc_html__( "WP MEMORY LIMIT", 'jvbpd' ),
		'desc' => esc_html__( "128M", 'jvbpd' ),
		'value' => WP_MEMORY_LIMIT,
		'state' =>
			intVal( apply_filters( 'jvbpd_memory_unit_conversion', '128M' ) ) <=
			intVal( apply_filters( 'jvbpd_memory_unit_conversion', WP_MEMORY_LIMIT ) ),
	),
	'wp_debug' => Array(
		'label' => esc_html__( "WP DEBUG", 'jvbpd' ),
		'desc' => esc_html__( "Off", 'jvbpd' ),
		'value' => ( WP_DEBUG ? esc_html__( "On", 'jvbpd' ) : esc_html__( "Off", 'jvbpd' ) ),
		'state' => defined( 'WP_DEBUG' ) && ! WP_DEBUG
	),
); ?>

<table width="100%">
	<thead>
		<tr>
			<th></th>
			<th style="text-align:center;"><?php esc_html_e( "Recommended", 'jvbpd' ); ?></th>
			<th style="text-align:center;"><?php esc_html_e( "Your Server", 'jvbpd' ); ?></th>
			<th style="text-align:center;"><?php esc_html_e( "Status", 'jvbpd' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<?php
			if( !empty( $arrCheckLists ) ) {
				foreach( $arrCheckLists as $strKey => $arrMeta ) {
					?>
					<tr>
						<th style="text-align:left;"><?php echo esc_html( $arrMeta[ 'label' ] ); ?></th>
						<td style="text-align:center;"><span><?php echo esc_html( $arrMeta[ 'desc' ] ); ?></span></td>
						<td style="text-align:center;"><?php echo esc_html($arrMeta[ 'value' ] ); ?></td>
						<td style="text-align:center;"><span class="dashicons <?php echo esc_attr( $arrMeta[ 'state' ] ? 'dashicons-yes' : 'dashicons-no' ); ?>"></span></td>
					</tr>
					<?php
				}
			} ?>
		</tr>
	</tbody>
</table>


<p class="jvbpd-wizard-actions step">
	<a href="<?php echo esc_url( $helper->get_next_step_link() ); ?>" class="button button-primary button-next button-large button-next"><?php esc_html_e( 'Next', 'jvbpd' ); ?></a>
</p>
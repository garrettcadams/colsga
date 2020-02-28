<?php
$helper->wc_setup_ready_actions();
?>

<h1><?php esc_html_e( 'Your site is ready!', 'jvbpd' ); ?></h1>

<div class="jvbpd-wizard-next-steps">
	<div class="jvbpd-wizard-next-steps-first">
		<h2><?php esc_html_e( 'Vist Your site', 'jvbpd' ); ?></h2>
		<ul>
			<li class="setup-product"><a class="button button-primary button-large" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank"><?php esc_html_e( 'Visit Home Page', 'jvbpd' ); ?></a></li>
			<li class="setup-product"><a class="button button-large" href="<?php echo esc_url( admin_url( '/' ) ); ?>" target="_blank"><?php esc_html_e( 'Visit Dashboard', 'jvbpd' ); ?></a></li>
		</ul>
	</div>
	<div class="jvbpd-wizard-next-steps-last">
		<h2><?php esc_html_e( 'Learn more', 'jvbpd' ); ?></h2>
		<ul>
			<?php
			foreach(
				Array(
					Array(
						'comment' => '<font color="red">' . esc_html__( "Important: Check List after import", 'jvbpd' ) . '</font>',
						'link' => esc_url_raw( apply_filters( 'jvbpd_doc_url', '' ) . 'check-list-after-demo-imported/', 'jvbpd' ),
					),
					Array(
						'comment' => esc_html__( "Online Documentation", 'jvbpd' ),
						'link' => esc_url_raw( apply_filters( 'jvbpd_doc_url', '' ) ),
					),
					Array(
						'comment' => esc_html__( "Visit our support site", 'jvbpd' ),
						'link' => esc_url_raw( 'https://javothemes.com/support/' ),
					),
				) as $arrSection
			){
				printf(
					'<li>%1$s( <a href="%2$s" target="_blank" title="%2$s" style="display:inline-block;">%3$s</a> )</li>',
					$arrSection[ 'comment' ], $arrSection[ 'link' ], esc_html__( "Detail", 'jvbpd' )
				);
			} ?>

		</ul>
	</div>
</div>
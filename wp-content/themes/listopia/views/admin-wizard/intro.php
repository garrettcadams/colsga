<h1><?php printf( esc_html__( 'Welcome! %s setup wizard', 'jvbpd' ), wp_get_theme()->Name ); ?></h1>
<p><?php printf( __( 'Thank you for choosing %s theme! This quick setup wizard will help you configure the basic settings. </strong>It\'s completely optional and shouldn\'t take longer than five minutes.</strong>', 'jvbpd' ), wp_get_theme()->Name ); ?></p>
<p><?php esc_html_e( 'No time right now? If you don\'t want to go through the wizard, you can skip and return to the WordPress dashboard. Come back anytime if you change your mind!', 'jvbpd' ); ?></p>
<p class="jvbpd-wizard-actions step">
<a href="<?php echo esc_url( $helper->get_next_step_link() ); ?>" class="button-primary button button-large button-next"><?php esc_html_e( "Let's go!", 'jvbpd' ); ?></a>
<a href="<?php echo esc_url( admin_url() ); ?>" class="button button-large"><?php esc_html_e( 'Not right now', 'jvbpd' ); ?></a>
</p>
<?php
class jvbpd_mailchimp{

	public function __construct(){
		add_shortcode( 'lynk_mailchimp', Array( __CLASS__, 'callback' ) );
		add_action( 'wp_ajax_lynk_mailchimp', Array( __CLASS__, 'response' ) );
		add_action( 'wp_ajax_nopriv_lynk_mailchimp'	, Array( __CLASS__, 'response' ) );
	}

	public static function callback( $atts, $content='' )
	{
		extract( shortcode_atts(Array(

			/*	Describe :		list_id
			*	Type :			String( Empty / 'map' )
			*/
			'list_id'			=> ''

		), $atts));

		$errors				= new wp_error;
		$jvbpd_cmp_action	= trailingslashit( home_url() );

		// Parameter

		// API KEY
		if( '' === ( jvbpd_tso()->get( 'mailchimp_api', '' ) ) )
		{
			$errors->add( 'mailchimp-no-api', sprintf( "<strong>%s</strong><br>%s"
				, __("The API Key does not exist", 'jvfrmtd')
				, __("Please insert API Key in Theme Settings > General > API Key", 'jvfrmtd')
			) );
		}

		// MAILCHIMP LIST
		if( '' === $list_id )
		{
			$errors->add( 'mailchimp-no-list', sprintf( "<strong>%s</strong><br>%s"
				, __("List is not selected.", 'jvfrmtd')
				, __("Please select list in Shortcode Edit.", 'jvfrmtd')
			) );
		}


		ob_start();
		if( ! count( $errors->get_error_codes() ) > 0 ):
			?>
			<div id="lynk_mailchimp" class="javo-mailchimp-container">
				<form id="newsletter-form" name="newsletter-form" data-url="<?php echo $jvbpd_cmp_action;?>" method="post" role="form">

					<div class="javo-mailchimp-wrap">
						<div class="form-group javo-mailchimp-inner">

							<div class="input-group input-group-sm javo-mailchimp-inner-name">
								<!--<label class="input-group-addon" for="lynk_cmp_name">
									<span class="glyphicon glyphicon-envelope"></span>
								</label>-->
								<input type="text" name="yname" id="lynk_cmp_name" class="javo-mailchimp-inner-name-input" placeholder="<?php _e("Your name", 'jvfrmtd');?>" class="form-control" required>
							</div><!-- /.input-group -->

							<div class="input-group input-group-sm javo-mailchimp-inner-mail">
								<input type="email" name="mc_email" id="lynk_cmp_email" class="javo-mailchimp-inner-mail-input"placeholder="<?php _e("Your email", 'jvfrmtd');?>" class="form-control" required>
								<div class="form-group javo-mailchimp-inner-sand">

									<button type="submit" class="btn btn-primary javo-mailchimp-inner-sand-icon">
										<span><i class="fa fa-location-arrow"><?php _e("", 'jvfrmtd'); ?></i></span>
									</button>

								</div><!--javo-mailchimp-inner-sand-->
							</div><!-- /.input-group -->


						</div><!-- /.form-group -->
					</div>




					<fieldset>
						<input type="hidden" name="lynk_mailchimp_security" value="<?php echo wp_create_nonce( "javo-mailchimp-shortcode" );?>">
						<input type="hidden" name="cm_list" value="<?php echo $list_id; ?>">
						<input type="hidden" name="ajaxurl" value="<?php echo admin_url( "admin-ajax.php" ); ?>">
						<input type="hidden" name="no_msg" value="<?php _e("Failed to register", 'jvfrmtd'); ?>">
					</fieldset>
				</form>

			</div>
			<?php
		else:
			?>
			<div class="well well-sm">
				<span class="glyphicon glyphicon-exclamation-sign"></span>
				<?php
				foreach( $errors->get_error_messages() as $messages ){
					echo "<p>{$messages}</p>";
				} ?>
			</div>
			<?php
		endif; // End if
		return ob_get_clean();
	}

	public static function response()
	{
		check_ajax_referer( 'javo-mailchimp-shortcode', 'nonce');

		$jvbpd_query		= new jvbpd_Array( $_POST );

		if( '' !== ( $jvbpd_api_key = jvbpd_tso()->get( 'mailchimp_api', '' ) ) )
		{
			include_once LYNK_SYS_DIR.'/functions/MCAPI.class.php';
			$mcapi		= new MCAPI( $jvbpd_api_key );

			$name		= explode(" ", $jvbpd_query->get('yname', '') );
			$fname		= !empty( $name[0] ) ? $name[0] : '';
			unset( $name[0] );

			$lname		= !empty($name) ? join( ' ', $name) : '';

			$merge_vars	= array(
				'FNAME'		=> $fname
				, 'LNAME'	=> $lname
			);

			$answer = $mcapi->listSubscribe( $jvbpd_query->get( 'list', '' ), $jvbpd_query->get( 'mc_email', '' ), $merge_vars );
			if( $mcapi->errorCode )
				{
				// An error ocurred, return error message
				$msg = $mcapi->errorMessage;
			}else{
				// It worked!
				$msg = __('Success!&nbsp; Check your inbox or spam folder for a message containing a confirmation link.','jvfrmtd');
			}
		}else{
			$msg = __( "Please insert API Key in Theme Settings > General > API Key", 'jvfrmtd' );
		}

		die( json_encode( Array( 'message' => $msg ) ) );
	}
}
new jvbpd_mailchimp;

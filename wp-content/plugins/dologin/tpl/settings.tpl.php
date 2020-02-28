<?php
namespace dologin;

defined( 'WPINC' ) || exit;

$current_user_phone = SMS::get_instance()->current_user_phone();

?>
<div class="wrap dologin-settings">
	<h2 class="dologin-h2"><?php echo __( 'DoLogin Security Settings', 'dologin' ); ?></h2>
	<span class="dologin-desc">
		v<?php echo Core::VER; ?>
	</span>

	<hr class="wp-header-end">

	<form method="post" action="<?php menu_page_url( 'dologin' ); ?>" class="dologin-relative">
	<?php wp_nonce_field( 'dologin' ); ?>

	<table class="form-table">
		<tr>
			<th scope="row" valign="top"><?php echo __( 'Lockout', 'dologin' ); ?></th>
			<td>
				<p><input type="text" size="3" maxlength="4" name="max_retries" value="<?php echo Conf::val( 'max_retries' ); ?>" /> <?php echo __( 'Allowed retries', 'dologin' ); ?></p>

				<p><input type="text" size="3" maxlength="4" name="duration" value="<?php echo Conf::val( 'duration' ); ?>" /> <?php echo __( 'minutes lockout', 'dologin' ); ?></p>
				<p class="description"><?php echo sprintf( __( 'If hit %1$s maximum retries in %2$s minutes, the login attempt from that IP will be temporarily disabled.', 'dologin' ), '<code>' . Conf::val( 'max_retries' ) . '</code>', '<code>' . Conf::val( 'duration' ) . '</code>' ); ?></p>
			</td>
		</tr>

		<tr>
			<th scope="row" valign="top"><?php echo __( 'GDPR Compliance', 'dologin' ); ?></th>
			<td>
				<p><label><input type="checkbox" name="gdpr" value="1" <?php echo Conf::val( 'gdpr' ) ? 'checked' : ''; ?> /> <?php echo __( 'Enable', 'dologin' ); ?></label></p>
				<p class="description">
					<?php echo __( 'With this feature turned on, all logged IPs get obfuscated (md5-hashed).', 'dologin' ); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row" valign="top"><?php echo __( 'Two Factor Auth', 'dologin' ); ?></th>
			<td>
				<p><label><input type="checkbox" name="sms" value="1" <?php echo Conf::val( 'sms' ) ? 'checked' : ''; ?> /> <?php echo __( 'Enable Two Step SMS Auth', 'dologin' ); ?></label></p>
				<p class="description">
					<?php echo __( 'Verify text code for each login attempt.', 'dologin' ); ?>
					<?php echo __( 'Users need to setup the Dologin Phone number in their profile.', 'dologin' ); ?>
					<?php echo __( 'The phone number need to specify the coutry calling codes.', 'dologin' ); ?>
					<?php echo sprintf( __( 'Text message is free sent by API from %s.', 'dologin' ), '<a href="https://www.doapi.us" target="_blank">DoAPI.us</a>' ); ?>
				</p>

				<p><label><input type="checkbox" name="sms_force" value="1" <?php echo Conf::val( 'sms_force' ) ? 'checked' : ''; ?> /> <?php echo __( 'Force SMS Auth Validation', 'dologin' ); ?></label></p>
				<p class="description">
					<?php echo __( 'If enabled this, any user without phone set in profile will not be able to login.', 'dologin' ); ?>
					<a href="profile.php"><?php echo __( 'Click here to set your Dologin Security phone number', 'dologin' ); ?></a>
					<?php if ( ! $current_user_phone ) : ?>
						<?php echo '<div class="dologin-warning-h3">' . __( 'You need to setup your Dologin Phone number before enabling this setting to avoid yourself being blocked from next time login.', 'dologin' ) . '</div>'; ?>
					<?php else : ?>
				</p>
				<p class="description">
						<button type="button" class="button button-primary" id="dologin_test_sms"><?php echo __( 'Test SMS message', 'dologin' ); ?></button>
						<span id='dologin_test_sms_res'></span>
						<?php echo __( 'This will send a test text message to your phone number.', 'dologin' ); ?>
					<?php endif; ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row" valign="top"><?php echo __( 'Google reCAPTCHA', 'dologin' ); ?></th>
			<td>
				<p><label><input type="checkbox" name="gg" value="1" <?php echo Conf::val( 'gg' ) ? 'checked' : ''; ?> /> <?php echo __( 'Enable', 'dologin' ); ?></label></p>

				<p class="description"><?php echo __( 'This will enable reCAPTCHA on login page.', 'dologin' ); ?></p>

				<div style="display:flex;">
					<div style="margin-right: 50px;">
						<p><label>
							<span class="dologin_text_label_prefix"><?php echo __( 'Site Key', 'dologin' ); ?>:</span>
							<input type="text" size="50" name="gg_pub_key" value="<?php echo Conf::val( 'gg_pub_key' ); ?>" />
						</label></p>
						<p><label>
							<span class="dologin_text_label_prefix"><?php echo __( 'Secret Key', 'dologin' ); ?>:</span>
							<input type="text" size="50" name="gg_priv_key" value="<?php echo Conf::val( 'gg_priv_key' ); ?>" />
						</label></p>
					</div>
					<div>
					<?php
						if ( Conf::val( 'gg' ) || ( Conf::val( 'gg_pub_key' ) && Conf::val( 'gg_priv_key' ) ) ) {
							Captcha::get_instance()->show();
						}
					?>
					</div>
				</div>

				<p class="description">
					<?php echo sprintf( __( '<a %s>Click here</a> to generate keys from Google reCAPTCHA.', 'dologin' ), 'href="https://www.google.com/recaptcha/admin#list" target="_blank"'); ?>
					<?php echo __( 'Note: v2 supported only.', 'dologin' ); ?>
				</p>

				<p><label><input type="checkbox" name="recapt_register" value="1" <?php echo Conf::val( 'recapt_register' ) ? 'checked' : ''; ?> /> <?php echo __( 'Enable on Register Page', 'dologin' ); ?></label></p>

				<!-- Need to wait for https://core.trac.wordpress.org/ticket/49521 fixed -->
				<p class="dologin-hide"><label><input type="checkbox" name="recapt_forget" value="1" <?php echo Conf::val( 'recapt_forget' ) ? 'checked' : ''; ?> /> <?php echo __( 'Enable on Lost Password Page', 'dologin' ); ?></label></p>

			</td>
		</tr>

		<tr>
			<th scope="row" valign="top"><?php echo __( 'Whitelist', 'dologin' ); ?></th>
			<td>
				<div class="field-col">
					<textarea name="whitelist" rows="10" cols="80"><?php echo esc_textarea( implode( "\n", Conf::val( 'whitelist' ) ) ); ?></textarea>
				</div>
				<div class="field-col field-col-desc">
					<p class="description">
						<?php echo __( 'Format', 'dologin' ); ?>: <code>prefix1:value1, prefix2:value2</code>.
						<?php echo __( 'Both prefix and value are case insensitive.', 'dologin' ); ?>
						<?php echo __( 'Spaces around comma/colon are allowed.', 'dologin' ); ?>
						<?php echo __( 'One rule set per line.', 'dologin' ); ?>
					</p>
					<p class="description">
						<?php echo __( 'Prefix list', 'dologin' ); ?>: <code>ip</code>, <code><?php echo implode( '</code>, <code>', IP::$PREFIX_SET ); ?></code>.
					</p>
					<p class="description"><?php echo __( 'IP prefix with colon is optional. IP value support wildcard (*).', 'dologin' ); ?></p>
					<p class="description"><?php echo __( 'Example', 'dologin' ); ?> 1) <code>ip:1.2.3.*</code></p>
					<p class="description"><?php echo __( 'Example', 'dologin' ); ?> 2) <code>42.20.*.*, continent_code: NA</code> (<?php echo __( 'Dropped optional prefix', 'dologin' ); ?> <code>ip:</code>)</p>
					<p class="description"><?php echo __( 'Example', 'dologin' ); ?> 3) <code>continent: North America, country_code: US, subdivision_code: NY</code></p>
					<p class="description"><?php echo __( 'Example', 'dologin' ); ?> 4) <code>subdivision_code: NY, postal: 10001</code></p>
					<p class="description">
						<button type="button" class="button button-link" id="dologin_get_ip"><?php echo __( 'Get my GeoLocation data from', 'dologin' ); ?> DoAPI.us</button>
						<code id="dologin_mygeolocation">-</code>
					</p>
				</div>
			</td>
		</tr>

		<tr>
			<th scope="row" valign="top"><?php echo __( 'Blacklist', 'dologin' ); ?></th>
			<td>
				<div class="field-col">
					<textarea name="blacklist" rows="10" cols="80"><?php echo esc_textarea( implode( "\n", Conf::val( 'blacklist' ) ) ); ?></textarea>
				</div>
				<div class="field-col field-col-desc">
					<p class="description">
						<?php echo sprintf( __( 'Same format as %s', 'dologin' ), '<strong>' . __( 'Whitelist', 'dologin' ) . '</strong>' ); ?>
					</p>
				</div>
			</td>
		</tr>

		<tr>
			<th scope="row" valign="top"><?php echo __( 'Auto Upgrade', 'dologin' ); ?></th>
			<td>
				<p><label><input type="checkbox" name="auto_upgrade" value="1" <?php echo Conf::val( 'auto_upgrade' ) ? 'checked' : ''; ?> /> <?php echo __( 'Enable', 'dologin' ); ?></label></p>
				<p class="description">
					<?php echo __( 'Enable this option to get the latest features at the first moment.', 'dologin' ); ?>
				</p>
			</td>
		</tr>

	</table>

	<p class="submit">
		<?php submit_button(); ?>
	</p>
	</form>
</div>

<script>
	jQuery( function( $ ) {
		$( '#dologin_test_sms' ).click( function( e ) {
			$.ajax( {
				url: '<?php echo get_rest_url( null, 'dologin/v1/test_sms' ); ?>',
				type: 'POST',
				dataType: 'json',
				data: {
					phone: '<?php echo SMS::get_instance()->current_user_phone(); ?>'
				},
				success: function( res ) {
					if ( res._res !== 'ok' ) {
						$( '#dologin_test_sms_res' ).attr( 'class', 'dologin-err' ).html( res._msg );
					} else {
						$( '#dologin_test_sms_res' ).attr( 'class', 'dologin-success' ).html( res.info );
					}
				}
			} );
		} );

		$( '#dologin_get_ip' ).click( function( e ) {
			$.ajax( {
				url: '<?php echo get_rest_url( null, 'dologin/v1/myip' ); ?>',
				dataType: 'json',
				success: function( data ) {
					var html = [];
					$.each( data, function( k, v ) {
						 html.push( k + ':' + v );
					});
					$( '#dologin_mygeolocation' ).html( html.join( ', ' ) );
				}
			} );
		} );
	} );
</script>

<div class="wrap dologin-settings">
	<h3>
		<?php echo __( 'Passwordless Login', 'dologin' ); ?>

		<a href="users.php" style="margin-left: 20px;"><?php echo __( 'Generate Links in Users List', 'dologin' ); ?></a>
	</h3>

	<p class="description"><?php echo __( 'Here you can generate login links and manage them.', 'dologin' ); ?></p>

	<table class="wp-list-table widefat striped">
		<thead>
		<tr>
			<th>#</th>
			<th><?php echo __( 'Date', 'dologin' ); ?></th>
			<th><?php echo __( 'User', 'dologin' ); ?></th>
			<th><?php echo __( 'Link', 'dologin' ); ?></th>
			<th><?php echo __( 'Created By', 'dologin' ); ?></th>
			<th><?php echo __( 'Count', 'dologin' ); ?></th>
			<th><?php echo __( 'Last Used At', 'dologin' ); ?></th>
			<th><?php echo __( 'Expired At', 'dologin' ); ?></th>
			<th><?php echo __( 'One Time Usage', 'dologin' ); ?></th>
			<th><?php echo __( 'Status', 'dologin' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ( $this->pswdless_log() as $v ) : ?>
			<tr>
				<td><?php echo $v->id; ?></td>
				<td><?php echo Util::readable_time( $v->dateline ); ?></td>
				<td><?php echo $v->username; ?></td>
				<td><?php echo $v->link; ?></td>
				<td><?php echo $v->src; ?></td>
				<td><?php echo $v->count; ?></td>
				<td><?php echo $v->last_used_at ? Util::readable_time( $v->last_used_at ) : '-'; ?></td>
				<td>
					<?php echo $v->expired_at > time() ? Util::readable_time( $v->expired_at - time(), 3600, false ) : '<font color="red">Expired</font>'; ?>

					<a href="<?php echo Util::build_url( Router::ACTION_PSWD, Pswdless::TYPE_EXPIRE_7, false, null, array( 'dologin_id' => $v->id ) ); ?>" class="button button-primary"><?php echo __( '+7 Days', 'dologin' ); ?></a>
				</td>
				<td>
					<?php echo $v->onetime ? '<font color="green">Yes</font>' : '<font color="red">No</font>'; ?>
					<a href="<?php echo Util::build_url( Router::ACTION_PSWD, Pswdless::TYPE_TOGGLE_ONETIME, false, null, array( 'dologin_id' => $v->id ) ); ?>"><span class="dashicons dashicons-controls-repeat"></span></a>
				</td>
				<td>
					<a href="<?php echo Util::build_url( Router::ACTION_PSWD, Pswdless::TYPE_LOCK, false, null, array( 'dologin_id' => $v->id ) ); ?>"><?php echo $v->active ? '<span class="dashicons dashicons-unlock"></span>' : '<span class="dashicons dashicons-lock"></span>'; ?></a>
					<?php
					if ( $v->active == 1 ) :
						echo '<font color="green">' . __( 'Active', 'dologin') . '</font>';
					else :
						echo '<font color="red">' . __( 'Disabled', 'dologin') . '</font>';
					endif;
					?>
					<a href="<?php echo Util::build_url( Router::ACTION_PSWD, Pswdless::TYPE_DEL, false, null, array( 'dologin_id' => $v->id ) ); ?>" class="dologin-right"><span class="dashicons dashicons-dismiss"></span></a>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

</div>

<div class="wrap dologin-settings">
	<h3><?php echo __( 'Login Attempts Log', 'dologin' ); ?></h3>

	<table class="wp-list-table widefat striped">
		<thead>
		<tr>
			<th>#</th>
			<th><?php echo __( 'Date', 'dologin' ); ?></th>
			<th><?php echo __( 'IP', 'dologin' ); ?></th>
			<th><?php echo __( 'GeoLocation', 'dologin' ); ?></th>
			<th><?php echo __( 'Login As', 'dologin' ); ?></th>
			<th><?php echo __( 'Gateway', 'dologin' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ( $this->log() as $v ) : ?>
			<tr>
				<td><?php echo $v->id; ?></td>
				<td><?php echo Util::readable_time( $v->dateline ); ?></td>
				<td><?php echo $v->ip; ?></td>
				<td><?php echo $v->ip_geo; ?></td>
				<td><?php echo $v->username; ?></td>
				<td><?php echo $v->gateway; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>

<h2 style="margin: 30px;">
	<a href="https://wordpress.org/support/plugin/dologin/reviews/?rate=5#new-post" target="_blank"><?php echo __( 'Rate Us!' ); ?>
		<span class="wporg-ratings rating-stars" style="text-decoration: none;">
			<span class="dashicons dashicons-star-filled" style="color:#ffb900 !important;"></span><span class="dashicons dashicons-star-filled" style="color:#ffb900 !important;"></span><span class="dashicons dashicons-star-filled" style="color:#ffb900 !important;"></span><span class="dashicons dashicons-star-filled" style="color:#ffb900 !important;"></span><span class="dashicons dashicons-star-filled" style="color:#ffb900 !important;"></span>
		</span>
	</a>
</h2>
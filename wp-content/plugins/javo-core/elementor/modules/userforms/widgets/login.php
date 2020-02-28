<?php
namespace jvbpdelement\Modules\Userforms\Widgets;

class jv_login extends Base {

	public function get_name() { return parent::LOGIN; }
	public function get_title() { return 'JV Login'; }
	public function get_icon() { return 'eicon-lock-user'; }

	protected function _register_controls() {
		parent::_register_controls();
	}

	protected function render() {
		parent::render();
		$settings = $this->get_settings();
		$current_url = remove_query_arg( 'fake_arg' );

		if ( 'yes' === $settings['redirect_after_login'] && ! empty( $settings['redirect_url'] ) ) {
			$this->add_render_attribute( 'form', Array(
				'data-redirect' => $settings['redirect_url'],
			) );
		}

		//if ( is_user_logged_in() && ! Plugin::elementor()->editor->is_edit_mode() ) {
		if ( is_user_logged_in() && !is_admin() ) {
			if ( 'yes' === $settings['show_logged_in_message'] ) {
				$current_user = wp_get_current_user();

				echo '<div class="elementor-login">' .
					sprintf( __( 'You are Logged in as %1$s (<a href="%2$s">Logout</a>)', 'jvfrmtd' ), $current_user->display_name, wp_logout_url( $current_url ) ) .
					'</div>';
			}

			return;
		}

	 $field_size ="";
	 if( !empty( $settings['input_size'] ) ) {
		$field_size = "field-size-". $settings['input_size'];
	 }


	$btn_size="";
	if( !empty( $settings['button_size'] ) ) {
		$btn_size = "btn-size-". $settings['button_size'];
	}

	$btn_block="";
	if ($settings['button_block'] == 'yes' ) {
		$btn_block = "btn-block";
	}

	$this->add_render_attribute( 'form', Array(
		'class' => 'jvbpd-login-form jv-modal-login',
		'method' => 'post',
		'action' => esc_url( wp_login_url( apply_filters( 'jvbpd_modal_login_redirect', '' ) ) ),
	) );

	$this->add_render_attribute( 'output', Array(
		'class' => 'btn-wrap submit-wrap',
	) );
	?>
		<form <?php echo $this->get_render_attribute_string( 'form' ); ?>>
			<div class="row">
				<div class="col-md-12 lava_login_wrap">

					<!-- User Name -->
					<div class="form-group">
						<label class="" for="reg-usernmae"><?php esc_html_e('Username', 'listopia' );?></label>
						<input type="text" name="log" id="username"  value="" class="form-control <?php echo $field_size ?>" placeholder="<?php esc_html_e("Username",'jvfrmtd' );?>" required>
					</div>

					<!-- User Password -->
					<div class="form-group">
						<label class="" for="reg-password"><?php esc_html_e('Password', 'listopia' );?></label>
						<input type="password" name="pwd" id="password" value="" class="form-control <?php echo $field_size ?>" placeholder="<?php esc_html_e("Password",'jvfrmtd' );?>" required>
					</div>

					<!-- Descriptions -->
					<div class="form-group">
						<?php if( $settings['show_remember_me'] ) : ?>
						<label class="control-label">
							<input name="rememberme" value="forever" type="checkbox"> <?php esc_html_e("Remember Me", 'jvfrmtd' );?>
						</label><!-- /.control-label -->
						<?php endif; ?>
						<?php if( $settings['show_lost_password'] ) : ?>
						<a href="<?php echo esc_url( wp_lostpassword_url() );?>">
							<p class="required"><?php esc_html_e('Forgot Your Password?', 'jvfrmtd' ); ?></p>
						</a>
						<?php endif; ?>
					</div>

					<!-- Login Button -->
					<div class="form-group">
						<div class="lava_login">
							<div <?php echo $this->get_render_attribute_string( 'output' ); ?>>
								<div class="button-wrap">
									<button type="submit" class="btn <?php echo $btn_size; ?> <?php echo $btn_block; ?>"><?php echo $settings['button_text']; ?></button>
								</div>
								<?php do_action( 'jvbpd_login2_modal_login_after' ); ?>
							</div><!-- /.col-md-12 -->
						</div><!-- /.row -->
					</div>
				</div>
			</div><!--/.row-->

			<div class="row bottom_row">
				<hr class="padding-5px">
				<div class="col-md-12 col-xs-12">
					<?php if( get_option( 'users_can_register' ) ) : ?>
						<?php if( $settings['show_register'] ) : ?>
							<p><?php esc_html_e("Don't have an account?", 'jvfrmtd' );?> <a href="#" data-toggle="modal" data-target="#register_panel" class="required"><?php esc_html_e('Sign Up', 'jvfrmtd' );?></a></p>
						<?php endif; ?>
					<?php else: ?>
						<div class="not_allowed_new_member">
							<span><?php esc_html_e("This site is not allowed new members. please contact administrator.", 'jvfrmtd' );?></span>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<input type="hidden" name="referer" value="<?php echo isset( $_GET[ 'referer' ] ) && '' != $_GET[ 'referer' ] ? esc_url( $_GET[ 'referer' ] ) : false; ?>">
		</form>
		<?php
	}



}
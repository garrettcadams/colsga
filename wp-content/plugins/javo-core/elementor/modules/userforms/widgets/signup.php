<?php
namespace jvbpdelement\Modules\Userforms\Widgets;

class jv_signup extends Base {

	public function get_name() { return parent::SIGNUP; }
	public function get_title() { return 'JV Signup'; }
	public function get_icon() { return 'eicon-lock-user'; }

	protected function _register_controls() {
		parent::_register_controls();
	}

	protected function render() {
		parent::render();
		$settings = $this->get_settings();
		$current_url = remove_query_arg( 'fake_arg' );

		if ( 'yes' === $settings['redirect_after_login'] && ! empty( $settings['redirect_url']['url'] ) ) {
			$redirect_url = $settings['redirect_url']['url'];
		} else {
			$redirect_url = $current_url;
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

		$this->add_render_attribute( 'form', Array(
			'method' => 'post',
			'data-jvbpd-signup-form' => '',
		) );

		$this->add_render_attribute( 'output', Array(
			'class' => 'btn-wrap submit-wrap',
		) ); ?>

		<form <?php echo $this->get_render_attribute_string( 'form' ); ?>>
			<div class="modal-body">
				<?php do_action( 'jvbpd_register_form_before' ); ?>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="" for="reg-username"><?php esc_html_e('Username', 'jvfrmtd' );?></label>
							<input type="text" id="reg-username" name="user_login" class="form-control <?php echo $field_size ?>" required="" placeholder="<?php esc_attr_e( 'Username', 'jvfrmtd' );?>">
						</div>
					</div><!-- /.col-md-6 -->
					<div class="col-md-6">
						<div class="form-group">
							<label class="" for="reg-email"><?php esc_html_e('Email Address', 'jvfrmtd' );?></label>
							<input type="email" id="reg-email" name="user_email" class="form-control <?php echo $field_size ?>" required="" placeholder="<?php esc_attr_e( 'Your email', 'jvfrmtd' );?>">
						</div>
					</div><!-- /.col-md-6 -->
				</div><!-- /.row -->
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="" for="firstname"><?php esc_html_e('First Name', 'jvfrmtd' );?></label>
							<input type="text" id="firstname" name="first_name" class="form-control <?php echo $field_size ?>" required="" placeholder="<?php esc_attr_e( 'Your first name', 'jvfrmtd' );?>">

						</div>
					</div><!-- /.col-md-6 -->
					<div class="col-md-6">
						<div class="form-group">
							<label class="" for="lastname"><?php esc_html_e('Last Name', 'jvfrmtd' );?></label>
							<input type="text" id="lastname" name="last_name" class="form-control <?php echo $field_size ?>" required="" placeholder="<?php esc_attr_e( 'Your last name', 'jvfrmtd' );?>">

						</div>
					</div><!-- /.col-md-6 -->
				</div><!-- /.row -->
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="" for="reg-password"><?php esc_html_e('Password', 'jvfrmtd' );?></label>
							<input type="password" id="reg-password" name="user_pass" class="form-control <?php echo $field_size ?>" required="" placeholder="<?php esc_attr_e( 'Desired Password', 'jvfrmtd' );?>">

						</div>
					</div><!-- /.col-md-6 -->
					<div class="col-md-6">
						<div class="form-group">
							<label class="" for="reg-con-password"><?php esc_html_e('Confirm Password', 'jvfrmtd' );?></label>
							<input type="password" id="reg-con-password" name="user_con_pass" class="form-control <?php echo $field_size ?>" required="" placeholder="<?php esc_attr_e( 'Confirm Password', 'jvfrmtd' );?>">

						</div>
					</div><!-- /.col-md-6 -->
				</div><!-- /.row -->
				<?php
				//echo $this->getPartSignUpAgree();
				do_action( 'jvbpd_register_form_after' ); ?>
			</div>

			<div <?php echo $this->get_render_attribute_string( 'output' ); ?>>
			<button type="submit" id="signup" name="submit" class="btn <?php echo $btn_size ?>"><?php echo $settings['button_text']; ?></button> &nbsp;

			</div>
		</form>
		<?php
	}

}
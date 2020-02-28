<?php
namespace jvbpdelement\Modules\Button\Widgets;

use Elementor\Controls_Manager;
class LoginSignUp extends Base {
	public function get_name() { return parent::LOGIN_SIGNUP_BUTTON; }
	public function get_title() { return 'Login / Sign-up Modal'; }
	public function get_icon() { return 'eicon-button'; }
	public function get_categories() { return [ 'jvbpd-page-builder-login' ]; }

	protected function _register_controls() {
		parent::_register_controls();

		$this->start_controls_section( 'section_type', array(
			'label' => esc_html__( 'Type', 'jvfrmtd' ),
		) );
		$this->add_control( 'button_type', Array(
			'label' => __( 'Button Type ', 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT,
			'default' => 'login',
			'options' => Array(
				'login'  => __( 'Login', 'jvfrmtd' ),
				'signup'  => __( 'Sign-up', 'jvfrmtd' ),
			),
		) );

		$this->add_control('login_type', Array(
			'type' => Controls_Manager::SELECT,
			'label' => esc_html__("Login Type", 'jvfrmtd'),
			'default' => 'modal',
			'options' => Array(
				'modal' => esc_html__("Modal Login", 'jvfrmtd'),
				'redirect' => esc_html__("Redirect Login Page", 'jvfrmtd'),
			),
		));

		$this->add_control( 'header_login_button', Array(
			'label' => esc_html__( "After login", 'jvfrmtd' ),
			'type' => Controls_Manager::HEADING,
			'separator' => 'before',
		) );

		$this->add_control( 'hide_button', Array(
			'label' => esc_html__( "Hide this button", 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'return_value' => 'hide',
			'prefix_class' => 'button-',
			'selectors' => Array(
				'body.elementor-editor-active {{WRAPPER}}.button-hide' => 'min-height:40px;',
				'body.logged-in {{WRAPPER}}.button-hide .jvbpd-button__container' => 'display:none;',
			),
		) );

		$this->add_group_control( \jvbpd_group_button_style::get_type(), Array(
			'name' => 'mypage',
			'label' => esc_html__( "Login button", 'jvfrmtd' ),
			'condition' => Array( 'hide_button!' => 'hide' ),
			'fields' => Array( 'button_txt', 'button_txt_hover' ),
			'params' => Array(),
		) );

		$this->end_controls_section();
	}

	protected function linkAttributes( $args=Array() ) {
		if( is_user_logged_in() ) {
			$args[ 'href' ] = wp_logout_url();
			if( function_exists( 'bp_core_get_user_domain' ) ) {
				$args[ 'href' ] = bp_core_get_user_domain( get_current_user_id() );
			}
		}else{
			if ('modal' == $this->get_settings('login_type')) {
				$args[ 'data-toggle' ] = 'modal';
				if( 'signup' == $this->get_settings( 'button_type' ) ) {
					$args[ 'data-target' ] = '#register_panel';
				}else{
					$args[ 'data-target' ] = '#login_panel';
				}
			}else{
				$args['href'] = wp_login_url();
			}
		}

		$output = '';
		foreach( $args as $key => $value ) {
			$output .= sprintf( '%1$s="%2$s"', $key, esc_attr( $value ) );
		}
		echo $output;
	}

	protected function render() {
		$this->button_render();
		// add_action( 'wp_footer', Array( $this, 'modal_render' ));
	}

	public function modal_render() {
		$this->render_login_modal();
		$this->render_join_modal();
	}

	public function render_login_modal() {
		?>
		<div class="modal fade login-type2 hello" id="login_panel" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog"><div class="modal-content no-padding"></div></div>
		</div>
		<?php
	}

	public function render_join_modal() {
		?>
		<div class="modal fade" id="register_panel" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog"><div class="modal-content no-padding"></div></div>
		</div>
		<?php
	}

}

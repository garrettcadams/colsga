<?php
namespace jvbpdelement\Modules\Userforms\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use jvbpdelement\Base\Base_Widget;
use jvbpdelement\Plugin;

abstract class Base extends Base_Widget {

	Const LOGIN = 'jvbpd_login';
	Const SIGNUP = 'jvbpd_signup';

	public $LOGIN = Array( self::LOGIN );
	public $SIGNUP = Array( self::SIGNUP );

	public function get_categories() { return [ 'jvbpd-page-builder-login' ]; }

	protected function _register_controls() {
		$this->start_controls_section(
			'section_fields_content',
			[
				'label' => __( 'Form Fields', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'show_labels',
			[
				'label' => __( 'Hide Label', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'yes' => __( 'Yes', 'jvfrmtd' ),
				'no' => __( 'No', 'jvfrmtd' ),
				'return' => 'no',
				'selectors' => [
					'{{WRAPPER}} .form-group label' => 'display:none;',
				],
			]
		);

		$this->add_control(
			'input_size',
			[
				'label' => __( 'Input Size', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'xs' => __( 'Extra Small', 'jvfrmtd' ),
					'sm' => __( 'Small', 'jvfrmtd' ),
					'md' => __( 'Medium', 'jvfrmtd' ),
					'lg' => __( 'Large', 'jvfrmtd' ),
					'xl' => __( 'Extra Large', 'jvfrmtd' ),
				],
				'default' => 'sm',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_button_content',
			[
				'label' => __( 'Button', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'button_text',
			[
				'label' => __( 'Text', 'jvfrmtd' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Log In', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'button_size',
			[
				'label' => __( 'Size', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'xs' => __( 'Extra Small', 'jvfrmtd' ),
					'sm' => __( 'Small', 'jvfrmtd' ),
					'md' => __( 'Medium', 'jvfrmtd' ),
					'lg' => __( 'Large', 'jvfrmtd' ),
					'xl' => __( 'Extra Large', 'jvfrmtd' ),
				],
				'default' => 'sm',
			]
		);


		$this->add_control(
			'button_block',
			[
				'label' => __( 'Button Full width', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'Yes', 'jvfrmtd' ),
				'label_off' => __( 'No', 'jvfrmtd' ),
				'return_value' => 'yes',
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'jvfrmtd' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'jvfrmtd' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'jvfrmtd' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'jvfrmtd' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .btn-wrap' => 'text-align:{{value}};',
				],
				'condition' => [
					'button_block!' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_login_content',
			[
				'label' => __( 'Additional Options', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'redirect_after_login',
			[
				'label' => __( 'Redirect After Login', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_off' => __( 'Off', 'jvfrmtd' ),
				'label_on' => __( 'On', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'redirect_url',
			[
				'type' => Controls_Manager::SELECT,
				'show_label' => false,
				'default' => '',
				'options' => Array(
					'' => esc_html__( 'Current Page( Default )', 'jvfrmtd' ),
					'profile' => esc_html__( 'Profile Page', 'jvfrmtd' ),
					'home' => esc_html__( 'Main Page', 'jvfrmtd' ),
					'admin' => esc_html__( 'WordPress Profile Page', 'jvfrmtd' ),
				),
				'separator' => false,
				'description' => __( 'Note: Because of security reasons, you can ONLY use your current domain here.', 'jvfrmtd' ),
				'condition' => [
					'redirect_after_login' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_lost_password',
			[
				'label' => __( 'Lost your password?', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'Hide', 'jvfrmtd' ),
				'label_on' => __( 'Show', 'jvfrmtd' ),
			]
		);

		if ( get_option( 'users_can_register' ) ) {
			$this->add_control(
				'show_register',
				[
					'label' => __( 'Register', 'jvfrmtd' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'label_off' => __( 'Hide', 'jvfrmtd' ),
					'label_on' => __( 'Show', 'jvfrmtd' ),
				]
			);
		}

		$this->add_control(
			'show_remember_me',
			[
				'label' => __( 'Remember Me', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'Hide', 'jvfrmtd' ),
				'label_on' => __( 'Show', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'show_logged_in_message',
			[
				'label' => __( 'Logged in Message', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'Hide', 'jvfrmtd' ),
				'label_on' => __( 'Show', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'custom_labels',
			[
				'label' => __( 'Custom Label', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [
					'show_labels' => 'yes',
				],
			]
		);

		$this->add_control(
			'user_label',
			[
				'label' => __( 'Username Label', 'jvfrmtd' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( ' Username or Email Address', 'jvfrmtd' ),
				'condition' => [
					'show_labels' => 'yes',
					'custom_labels' => 'yes',
				],
			]
		);

		$this->add_control(
			'user_placeholder',
			[
				'label' => __( 'Username Placeholder', 'jvfrmtd' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( ' Username or Email Address', 'jvfrmtd' ),
				'condition' => [
					'show_labels' => 'yes',
					'custom_labels' => 'yes',
				],
			]
		);

		$this->add_control(
			'password_label',
			[
				'label' => __( 'Password Label', 'jvfrmtd' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Password', 'jvfrmtd' ),
				'condition' => [
					'show_labels' => 'yes',
					'custom_labels' => 'yes',
				],
			]
		);

		$this->add_control(
			'password_placeholder',
			[
				'label' => __( 'Password Placeholder', 'jvfrmtd' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Password', 'jvfrmtd' ),
				'condition' => [
					'show_labels' => 'yes',
					'custom_labels' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Form', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'row_gap',
			[
				'label' => __( 'Rows Gap', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => '15',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'selectors' => [
					'{{WRAPPER}} form.jvbpd-login-form.jv-modal-login .lava_login_wrap .form-group' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'links_color',
			[
				'label' => __( 'Links Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#cccccc',
				'selectors' => [
					'{{WRAPPER}} form.jvbpd-login-form.jv-modal-login a' => 'color: {{VALUE}};',
				],
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
			]
		);

		$this->add_control(
			'links_hover_color',
			[
				'label' => __( 'Links Hover Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#454545',
				'selectors' => [
					'{{WRAPPER}} form.jvbpd-login-form.jv-modal-login a:hover' => 'color: {{VALUE}};',
				],
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_4,
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_labels',
			[
				'label' => __( 'Label', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_labels!' => 'yes',
				],
			]
		);

		$this->add_control(
			'label_spacing',
			[
				'label' => __( 'Spacing', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 5,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
				],
            ],
            'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} form.jvbpd-login-form.jv-modal-login .lava_login_wrap .form-group label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					// for the label position = above option
				],
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.jvbpd-login-form.jv-modal-login .lava_login_wrap .form-group label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} form.jvbpd-login-form.jv-modal-login .lava_login_wrap .form-group label',
				'scheme' => Scheme_Typography::TYPOGRAPHY_3,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_field_style',
			[
				'label' => __( 'Fields', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'field_text_color',
			[
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'defualt' => '#000000',
				'selectors' => [
					'{{WRAPPER}} form.jvbpd-login-form.jv-modal-login .lava_login_wrap .form-group input' => 'color: {{VALUE}};',
				],
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'field_typography',
				'selector' => '{{WRAPPER}} form.jvbpd-login-form.jv-modal-login .lava_login_wrap .form-group input',
				'scheme' => Scheme_Typography::TYPOGRAPHY_3,
			]
		);

		$this->add_control(
			'field_background_color',
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} form.jvbpd-login-form.jv-modal-login .lava_login_wrap .form-group input' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'field_border',
				'selector' => '{{WRAPPER}} form.jvbpd-login-form.jv-modal-login .lava_login_wrap .form-group input',
			]
		);

		$this->add_control(
			'field_border_radius',
			[
				'label' => __( 'Border Radius', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'	   => [
					'top' => 0,
					'right' => 0,
					'bottom' => 0,
					'left' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} form.jvbpd-login-form.jv-modal-login .lava_login_wrap .form-group input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_button_style',
			[
				'label' => __( 'Button', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => __( 'Normal', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#454545',
				'selectors' => [
					'{{WRAPPER}} form.jvbpd-login-form.jv-modal-login .lava_login_wrap .form-group .submit-wrap .button-wrap button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} form.jvbpd-login-form.jv-modal-login .lava_login_wrap .form-group .submit-wrap .button-wrap button',
			]
		);

		$this->add_control(
			'button_background_color',
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#cccccc',
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_4,
				],
				'selectors' => [
					'{{WRAPPER}} form.jvbpd-login-form.jv-modal-login .lava_login_wrap .form-group .submit-wrap .button-wrap button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(), [
				'name' => 'button_border',
				'selector' => '{{WRAPPER}} form.jvbpd-login-form.jv-modal-login .lava_login_wrap .form-group .submit-wrap .button-wrap button',
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label' => __( 'Border Radius', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} form.jvbpd-login-form.jv-modal-login .lava_login_wrap .form-group .submit-wrap .button-wrap button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_text_padding',
			[
				'label' => __( 'Button Padding', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} form.jvbpd-login-form.jv-modal-login .lava_login_wrap .form-group .submit-wrap .button-wrap button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => __( 'Hover', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.jvbpd-login-form.jv-modal-login .lava_login_wrap .form-group .submit-wrap .button-wrap button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_hover_color',
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} form.jvbpd-login-form.jv-modal-login .lava_login_wrap .form-group .submit-wrap .button-wrap button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(), [
				'name' => 'button_hover_border',
				'selector' => '{{WRAPPER}} form.jvbpd-login-form.jv-modal-login .lava_login_wrap .form-group .submit-wrap .button-wrap button:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function form_fields_render_attributes() {
		$settings = $this->get_settings();

		if ( ! empty( $settings['button_size'] ) ) {
			$this->add_render_attribute( 'button', 'class', 'elementor-size-' . $settings['button_size'] );
		}

		if ( $settings['button_hover_animation'] ) {
			$this->add_render_attribute( 'button', 'class', 'elementor-animation-' . $settings['button_hover_animation'] );
		}

		$this->add_render_attribute(
			[
				'wrapper' => [
					'class' => [
						'elementor-form-fields-wrapper',
					],
				],
				'field-group' => [
					'class' => [
						'elementor-field-type-text',
						'elementor-field-group',
						'elementor-column',
						'elementor-col-100',
					],
				],
				'submit-group' => [
					'class' => [
						'elementor-field-group',
						'elementor-column',
						'elementor-field-type-submit',
						'elementor-col-100',
					],
				],

				'button' => [
					'class' => [
						'elementor-button',
					],
					'name' => 'wp-submit',
				],
				'user_label' => [
					'for' => 'user',
				],
				'user_input' => [
					'type' => 'text',
					'name' => 'log',
					'id' => 'user',
					'placeholder' => $settings['user_placeholder'],
					'class' => [
						'elementor-field',
						'elementor-field-textual',
						'elementor-size-' . $settings['input_size'],
					],
				],
				'password_input' => [
					'type' => 'password',
					'name' => 'pwd',
					'id' => 'password',
					'placeholder' => $settings['password_placeholder'],
					'class' => [
						'elementor-field',
						'elementor-field-textual',
						'elementor-size-' . $settings['input_size'],
					],
				],
				//TODO: add unique ID
				'label_user' => [
					'for' => 'user',
					'class' => 'elementor-field-label',
				],

				'label_password' => [
					'for' => 'password',
					'class' => 'elementor-field-label',
				],
			]
		);

		if ( ! $settings['show_labels'] ) {
			$this->add_render_attribute( 'label', 'class', 'elementor-screen-only' );
		}

		$this->add_render_attribute( 'field-group', 'class', 'elementor-field-required' )
			->add_render_attribute( 'input', 'required', true )
			->add_render_attribute( 'input', 'aria-required', 'true' );
	}
	//

	protected function render() {
	$settings = $this->get_settings();

	}
}
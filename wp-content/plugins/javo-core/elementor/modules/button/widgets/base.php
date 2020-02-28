<?php
namespace jvbpdelement\Modules\Button\Widgets;

use jvbpdelement\Base\Base_Widget;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

abstract class Base extends Base_Widget {

	Const FAVORITE_BUTTON = 'jvbpd-favorites-button';
	Const LOGIN_SIGNUP_BUTTON = 'jvbpd-login-signup-modal';
	Const ADDFORM_SUBMIT_BUTTON = 'jvbpd-add-submit-button';

	protected function _register_controls() {
		$this->start_controls_section( 'section_general', array(
			'label' => esc_html__( 'General', 'jvfrmtd' ),
		) );

		jvbpd_elements_tools()->add_button_control( $this );
		$this->end_controls_section();
	}

	public function get_categories() { return [ 'jvbpd-single-listing' ]; }
	protected function __html( $setting = null, $format = '%s' ) {
		return call_user_func( array( $this, '__render_html' ), $setting, $format );
	}

	protected function __render_html( $setting = null, $format = '%s' ) {

		if ( is_array( $setting ) ) {
			$key     = $setting[1];
			$setting = $setting[0];
		}

		$val = $this->get_settings( $setting );

		if ( ! is_array( $val ) && '0' === $val ) {
			printf( $format, $val );
		}

		if ( is_array( $val ) && empty( $val[ $key ] ) ) {
			return '';
		}

		if ( ! is_array( $val ) && empty( $val ) ) {
			return '';
		}

		if ( is_array( $val ) ) {
			return sprintf( $format, $val[ $key ] );
		} else {
			return sprintf( $format, $val );
		}
	}

	protected function button_normal_render( $use_icon ) {
		$labelKey = 'txt_icon_button_label_normal';
		$iconKey = 'txt_icon_button_icon_normal';
		if( self::LOGIN_SIGNUP_BUTTON == $this->get_name() && is_user_logged_in() ) {
			$labelKey = 'mypage_button_label_normal';
			$iconKey = 'mypage_button_icon_normal';
		} ?>
		<div class="jvbpd-button_inner jvbpd-button_inner-normal">
			<?php
			if ( filter_var( $use_icon, FILTER_VALIDATE_BOOLEAN ) ) {
				echo $this->__html( $iconKey, '<span class="jvbpd-button_icon"><i class="%s"></i></span>' );
			}
			echo $this->__html( $labelKey, '<span class="jvbpd-button_txt">%s</span>' ); ?>
		</div>
		<?php
	}

	protected function button_hover_render( $use_icon ) {
		$labelKey = 'txt_icon_button_label_hover';
		$iconKey = 'txt_icon_button_icon_hover';
		if( self::LOGIN_SIGNUP_BUTTON == $this->get_name() && is_user_logged_in() ) {
			$labelKey = 'mypage_button_label_hover';
			$iconKey = 'mypage_button_icon_hover';
		} ?>
		<div class="jvbpd-button_inner jvbpd-button_inner-hover">
			<?php
			if ( filter_var( $use_icon, FILTER_VALIDATE_BOOLEAN ) ) {
				echo $this->__html( $iconKey, '<span class="jvbpd-button_icon"><i class="%s"></i></span>' );
			}
			echo $this->__html( $labelKey, '<span class="jvbpd-button_txt">%s</span>' ); ?>
		</div>
		<?php
	}

	protected function button_render() {
		$settings = $this->get_settings();

		$classes_list = array( 'jvbpd-advanced-button' );

		$button_url_data = $this->get_settings( 'button_url' );
		$button_url = $button_url_data['url'];
		$button_is_external = $button_url_data['is_external'];
		$button_nofollow = $button_url_data['nofollow'];

		$position = $this->get_settings( 'icon_normal_style_btn_icon_arrange' );
		$use_icon = $this->get_settings( 'icon_normal_style_use_btn_icon' );
		$hover_effect = $this->get_settings( 'settings_hover_effect' );

		$classes_list[] = 'jvbpd-advanced-button--icon-' . $position;
		$classes_list[] = 'hover-' . $hover_effect;

		$classes = implode( ' ', $classes_list );
		?>
		<div class="jvbpd-button__container">
			<a <?php $this->linkAttributes( Array( 'class' => $classes ) ); ?>>
				<div class="jvbpd-button_wapper jvbpd-button_wrapper-normal"></div>
				<div class="jvbpd-button_wapper jvbpd-button_wrapper-hover"></div>
				<?php $this->button_normal_render( $use_icon ); ?>
				<?php $this->button_hover_render( $use_icon ); ?>
			</a>
		</div>
		<?php
    }

}
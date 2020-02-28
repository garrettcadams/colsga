<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) exit;

class jvbpd_favorites_button extends Widget_Base {

	public function get_name() { return 'jvbpd-favorites-button'; }

	public function get_title() { return 'Favorites Button'; }

	public function get_icon() { return 'eicon-button'; }

	public function get_categories() { return [ 'jvbpd-single-listing' ]; }

	protected function _register_controls() {

		$this->start_controls_section( 'section_general', array(
			'label' => esc_html__( 'General', 'jvfrmtd' ),
		) );

		jvbpd_elements_tools()->add_button_control( $this );

		$this->end_controls_section();
	}

	public function getFavoritesLink() {
		$user_domain = Array();
		if( function_exists( 'buddypress' ) ) {

			$user_domain[] = untrailingslashit( buddypress()->loggedin_user->domain );
			$user_domain[] = 'favorites';
		}
		return join( '/', $user_domain );
	}

	public function __html( $setting = null, $format = '%s' ) {
		call_user_func( array( $this, '__render_html' ), $setting, $format );
	}

	public function __render_html( $setting = null, $format = '%s' ) {

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
			printf( $format, $val[ $key ] );
		} else {
			printf( $format, $val );
		}
	}

	public function linkAttributes( $args=Array() ) {

		if( is_user_logged_in() ) {
			$args[ 'href' ] = $this->getFavoritesLink();
		}else{
			$args[ 'data-toggle' ] = 'modal';
			$args[ 'data-target' ] = '#login_panel';
		}

		$output = '';
		foreach( $args as $key => $value ) {
			$output .= sprintf( '%1$s="%2$s"', $key, esc_attr( $value ) );
		}
		echo $output;
	}

	protected function render() {
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
				<div class="jvbpd-button_inner jvbpd-button_inner-normal">
					<?php
						if ( filter_var( $use_icon, FILTER_VALIDATE_BOOLEAN ) ) {
							echo $this->__html( 'txt_icon_button_icon_normal', '<span class="jvbpd-button_icon"><i class="%s"></i></span>' );
						}
						echo $this->__html( 'txt_icon_button_label_normal', '<span class="jvbpd-button_txt">%s</span>' );
					?>
				</div>
				<div class="jvbpd-button_inner jvbpd-button_inner-hover">
					<?php
						if ( filter_var( $use_icon, FILTER_VALIDATE_BOOLEAN ) ) {
							echo $this->__html( 'txt_icon_button_icon_hover', '<span class="jvbpd-button_icon"><i class="%s"></i></span>' );
						}
						echo $this->__html( 'txt_icon_button_label_hover', '<span class="jvbpd-button_txt">%s</span>' );
					?>
				</div>
			</a>
		</div>
		<?php
    }
}
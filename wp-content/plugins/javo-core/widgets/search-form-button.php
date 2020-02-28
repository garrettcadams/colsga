<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) exit;

class jvbpd_search_form_button extends Widget_Base {

	public function get_name() {
		return 'jvbpd-search-form-button';
	}

	public function get_title() {
		return 'Search Form Button ( Listing )';
	}

	public function get_icon() {
		return 'eicon-button';
	}

	public function get_categories() {
		return [ 'jvbpd-page-builder-search' ];
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_general',
			array(
				'label' => esc_html__( 'Note', 'jvfrmtd' ),
			)
		);

		$this->add_control( 'Des', Array(
			'type' => Controls_Manager::RAW_HTML,
			'raw'  => sprintf(
				'<div class="elementor-jv-notice" style="background-color:#9b0a46; color:#ffc6c6; padding:10px;"><ul>'.
				'<li class="doc-link">'.
				esc_html__('How to use this widget.','jvfrmtd').
				'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-search-form/" style="color:#fff;"> ' .
				esc_html__( 'Documentation', 'jvfrmtd' ) .
				'</a></li></ul></div>'
			)
		) );

		jvbpd_elements_tools()->add_button_control( $this );

		$this->end_controls_section();

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

	protected function render() {
		$settings = $this->get_settings();

		$classes_list = array( 'jvbpd-advanced-button', 'jvbpd-search-submit' );

		$position = $this->get_settings( 'icon_normal_style_btn_icon_arrange' );
		$use_icon = $this->get_settings( 'icon_normal_style_use_btn_icon' );
		$hover_effect = $this->get_settings( 'settings_hover_effect' );

		$classes_list[] = 'jvbpd-advanced-button--icon-' . $position;
		$classes_list[] = 'hover-' . $hover_effect;

		$classes = implode( ' ', $classes_list );
		?>
		<div class="jvbpd-button__container">
			<a class="<?php echo $classes; ?>">
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

<?php
/**
Widget Name: ACF Form
Author: Javo
Version: 1.0.0.1
*/

namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


class jvbpd_acf_form extends Widget_Base {

	public function get_name() {
		return 'jvbpd-acf-form';
	}

	public function get_title() {
		return 'ACF Form';   // title to show on elementor
	}

	public function get_icon() {
		return 'fa fa-user-o';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-core-add-form' ];    // category of the widget
	}

    protected function _register_controls() {
		$this->start_controls_section( 'section_header', Array(
			'label' => __('Team', 'jvfrmtd'),
		) );

		jvbpd_elements_tools()->getACFOptions( $this );
		$this->end_controls_section();
	}

    protected function render() {

		$settings = $this->get_settings();
		$isPreviewMode = is_admin();
		/*
		if( $isPreviewMode) {
			$previewBaseURL = jvbpdCore()->assets_url . '/images/elementor/listipia/';
			$previewURL = $previewBaseURL . 'single-button.jpg';
			printf( '<img src="%s">', esc_url_raw( $previewURL ) );
		}else{ */
			$this->getContent( $settings, get_post() );
			/*
		} */
    }

	public function renderField( $field ) {
		$format = '';
		$edit = isset( $GLOBALS[ 'edit' ] ) ? $GLOBALS[ 'edit' ]->ID : 0;
		if( function_exists( 'acf_render_field' ) ) {
			acf_render_field(array(
				'type'		=> $field[ 'type' ],
				'prefix'	=> 'lava_additem_meta',
				'name'		=> $field[ 'name' ],
				'value'		=> get_post_meta( $edit, $field[ 'name' ], true ),
				'choices'	=> Array( 'aaa' => 'aaaa' ),
				'class'		=> '',
				'disabled'	=> false,
			));
		};
	}

	public function getContent( $settings, $obj ) {

		$acfGroup = $settings[ 'acf_group' ];
		$acfFieldID = isset( $settings[ 'acf_field_' . $acfGroup ] ) ? $settings[ 'acf_field_' . $acfGroup ] : false;
		$acfField = get_field_object( $acfFieldID );

		the_field( $acfFieldID );

		/**
		?>
		<div class="form-inner">
			<input type="hidden" id="_acf_<?php echo esc_attr($k); ?>" name="_acf_<?php echo esc_attr($k); ?>" value="<?php echo esc_attr($v); ?>" />
			<label class="field-title"><?php echo $acfField[ 'label' ]; ?> </label>
			<?php $this->renderField( $acfField ); ?>
		</div>
		<?php **/
	}
}
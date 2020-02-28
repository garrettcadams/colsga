<?php
namespace jvbpdelement\Modules\Slider\Widgets;

use jvbpdelement\Base\Base_Widget;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;


abstract class Base extends Base_Widget {

	Const PAGE_SLIDER = 'jv-page-slider';
	Const SINGLE_LISTING_SLIDER = 'jv-single-listing-slider';

	public function get_categories() { return [ 'jvbpd-elements' ]; }

	protected function _register_controls() {
		$this->add_slick_setting_controls();

		if( self::PAGE_SLIDER == $this->get_name() ) {
			$this->add_slide_controls();
		}
	}

	protected function add_slick_setting_controls() {

		$this->start_controls_section( 'section_slider_option', Array(
			'label' => esc_html__( "Slider Option", 'jvfrmtd' ),
		) );

			$this->add_control( 'heading_general', Array(
				'label' => esc_html__( "General", 'jvfrmtd' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			) );

			$this->add_control( 'infinite', Array(
				'label' => esc_html__( "Infinite loop", 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'none',
			) );

			$this->add_control( 'adaptiveHeight', Array(
				'label' => esc_html__( "Adapts slider height", 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'none',
			) );

			$this->add_control( 'heading_autoplay', Array(
				'label' => esc_html__( "Autoplay", 'jvfrmtd' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			) );

			$this->add_control( 'autoplay', Array(
				'label' => esc_html__( "Use Autoplay", 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'none',
			) );

			$this->add_control( 'autoplaySpeed', Array(
				'label' => esc_html__( "Autoplay Speed", 'jvfrmtd' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 5000,
				'condition' => Array(
					'autoplay' => 'yes',
				),
				'separator' => 'none',
			) );

			$this->add_control( 'heading_navi', Array(
				'label' => esc_html__( "Navigation", 'jvfrmtd' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			) );

			$this->add_control( 'dots', Array(
				'label' => esc_html__( "Dots", 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'none',
			) );

			$this->add_control( 'arrows', Array(
				'label' => esc_html__( "Arrows", 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'none',
			) );

			$this->add_control( 'heading_effect', Array(
				'label' => esc_html__( "Effects", 'jvfrmtd' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			) );

			$this->add_control( 'fade', Array(
				'label' => esc_html__( "Use fade", 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'none',
			) );

			$this->add_control( 'lazyLoad', Array(
				'label' => esc_html__( "Lazyload", 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'ondemand',
				'options' => Array(
					'ondemand' => esc_html__( "Ondemand", 'jvfrmtd' ),
					'progressive' => esc_html__( "Progressive", 'jvfrmtd' ),
				),
				'separator' => 'none',
			) );

		$this->end_controls_section();
	}

	public function add_slide_controls() {
		$this->start_controls_section( 'section_slide_option', Array(
			'label' => esc_html__( "Slide Option", 'jvfrmtd' ),
		) );

		$repeater = new Repeater();

		$repeater->add_control( 'image', Array(
			'type' =>  Controls_Manager::MEDIA,
		) );

		$this->add_control( 'slides', Array(
			'type' => Controls_Manager::REPEATER,
			'fields' => array_values( $repeater->get_controls() ),
		) );

		$this->end_controls_section();
	}

	protected function getSlides() {
		$sliders = Array();
		if( self::PAGE_SLIDER == $this->get_name() ) {
			$_sliders = $this->get_settings( 'slides' );
			foreach( $_sliders as $element ) {
				$sliders[] = $element[ 'image' ][ 'id' ];
			}
		}
		return $sliders;
	}

	protected function getSlidesContext() {
		$slides = $this->getSlides();
		foreach( $slides as $image ) {
			printf( '<img src="%s" height="450">', wp_get_attachment_image_url( $image ) );
		}
	}

	protected function render() {

		$sliderSettings = Array();
		foreach( Array( 'adaptiveHeight', 'autoplay', 'autoplaySpeed', 'arrows', 'dots', 'fade', 'lazyLoad', 'infinite' ) as $options ) {
			$sliderSettings[ $options ] = $this->get_settings( $options );
		}

		$this->add_render_attribute( 'container', Array(
			'class' => 'jvbpd-slider-wrap',
			'data-settings' => wp_json_encode( $sliderSettings ),
		) );

		?>
		<div <?php echo $this->get_render_attribute_string( 'container' ); ?>>
			<?php $this->getSlidesContext(); ?>
		</div>
		<?php
	}
}
<?php
namespace jvbpdelement\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class jvbpd_animation_headline extends Widget_Base {

	public function get_name() {
		return 'animation-headline';
	}

	public function get_title() {
		return __( 'Animation Headline', 'jvfrmtd' );
	}

	public function get_icon() {
		return 'eicon-animated-headline';
	}

	public function get_categories() {
        return [ 'jvbpd-elements' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'text_elements',
			[
				'label' => __( 'Headline', 'jvfrmtd' ),
			]
		);

		$this->add_control(
			'headline_style',
			[
				'label' => __( 'Style', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'highlight',
				'options' => [
					'highlight' => __( 'Highlighted', 'jvfrmtd' ),
					'rotate' => __( 'Rotating', 'jvfrmtd' ),
				],
				'prefix_class' => 'elementor-headline--style-',
				'render_type' => 'template',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'animation_type',
			[
				'label' => __( 'Animation', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'slidingVertical' => 'slidingVertical',
					'slidingHorizontal' => 'slidingHorizontal',
					'fadeIn' => 'fadeIn',
					'verticalFlip' => 'verticalFlip',
					'horizontalFlip' => 'horizontalFlip',
					'antiClock' => 'antiClock',
					'clockWise' => 'clockWise',
					'popEffect' => 'popEffect',
					'pushEffect' => 'pushEffect',
				],
				'default' => 'typing',
				'condition' => [
					'headline_style' => 'rotate',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'marker',
			[
				'label' => __( 'Shape', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'circle',
				'options' => [
					'circle' => _x( 'Circle', 'Shapes', 'jvfrmtd' ),
					'curly' => _x( 'Curly', 'Shapes', 'jvfrmtd' ),
					'underline' => _x( 'Underline', 'Shapes', 'jvfrmtd' ),
					'double' => _x( 'Double', 'Shapes', 'jvfrmtd' ),
					'double_underline' => _x( 'Double Underline', 'Shapes', 'jvfrmtd' ),
					'underline_zigzag' => _x( 'Underline Zigzag', 'Shapes', 'jvfrmtd' ),
					'diagonal' => _x( 'Diagonal', 'Shapes', 'jvfrmtd' ),
					'strikethrough' => _x( 'Strikethrough', 'Shapes', 'jvfrmtd' ),
					'x' => 'X',
				],
				'render_type' => 'template',
				'condition' => [
					'headline_style' => 'highlight',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'before_text',
			[
				'label' => __( 'Before Text', 'jvfrmtd' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'This page is', 'jvfrmtd' ),
				'placeholder' => __( 'Your Headline', 'jvfrmtd' ),
				'label_block' => true,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'highlighted_text',
			[
				'label' => __( 'Highlighted Text', 'jvfrmtd' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Amazing', 'jvfrmtd' ),
				'label_block' => true,
				'condition' => [
					'headline_style' => 'highlight',
				],
				'separator' => 'none',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'rotating_text',
			[
				'label' => __( 'Rotating Text', 'jvfrmtd' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => __( 'Enter each word in a separate line', 'jvfrmtd' ),
				'separator' => 'none',
				'default' => "Better\nBigger\nFaster",
				'rows' => 5,
				'condition' => [
					'headline_style' => 'rotate',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'after_text',
			[
				'label' => __( 'After Text', 'jvfrmtd' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Your Headline', 'jvfrmtd' ),
				'label_block' => true,
				'separator' => 'none',
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label' => __( 'Alignment', 'jvfrmtd' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
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
				'default' => 'center',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .elementor-headline' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'tag',
			[
				'label' => __( 'HTML Tag', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h3',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_marker',
			[
				'label' => __( 'Shape', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'headline_style' => 'highlight',
				],
			]
		);

		$this->add_control(
			'marker_color',
			[
				'label' => __( 'Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_4,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-headline-dynamic-wrapper path' => 'stroke: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'stroke_width',
			[
				'label' => __( 'Width', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-headline-dynamic-wrapper path' => 'stroke-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'above_content',
			[
				'label' => __( 'Bring to Front', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'selectors' => [
					"{{WRAPPER}} .elementor-headline-dynamic-wrapper svg" => 'z-index: 2',
					"{{WRAPPER}} .elementor-headline-dynamic-text" => 'z-index: auto',
				],
			]
		);

		$this->add_control(
			'rounded_edges',
			[
				'label' => __( 'Rounded Edges', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'selectors' => [
					"{{WRAPPER}} .elementor-headline-dynamic-wrapper path" => 'stroke-linecap: round; stroke-linejoin: round',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_text',
			[
				'label' => __( 'Headline', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				],
				'selectors' => [
					'{{WRAPPER}} .before-text' => 'color: {{VALUE}}',

				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .before-text',
			]
		);

		$this->add_control(
			'heading_words_style',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Animated Text', 'jvfrmtd' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'words_color',
			[
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				],
				'selectors' => [
					'{{WRAPPER}} .animation-text-wrap span' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'words_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .animation-text-wrap span',
				'exclude' => ['font_size'],
			]
		);


		$this->add_control(
			'after_text_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'After Text', 'jvfrmtd' ),
				'separator' => 'before',
			]
		);


		$this->add_control(
			'after_title_color',
			[
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				],
				'selectors' => [
					'{{WRAPPER}} .after-text' => 'color: {{VALUE}}',

				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'after_title_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .after-text',
			]
		);


		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();

		$tag = $settings['tag'];

		$this->add_render_attribute( 'headline', 'class', 'elementor-headline' );

		if ( 'rotate' === $settings['headline_style'] ) {
			$this->add_render_attribute( 'headline', 'class', 'elementor-headline-animation-type-' . $settings['animation_type'] );

			$is_letter_animation = in_array( $settings['animation_type'], [ 'typing', 'swirl', 'blinds', 'wave' ] );

			if ( $is_letter_animation ) {
				$this->add_render_attribute( 'headline', 'class', 'elementor-headline-letters' );
			}
		}

		?>


<<?php echo $tag; ?> class="sentence" >
		<?php if ( ! empty( $settings['before_text'] ) ) : ?>
			<span class="before-text"><?php echo $settings['before_text']; ?></span>
		<?php endif; ?>
    <div class="animation-text-wrap <?php echo $settings['animation_type']; ?>">
      <span>Handsome.</span>
      <span>Clean.</span>
      <span>Elegant.</span>
      <span>Magnificent.</span>
      <span>Adorable.</span>
	  <?php if ( ! empty( $settings['rotating_text'] ) ) : ?>
				<span class=""></span>
	 <?php endif; ?>
    </div>
	<?php if ( ! empty( $settings['after_text'] ) ) : ?>
		<span class="after-text"><?php echo $settings['after_text']; ?></span>
	<?php endif; ?>
</<?php echo $tag; ?>>

<?php
	}

}

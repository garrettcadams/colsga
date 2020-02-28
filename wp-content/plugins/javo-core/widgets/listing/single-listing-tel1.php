<?php
/**
Widget Name: Single Tel1 widget
Author: Javo
Version: 1.0.0.0
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


class jvbpd_single_tel1 extends Widget_Base {

	public function get_name() {
		return 'jvbpd-single-tel1';
	}

	public function get_title() {
		return 'Listing Tel1';   // title to show on elementor
	}

	public function get_icon() {
		return 'fa fa-user-o';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-single-listing' ];    // category of the widget
	}

    protected function _register_controls() {
      $this->start_controls_section(
          'section_title',
          [
              'label' => __('Listing Address', 'jvfrmtd'),
          ]
      );

      $this->add_control(
        'tel_pre_txt',
        [
           'label'       => __( 'Text (Pre)', 'jvfrmtd' ),
           'type'        => Controls_Manager::TEXT,
           'placeholder' => __( 'Type your title text here', 'jvfrmtd' ),
        ]
        );
      $this->end_controls_section();

      //Style

      $this->start_controls_section(
      'text_style',
          [
            'label' => __( 'Style', 'jvfrmtd' ),
            'tab'   => Controls_Manager::TAB_STYLE,
          ]
      );

      $this->add_control(
      'field_align',
      [
         'label'       => __( 'Align', 'jvfrmtd' ),
         'type' => Controls_Manager::SELECT,
         'default' => 'left',
         'options' => [
          'left'  => __( 'Left', 'jvfrmtd' ),
          'center' => __( 'Center', 'jvfrmtd' ),
          'right' => __( 'Right', 'jvfrmtd' ),
         ],
       'selectors' => [ // You can use the selected value in an auto-generated css rule.
          '{{WRAPPER}} .jv-listing-phone1' => 'text-align: {{VALUE}}',
       ],
      ] );

      $this->add_control(
      'label_color',
      [
          'label' => __( 'Title Color', 'jvfrmtd' ),
          'type' => Controls_Manager::COLOR,
          'scheme' => [
              'type' => Scheme_Color::get_type(),
              'value' => Scheme_Color::COLOR_1,
          ],
          'selectors' => [
              '{{WRAPPER}} .jv-listing-phone1' => 'color: {{VALUE}}',
          ],
      ]
      );

      $this->add_group_control( Group_Control_Typography::get_type(), [
        'name' => 'label_typography',
        'selector' => '{{WRAPPER}} .jv-listing-phone1',
        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
      ] );

      $this->end_controls_section();
    }

    protected function render() {

		$settings = $this->get_settings();
		wp_reset_postdata();
    if (is_admin()){
      echo '<div class="jv-listing-phone1">'. $settings['tel_pre_txt'] .'000-000-0000</div>';
		}else{
			$this->getContent( $settings, get_post() );
		}
    }

	public function getContent( $settings, $obj ) {
    $list_phone1 = get_post_meta(get_the_ID(), '_phone1', true);
		?>
    <div class="jv-listing-phone1"><?php echo $settings['tel_pre_txt']; ?><?php echo $list_phone1; ?></div>
		<?php
	}
}

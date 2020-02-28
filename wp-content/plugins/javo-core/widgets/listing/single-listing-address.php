<?php
/**
Widget Name: Single Address widget
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


class jvbpd_single_address extends Widget_Base {

	public function get_name() {
		return 'jvbpd-single-address';
	}

	public function get_title() {
		return 'Address';   // title to show on elementor
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
        'showmap_link_txt',
        [
           'label'       => __( 'Map Link Text', 'jvfrmtd' ),
           'type'        => Controls_Manager::TEXT,
           'default'     => __( 'Show Map', 'jvfrmtd' ),
           'placeholder' => __( 'Type your title text here', 'jvfrmtd' ),
        ]
        );
      
	  $this->add_control(
      'showmap_label_color',
      [
          'label' => __( 'Title Color', 'jvfrmtd' ),
          'type' => Controls_Manager::COLOR,
		  'default' => '#333',
          'scheme' => [
              'type' => Scheme_Color::get_type(),
              'value' => Scheme_Color::COLOR_1,
          ],
          'selectors' => [
              '{{WRAPPER}} .jv-listing-address .google-map a' => 'color: {{VALUE}}',
          ],
      ]
      );

      $this->add_group_control( Group_Control_Typography::get_type(), [
        'name' => 'showmap_label_typography',
        'selector' => '{{WRAPPER}} .jv-listing-address .google-map a',
        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
      ] );

		$this->add_control( 'showmap_label_space',Array(
		'label' => esc_html__( 'Label Space', 'jvfrmtd' ),
		'type' => Controls_Manager::SLIDER,
		'default' => [
			'size' => 10,
		],
		'range' => [
			'px' => [
				'min' => 0,
				'max' => 40,
			],
		'	%' => [
				'min' => 0,
				'max' => 40,
			],
		],
		'size_units' => [ 'px', '%' ],
		'selectors' => [
			'{{WRAPPER}} .jv-listing-address .google-map a' => 'margin-left: {{SIZE}}{{UNIT}};',
		],
	) );

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
          '{{WRAPPER}} .jv-listing-address' => 'text-align: {{VALUE}}',
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
              '{{WRAPPER}} .jv-listing-address' => 'color: {{VALUE}}',
          ],
      ]
      );

      $this->add_group_control( Group_Control_Typography::get_type(), [
        'name' => 'label_typography',
        'selector' => '{{WRAPPER}} .jv-listing-address',
        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
      ] );

      $this->end_controls_section();
    }

    protected function render() {

		$settings = $this->get_settings();
		wp_reset_postdata();
    if (is_admin()){
      echo '<div class="jv-listing-address"><span class="address">Sample Listing Address</span><span class="google-map"><a href="#">'. $settings['showmap_link_txt'] .'</a></span></div>';
		}else{
			$this->getContent( $settings, get_post() );
		}
    }

	public function getContent( $settings, $obj ) {
    $list_address = get_post_meta(get_the_ID(), '_address', true);
    $list_lat = get_post_meta(get_the_ID(), 'lv_listing_lat', true);
    $list_lng = get_post_meta(get_the_ID(), 'lv_listing_lng', true);
		?>
    <div class="jv-listing-address">
      <span class="address"><?php echo $list_address; ?></span>
      <span class="google-map"><a href="http://google.com/maps/?q=<?php echo $list_lat; ?>,<?php echo $list_lng; ?>" target="_blank">&nbsp;<?php echo $settings['showmap_link_txt']; ?></a></span>
    </div>
		<?php
	}
}

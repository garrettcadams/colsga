<?php
/**
Widget Name: Single FAQ widget
Author: Javo
Version: 1.0.0.0
*/


namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Scheme_Typography;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


class jvbpd_single_faq extends Widget_Base {

	public function get_name() {
		return 'jvbpd-team-members';
	}

	public function get_title() {
		return 'FAQ';   // title to show on elementor
	}

	public function get_icon() {
		return 'eicon-toggle';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-single-listing' ];    // category of the widget
	}

    protected function _register_controls() {

       $this->start_controls_section(
			'section_general',
			array(
				'label' => esc_html__( 'FAQ', 'jvfrmtd' ),
			)
		);

		$this->add_control(
		'Des',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => sprintf(
					'<div class="elementor-jv-notice" style="background-color:#9b0a46; color:#ffc6c6; padding:10px;"><ul>'.
					'<li class="doc-link">'.
					esc_html__('How to use this widget.','jvfrmtd').
					'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-single-listing-page/" style="color:#fff;"> ' .
					esc_html__( 'Documentation', 'jvfrmtd' ) .
					'</a></li><li>&nbsp;</li>'.
					'<li class="notice">'.
					esc_html__('This widget is for only single listing detail page.', 'jvfrmtd').
					'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-notice/" style="color:#fff;">' .
					esc_html__( 'Detail', 'jvfrmtd' ) .
					'</a><br/></li><li>&nbsp;</li><li>'.
					esc_html__( 'Please do not use in other pages.', 'jvfrmtd' ) .
					'</li></ul></div>'
				)
			)
		);
		$this->end_controls_section();


		//FAQ Style
        $this->start_controls_section(
            'faq_title_style',
            [
                'label' => __( 'FAQ Title Style','jvfrmtd'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'faq_title_color',
            [
                'label' => __( 'Text Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
				'default' => '#454545',
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .panel-title > a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'faq_titles_background_color',
            [
                'label' => __( 'Background Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
				'default' => '#fff',
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .panel-heading' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name' => 'faq_title_typography',
            'selector' => '{{WRAPPER}} .panel-title > a',
            'scheme' => Scheme_Typography::TYPOGRAPHY_1,
        ] );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'faq_title_border',
				'selector' => '{{WRAPPER}} .panel-default',
			]
		);

		$this->end_controls_section();

        $this->start_controls_section(
            'faq_icon_style',
            [
                'label' => __( 'Icon Style','jvfrmtd'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'faq_title_icon_color',
            [
                'label' => __( 'Icon Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
				'default' => '#454545',
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .panel-heading > i' => 'color: {{VALUE}}',
                ],
            ]
        );

		$this->add_control(
		  'faq_title_icon_size',
		  [
			  'label' => __( 'Icon Size', 'jvfrmtd' ),
			  'type' => Controls_Manager::SLIDER,
			  'default' => [
				  'size' => 15,
				  'unit' => 'px',
			  ],
			  'range' => [
				  'px' => [
					  'min' => 0,
					  'max' => 30,
				  ],
				  '%' => [
					  'min' => 0,
					  'max' => 100,
				  ],
			  ],
			  'size_units' => [ 'px', '%' ],
			  'selectors' => [
				  '{{WRAPPER}} .panel-heading > i' => 'font-size: {{SIZE}}{{UNIT}};',
			  ],
		  ]
		);

		$this->end_controls_section();

        $this->start_controls_section(
            'faq_content_style',
            [
                'label' => __( 'FAQ Content Style','jvfrmtd'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_control(
            'faq_content_backgrond_color',
            [
                'label' => __( 'Text Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
				'default' => '#333333',
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .lava_faq_content' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'faq_content_Background_color',
            [
                'label' => __( 'Background Color', 'jvfrmtd' ),
                'type' => Controls_Manager::COLOR,
				'default' => '#eee',
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .panel-body' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name' => 'faq_content_typography',
            'selector' => '{{WRAPPER}} .lava_faq_content',
            'scheme' => Scheme_Typography::TYPOGRAPHY_1,
        ] );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'faq_content_border',
				'selector' => '{{WRAPPER}} .panel-body',
			]
		);

		$this->end_controls_section();
    }

    protected function render() {

		jvbpd_elements_tools()->switch_preview_post();
		$settings = $this->get_settings();
		$isVisible = false;

		//wp_reset_postdata();
		$isPreviewMode = false;

		if( class_exists( 'lvjr_Faq' ) ) {
			$objFaq = new \lvjr_Faq( get_the_ID() );
			if( !empty( $objFaq->values ) ) {
				$isVisible = true;
			}
		}

		if( $isPreviewMode ) {
			$previewBaseURL = jvbpdCore()->assets_url . '/images/elementor/listipia/';
			$previewURL = $previewBaseURL . 'single-faq.jpg';
			printf( '<img src="%s">', esc_url_raw( $previewURL ) );
		}else{
			if( $isVisible ) {
				$this->getContent( $settings, $objFaq );
			}
		}
		jvbpd_elements_tools()->restore_preview_post();
    }

	public function getContent( $settings, $obj ) {
		?>
		<div class="detail-block faq">
			<!--<h3><?php esc_html_e( "FAQ", 'listopia' ); ?></h3>-->

			<div class="panel-group" id="lava_faq" role="tablist" aria-multiselectable="true">
				<?php
				foreach( (array) $obj->values as $intIndex => $arrFaQ ) {

					/*
					 * Bootstrap 4.0.0
					printf( '
						<div class="panel panel-default">
							<div class="panel-heading" role="tab">
								<i class="jvbpd-icon2-arrow-right"></i>
								<h4 class="panel-title">
								<a role="button" data-toggle="collapse" data-parent="#lava_faq" href="#%4$s" aria-expanded="true" aria-controls="%4$s">%1$s</a>
								</h4>
							</div>
							<div id="%4$s" class="panel-collapse collapse%3$s" role="tabpanel">
								<div class="panel-body"><div class="lava_faq_content">%2$s</div></div>
							</div>
						</div>',
						$arrFaQ[ 'frequently' ], $arrFaQ[ 'question' ],
						( $intIndex == 0 ? ' show' : '' ), 'lavaFaQ' . $intIndex
					); */

					/* Bootstrap 4.1.3 */
					// collapse trigger 'href' for iPhone
					printf( '
						<div class="panel panel-default">
							<div class="panel-heading" role="tab">
								<i class="jvbpd-icon2-arrow-right"></i>
								<h4 class="panel-title">
								<a role="button" data-toggle="collapse" href="#%4$s" data-target="#%4$s" aria-expanded="true" aria-controls="%4$s">%1$s</a>
								</h4>
							</div>
							<div id="%4$s" class="panel-collapse collapse%3$s" role="tabpanel" data-parent="#lava_faq">
								<div class="panel-body"><div class="lava_faq_content">%2$s</div></div>
							</div>
						</div>',
						$arrFaQ[ 'frequently' ], $arrFaQ[ 'question' ],
						( $intIndex == 0 ? ' show' : '' ), 'lavaFaQ' . $intIndex
					);
				}?>
			</div>
		</div><!-- detail-block contact -->
		<?php
	}
}
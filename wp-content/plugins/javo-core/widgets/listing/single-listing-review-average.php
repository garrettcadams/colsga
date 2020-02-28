<?php
/**
 * Widget Name: Single reviews average widget
 * Author: Javo
 * Version: 1.0.0.1
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


class jvbpd_single_review_average extends Widget_Base {

	public function get_name() {
		return 'jvbpd-single-review-average';
	}

	public function get_title() {
		return 'Review Average';   // title to show on elementor
	}

	public function get_icon() {
		return 'jvic-star-4';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-single-listing' ];    // category of the widget
	}

    protected function _register_controls() {

        $this->start_controls_section(
            'section_review_avg',
            [
                'label' => __('Review Average', 'jvfrmtd'),
            ]
        );

        $this->add_control(

            'avg_style', [
                'type' => Controls_Manager::SELECT,
                'label' => __('Choose Style', 'jvfrmtd'),
                'default' => 'style1',
                'options' => [
                    'style1' => __('5 Stars', 'jvfrmtd'),
                    'style2' => __('1 Heart', 'jvfrmtd'),
                ],
            ]
        );
        $this->end_controls_section();

		$this->start_controls_section(

			'review_defualt_settings',
			[
				'label' => __( 'Rating General', 'jvfrmtd' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control( 'icon_align', [
            'label' => __( 'Alignment', 'jvfrmtd' ),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'flex-start' => [
                    'title' => __( 'Left', 'jvfrmtd' ),
                    'icon' => 'fa fa-align-left',
                ],
                'center' => [
                    'title' => __( 'Center', 'jvfrmtd' ),
                    'icon' => 'fa fa-align-center',
                ],
                'flex-end' => [
                    'title' => __( 'Right', 'jvfrmtd' ),
                    'icon' => 'fa fa-align-right',
                ],
            ],
            'default' => 'center',
            'selectors' => [
                '{{WRAPPER}} .jv-rating-avg-wrap' => 'justify-content: {{VALUE}};',
            ],
        ] );

		$this->end_controls_section();

		$this->start_controls_section(
				'review_settings',
				[
					'label' => __( 'Rating Icon', 'jvfrmtd' ),
					'tab'   => Controls_Manager::TAB_STYLE,
				]
		);

			$this->add_control( 'show_icon', [
				'label' => __( 'Show Icon', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'label_on',
				'label_on' => __( 'Show', 'jvfrmtd' ),
				'label_off' => __( 'Hide', 'jvfrmtd' ),
				'return_value' => 'yes',				
			]);

			$this->add_control(

				'icon_color',
				[
					'label' => __( 'Icon Color', 'jvfrmtd' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#4c4c4c',
					'selectors' => [
						' {{WRAPPER}} .jv-listing-rating-ave' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(

				'rating_icon_spacing',
				[
					'label' => __( 'Rating Icon Spacing', 'jvfrmtd' ),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'size' => 10,
					],

					'range' => [
						'px' => [
							'min' => 0,
							'max' => 20,
						],
					],

					'size_units' => 'px',
					'selectors' => [
						'{{WRAPPER}} .jv-listing-rating-ave' => 'letter-spacing: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(

				'rating_icon_size',
				[

					'label' => __( 'Rating Icon Size', 'jvfrmtd' ),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'size' => 15,
					],

					'range' => [
						'px' => [
							'min' => 0,
							'max' => 30,
						],
					],

					'size_units' => 'px',
					'selectors' => [
						'{{WRAPPER}} .jv-listing-rating-ave' => 'font-size: {{SIZE}}{{UNIT}};',
					],
				]
			);

			
		$this->add_responsive_control(

			'rating_icon_margin',
			[
				'label'      => esc_html__( 'Rating Icon Margin', 'jvfrmtd' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'default'	   => [
					'right' => 7,
					'left' => 0,
					'isLinked' => false,
				],
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .jv-listing-rating-ave' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);



		$this->end_controls_section();

		$this->start_controls_section(

			'rating_ave_number',
			[
				'label' => __( 'Rating Avg Number', 'jvfrmtd' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control( 'show_avg_number', [
			'label' => __( 'Show Avg Number', 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => 'label_on',
			'label_on' => __( 'Show', 'jvfrmtd' ),
			'label_off' => __( 'Hide', 'jvfrmtd' ),
			'return_value' => 'yes',			
		]);

		$this->add_responsive_control(

			'rating_number_padding',
			[
				'label'      => esc_html__( 'Rating Number Padding', 'jvfrmtd' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'default'	   => [
					'right' => 7,
					'left' => 7,
					'isLinked' => false,
				],
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .jv-listing-rating-number-ave' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(

			'rating_number_margin',
			[
				'label'      => esc_html__( 'Rating Number Margin', 'jvfrmtd' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'default'	   => [
					'right' => 0,
					'left' => 7,
					'isLinked' => false,
				],
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .jv-listing-rating-number-ave' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(

			'rating_number_radius',
			[
				'label' => __( 'Rating Number Radius', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],

					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ '%', 'px' ],
				'selectors' => [
					'{{WRAPPER}} .jv-listing-rating-number-ave' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(

			'rating_number_color',
			[
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#4c4c4c',
				'selectors' => [
					' {{WRAPPER}} .jv-listing-rating-number-ave' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(

			'rating_number_background_color',
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#e6e6e6',
				'selectors' => [
					' {{WRAPPER}} .jv-listing-rating-number-ave' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(

			Group_Control_Typography::get_type(),
			[
				'name' => 'rating_number_typography',
				'selector' => '{{WRAPPER}} .jv-listing-rating-number-ave',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(

			'review_count',
			[
				'label' => __( 'Review Count', 'jvfrmtd' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control( 'show_review_count', [
			'label' => __( 'Show Review Count', 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => 'label_on',
			'label_on' => __( 'Show', 'jvfrmtd' ),
			'label_off' => __( 'Hide', 'jvfrmtd' ),
			'return_value' => 'yes',			
		]);

		$this->add_control(

			'review_count_color',
			[
				'label' => __( 'Text Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#333333',
				'selectors' => [
					' {{WRAPPER}} .jv-listing-review-count' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(

			Group_Control_Typography::get_type(),
			[
				'name' => 'review_count_typography',
				'selector' => '{{WRAPPER}} .jv-listing-review-count',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
			]
		);

		$this->add_responsive_control(

			'rating_count_margin',
			[
				'label'      => esc_html__( 'Rating Count Margin', 'jvfrmtd' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'default'	   => [
					'right' => 0,
					'left' => 7,
					'isLinked' => false,
				],
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .jv-listing-review-count' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

    }

    protected function render() {

		$settings = $this->get_settings();
		wp_reset_postdata();

		$this->getContent( $settings, get_post() );
    }

	public function getAvatar( $user=null ) {
		$output ='';
		if( function_exists( 'bp_core_fetch_avatar' ) ) {
			$output = bp_core_fetch_avatar( Array(
				'object' => 'user',
				'item_id' => $user->ID,
				'width' => 24,
				'height' => 24,
			) );
		}
		echo $output;
	}

	public function getContent( $settings, $obj ) {

		$avg_style = $settings['avg_style'];
		$list_rating_ave = get_post_meta(get_the_ID(), 'rating_average', true);
		$amount_rating = wp_count_comments( get_the_ID() );

		$show_icon = $settings['show_icon'];
		$show_avg_number = $settings['show_avg_number'];
		$show_review_count = $settings['show_review_count'];

		if(!function_exists('lv_directoryReview')){
			return;
		}

		if ($avg_style=='style1'){
			?>
			<div class="jv-rating-avg-wrap">
				<?php if ($show_icon=='yes'){?><div class="jv-listing-rating-ave"><?php echo lv_directoryReview()->core->fa_get();?></div><?php }?>
				<?php if ($show_avg_number=='yes'){?><div class="jv-listing-rating-number-ave"><?php echo $list_rating_ave; ?></div><?php }?>
				<?php if ($show_review_count=='yes'){?><div class="jv-listing-review-count"><?php echo $amount_rating->approved; ?> <?php _e('review(s)','jvfrmtd')?></div><?php }?>
			</div>
			<?php
		}else{

			$reviewers = (object) Array(
				'reviewers' => Array(),
				'reviewers_count' => 0,
			);

			if( function_exists( 'lv_directoryReview' ) && method_exists( lv_directoryReview()->core, 'getReviewers' ) ) {
				$reviewers = lv_directoryReview()->core->getReviewers( get_post() );
			} ?>

			<div class="review-avg-wrap style2">
				<div href="#" class="review-avg-amt">
					<i class="icon jvic-heart-4"></i>
					<span><?php echo $reviewers->reviewers_count; ?></span>
				</div>

				<ul class="reviewed-users-image">
					<?php
					foreach( $reviewers->reviewers as $reviewer_id ) {
						?>
						<li>
							<a><?php $this->getAvatar( new \WP_User( $reviewer_id ) ); ?></a>
						</li>
						<?php
					} ?>
				</ul>

				<div class="reviewed-users-names">
					<?php
					if( 1 == $reviewers->reviewers_count ) {
						printf( '<a>%1$s</a>', get_user_by( 'id', $reviewers->reviewers[0] )->display_name );
					}elseif( 1 < $reviewers->reviewers_count ){
						for( $reviewInt=0; $reviewInt < 2; $reviewInt++ ) {
							printf( '<a>%1$s</a>', get_user_by( 'id', $reviewers->reviewers[$reviewInt] )->display_name );
							if( $reviewInt == 0 ) { printf( ', ' ); }
						}
						if( $reviewers->reviewers_count > 2 ) {
							printf(
								' '.
								esc_html__( 'and', 'jvfrmtd' ) .'<br>' .
								esc_html__( "%s more users here", 'jvfrmtd' ), ( $reviewers->reviewers_count -2)
							);
						}
					} ?>
				</div>
			</div>

		<?php
		} // if
	}
}

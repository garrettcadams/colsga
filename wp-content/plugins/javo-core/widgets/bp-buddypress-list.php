<?php
namespace jvbpdelement\Widgets;

use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Scheme_Typography;

if ( ! defined( 'ABSPATH' ) ) exit;

class Jvbpd_Bp_BuddyPress_list extends Widget_Base {

    public $_render_type = '';
	public function get_name() { return 'jvbpd-bp-buddypress-list'; }
	public function get_title() { return 'Buddypress List'; }
	public function get_icon() { return 'eicon-button'; }
	public function get_categories() { return [ 'jvbpd-elements-bp' ]; }

	protected function _register_controls() {
		$this->start_controls_section( 'section_general', array(
			'label' => esc_html__( 'General', 'jvfrmtd' ),
        ) );
            $this->add_control( 'render_type', Array(
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__( "Type", 'jvfrmtd' ),
                'options' => Array(
                    'member' => esc_html__( "Member", 'jvfrmtd' ),
                    'group' => esc_html__( "Group", 'jvfrmtd' ),
                    'active' => esc_html__( "Active Members", 'jvfrmtd' ),
                ),
                'default' => 'member',
			));

			$this->add_control( 'render_module', Array(
				'label' => esc_html__( 'Module', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT2,
				'default' => '',
				'options' => jvbpd_elements_tools()->getBPModuleIDs(),
                'description' => esc_html__('Please select a buddypress module template. If you don`t have any, please create one in Javo page builder ( Javo setting > Page Builder ).', 'jvfrmtd'),
            ) );

			$this->add_control(
			'widget_title',
				[
					'label' => __( 'Title', 'jvfrmtd' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'Default title', 'jvfrmtd' ),
					'placeholder' => __( 'Type your title here', 'jvfrmtd' ),
				]
			);

			$this->add_control( 'filter_type', Array(
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__( "Type", 'jvfrmtd' ),
                'options' => Array(
					'' => esc_html__( "Inline", 'jvfrmtd' ),
                    'dropdown' => esc_html__( "Dropdown", 'jvfrmtd' ),
                ),
                'default' => '',
            ));

            $this->add_control( 'member_type', Array(
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__( "Member Type", 'jvfrmtd' ),
                'options' => Array(
                    'newest' => esc_html__( "Newest", 'jvfrmtd' ),
                    'active' => esc_html__( "Active", 'jvfrmtd' ),
                    'popular' => esc_html__( "Popular", 'jvfrmtd' ),
                ),
                'default' => 'newest',
                'condition' => Array(
                    'render_type' => 'member'
                ),
			));

            $this->add_control( 'max_amount', Array(
                'label' => __( 'Max', 'jvfrmtd' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 100,
				'step' => 1,
				'default' => 10,
            ));

            $this->add_control( 'group_type', Array(
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__( "Group Type", 'jvfrmtd' ),
                'options' => Array(
                    'newest' => esc_html__( "Newest", 'jvfrmtd' ),
                    'activity' => esc_html__( "Activity", 'jvfrmtd' ),
                ),
                'default' => 'newest',
                'condition' => Array(
                    'render_type' => 'group'
                ),
			));

		$this->end_controls_section();

		$this->start_controls_section( 'section_masonry', array(
			'label' => esc_html__( 'Masonry', 'jvfrmtd' ),
		) );
			$this->add_control( 'masonry_columns', Array(
				'label' => esc_html__( "Columns", 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 1,
				'prefix_class' => 'columns-',
				'options' => jvbpd_elements_tools()->getColumnsOption(1, 4),
			));

			$aniOptions = Array();
			for($aniID=1;$aniID<=8;$aniID++){
				$aniOptions[$aniID] = sprintf(esc_html__("Effect %s", 'jvfrmtd'), $aniID);
			}
			$this->add_control( 'masonry_ani', Array(
				'label' => esc_html__( "Animation type", 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'default' => 1,
				'options' => $aniOptions,
			));
		$this->end_controls_section();

		$this->start_controls_section( 'section_tab', array(
			'label' => esc_html__( 'Layout', 'jvfrmtd' ),
		) );


			/*
			$this->add_control( 'grid_fr', Array(
                'label' => __( 'Grid Fr', 'jvfrmtd' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 10,
				'step' => 1,
				'default' => 3,
				'selectors' => [
					'{{WRAPPER}} .item-list' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				],
			)); */


			$this->add_control(
			'grid_minmax',
			[
				'label' => __( "Min height", 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'description' => __( 'Minimum height', 'jvfrmtd'),
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .item-list' => 'grid-auto-rows:minmax({{SIZE}}{{UNIT}}, auto);',
				],
			]
			);

			$this->add_control(
			'cover_image_minmax',
			[
				'label' => __( "Cover Image height", 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'description' => __( 'Cover Image height', 'jvfrmtd'),
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .vcard .cover-wrap' => 'grid-auto-rows:minmax({{SIZE}}{{UNIT}}, auto);',
				],
			]
			);

			$this->add_control(
			'item_des_minmax',
			[
				'label' => __( "Item Des height", 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'description' => __( 'Cover Image height', 'jvfrmtd'),
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .vcard .item' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
			);


			$this->add_control(
			'grid_row_gap',
			[
				'label' => __( "Row Gap", 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'description' => __( 'Row gap', 'jvfrmtd'),
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'em', 'px' ],
				'selectors' => [
					'{{WRAPPER}} .item-list' => 'grid-row-gap:{{SIZE}}{{UNIT}};',
				],
			]
			);


			$this->add_control(
			'grid_col_gap',
			[
				'label' => __( "Column Gap", 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'description' => __( 'Minimum height', 'jvfrmtd'),
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .item-list' => 'grid-column-gap:{{SIZE}}{{UNIT}};',
				],
			]
			);


		$this->end_controls_section();


		$this->start_controls_section(
			'members_style',
			[
				'label' => __( 'General Style', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'members_bg_color',
			[
				'label' => __( 'Background Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .vcard' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'vcard_border',
				'label' => __('Border','jvfrmtd'),
				'selector' => '{{WRAPPER}} .vcard',
			]
		);

		$this->add_control( 'vcard_radius', [
			'label' => __( 'Border Radius', 'jvfrmtd' ),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors' => [
					'{{WRAPPER}} .vcard' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
			'members_display_style',
			[
				'label' => __( 'Display Type', 'jvfrmtd' ),
				'type' => Controls_Manager::SELECT,
				'frontend_available' => true,
				'options' => [
					'inline-block' => __( 'Inline-block', 'jvfrmtd' ),
					'block' => __( 'Block', 'jvfrmtd' ),
				],
				'selectors' => [
					'{{wrapper}} .jv-member' => 'display:{{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'members_align',
			[
				'label' => __( 'Alignment', 'jvfrmtd' ),
				'type' => Controls_Manager::CHOOSE,
				'default' =>'center',
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
				'selectors' => [
					'{{WRAPPER}} .jv-members-wrap' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'member_padding',
			[
				'label' => __( 'Padding', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .vcard' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'member_margin',
			[
				'label' => __( 'Margin', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .vcard' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

        $this->end_controls_section();

		$this->start_controls_section(
			'member_name_style',
			[
				'label' => __( 'Title Style', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'member_name_typo',
			'selector' => '{{WRAPPER}} .item-title a',
			'scheme' => Scheme_Typography::TYPOGRAPHY_1,
			]
		);

		$this->add_control(
			'member_name_color',
			[
				'label' => __( 'Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .item-title a' => 'color: {{VALUE}};',
				],
			]
		);
        $this->end_controls_section();

		$this->start_controls_section(
			'member_description_style',
			[
				'label' => __( 'Description Style', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'member_description_typo',
			'selector' => '{{WRAPPER}} .item-meta span',
			'scheme' => Scheme_Typography::TYPOGRAPHY_1,
			]
		);

		$this->add_control(
			'member_description_color',
			[
				'label' => __( 'Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .item-meta span' => 'color: {{VALUE}};',
				],
			]
		);
        $this->end_controls_section();

		$this->start_controls_section(
			'member_avatar_style',
			[
				'label' => __( 'Avatar Style', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'member_avatar_size',
			[
				'label' => __( "Size", 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30,
				],
				'description' => __( 'Default : 260px to show 4 icons and hide others', 'jvfrmtd'),
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .item-avatar img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control( 'member_avatar_radius', [
			'label' => __( 'Border Radius', 'jvfrmtd' ),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors' => [
					'{{WRAPPER}} .item-avatar img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'member_avatar_border',
				'label' => __('Border','jvfrmtd'),
				'selector' => '{{WRAPPER}} .item-avatar img',
			]
		);

        $this->end_controls_section();
	}


    // Change global templates
    public function has_membership( $args=Array() ) { return $this->_render_type == 'group' ? bp_has_groups( $args ) : bp_has_members( $args ); }
    public function get_membership() { return $this->_render_type == 'group' ? bp_groups() : bp_members(); }
    public function the_membership() { return $this->_render_type == 'group' ? bp_the_group() : bp_the_member(); }

    // Membership meta
    public function _get_name() { return $this->_render_type == 'group' ? bp_get_group_name() : bp_get_member_name(); }
    public function _get_permalink() { return $this->_render_type == 'group' ? esc_url( bp_get_group_permalink() ) : esc_url( bp_get_member_permalink() ); }
	public function _get_avatar( $args ) { return $this->_render_type == 'group' ? bp_group_avatar( $args ) : bp_member_avatar( $args ); }

    public function render() {
        $this->_render_type =  $this->get_settings( 'render_type' );
        $args = Array(
            'per_page' => 20,
            // 'max' => -1
        );

        if( $this->_render_type == 'group' && ! bp_is_active( 'groups' ) ) {
            printf( '<div class="not-active-group-component">%1$s</div>', esc_html__( "To use the groups function, you must activate the buddypress groups component.", 'jvfrmtd' ) );
            return;
		}elseif( $this->_render_type == 'member' && ! bp_is_active( 'friends' ) ) {
			printf( '<div class="not-active-friend-component">%1$s</div>', esc_html__( "To use the friends function, you must activate the buddypress friends component.", 'jvfrmtd' ) );
            return;
		}

        if( $this->_render_type == 'group' ) {
            $args['type'] = $this->get_settings( 'group_type' );
        }else{
            $args['type'] = $this->get_settings( 'member_type' );
        }

        $this->add_render_attribute( 'wrap', Array(
            'class' => Array( 'jv-bp-grid-list', 'type-' . $this->_render_type ),
		));
		// $this->add_render_attribute( 'slide-option', Array(
		// 	'type' => 'hidden',
		// 	'class' => 'slider-value',
		// 	'value' => $this->getSliderOption(),
		// ));

		$widget_title = $this->get_settings( 'widget_title' );
		$filter_type = $this->get_settings( 'filter_type' );
		$max_amount = $this->get_settings( 'max_amount' );
		$member_type = $this->get_settings( 'member_type' );
		$moduleId = $this->get_settings('render_module');
		$ani_type = $this->get_settings('masonry_ani');

		?>
		<div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
		<div class="00">
			<?php
			$scdName = '';
			switch ($this->_render_type) {
				case 'group': $scdName = 'jvbpd_bp_group_list'; break;
				case 'member': $scdName = 'jvbpd_bp_member_list'; break;
				case 'active': $scdName = 'jvbpd_bp_active_member_list'; break;
			}
			$scode = sprintf(
				'[%1$s title="%2$s" filter_type="%3$s" type="%4$s" max="%5$s" moduleid="%6$s" ani="%7$s"]',
				$scdName, $widget_title, $filter_type, $member_type, $max_amount, $moduleId, $ani_type
			);
			echo do_shortcode($scode);?>
		</div>

		<!-- <script>
			jQuery( document ).ready( function( $ ) {
				new AnimOnScroll( document.getElementById( 'grid' ), {
					minDuration : 0.4,
					maxDuration : 0.7,
					viewportFactor : 0.2
				} );

			});

		</script> -->
        </div>
        <?php
    }

}
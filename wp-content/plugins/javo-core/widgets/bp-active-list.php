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

class Jvbpd_Bp_Active_list extends Widget_Base {

    public $_render_type = '';
	public function get_name() { return 'jvbpd-bp-active-list'; }
	public function get_title() { return 'Buddypress Active'; }
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

            $this->add_control( 'list_type', Array(
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__( "List Type", 'jvfrmtd' ),
                'options' => Array(
                    'list' => esc_html__( "list", 'jvfrmtd' ),
                    'grid' => esc_html__( "grid", 'jvfrmtd' ),
                    'small-grid' => esc_html__( "small-grid", 'jvfrmtd' ),
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

		$this->start_controls_section( 'section_tab', array(
			'label' => esc_html__( 'Layout', 'jvfrmtd' ),
        ) );

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
			));


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

	public function render_module() {
		$moduleID = $this->get_settings('render_module');
		$output = '';
		if( false !== get_post_status($moduleID) ) {
			$output = Plugin::instance()->frontend->get_builder_content_for_display( $moduleID );
		}
		echo $output;
	}


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


        $this->add_render_attribute( 'wrap', Array(
            'class' => Array( 'jv-bp-grid-list', 'type-' . $this->_render_type ),
		));

		$widget_title = $this->get_settings( 'widget_title' );
		$filter_type = $this->get_settings( 'filter_type' );
		$max_amount = $this->get_settings( 'max_amount' );
		$member_type = $this->get_settings( 'member_type' );
		$list_type = $this->get_settings( 'list_type' );
        ?>
		<div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>

            <?php bp_nouveau_before_activity_directory_content(); ?>

            <?php if ( is_user_logged_in() ) : ?>

                <?php bp_get_template_part( 'activity/post-form' ); ?>

            <?php endif; ?>

            <?php bp_nouveau_template_notices(); ?>

            <?php if ( ! bp_nouveau_is_object_nav_in_sidebar() ) : ?>

                <?php bp_get_template_part( 'common/nav/directory-nav' ); ?>

            <?php endif; ?>

            <div class="screen-content">

                <?php bp_get_template_part( 'common/search-and-filters-bar' ); ?>

                <?php bp_nouveau_activity_hook( 'before_directory', 'list' ); ?>

                <div id="activity-stream" class="activity" data-bp-list="activity">

                    <div id="bp-ajax-loader"><?php bp_nouveau_user_feedback( 'directory-activity-loading' ); ?></div>


                    <?php bp_nouveau_before_loop(); ?>

                    <?php if ( bp_has_activities( bp_ajax_querystring( 'activity' ) ) ) : ?>

                        <?php if ( empty( $_POST['page'] ) || 1 === (int) $_POST['page'] ) : ?>
                            <ul class="activity-list item-list bp-list">
                        <?php endif; ?>

                        <?php
                        while ( bp_activities() ) :
                            bp_the_activity();
                            bp_nouveau_activity_hook( 'before', 'entry' ); ?>

                            <li class="<?php bp_activity_css_class(); ?>" id="activity-<?php bp_activity_id(); ?>" data-bp-activity-id="<?php bp_activity_id(); ?>" data-bp-timestamp="<?php bp_nouveau_activity_timestamp(); ?>">


                            <?php //bp_get_template_part( 'activity/entry' ); ?>


                            <?php $this->render_module(); ?>

                            </li>

                            <?php
                            bp_nouveau_activity_hook( 'after', 'entry' );


                        endwhile; ?>

                        <?php if ( bp_activity_has_more_items() ) : ?>

                            <li class="load-more">
                                <a href="<?php bp_activity_load_more_link(); ?>"><?php echo esc_html_x( 'Load More', 'button', 'buddypress' ); ?></a>
                            </li>

                        <?php endif; ?>

                        <?php if ( empty( $_POST['page'] ) || 1 === (int) $_POST['page'] ) : ?>
                            </ul>
                        <?php endif; ?>

                    <?php else : ?>

                            <?php bp_nouveau_user_feedback( 'activity-loop-none' ); ?>

                    <?php endif; ?>

                    <?php bp_nouveau_after_loop(); ?>

                </div><!-- .activity -->

                <?php bp_nouveau_after_activity_directory_content(); ?>

            </div><!-- // .screen-content -->


        </div>
        <?php
    }

}
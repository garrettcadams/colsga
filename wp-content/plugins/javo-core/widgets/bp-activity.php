<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Scheme_Typography;

if ( ! defined( 'ABSPATH' ) ) exit;

class Jvbpd_Bp_Activity extends Widget_Base {

    public $_render_type = '';
	public function get_name() { return 'jvbpd-bp-activity'; }
	public function get_title() { return 'Buddypress Activity'; }
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
                ),
                'default' => 'group',
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

		$this->start_controls_section( 'section_slider', array(
			'label' => esc_html__( 'Slider Options', 'jvfrmtd' ),
		) );

		$this->add_control( 'effect', Array(
			'type' => Controls_Manager::SELECT,
			'label' => __( 'Effect', 'jvfrmtd' ),
			'default' => 'slide',
			'options' => Array(
				'slide' => __( 'Slide', 'jvfrmtd' ),
				'fade' => __( 'Fade', 'jvfrmtd' ),
				'cube' => __( 'Cube', 'jvfrmtd' ),
			),
			'separator' => 'before',
			'frontend_available' => true,
		) );

		$this->add_control( 'speed', Array(
			'label' => __( 'Transition Duration', 'jvfrmtd' ),
			'type' => Controls_Manager::NUMBER,
			'default' => 5000,
			'frontend_available' => true,
		) );

		$this->add_control( 'autoplay', Array(
			'label' => __( 'Autoplay', 'jvfrmtd' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => '',
			'return_value' => 'yes',
			'separator' => 'before',
			'frontend_available' => true,
		) );

		$this->add_control( 'autoplay_speed', Array(
			'label' => __( 'Autoplay Speed', 'jvfrmtd' ),
			'type' => Controls_Manager::NUMBER,
			'default' => 500,
			'condition' => Array(
				'autoplay' => 'yes',
			),
			'frontend_available' => true,
		) );

		$slides_per_view = range( 1, 10 );
		$slides_per_view = array_combine( $slides_per_view, $slides_per_view );

		$this->add_control( 'slidesPerView', Array(
			'type' => Controls_Manager::SELECT,
			'label' => __( 'Slides Per View', 'jvfrmtd' ),
			'options' => Array( '' => __( 'Default', 'jvfrmtd' ) ) + $slides_per_view,
			'condition' => Array(
				'effect' => 'slide',
			),
			'frontend_available' => true,
		) );

		$this->add_control( 'spaceBetween', Array(
			'label' => __( 'Between Space', 'jvfrmtd' ),
			'type' => Controls_Manager::NUMBER,
			'default' => '0',
		) );

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
					'{{WRAPPER}} .jv-member' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .jv-member' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
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
					'{{WRAPPER}} .jv-member' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
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
			'selector' => '{{WRAPPER}} .jv-member-name a',
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
					'{{WRAPPER}} .jv-member-name a' => 'color: {{VALUE}};',
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
			'selector' => '{{WRAPPER}} .jv-member-name a',
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
					'{{WRAPPER}} .jv-member-name a' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .jv-member-avatar img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control( 'member_avatar_radius', [
			'label' => __( 'Border Radius', 'jvfrmtd' ),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors' => [
					'{{WRAPPER}} .jv-member-avatar img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'member_avatar_border',
				'label' => __('Border','jvfrmtd'),
				'selector' => '{{WRAPPER}} .jv-member-avatar img',
			]
		);

        $this->end_controls_section();
	}

	public function getSliderOption() {
		$output = Array();
		foreach(
			Array(
				'speed' => false,
				'autoplay' => false,
				'autoplay_speed' => false,
				'slidesPerView' => false,
				'skin' => false,
				'effect' => false,
				'spaceBetween' => false,
			)
		as $setting => $value ) {
			$output[ $setting ] = false != $value ? $value : $this->get_settings( $setting );
		}

		$output = array_filter( $output );
		return wp_json_encode( $output, JSON_NUMERIC_CHECK );
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
            'class' => Array( 'jvbpd-bp-membership', 'type-' . $this->_render_type, 'is-jvcore-swiper' ),
		));
		$this->add_render_attribute( 'slide-option', Array(
			'type' => 'hidden',
			'class' => 'slider-value',
			'value' => $this->getSliderOption(),
		));
        ?>


            <?php if ( bp_has_members( bp_ajax_querystring( 'members' ) ) ) : ?>

  <div id="pag-top" class="pagination">

    <div class="pag-count" id="member-dir-count-top">

      <?php bp_members_pagination_count(); ?>

   </div>

   <div class="pagination-links" id="member-dir-pag-top">

      <?php bp_members_pagination_links(); ?>

   </div>

  </div>

  <?php do_action( 'bp_before_directory_members_list' ); ?>

  <ul id="members-list" class="item-list" role="main">

  <?php while ( bp_members() ) : bp_the_member(); ?>

    <li>
      <div class="item-avatar">
         <a href="<?php bp_member_permalink(); ?>"><?php bp_member_avatar(); ?></a>
      </div>

      <div class="item">
        <div class="item-title">
           <a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>

           <?php if ( bp_get_member_latest_update() ) : ?>

              <span class="update"> <?php bp_member_latest_update(); ?></span>

           <?php endif; ?>

       </div>

       <div class="item-meta"><span class="activity"><?php bp_member_last_active(); ?></span></div>

       <?php do_action( 'bp_directory_members_item' ); ?>

      <?php
       /***
        * If you want to show specific profile fields here you can,
        * but it'll add an extra query for each member in the loop
        * (only one regardless of the number of fields you show):
        *
        * bp_member_profile_data( 'field=the field name' );
       */
       ?>
       </div>

       <div class="action">

           <?php do_action( 'bp_directory_members_actions' ); ?>

      </div>

      <div class="clear"></div>
   </li>

 <?php endwhile; ?>

 </ul>

 <?php do_action( 'bp_after_directory_members_list' ); ?>

 <?php bp_member_hidden_fields(); ?>

 <div id="pag-bottom" class="pagination">

    <div class="pag-count" id="member-dir-count-bottom">

       <?php bp_members_pagination_count(); ?>

    </div>

    <div class="pagination-links" id="member-dir-pag-bottom">

      <?php bp_members_pagination_links(); ?>

    </div>

  </div>

<?php else: ?>

   <div id="message" class="info">
      <p><?php _e( "Sorry, no members were found.", 'buddypress' ); ?></p>
   </div>

<?php endif;
    }

}
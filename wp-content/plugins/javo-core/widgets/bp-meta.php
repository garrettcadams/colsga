<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Scheme_Typography;

if ( ! defined( 'ABSPATH' ) ) exit;

class Jvbpd_Bp_Meta extends Widget_Base {

    public $_render_type = '';
	public function get_name() { return 'jvbpd-bp-meta'; }
	public function get_title() { return 'Buddypress Module Meta'; }
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
                'default' => 'member',
            ));

            $this->add_control( 'bp_meta', Array(
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__( "Meta", 'jvfrmtd' ),
                'options' => Array(
                    'avatar' => esc_html__( "Avatar", 'jvfrmtd' ),
                    'name' => esc_html__( "Name", 'jvfrmtd' ),
                    // 'action_btn' => esc_html__( "Action Buttons ( login )", 'jvfrmtd' ),
                    'add_friend' => esc_html__( "Friend button", 'jvfrmtd' ),
                    'last_active' => esc_html__( "Last active", 'jvfrmtd' ),
                    'member_registered' => esc_html__( "Member registered", 'jvfrmtd' ),
                    'friend_count' => esc_html__( "Friends count", 'jvfrmtd' ),
                    'last_update_summary' => esc_html__( "Last update", 'jvfrmtd' ),
                ),
                'default' => 'name',
                'condition' => Array(
                    'render_type' => 'member'
                )
            ));

            $this->add_control( 'group_meta', Array(
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__( "Meta", 'jvfrmtd' ),
                'options' => Array(
                    'avatar' => esc_html__( "Group Avatar", 'jvfrmtd' ),
                    'name' => esc_html__( "Group Name", 'jvfrmtd' ),
                    // 'group_item' => esc_html__( "Item ( For dev )", 'jvfrmtd' ),
                    // 'group_actions' => esc_html__( "Action ( For dev )", 'jvfrmtd' ),
                    'group_join' => esc_html__( "Group join button", 'jvfrmtd' ),
                    'last_active' => esc_html__( "Last active", 'jvfrmtd' ),
                    'bp_group_member_count' => esc_html__( "Member count", 'jvfrmtd' ),
                    'bp_group_type' => esc_html__( "Group type", 'jvfrmtd' ),
                    'group_description' => esc_html__( "Group Description", 'jvfrmtd' ),

                ),
                'default' => 'name',
                'condition' => Array(
                    'render_type' => 'group'
                )
            ));

            $this->add_control(
			'able_link',
			[
				'label' => __( 'Link to detail page', 'jvfrmtd' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'jvfrmtd' ),
				'label_off' => __( 'Off', 'jvfrmtd' ),
				'return_value' => 'yes',
				'default' => 'label_off',
			]
		    );


            $this->add_responsive_control(
			'meta_align',
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
					'{{WRAPPER}} .jv-bp-module' => 'text-align: {{VALUE}};',
				],
			]
		);




		$this->end_controls_section();


		$this->start_controls_section( 'img_style', array(
            'label' => esc_html__( 'Image', 'jvfrmtd' ),
            'condition' => Array(
                    'bp_meta' => 'avatar'
            ),
        ) );

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
					'{{WRAPPER}} .jv-bp-module img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control( 'member_avatar_radius', [
			'label' => __( 'Border Radius', 'jvfrmtd' ),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors' => [
					'{{WRAPPER}} .jv-bp-module img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'member_avatar_border',
				'label' => __('Border','jvfrmtd'),
				'selector' => '{{WRAPPER}} .jv-bp-module img',
			]
		);

		$this->end_controls_section();


		$this->start_controls_section( 'section_style', array(
            'label' => esc_html__( 'Style', 'jvfrmtd' ),
            'condition' => Array(
                    'bp_meta!' => 'avatar'
            ),
        ) );

        $this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'meta_typo',
			'selector' => '{{WRAPPER}} .jv-bp-module',
			'scheme' => Scheme_Typography::TYPOGRAPHY_1,
			]
		);

		$this->add_control(
			'meta_color',
			[
				'label' => __( 'Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .jv-bp-module' => 'color: {{VALUE}};',
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

        $this->end_controls_section();
	}


    public function render() {
        $this->_render_type =  $this->get_settings( 'render_type' );
        $bp_meta =  $this->get_settings( 'bp_meta' );
        $group_meta =  $this->get_settings( 'group_meta' );
        $able_link =  $this->get_settings( 'able_link' );

        if( $this->_render_type == 'group' && ! bp_is_active( 'groups' ) ) {
            printf( '<div class="not-active-group-component">%1$s</div>', esc_html__( "To use the groups function, you must activate the buddypress groups component.", 'jvfrmtd' ) );
            return;
		}elseif( $this->_render_type == 'member' && ! bp_is_active( 'friends' ) ) {
			printf( '<div class="not-active-friend-component">%1$s</div>', esc_html__( "To use the friends function, you must activate the buddypress friends component.", 'jvfrmtd' ) );
            return;
		}

        $this->add_render_attribute( 'wrap', Array(
            'class' => Array(
                'jv-bp-module',
                'bp_meta-' . $this->_render_type,
                'bp-meta-field-' . ('member'==$this->_render_type ? $bp_meta : $group_meta ),
            ),
        ));

        $link ="";
        $output ="";

        if( $this->_render_type == 'member') {

            if (is_admin()){
                $link="<a href='#'>";
            }else{
                $link="<a href='". bp_get_member_permalink() ."' title='". bp_get_member_name() ."'>";
            }

        switch ($bp_meta) {
            case 'avatar':
                if (is_admin()) {
                    $output = "<img src='https://demo1.wpjavo.com/lynk/wp-content/uploads/sites/2/avatars/1/596720e1b92af-bpthumb.jpg'>";
                }else {
                    $output = bp_get_member_avatar();
                }
                break;

            case 'name':
                if (is_admin()) {
                    $output = "Name";
                }else {
                    $output = bp_get_member_name();
                }
                break;
            case 'action_btn':
                if (is_admin()) {
                    $output = "<button>Private message</button>";
                }else {
                    $output = do_action( 'bp_directory_members_actions' );
                }
                break;
            case 'last_active':
                if (is_admin()) {
                    $output = "2 days ago";
                }else {
                    $output = bp_get_member_last_active();
                }
                break;
            case 'member_registered':
                if (is_admin()) {
                    $output = "Registered 2 months ago";
                }else {
                    $output = bp_get_member_registered();
                }
                break;
            case 'last_update_summary':
                if (is_admin()) {
                    $output = "Hello! How are you?";
                }else {
                    $output = bp_get_member_latest_update();
                }
                break;
            case 'friend_count':
                if (is_admin()) {
                    $output = "10";
                }else {
                    //$output = bp_member_total_friend_count();
                }
                break;

            default:
                # code...
                break;
        }

        }elseif( $this->_render_type == 'group' ) {

            if (is_admin()){
                $link="<a href='#'>";
            }else{
                $link="<a href='". bp_get_group_permalink() ."' title='". bp_get_group_name() ."'>";
            }


        switch ($group_meta) {
            case 'avatar':
                if (is_admin()) {
                    $output = "<img src='https://demo1.wpjavo.com/lynk/wp-content/uploads/sites/2/avatars/1/596720e1b92af-bpthumb.jpg'>";
                }else {
                    $output = bp_get_group_avatar();
                }
                break;

            case 'name':
                if (is_admin()) {
                    $output = "Group Name";
                }else {
                    $output = bp_get_group_name();
                }
                break;
            case 'group_item':
                if (is_admin()) {
                    $output = "Group Item";
                }else {
                    $output = do_action( 'bp_directory_groups_item' );
                }
                break;
            case 'group_actions':
                if (is_admin()) {
                    $output = "Group actions";
                }else {
                    $output = do_action( 'bp_directory_groups_actions' );
                }
                break;
            case 'bp_group_member_count':
                if (is_admin()) {
                    $output = "65";
                }else {
                    $output = bp_get_group_member_count();
                }
                break;
            case 'bp_group_type':
                if (is_admin()) {
                    $output = "Group type";
                }else {
                    $output = bp_get_group_type();
                }
                break;
            case 'group_description':
                if (is_admin()) {
                    $output = "Hello! How are you?";
                }else {
                    $output = bp_get_group_description_excerpt();
                }
                break;
            case 'last_active':
                if (is_admin()) {
                    $output = "10";
                }else {
                    $output = bp_get_group_last_active();
                }
                break;

            case 'group_join': $output = '{group_join}'; break;
            default:
                # code...
                break;
        }

        }

        ?>
        <div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
        <?php
            if ("yes" === $able_link){ echo $link; }
                if( wp_doing_ajax() ) {
                    printf('{%s}', ( $this->_render_type == 'member' ? $bp_meta : $group_meta ) );
                }else{
                    echo $output;
                }
            if ("yes" === $able_link){ echo "</a>"; }
                ?>

        </div>
        <?php
    }

}
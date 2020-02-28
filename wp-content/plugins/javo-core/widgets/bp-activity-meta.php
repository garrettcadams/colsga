<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Scheme_Typography;

if ( ! defined( 'ABSPATH' ) ) exit;

class Jvbpd_Bp_Activity_Meta extends Widget_Base {

	public function get_name() { return 'jvbpd-bp-activity-meta'; }
	public function get_title() { return 'Buddypress Activity Meta'; }
	public function get_icon() { return 'eicon-button'; }
	public function get_categories() { return [ 'jvbpd-elements-bp' ]; }

	protected function _register_controls() {
		$this->start_controls_section( 'section_general', array(
			'label' => esc_html__( 'General', 'jvfrmtd' ),
        ) );


            $this->add_control( 'bp_activity_meta', Array(
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__( "Meta", 'jvfrmtd' ),
                'options' => Array(
                    'avatar' => esc_html__( "Avatar", 'jvfrmtd' ),
                    'activity_action' => esc_html__( "Activity Action", 'jvfrmtd' ),
                    'content' => esc_html__( "Content", 'jvfrmtd' ),
                    'activity_buttons' => esc_html__( "Buttons", 'jvfrmtd' ),
                    'comments' => esc_html__( "comment", 'jvfrmtd' ),
                ),
                'default' => 'avatar',
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
                    'bp_activity_meta' => 'avatar'
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
                    'bp_activity_meta!' => 'avatar'
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


    public function comment_area(){

        bp_nouveau_activity_hook( 'before', 'entry_comments' );

        if ( bp_activity_get_comment_count() || ( is_user_logged_in() && ( bp_activity_can_comment() || bp_is_single_activity() ) ) ) :

            ?><div class="activity-comments"><?php
            bp_activity_comments();
            bp_nouveau_activity_comment_form();
            ?></div><?php
        endif;

        bp_nouveau_activity_hook( 'after', 'entry_comments' );

    }

    public function activity_content(){

    if ( bp_nouveau_activity_has_content() ) :
                    bp_nouveau_activity_content();
                endif;
    }

    public function render() {

        $bp_activity_meta =  $this->get_settings( 'bp_activity_meta' );
        $able_link =  $this->get_settings( 'able_link' );


        if( $this->_render_type == 'group' && ! bp_is_active( 'groups' ) ) {
            printf( '<div class="not-active-group-component">%1$s</div>', esc_html__( "To use the groups function, you must activate the buddypress groups component.", 'jvfrmtd' ) );
            return;
		}elseif( $this->_render_type == 'member' && ! bp_is_active( 'friends' ) ) {
			printf( '<div class="not-active-friend-component">%1$s</div>', esc_html__( "To use the friends function, you must activate the buddypress friends component.", 'jvfrmtd' ) );
            return;
		}


        $this->add_render_attribute( 'wrap', Array(
            'class' => Array( 'jv-bp-module', 'bp_activity_meta-' . $bp_activity_meta ),
        ));

        $link ="";
        $output ="";


            if (is_admin()){
                $link="<a href='#'>";
            }else{
                $link="<a href='". bp_get_activity_user_link() ."'>";



            }


        switch ($bp_activity_meta) {
            case 'avatar':
                if (is_admin()) {
                    $output = "<img src='https://demo1.wpjavo.com/lynk/wp-content/uploads/sites/2/avatars/1/596720e1b92af-bpthumb.jpg'>";
                }else {
                    $output = bp_activity_avatar( array( 'type' => 'full' ) );
                }
                break;

            case 'activity_action':
                if (is_admin()) {
                    $output = "Admin posted an update an hour ago";
                }else {
                    $output = bp_activity_action();
                }
                break;
            case 'content':
                if (is_admin()) {
                    $output = "This is the content. I really love this post. It is sample content.";
                }else {
                    $output = $this->activity_content();
                }
                break;
            case 'activity_buttons':
                if (is_admin()) {
                    $output = "Comment | Favorite | Delete";
                }else {
                    $output = bp_nouveau_activity_entry_buttons();;
                }
                break;
            case 'comments':
                if (is_admin()) {
                    $output = "comment form";
                }else {
                    $output = $this->comment_area();
                }
                break;


            default:
                # code...
                break;
        }


        ?>
        <div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
        <?php
            if ("yes" === $able_link){ echo $link; }

                echo $output;

            if ("yes" === $able_link){ echo "</a>"; }
                ?>

        </div>
        <?php
    }

}
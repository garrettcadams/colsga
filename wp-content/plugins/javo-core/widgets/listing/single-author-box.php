<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit;

class Jvbpd_Single_Author_Box extends Widget_Base {

	public function get_name() { return 'jvbpd-single-author-box'; }
	public function get_title() { return 'Single Author Box'; }
	public function get_icon() { return 'eicon-button'; }
	public function get_categories() { return [ 'jvbpd-elements' ]; }

	protected function _register_controls() {
		$this->start_controls_section( 'section_general', array(
			'label' => esc_html__( 'General', 'jvfrmtd' ),
		) );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_author_box',
			[
				'label' => __( 'Wrap', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,			
			]
		);

		$this->add_responsive_control(
			'padding',
			[
				'label' => __( 'Padding', 'jvfrmtd' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'	   => [
					'right' => 30,
					'left' => 30,
					'top' => 30,
					'bottom' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .card-block' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					'{{WRAPPER}} .media' => 'padding: 0; margin:0;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'label' => __( 'Border', 'plugin-domain' ),
				'selector' => '{{WRAPPER}} .card',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow',
				'label' => __( 'Box Shadow', 'plugin-domain' ),
				'selector' => '{{WRAPPER}} .card',
			]
		);




		$this->end_controls_section();


		$this->start_controls_section(
			'section_author_style',
			[
				'label' => __( 'Author', 'jvfrmtd' ),
				'tab' => Controls_Manager::TAB_STYLE,			
			]
		);

		$this->add_control(
			'author_avatar_radius',
			[
				'label' => __( 'Author Image Radius', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
			    'default' => [
					'size' => 50,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} img.photo' => 'width:50px; height:50px; border-radius: {{SIZE}}{{UNIT}}',
				],				
			]
		);


		$this->add_control(
			'author_avatar_margin_right',
			[
				'label' => __( 'Author Margin Right ', 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
			    'default' => [
					'size' => 30,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} img.photo' => 'margin-right: {{SIZE}}{{UNIT}} !important;',
				],				
			]
		);

		$this->add_control(
			'author_name_color',
			[
				'label' => __( 'Author Name Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .card-title, {{WRAPPER}} .card-title a' => 'color: {{VALUE}}',
				],			
			]
		);


		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'author_name_typography',
				'label' => __( 'Title', 'jvfrmtd' ),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .card-title, {{WRAPPER}} .card-title a',			
			]
		);

		$this->add_control(
			'author_bio_color',
			[
				'label' => __( 'Biographical Color', 'jvfrmtd' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .card-text' => 'color: {{VALUE}}',
				],			
			]
		);


		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'author_bio_typography',
				'label' => __( 'Biographical Description', 'jvfrmtd' ),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .card-text',				
			]
		);


		$this->end_controls_section();

	}



	public function getAUthorLink() {
		$user_id = $this->getAuthorMeta( 'ID' );
		$url = get_author_posts_url( $user_id );
		if( function_exists( 'bp_core_get_user_domain' ) ) {
			$url = bp_core_get_user_domain( $user_id );
		}
		return $url;
	}

	public function getAuthorMeta( $key, $default=false ) {
		$author = get_user_by( 'id', get_post()->post_author );
		if( $author ) {
			$default = $author->get( $key );
		}
		return $default;
	}

	public function post_author_avatar( $imageSize='thumbnail' ) {
		$user_id = $this->getAuthorMeta( 'ID' );
		$avatar = null;
		if( function_exists( 'bp_core_fetch_avatar' ) ) {
			$imageSize = apply_filters( 'jvbpd_core/widget/single-author-box/avatar/size', $imageSize );
			$avatar = bp_core_fetch_avatar( Array( 'item_id' => $user_id, 'type' => $imageSize, 'class' => 'd-flex mr-3' ) );
		}
		return $avatar;
	}
	public function getBpAuthorMeta( $key='', $default=false ) {
		$user_id = $this->getAuthorMeta( 'ID' );
		if( function_exists( 'bp_get_profile_field_data' ) ) {
			$default = bp_get_profile_field_data( Array( 'user_id' => $user_id, 'field' => $key ) );
		}
		return $default;
	}

	protected function render() {
		$this->add_render_attribute( 'wrap', Array(
			'class' => Array( 'jvbpd-single-author-box', 'card' ),
		) );
		$this->add_render_attribute( 'media_wrap', Array(
			'class' => Array( 'media' ),
		) ); ?>

		<div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
			<div class="card-block">
				<div <?php echo $this->get_render_attribute_string( 'media_wrap' ); ?>>
					<a href="<?php echo $this->getAUthorLink(); ?>">
						<?php echo $this->post_author_avatar(); ?>
					</a>
					<div class="media-body">
						<h4 class="card-title">
							<a href="<?php echo $this->getAUthorLink(); ?>">
								<?php echo $this->getAuthorMeta( 'display_name' ); ?>
							</a>
						</h4>
						<p class="card-text"><?php echo $this->getAuthorMeta( 'description' ); ?></p>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
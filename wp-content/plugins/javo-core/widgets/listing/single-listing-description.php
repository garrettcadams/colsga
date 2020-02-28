<?php
/**
Widget Name: Single description widget
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


class jvbpd_single_description extends Widget_Base {

	public function get_name() {
		return 'jvbpd-single-description';
	}

	public function get_title() {
		return 'Listing Description';   // title to show on elementor
	}

	public function get_icon() {
		return 'fa fa-file-text-o';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-single-listing' ];    // category of the widget
	}

    protected function _register_controls() {

    $this->start_controls_section(
		'section_general',
			array(
				'label' => esc_html__( 'Description', 'jvfrmtd' ),
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
	
	$this->start_controls_section(
		'description_style',
		[
			'label' => __( 'Style','jvfrmtd'),
			'tab' => Controls_Manager::TAB_STYLE,
		]
	);

	$this->add_control(
		'description_text_color',
		[
			'label' => __( 'Text Color', 'jvfrmtd' ),
			'type' => Controls_Manager::COLOR,
			'default' => '#454545',
			'scheme' => [
				'type' => Scheme_Color::get_type(),
				'value' => Scheme_Color::COLOR_1,
			],
			'selectors' => [
				'{{WRAPPER}} .jv-custom-post-content-inner p, {{WRAPPER}} .jv-custom-post-content-inner' => 'color: {{VALUE}}',
			],
		]
	);

	$this->add_group_control( Group_Control_Typography::get_type(), [
		'name' => 'description_typography',
		'selector' => '{{WRAPPER}} .jv-custom-post-content-inner p, {{WRAPPER}} .jv-custom-post-content-inner',
		'scheme' => Scheme_Typography::TYPOGRAPHY_1,
	] );

	$this->end_controls_section();
	}

    protected function render() {

		wp_reset_postdata();
    $settings = $this->get_settings();
		$isPreviewMode = is_admin();
		//if( $isPreviewMode ) {
			//$previewBaseURL = jvbpdCore()->assets_url . '/images/elementor/listipia/';
			//$previewURL = $previewBaseURL . 'single-description.jpg';
			//printf( '<img src="%s">', esc_url_raw( $previewURL ) );
		//}else{
			$this->getContent( $settings, get_post() );
		//}

    }

	public function getContent( $settings, $obj ) {
    ?>
		<div class="item-description" id="javo-item-describe-section" data-jv-detail-nav>	
			<!-- Post Content Container -->
			<div class="jv-custom-post-content loaded">
				<div class="jv-custom-post-content-inner">
					<?php
              $isPreviewMode = is_admin();
          		if( $isPreviewMode ) {
                echo "This is a sample content for preview. Quisque vel sem ac enim facilisis ultrices. Vivamus neque sapien, vehicula vel lorem non, malesuada pretium sapien. Morbi blandit, felis ac rhoncus porta, tortor ligula viverra libero, ac semper velit lectus nec orci. Aenean posuere nunc eu sollicitudin maximus. In ut nibh in massa interdum euismod ut eu nibh. Sed non dignissim tellus. Cras ex lectus, pharetra non turpis non, ornare placerat arcu. Vestibulum et risus consectetur, semper metus at, posuere velit. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Curabitur dignissim tristique vestibulum.Quisque vel sem ac enim facilisis ultrices. Vivamus neque sapien, vehicula vel lorem non, malesuada pretium sapien. Morbi blandit, felis ac rhoncus porta, tortor ligula viverra libero, ac semper velit lectus nec orci. Aenean posuere nunc eu sollicitudin maximus. In ut nibh in massa interdum euismod ut eu nibh.";
              }else{
                echo apply_filters( 'the_content', $obj->post_content );
              }
              ?>
			</div><!-- /.jv-custom-post-content-inner -->
				<div class="jv-custom-post-content-trigger hidden">
					<i class="fa fa-plus"></i>
					<?php esc_html_e( "Read More", 'listopia' ); ?>
				</div><!-- /.jv-custom-post-content-trigger -->
			</div><!-- /.jv-custom-post-content -->
	
		</div><!-- /#javo-item-describe-section -->
		<?php
	}
}

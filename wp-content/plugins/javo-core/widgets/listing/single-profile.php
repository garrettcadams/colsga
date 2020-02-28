<?php
/**
Widget Name: Single profile widget
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


class jvbpd_single_profile extends Widget_Base {

	public function get_name() {
		return 'jvbpd-single-profile';
	}

	public function get_title() {
		return 'JV Profile';   // title to show on elementor
	}

	public function get_icon() {
		return 'eicon-person';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-single-listing' ];    // category of the widget
	}

    protected function _register_controls() {

        $this->start_controls_section(
			'section_general',
			array(
				'label' => esc_html__( 'Profile', 'jvfrmtd' ),
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
    }

    protected function render() {
		$settings = $this->get_settings();
		wp_reset_postdata();

		$isPreviewMode = is_admin();

		if( $isPreviewMode) {
			$previewBaseURL = jvbpdCore()->assets_url . '/images/elementor/listipia/';
			$previewURL = $previewBaseURL . 'single-profile.jpg';
			printf( '<img src="%s">', esc_url_raw( $previewURL ) );
		}else{
			$this->getContent( $settings, get_post() );
		}
    }


	public function getAvatar( $user=null ) {
		$output ='';
		if( function_exists( 'bp_core_fetch_avatar' ) ) {
			$output = bp_core_fetch_avatar( Array(
				'object' => 'user',
				'item_id' => $user->ID,
				'width' => 200,
				'height' => 200,
			) );
		}
		echo $output;
	}

	public function getContent( $settings, $obj ) {
		$author = get_userdata( $obj->post_author );
		if( ! $author instanceof \WP_User ) {
			return false;
		}



		?>
		<div class="col-md-12 col-xs-12" id="javo-item-profile-section" data-jv-detail-nav>
			<div class="text-center">
				<?php $this->getAvatar( $author ); ?>

				<!--
				<img src="http://demo1.wpjavo.com/listopia/wp-content/uploads/sites/3/avatars/1/59ddeb8c3c54e-bpfull.jpg" class="avatar user-1-avatar avatar-150 photo" width="100" height="100" alt="Profile picture of javo"> -->

				<div class="jvbpd-single-author-name">
					<?php printf( esc_html__( "Posted By %s", 'listopia' ), ucfirst( $author->display_name ) ); ?>
				</div>

				<?php if( function_exists( 'bp_core_get_user_domain' ) ) : ?>
					<div class="row">
						<div class="col">
							<a href="<?php echo bp_core_get_user_domain( $author->ID ); ?>" class="btn btn-primary text-white btn-block"><?php esc_html_e( "Profile", 'listopia' ); ?></a>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
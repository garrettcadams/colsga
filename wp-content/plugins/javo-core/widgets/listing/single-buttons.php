<?php
/**Widget Name: Single buttons widget
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


class jvbpd_single_buttons extends Widget_Base {

	public function get_name() {
		return 'jvbpd-single-buttons';
	}

	public function get_title() {
		return 'Buttons';   // title to show on elementor
	}

	public function get_icon() {
		return 'fa fa-user-o';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-single-listing' ];    // category of the widget
	}

    protected function _register_controls() {

        $this->start_controls_section(
            'section_team',
            [
                'label' => __('Team', 'jvfrmtd'),
            ]
        );

        $this->add_control(

            'style', [
                'type' => Controls_Manager::SELECT,
                'label' => __('Choose Team Style', 'jvfrmtd'),
                'default' => 'style1',
                'options' => [
                    'style1' => __('Style 1', 'jvfrmtd'),
                    'style2' => __('Style 2', 'jvfrmtd'),
                ],
                'prefix_class' => 'lae-team-members-',
            ]
        );
        $this->end_controls_section();
    }

    protected function render() {

		$settings = $this->get_settings();
		wp_reset_postdata();
		$isPreviewMode = is_admin();

		if( $isPreviewMode) {
			$previewBaseURL = jvbpdCore()->assets_url . '/images/elementor/listipia/';
			$previewURL = $previewBaseURL . 'single-button.jpg';
			printf( '<img src="%s">', esc_url_raw( $previewURL ) );
		}else{
			$this->getContent( $settings, get_post() );
		}
    }

	public function getContent( $settings, $obj ) {
		?>
		<div class="row jvbpd-meta-details-wrap">
				<div class="col-md-3 single-header-terms">
					<?php
						$jvbpd_category = wp_get_object_terms(get_the_ID(), 'listing_category', Array( 'fields' => 'names'));
						$jvbpd_location = wp_get_object_terms(get_the_ID(), 'listing_location', Array( 'fields' => 'names' ));
						$jvbpd_category_link = get_term_link($jvbpd_category[0], 'listing_category');
						$jvbpd_location_link = get_term_link($jvbpd_location[0], 'listing_location');
					?>
					<div class="tax-item-category"><i class="jvbpd-icon3-desktop"></i><a href="<?php echo esc_url($jvbpd_category_link); ?>" target="_blank"><?php echo $jvbpd_category[0]; ?></a></div>
					<div class="tax-item-location"><i class="jvbpd-icon3-map"></i><a href="<?php echo esc_url($jvbpd_location_link); ?>" target="_blank"><?php echo $jvbpd_location[0]; ?></a></div>
				</div>
				<div class="col-md-9 jvbpd-meta-details-right">
					<div class="btn-favorite">
							<?php if( class_exists( 'lvDirectoryFavorite_button' ) ) {
								$objFavorite = new \lvDirectoryFavorite_button(
									Array(
										'post_id' => get_the_ID(),
										'show_count' => true,
										'show_add_text' => "<span>".__('Save','jvfrmtd')."</span>",
										'save' => "<i class='jvbpd-icon2-bookmark2'></i>",
										'unsave' => "<i class='fa fa-heart'></i>",
										'class' => Array( 'btn', 'lava-single-page-favorite', 'admin-color-setting-hover' ),
									)
								);
								$objFavorite->output();
							} ?>
					</div> <!-- btn-favorite -->
					<div class="btn-share">
						<button type="button" class="btn btn-block admin-color-setting-hover lava-Di-share-trigger">
							<i class="jvbpd-icon2-flag"></i> <?php esc_html_e( "Share", 'jvfrmtd' ); ?>
						</button>
					</div> <!-- btn-share -->
					<div class="btn-amount-review">
						<a href="#javo-item-review-section" class="admin-color-setting-hover"><?php esc_html_e( "2 Ratings", 'jvfrmtd' ); ?> </a>
					</div> <!-- btn-amount-review -->
					<div class="btn-submit-review">
						<a href="#javo-item-review-section" class="admin-color-setting-hover"><i class="jvbpd-icon1-comment-o"></i> <?php esc_html_e( "Submit Review", 'jvfrmtd' ); ?></a>
					</div> <!-- btn-submit-review -->
					<div class="btn-score-review">
						<a href="#javo-item-review-section" class="admin-color-setting-hover"><?php esc_html_e( "4.8 / 5", 'jvfrmtd' ); ?></a>
					</div> <!-- btn-score-review -->
					<?php
					if( function_exists( 'pvc_post_views' ) ){ ?>
						<div class="btn-view-count">
							<?php pvc_post_views( $post_id = 0, $echo = true ); ?>
						</div> <!-- btn-view-count -->
					<?php } ?>
				</div>
			</div>
		<?php
	}
}
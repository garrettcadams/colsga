<?php
/**Widget Name: Single title-line widget
Author: Javo
Version: 1.0.0.1
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

class jvbpd_single_title_line extends Widget_Base {

	public $review_instance = NULL;

	public function get_name() {
		return 'jvbpd-single-title-line';
	}

	public function get_title() {
		return 'Title Line ( Group )';   // title to show on elementor
	}

	public function get_icon() {
		return 'eicon-gallery-group';    // eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-single-listing' ];    // category of the widget
	}

    protected function _register_controls() {
		$this->getReviewInstance();
        $this->start_controls_section(
			'section_general',
			array(
				'label' => esc_html__( 'Title Line', 'jvfrmtd' ),
			)
		);

		$this->add_control( 'Des', array(
			'type' => Controls_Manager::RAW_HTML,
			'raw'  => sprintf(
				'<div class="elementor-jv-notice" style="background-color:#9b0a46; color:#ffc6c6; padding:10px;"><ul>'.
				'<li class="doc-link">'.
				esc_html__('How to use this widget.','jvfrmtd').
				'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-title-line/" style="color:#fff;"> ' .
				esc_html__( 'Documentation', 'jvfrmtd' ) .
				'</a></li><li>&nbsp;</li>'.
				'<li class="notice">'.
				esc_html__('This widget is for only single listing detail page.', 'jvfrmtd').
				'<a target="_blank" href="http://doc.wpjavo.com/listopia/elementor-notice/" style="color:#fff;"> ' .
				esc_html__( 'Detail', 'jvfrmtd' ) .
				'</a><br/></li><li>&nbsp;</li><li>'.
				esc_html__( 'Please do not use in other pages.', 'jvfrmtd' ) .
				'</li></ul></div>'
			)
		) );

        $this->add_responsive_control( 'width', Array(
            'label' => __( "Button's Width", 'jvfrmtd' ),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => 260,
            ],
            'description' => __( 'Default : 260px to show 4 icons and hide others', 'jvfrmtd'),
            'range' => [
                'px' => [
                    'min' => 50,
                    'max' => 360,
                    'step' => 1,
                ],
                '%' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'size_units' => [ 'px', '%' ],
            'selectors' => [
                '{{WRAPPER}} .javo-core-single-featured-switcher' => 'width: {{SIZE}}{{UNIT}};',
            ],
		) );

		$this->add_control( 'header_switchers', Array(
			'label' => __( 'Header Switchers', 'jvfrmtd' ),
			'type' => Controls_Manager::REPEATER,
			'fields' => Array(
				Array(
					'name' => 'list_class',
					'label' => __( 'Button Type', 'jvfrmtd' ),
					'type' => Controls_Manager::SELECT,
					'options' => jvbpd_elements_tools()->getHeaderSwitcherOptions(),
				),
				Array(
					'name' => 'list_title',
					'label' => esc_html__( "Icon Title", 'jvfrmtd' ),
					'type' => Controls_Manager::TEXT,
					'default' => '',
					'label_block' => true,
				),
				Array(
					'name' => 'list_icon',
					'label' => esc_html__( "Icon", 'jvfrmtd' ),
					'type' => Controls_Manager::TEXT,
					'default' => '',
					'label_block' => true,
				),
			),
			'title_field' => 'Button : {{{ list_title }}}',
		) );

		$this->add_control(
			'second_width',
			[
				'label' => __( "Second Button's Width", 'jvfrmtd' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 500,
				],
				'description' => __( 'Default : 260px to show 4 icons and hide others', 'jvfrmtd'),
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 800,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .title-line-btns' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control( 'second_header_switchers', Array(
			'label' => esc_html__( 'Header Buttons', 'jvfrmtd' ),
			'type' => Controls_Manager::REPEATER,
			'fields' => Array(
				Array(
					'name' => 'list_class',
					'label' => __( 'Button Type', 'jvfrmtd' ),
					'type' => Controls_Manager::SELECT,
					'options' => Array(
						'' => esc_html__( 'Select a button', 'jvfrmtd' ),
						'score-review' => esc_html__( 'Average', 'jvfrmtd' ),
						'share' =>  esc_html__( 'Share', 'jvfrmtd' ),
						'amount-review' => esc_html__( 'Ratings', 'jvfrmtd' ),
						'submit-review' => esc_html__( 'Submit Reivew', 'jvfrmtd' ),
						'favorite' => esc_html__( 'Favorite', 'jvfrmtd' ),
						'favorite_count' => esc_html__( 'Favorite Count', 'jvfrmtd' ),
						'post_count' => esc_html__( 'Post count', 'jvfrmtd' ),
					),
				),
				Array(
					'name' => 'list_title',
					'label' => esc_html__( "Icon Title", 'jvfrmtd' ),
					'type' => Controls_Manager::TEXT,
					'default' => '',
					'label_block' => true,
				),
				Array(
					'name' => 'review_landing_id',
					'label' => esc_html__( "Landing ID", 'jvfrmtd' ),
					'type' => Controls_Manager::TEXT,
					'default' => '',
					'label_block' => true,
					'condition' => [
						'list_class' => 'submit-review',
					],
					'description' => esc_html__("Please add an ID (without #) of section to move. It should be a section of review area", "jvfrmtd"),
				),
			),
			'title_field' => 'Button : {{{ list_title }}}',
		) );

		$this->end_controls_section();
    }

	public function getReviewInstance() {
		$instance = false;
		if( function_exists( 'lv_directoryReview' ) ) {
			$instance = lv_directoryReview();
		}
		$this->review_instance = $instance;
	}

    protected function render() {
		jvbpd_elements_tools()->switch_preview_post();
		$this->getReviewInstance();
		$settings = $this->get_settings();
		$this->getContent( $settings, get_post() );
		jvbpd_elements_tools()->restore_preview_post();
    }

	public function verify( $section='' ) {
		$result = true;
		$addons = Array(
			'3dview' => Array(
				'meta_key' => '_3dViewer',
				'core' => 'Lava_Directory_3DViewer',
			),
			'video' => Array(
				'meta_key' => Array( '_video_portal', '_video_id' ),
				'core' => 'Lava_Directory_Video',
			),
		);
		if( array_key_exists( $section, $addons ) ) {
			$result = false;
			// Check this plugin activated
			if( class_exists( $addons[ $section ][ 'core' ] ) ) {
				if( is_array( $addons[ $section ][ 'meta_key' ] ) ) {
					$value = true;
					foreach( $addons[ $section ][ 'meta_key' ] as $meta_key ) {
						$getMeta = get_post_meta( get_the_ID(), $meta_key, true );
						$value = $value && !empty( $getMeta );
					}
				}else{
					$value = get_post_meta( get_the_ID(), $addons[ $section ][ 'meta_key' ], true );
				}
				$result = !empty( $value );
			}
		}
		return $result;
	}

	public function render_meta_detail() {

		$element_prefix = 'jvbpd_widget_single_title_line_';
		foreach(
			Array(
				'share',
				'favorite',
				'favorite_count',
				'post_count',
				'amount_review',
				'submit_review',
				'score_review',
			) as $callbackName
		) {
			add_filter( $element_prefix . $callbackName, array( $this, $callbackName ), 10, 2 );
		}

		$metaRender = Array();
		$detailMetaLists = $this->get_settings( 'second_header_switchers' );
		if( ! is_array( $detailMetaLists ) ) {
			return false;
		}

		foreach( $detailMetaLists as $metaItem ) {
			$metaItem = wp_parse_args( $metaItem, Array(
				'list_class' => false,
				'list_icon' => false,
			));
			$callback = str_replace( '-', '_', $metaItem[ 'list_class' ] );
			if( ! method_exists( $this, $callback ) ) {
				continue;
			}
			if( $callback == 'share' ) {
				add_action( 'wp_footer', array( __CLASS__, 'share_modal' ) );
			}
			$item_render = '';
			$item_render .= '<li>';
			$item_render .= apply_filters( $element_prefix . $callback, null, $metaItem );
			$item_render .= '</li>';
			$metaRender[] = $item_render;
		}
		$output = '';
		$output .= '<div class="scrolltabs"><ul class="title-line-btns pull-right responsive-tabdrop">';
		$output .= join( '', $metaRender );
		$output .= '</ul></div>';
		echo $output;
	}

	public function favorite( $output='', $meta=Array() ) {

		if( ! class_exists( '\lvDirectoryFavorite_button' ) ) {
			return $output;
		}

		ob_start(); ?>
		<div class="btn-favorite">
			<?php
			$objFavorite = new \lvDirectoryFavorite_button(
				Array(
					'post_id' => get_the_ID(),
					'show_count' => true,
					'save' => sprintf( "<i class='fa fa-bookmark-o'></i> %s", esc_html__( "Save", 'jvfrmtd' ) ),
					'unsave' => sprintf( "<i class='fa fa-bookmark'></i> %s", esc_html__( "Unsave", 'jvfrmtd' ) ),
					'class' => Array( 'btn', 'lava-single-page-favorite', 'admin-color-setting-hover' ),
				)
			);
			add_filter( 'lava_' . get_post_type() . '_favorite_button_template', array( $this, 'favorite_template' ) );
			$objFavorite->output();
			remove_filter( 'lava_' . get_post_type() . '_favorite_button_template', array( $this, 'favorite_template' ) ); ?>
		</div> <!-- btn-favorite -->

		<?php
		return ob_get_clean();
	}

	public function share( $output='', $meta=Array() ) {
		return sprintf( '<div class="btn-%1$s"><button type="button" class="btn btn-block admin-color-setting-hover jvbpd-single-share-opener"><i class="%2$s"></i> <span>%3$s</span></button></div>', $meta['list_class'], $meta['list_icon'], $meta['list_title'] );
	}

	public function amount_review( $output='', $meta=Array() ) {
		$amount = 0;
		if( function_exists( 'lava_directory' ) ){
			$amount = intVal( lava_directory()->admin->reviewCount( get_the_ID() ) );
		}
		return sprintf( '<div class="btn-amount-review"><a href="#javo-item-review-section" class="admin-color-setting-hover">%1$s %2$s</a></div>', $amount, esc_html__( " Ratings", 'jvfrmtd' ) );
	}

	public function submit_review( $output='', $meta=Array() ) {
		return sprintf( '<div class="btn-submit-review"><a href="#%2$s" class="admin-color-setting-hover"><i class="jvbpd-icon1-comment-o"></i> %1$s</a></div>', esc_html__( "Submit Review", 'jvfrmtd' ), $meta['review_landing_id']);
	}

	public function score_review( $output='', $meta=Array() ) {
		$nowScore = 0;
		$maxScore = 5;

		if( $this->review_instance ) {
			$nowScore = get_post_meta( get_the_ID(), 'rating_average', true );
		}

		return sprintf( '<div class="btn-score-review"><a href="#javo-item-review-section" class="admin-color-setting-hover">%1$s / %2$s</a></div>', number_format( floatVal( $nowScore ), 1 ), $maxScore );
	}

	public function post_count( $output='', $meta=Array() ) {
		$count = 0;
		if(function_exists('pvc_get_post_views')) {
			$count = pvc_get_post_views(get_the_ID());
		}
		return sprintf('<div class="btn-view-count"><a>%1$s</a></div', $count);
	}

	public function favorite_count($output='', $meta=Array()) {
		$count = get_post_meta(get_the_ID(), '_save_count', true);
		return sprintf('<div class="btn-favorite-count"><a>%1$s</a></div>', intVal($count));
	}

	public function getContent( $settings, $obj ) {
		if( class_exists( '\lvDirectoryVideo_Render' ) ) {
			$objVideo = new \lvDirectoryVideo_Render( $obj, array(
				'width' => '100',
				'height' => '100',
				'unit' => '%',
			) );
			$is_has_video = $objVideo->hasVideo();
		}else{
			$is_has_video = false;
		}
		if( class_exists( '\lvDirectory3DViewer_Render' ) ) {
			$obj3DViewer = new \lvDirectory3DViewer_Render( $obj );
			$is_has_3d = $obj3DViewer->viewer;
		}else{
			$is_has_3d = false;
		} ?>
		 <div class="single-item-tab-feature-bg-wrap <?php echo isset($block_meta) ? $block_meta : ''; ?>">
			<div class="single-item-tab-bg" <?php echo isset($single_addon_options['background_transparent']) && $single_addon_options['background_transparent'] == 'disable' ? 'style="bottom:0 !important;"' : ''; ?>>
				<div class="container captions">
					<div class="header-inner <?php if(class_exists( 'Lava_Directory_Review' )) echo 'jv-header-rating'; ?>">
						<div class="item-bg-left pull-left text-left">
							<div class="uppercase">
								<?php
								$imgCompanyLogo = get_post_meta( get_the_ID(), '_logo', true );
								$imgCompanyLogoSrc = wp_get_attachment_image_url( $imgCompanyLogo );
								$strALT = '';
								if($imgCompanyLogoSrc != ''){
									$strALT = explode("/",$imgCompanyLogoSrc);
									$strALT = array_pop($strALT);
								}
								if( $imgCompanyLogoSrc ) {
									printf( '<div class="logo inline-block"><img src="%1$s" class="rounded-circle" width="50" height="50" alt="%2$s"></div>', $imgCompanyLogoSrc, $strALT );
								} ?>

								<h1 class="jv-listing-title"><?php  echo get_the_title(); ?></h1>
								<?php
								if( function_exists( 'lava_directory_get_edit_page' ) && ( get_post()->post_author == get_current_user_id() || current_user_can( 'manage_options' ) ) ) {
									?>
									<div class="edit-button">
										<a href="<?php echo lava_directory_get_edit_page( get_the_ID() ); ?>">
											<i class="fa fa-pencil"></i>
											<?php esc_html_e( "Edit", 'litopia' ); ?>
										</a>
									</div>
									<?php
								} ?>
								<?php
								$strTagLine = get_post_meta( get_the_ID(), '_tagline', true );
								if( $strTagLine ) {
									printf( '<div class="tagline jv-listing-sub-title">%1$s</div>', $strTagLine );
								} ?>
								<a href="<?php echo function_exists( 'jvbpd_getUserPage' ) && jvbpd_getUserPage( get_the_author_meta( 'ID' ) ); ?>" class="header-avatar" style="display:none;">
									<?php echo get_avatar( get_the_author_meta( 'ID' ) ); ?>
								</a>
							</div>
							<div class="jv-addons-meta-wrap">
								<?php echo apply_filters( 'jvbpd_' . get_post_type() . '_single_listing_rating', '' );?>
							</div>
						</div>
						<div class="clearfix"></div>
					</div> <!-- header-inner -->

					<?php
					$list = $this->get_settings( 'header_switchers' );
					if ( $list ) {
						echo '<div class="scrolltabs"><ul class="javo-core-single-featured-switcher float-right list-inline responsive-tabdrop">';
						foreach ( $list as $item ) {
							if( ! $this->verify( $item[ 'list_class' ] ) ) {
								continue;
							}
							echo '<li role="presentation" class="switch-'. $item['list_class'] .'"><a class="javo-tooltip" data-original-title="'. $item['list_title'] .'"><i class="'. $item['list_icon'] .'" aria-hidden="true"></i><span class="switcher-label">'. $item['list_title'] .'</span></a></li>';
						}
						echo '</ul></div>';
					} ?>
				</div> <!-- container -->

				<div class="container">
					<div class="row jvbpd-meta-details-wrap">
						<div class="col-md-4 single-header-terms">
							<?php
							foreach(
								Array(
									'listing_category' => Array(
										'icon' => 'jvbpd-icon3-desktop',
										'default' => esc_html__( "Category", 'listopia' ),
									),
									'listing_location' => Array(
										'icon' => 'jvbpd-icon3-desktop',
										'default' => esc_html__( "Location", 'listopia' ),
									),
								)
								as $strListingTerm => $arrTermMeta ) {
								$strTermLink = '#';
								$strTermLinkTarget = '_self';
								$arrCurrentTerms = wp_get_object_terms( $obj->ID, $strListingTerm, array( 'fields' => 'all' ) );
								if( !is_wp_error( $arrCurrentTerms ) && isset( $arrCurrentTerms[0] ) ) {
									$arrTermMeta[ 'default' ] = $arrCurrentTerms[0]->name;
									$strTermLink = get_term_link( $arrCurrentTerms[0], $strListingTerm );
									$strTermLinkTarget = '_blank';
								}
								printf(
									'<div class="tax-item-%1$s"><i class="%2$s"></i> <a href="%3$s" target="%4$s" title="%5$s">%5$s</a></div>',
									$strListingTerm, $arrTermMeta[ 'icon' ], esc_url_raw( $strTermLink ), $strTermLinkTarget, $arrTermMeta[ 'default' ]
								);
							} ?>
						</div>
						<div class="col-md-8 jvbpd-meta-details-right"><?php $this->render_meta_detail(); ?></div>
					</div>
				</div>
			</div> <!-- single-item-tab-bg -->
		</div>

	<?php
	}

	public static function share_modal() {
		wp_enqueue_script( jvbpdCore()->var_instance->getHandleName( 'clipboard' ) );
		printf( '<script type="text/html" id="%s">', 'jvbpd-single-share-contents' );
		jvbpdCore()->template_instance->load_template( 'part-single-share-modal' );
		printf( '</script>' );
	}

	public function favorite_template( $template='' ) {
		return '<a href="#" class="%1$s" data-save="%2$s %4$s (##)" data-saved="%3$s %4$s (##)" data-post-id="%5$s" data-show-count="%8$s">%6$s %4$s %7$s</a>';
	}
}

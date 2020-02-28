<?php
/**
* Widget Name: Single gallery
* Author: Javo
* Version: 1.0.0.1
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


class jvbpd_single_gallery extends Widget_Base {

	public function get_name() {
		return 'jvbpd-single-gallery';
	}

	public function get_title() {
		return 'Gallery (Single Listing)';   // title to show on elementor
	}

	public function get_icon() {
		return 'eicon-gallery-grid';    //   eicon-posts-ticker-> eicon ow asche icon to show on elelmentor
	}

	public function get_categories() {
		return [ 'jvbpd-single-listing' ];    // category of the widget
	}

    protected function _register_controls() {

        $this->start_controls_section(
			'section_general',
			array(
				'label' => esc_html__( 'Gallery', 'jvfrmtd' ),
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

		$this->add_control( 'render_type', Array(
			'type' => Controls_Manager::SELECT,
			'label' => esc_html__( 'Render Type', 'jvfrmtd' ),
			'default' => 'row2',
			'options' => Array(
				'row' => esc_html__( "One row - 4 cols", 'jvfrmtd' ),
				'row3col' => esc_html__( "One row - 3 cols", 'jvfrmtd' ),
				'row2' => esc_html__( "Two rows type", 'jvfrmtd' ),
				'masonry' => esc_html__( "Masorny", 'jvfrmtd' ),
			),
			'frontend_available' => true,
		));

		$aniOptions = Array();
		for($aniID=1;$aniID<=11;$aniID++){
			$aniOptions[$aniID] = sprintf(esc_html__("Effect %s", 'jvfrmtd'), $aniID);
		}
		$this->add_control( 'masonry_ani', Array(
			'label' => esc_html__( "Animation type", 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT,
			'default' => 1,
			'options' => $aniOptions,
			'condition' => Array( 'render_type' => 'masonry' ),
			'frontend_available' => true,
		));
		$this->add_responsive_control('masonry_cols', Array(
			'label' => esc_html__( "Columns", 'jvfrmtd' ),
			'type' => Controls_Manager::SELECT,
			'prefix_class' => 'columns%s-',
			'options' => jvbpd_elements_tools()->getColumnsOption(1, 4),
			'devices' => Array( 'desktop', 'tablet', 'mobile' ),
			'desktop_default' => 3,
			'tablet_default' => 2,
			'mobile_default' => 1,
			'condition' => Array( 'render_type' => 'masonry' ),
		));

		$this->end_controls_section();
	}

    protected function render() {
		jvbpd_elements_tools()->switch_preview_post();
		$settings = $this->get_settings();
		$this->add_render_attribute('wrap', Array(
			'id' => 'javo-item-detail-image-section',
			'class' => 'jvbpd-sinlge-gallery-wrap',
			'data-images' => $this->getDetailImagesJSON(),
		)); ?>
		<div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
			<?php
			echo call_user_func(Array($this, 'getHeader'));
			if(method_exists($this, $settings['render_type'])) {
				call_user_func_array(Array($this, $settings['render_type']), Array($settings));
			}
			/*
			if( 'row' == $this->get_settings( 'render_type' ) ) {
				$this->getOneRowtype();
			} elseif( 'row3col' == $this->get_settings( 'render_type' ) ) {
				$this->getOneRow3Coltype();
			}else{
				$this->getRow2type();
			} */ ?>
		</div>
		<?php
		jvbpd_elements_tools()->restore_preview_post();
	}

	public function getHeader() {
		$this->add_render_attribute('header', Array(
			'class' => 'page-header',
		));
		return sprintf(
			'<h3 %1$s>%2$s</h3>',
			$this->get_render_attribute_string('header'),
			esc_html__( "Gallery", 'jvfrmtd' )
		);
	}

	public function getDetailImages( $post_id=0 ) {
		if( ! $post_id ) {
			$post_id = get_the_ID();
		}
		$images = (Array) get_post_meta( $post_id, 'detail_images', true );
		return array_filter( $images );
	}

	public function getDetailImagesArray( $post_id=0, $images_size=Array( 400, 400 ) ) {
		$arrOutput = Array();
		$arrDetailImage = $this->getDetailImages( $post_id );
		if( !$arrDetailImage ) {
			return $arrOutput;
		}

		$images	= sizeof( $arrDetailImage );

		for( $intCount=0; $intCount< $images; $intCount++ ) {
			$arrOutput[ $intCount ] = '';
		}

		if( is_array( $arrDetailImage )){
			foreach( $arrDetailImage as $attach_index => $attach_id ) {
				$image_size = $images_size;
				if('rand' == $image_size) {
					switch($attach_index % 3) {
						case 0: $image_size = 'jvbpd-box-v'; break;
						case 1: $image_size = 'jvbpd-large-v'; break;
						case 2: $image_size = 'jvbpd-large'; break;
					}
				}
				if( $strBuffer = wp_get_attachment_image( $attach_id, $image_size, 1, Array( 'class' => 'img-responsive' ) ) )
					$arrOutput[ $attach_index ] = $strBuffer;
			}
		}
		return $arrOutput;
	}

	public function getDetailImagePart( $index=0 ) {
		$arrImages = $this->getDetailImagesArray( get_the_ID(), 'jvbpd-box-v' );
		return isset( $arrImages[ $index ] ) ? $arrImages[ $index ] : '';
	}

	public function getDetailImagesJSON( $post_id=0 ) {
		if(0 == $post_id) {
			$post_id = get_the_ID();
		}
		$arrOutput = Array();
		$arrDetailImage = $this->getDetailImages( $post_id );
		if( !empty( $arrDetailImage ) ) : foreach( $arrDetailImage as $attach_id ) {
			$strFullSize = wp_get_attachment_image_src( $attach_id, 'full' );
			$strThumbSize = wp_get_attachment_image_src( $attach_id, 'jvbpd-tiny' );
			if( !empty( $strFullSize[0] ) ) {
				$arrOutput[] = Array(
					'src' => $strFullSize[0],
					'thumb' => $strThumbSize[0]
				);
			}
		} endif;
		return esc_attr( json_encode( $arrOutput ) );
	}

	public function row() {
		?>
		<div class="row">
			<div class="col-md-3 col-3">
				<a class="link-display">
					<?php echo $this->getDetailImagePart(); ?>
				</a>
			</div>
			<div class="col-md-3 col-3">
				<a class="link-display">
					<?php echo $this->getDetailImagePart(1); ?>
				</a>
			</div>
			<div class="col-md-3 col-3">
				<a class="link-display">
					<?php echo $this->getDetailImagePart(2); ?>
				</a>
			</div>
			<div class="col-md-3 col-3">
				<a class="link-display">
					<?php echo $this->getDetailImagePart(3); ?>
					<div class="overlay">
						<?php
						if( sizeof( $this->getDetailImages() ) > 4 ) {
							printf( '<span>+ %s %s</span>', sizeof( $this->getDetailImages() ) - 4, esc_html__( "More",'jvfrmtd' ));
						} ?>
					</div>
				</a>
			</div>
		</div>
		<?php
	}
	public function row3col() {
		?>
		<div class="row">
			<div class="col-md-4 col-4">
				<a class="link-display">
					<?php echo $this->getDetailImagePart(); ?>
				</a>
			</div>
			<div class="col-md-4 col-4">
				<a class="link-display">
					<?php echo $this->getDetailImagePart(1); ?>
				</a>
			</div>
			<div class="col-md-4 col-4">
				<a class="link-display">
					<?php echo $this->getDetailImagePart(3); ?>
					<div class="overlay">
						<?php
						if( sizeof( $this->getDetailImages() ) > 3 ) {
							printf( '<span>+ %s %s</span>', sizeof( $this->getDetailImages() ) - 3, esc_html__( "More",'jvfrmtd' ));
						} ?>
					</div>
				</a>
			</div>
		</div>
		<?php
	}

	public function row2() {
		?>
		<div class="row">
			<div class="col-md-4 col-4">
				<a class="link-display">
					<?php echo $this->getDetailImagePart(); ?>
				</a>
			</div>
			<div class="col-md-4 col-4">
				<a class="link-display">
					<?php echo $this->getDetailImagePart(1); ?>
				</a>
			</div>
			<div class="col-md-4 col-4">
				<a class="link-display">
					<?php echo $this->getDetailImagePart(2); ?>
				</a>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 col-6">
				<a class="link-display">
					<?php echo $this->getDetailImagePart(3); ?>
				</a>
			</div>
			<div class="col-md-6 col-6">
				<a class="link-display">
					<?php echo $this->getDetailImagePart(4); ?>
					<div class="overlay">
						<?php
						if( sizeof( $this->getDetailImages() ) > 5 ) {
							printf( '<span>+ %s %s</span>', sizeof( $this->getDetailImages() ) - 5, esc_html__( "More",'jvfrmtd' ));
						} ?>
					</div>
				</a>
			</div>
		</div>
		<?php
	}

	public function masonry() {
		$this->add_render_attribute('masonry-wrap', 'class', 'mansory-wrap');
		$this->add_render_attribute('gallery-item', 'class', Array('gallery-item' /*, 'w-25', 'p-1' */));
		$this->add_render_attribute('gallery-link', Array(
			'class' => Array('gallery-link', 'link-display', 'd-block', 'position-relative'),
			'style' => 'overflow:hidden;',
		) );
		// $arrImages = $this->getDetailImagesArray( get_the_ID(), 'rand');
		$arrImages = $this->getDetailImagesArray( get_the_ID(), 'full');
		$output = sprintf('<ul %s>', $this->get_render_attribute_string('masonry-wrap'));
		foreach($arrImages as $image) {
			$output .= sprintf(
				'<li %1$s><a %2$s>%3$s</a></li>',
				$this->get_render_attribute_string('gallery-item'),
				$this->get_render_attribute_string('gallery-link'),
				$image
			);
		}
		$output .= '</ul>';
		echo $output;
	}
}

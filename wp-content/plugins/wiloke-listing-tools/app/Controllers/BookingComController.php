<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Models\BookingCom;

class BookingComController extends Controller {
	protected $bookingComID = null;
	public function __construct() {
		add_action('wilcity/deleted/listing', array($this, 'deleteRelatedBookingComCreator'));
		add_action('after_delete_post', array($this, 'updateBookingComCreatorDependsOnListingStatus'), 10, 1);
		add_action('wp_enqueue_scripts', array($this, 'printBannerSettings'));
	}

	protected function isIncludeBookingDotCom($postID){
		$this->bookingComID = BookingCom::getCreatorIDByParentID($postID);
		if ( empty($this->bookingComID) ){
			return false;
		}

		if ( !GetSettings::isPlanAvailableInListing($postID, 'bookingcombannercreator') ){
			return false;
		}

		return true;
	}

	public function printBannerSettings(){
		if ( !is_singular() ){
			return false;
		}
		global $post;
		if ( !$this->isIncludeBookingDotCom($post->ID) ){
			return false;
		}

		$bcCopyColor = get_post_meta( $this->bookingComID, '_bdotcom_bc_mbe_copy_colour', true );
		$bcCopyColor = empty($bcCopyColor) ? $bcCopyColor : BDOTCOM_BC_DEFAULT_COPY_COLOUR;

		$mceButton =  get_post_meta( $this->bookingComID, '_bdotcom_bc_mbe_button', true );
		$mceButton = ( !empty( $mceButton  ) && ( $mceButton == 'yes'  || $mceButton == 1 || $mceButton == 'on' ) ) ? true : false ;

		$mceBorderColor  = get_post_meta( $this->bookingComID, '_bdotcom_bc_mbe_button_border_colour', true );
		$mceBorderColor = empty($mceBorderColor) ? BDOTCOM_BC_DEFAULT_BUTTON_BORDER_COLOUR : $mceBorderColor;
		$mceButtonCopyColor  = get_post_meta( $this->bookingComID, '_bdotcom_bc_mbe_copy_colour', true );
		$mceButtonCopyColor = empty($mceButtonCopyColor) ? BDOTCOM_BC_DEFAULT_COPY_COLOUR : $mceButtonCopyColor;

		$mceButtonBg  = get_post_meta( $this->bookingComID, '_bdotcom_bc_mbe_button_bg', true );
		$mceButtonBg = empty($mceButtonBg) ? BDOTCOM_BC_DEFAULT_BUTTON_BG : $mceButtonBg;
		$bdotcom_bc_mbe_edit_css  = get_post_meta( $this->bookingComID, '_bdotcom_bc_mbe_edit_css', true );
		$bookingPath = get_post_meta( $this->bookingComID, '_bdotcom_bc_mbe_img_path', true );
		$mceThemes = get_post_meta( $this->bookingComID, '_bdotcom_bc_mbe_themes', true );
		$bannerImg = '';
		if ( !empty( $mceThemes ) && $mceThemes == 'custom_theme' ) {
			$bannerImg = $bookingPath;
		} elseif ( $mceThemes != BDOTCOM_BC_DEFAULT_THEME && $mceThemes != 'custom_theme' ) {
			$aDefaultImgs = bdotcom_bc_default_image_paths();
			foreach( $aDefaultImgs as $aDefImg ) {
				if( $mceThemes == $aDefImg[0] ) {
					$bannerImg = $aDefImg[2] ;
				}
			}
		}

		else {// default image when no other image is chosen
			$bannerImg = BDOTCOM_BC_DEFAULT_THEME ;
		}

		$style = '';
		$style .= '#bdotcom_bc_mbe_banner_' .  $this->bookingComID . ' { background-image: url("' .  $bannerImg . '");}';
		$style .= '#bdotcom_bc_mbe_banner_' .  $this->bookingComID . ' .bdotcom_bc_copy_wrapper .bdotcom_bc_copy,
                 #bdotcom_bc_mbe_banner_' .  $this->bookingComID . ' .bdotcom_bc_copy_wrapper .bdotcom_bc_copy h1,
                 #bdotcom_bc_mbe_banner_' .  $this->bookingComID . ' .bdotcom_bc_copy_wrapper .bdotcom_bc_copy h2,
                 #bdotcom_bc_mbe_banner_' .  $this->bookingComID . ' .bdotcom_bc_copy_wrapper .bdotcom_bc_copy h3,
                 #bdotcom_bc_mbe_banner_' .  $this->bookingComID . ' .bdotcom_bc_copy_wrapper .bdotcom_bc_copy h4,
                 #bdotcom_bc_mbe_banner_' .  $this->bookingComID . ' .bdotcom_bc_copy_wrapper .bdotcom_bc_copy h5,
                 #bdotcom_bc_mbe_banner_' .  $this->bookingComID . ' .bdotcom_bc_copy_wrapper .bdotcom_bc_copy h6,
                 #bdotcom_bc_mbe_banner_' .  $this->bookingComID . ' .bdotcom_bc_copy_wrapper .bdotcom_bc_copy p,
                 #bdotcom_bc_mbe_banner_' .  $this->bookingComID . ' .bdotcom_bc_copy_wrapper .bdotcom_bc_copy div { color: ' .  $bcCopyColor . ';}';


		if( $mceButton ) {
			$style .= '#bdotcom_bc_mbe_banner_' .  $this->bookingComID . ' .bdotcom_bc_mbe_button {' ;
			if( !empty( $mceButton_border_width ) && is_numeric( $mceButton_border_width ) ) {
				$style .= 'border:' . $mceButton_border_width . 'px ' . $mceBorderColor . ' solid;' ;
			}
			$style .= 'color:' . $mceButtonCopyColor . ' ;' ;
			$style .= 'background:' . $mceButtonBg . ' ; }' ;
		}

		$style .= $bdotcom_bc_mbe_edit_css ;// custom css to refine the banner if needed

		wp_add_inline_style('bdotcom_bc_general_css', $style);
	}

	public function updateBookingComCreatorDependsOnListingStatus($postID){
		$aPostTypes = General::getPostTypeKeys(false, false);
		$postStatus = get_post_status($postID);

		if ( !in_array($postStatus, $aPostTypes) ){
			return false;
		}
		$bookingID = BookingCom::getCreatorIDByParentID($postID);

		if ( empty($bookingID) ){
			return false;
		}

		wp_delete_post($bookingID, true);
	}

	public function deleteRelatedBookingComCreator($listingID){
		$bookingID = BookingCom::getCreatorIDByParentID($listingID);
		if ( !empty($bookingID) ){
			wp_delete_post($bookingID, true);
		}
	}
}
<?php

namespace WILCITY_APP\Controllers;


use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Framework\Upload\Upload;

class ImageController extends Controller {
	use VerifyToken;
	use ParsePost;

	public function __construct() {
		add_action( 'rest_api_init', function () {
			register_rest_route( WILOKE_PREFIX.'/v2', 'image', array(
				'methods' => 'POST',
				'callback' => array($this, 'uploadImage'),
			));
		} );
	}

	public function uploadImage(){
		$oToken = $this->verifyPermanentToken();
		if ( !$oToken ){
			return $this->tokenExpiration();
		}
		$oToken->getUserID();
		$aData = $this->parsePost();
		if ( empty($aData) ){
			return false;
		}

		foreach ($aData as $img){
			var_export($img);
		}

//		$instUploadImg = new Upload();
//		$instUploadImg->userID = $oToken->getUserID();
//		$instUploadImg->aData['uploadTo'] = $instUploadImg::getUserUploadFolder();
//
//		$instUploadImg->aData['aFile'] = $aFile;
//		$imgID = $instUploadImg->uploadFakeFile();
	}
}

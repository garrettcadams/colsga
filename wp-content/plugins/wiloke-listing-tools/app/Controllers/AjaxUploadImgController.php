<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Framework\Upload\Upload;
use WilokeListingTools\Frontend\User;

class AjaxUploadImgController extends Controller {
	private $aValidFileTypes = array('image/jpeg', 'image/png', 'image/gif', 'image/jpg');

	public function __construct() {
		add_action('wp_ajax_wilcity_ajax_upload_imgs', array($this, 'uploadImgsViaAjax'));
		add_action('wp_ajax_wilcity_delete_attachment', array($this, 'deleteAttachment'));
	}

	public function deleteAttachment(){
		if ( !isset($_POST['id']) || empty($_POST['id']) ){
			wp_send_json_error();
		}

		$id = abs($_POST['id']);

		if ( get_post_field('post_author', $id) != User::getCurrentUserID() ){
			wp_send_json_error();
		}

		wp_delete_attachment($id);
		wp_send_json_success();
	}

	private function deletePreviousImg(){
		if ( isset($_GET['previous']) && !empty($_GET['previous']) && $_GET['previous'] !== 'undefined' ){
			if ( get_post_field('post_author', $_GET['previous']) == User::getCurrentUserID() ){
				wp_delete_attachment($_GET['previous']);
			}
		}
	}

	public function uploadImgsViaAjax(){
		$this->deletePreviousImg();
		if(!is_array($_FILES)) {
			wp_send_json_error(array(
				'msg' => esc_html__('You need to upload 1 image at least', 'wiloke-listing-tools')
			));
		}

		$aGalleries = array();
		$aErrors = array();

		foreach ($_FILES as $aFile){
			if ( !in_array($aFile['type'], $this->aValidFileTypes) ){
				$aErrors[] = array(
					'name' => $aFile['name'],
					'msg'  => esc_html__('Invalid File Type', 'wiloke-listing-tools')
				);
			}

			$instUploadImg = new Upload();
			$instUploadImg->userID = get_current_user_id();
			$instUploadImg->aData['uploadTo'] = $instUploadImg::getUserUploadFolder();
			$instUploadImg->aData['aFile'] = $aFile;
			$imgID = $instUploadImg->uploadFakeFile();

			if ( is_numeric($imgID) ){
				$aGalleries[] = array(
					'src' => wp_get_attachment_image_url($imgID, 'thumbnail'),
					'id'  => $imgID
				);
			}else{
				$aErrors[] = array(
					'name' => $aFile['name'],
					'msg'  => $imgID
				);
			}
		}

		if ( empty($aGalleries) ){
			wp_send_json_error(array(
				'msg' => esc_html__('Unfortunately, We could not upload your images. Possible reason: Wrong Image Format or Your image is exceeded the allowable file size.', 'wiloke-listing-tools')
			));
		}else{
			wp_send_json_success(array(
				'aImgs' => $aGalleries,
				'aErrors' => $aErrors
			));
		}
	}
}
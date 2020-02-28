<?php
namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Upload\Upload;
use WilokeListingTools\Frontend\User;

trait InsertImg {
	protected function isImgData($aRawImage){
		if ( !is_array($aRawImage) ){
			return false;
		}

		if ( isset($aRawImage[0])  && isset($aRawImage[0]['imgID'])){
			return true;
		}

		if ( isset($aRawImage[0])  && isset($aRawImage[0]['id'])){
			return true;
		}

		if ( isset($aRawImage[0])  && isset($aRawImage[0]['fileType']) ){
			return true;
		}

		return false;
	}

	protected function migrateImage($imgSrc){
		$wp_upload_dir = wp_upload_dir();
		$aParseImgSrc = explode('/', $imgSrc);
		$filename = end($aParseImgSrc);
		$filetype = wp_check_filetype( $filename, null );

		if ( is_file($wp_upload_dir['path'] . '/' . $filename) ){
			global $wpdb;
			$postTitle = preg_replace( '/\.[^.]+$/', '', $filename );

			$postID = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT ID FROM $wpdb->posts WHERE post_title=%s and post_mime_type=%s",
					$postTitle, $filetype['type']
				)
			);

			return array(
				'id'    => $postID,
				'src'   => $wp_upload_dir['url'] . '/' . $filename
			);
		}

		$ch = curl_init ($imgSrc);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
		$raw=curl_exec($ch);
		curl_close ($ch);
		$fp = fopen($wp_upload_dir['path'] . '/' . $filename,'x');
		$writeStatus = fwrite($fp, $raw);
		fclose($fp);

		if ( $writeStatus === false ){
			return false;
		}


		// Get the path to the upload directory.
		// Prepare an array of post data for the attachment.
		$attachment = array(
			'post_mime_type' => $filetype['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', $filename ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);

		// Insert the attachment.
		$attach_id = wp_insert_attachment( $attachment, $wp_upload_dir['path'] . '/' . $filename);

		// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		$imagenew = get_post( $attach_id );
		if ( empty($imagenew) ){
			return array(
				'id' => $attach_id,
				'src' => $wp_upload_dir['url'] . '/' . $filename
			);
		}
		$fullsizepath = get_attached_file( $imagenew->ID );
		$attach_data = wp_generate_attachment_metadata( $attach_id, $fullsizepath );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		return array(
			'id' => $attach_id,
			'src' => $wp_upload_dir['url'] . '/' . $filename
		);
	}

	protected function insertImg($aRawImage){
		if ( empty($aRawImage) || ( isset($aRawImage[0]['imgID']) && empty($aRawImage[0]['imgID']) ) ){
			return false;
		}
		if ( isset($aRawImage[0])  && isset($aRawImage[0]['id']) ){

			$user = wp_get_current_user();

			$aRoles = $user->roles;

			if ( in_array('administrator', $aRoles) || get_post_field('post_author', $aRawImage[0]['id']) ==  User::getCurrentUserID() ){
				return array(
					'src' => wp_get_attachment_image_url($aRawImage[0]['id'], 'large'),
					'id'  => $aRawImage[0]['id']
				);
			}

			return false;
		}

		$instUploadImg = new Upload();

		$instUploadImg->userID = get_current_user_id();
		$instUploadImg->aData['imageData']  = $aRawImage[0]['src'];
		$instUploadImg->aData['fileName']   = $aRawImage[0]['fileName'];
		$instUploadImg->aData['fileType']   = $aRawImage[0]['fileType'];
		$instUploadImg->aData['uploadTo']   = $instUploadImg::getUserUploadFolder();

		$id = $instUploadImg->image();
		return array(
			'src' => wp_get_attachment_image_url($id, 'large'),
			'id'  => $id
		);
	}
}
<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Upload\Upload;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Framework\Helpers\Validation;

trait InsertGallery{
	protected function insertGallery(){
		if ( empty($this->aRawGallery) ){
			SetSettings::deletePostMeta($this->listingID, 'gallery');
			return false;
		}

		$aNewGalleryID = array();
		$aExistingImgs = GetSettings::getPostMeta($this->listingID, 'gallery');

		$aNewUploadedImgs = array();

		foreach ($this->aRawGallery as $aGallery){
			if ( isset($aGallery['imgID']) && !empty($aGallery['imgID']) ){
				$this->aGallery[$aGallery['imgID']] = $aGallery['src'];
				$aNewGalleryID[] = $aGallery['imgID'];
			}else if( isset($aGallery['id']) && !empty($aGallery['id']) ){
				if ( Validation::isPostAuthor(get_post_field('post_author', $aGallery['id'])) ){
					$aNewGalleryID[] = $aGallery['id'];
					$aNewUploadedImgs[] = $aGallery['id'];
					$this->aGallery[$aGallery['id']] = wp_get_attachment_image_url($aGallery['id'], 'large');
				}
			}else{
				$instUploadImg = new Upload();
				$instUploadImg->userID = get_current_user_id();
				$instUploadImg->aData['imageData']  = $aGallery['src'];
				$instUploadImg->aData['fileName']   = $aGallery['fileName'];
				$instUploadImg->aData['fileType']   = $aGallery['fileType'];
				$instUploadImg->aData['uploadTo']   = $instUploadImg::getUserUploadFolder();
				$imgID = $instUploadImg->image();
				$this->aGallery[$imgID] = wp_get_attachment_image_url($imgID, 'large');
				$aNewGalleryID[] = $imgID;
				$aNewUploadedImgs[] = $imgID;
			}
		}

		if ( !empty($aExistingImgs) && (!empty($aNewUploadedImgs) || count($aNewGalleryID) < count($aExistingImgs)) ){
			$aExistingImgIds = array_keys($aExistingImgs);
			foreach ($aExistingImgIds as $oldID){
				if ( !in_array($oldID, $aNewGalleryID) ){
					Upload::deleteImg($oldID);
					$aDeletedImgs[] = $oldID;
				}
			}
		}

		if ( !empty($this->aGallery) ){
			SetSettings::setPostMeta($this->listingID, 'gallery', $this->aGallery);
		}
	}
}
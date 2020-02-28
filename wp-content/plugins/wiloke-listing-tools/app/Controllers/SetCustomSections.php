<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\SetSettings;

trait SetCustomSections {
	protected function setCustomSections(){
		if ( empty($this->aCustomSections) ){
			return false;
		}

		foreach ($this->aCustomSections as $sectionKey => $val){
			if ( empty($val) ){
				SetSettings::deletePostMeta($this->listingID, $sectionKey, wilokeListingToolsRepository()->get('addlisting:customMetaBoxPrefix'));
			}else{
				if ( $this->isImgData($val) ){
					$aImgData = $this->insertImg($val);
					if ( is_array($aImgData) ){
						SetSettings::setPostMeta($this->listingID, $sectionKey, $aImgData['src'], wilokeListingToolsRepository()->get('addlisting:customMetaBoxPrefix'));
						SetSettings::setPostMeta($this->listingID, $sectionKey . '_id', $aImgData['id'], wilokeListingToolsRepository()->get('addlisting:customMetaBoxPrefix'));
					}
				}else{
					SetSettings::setPostMeta($this->listingID, $sectionKey, $val, wilokeListingToolsRepository()->get('addlisting:customMetaBoxPrefix'));
				}
			}

		}
	}
}
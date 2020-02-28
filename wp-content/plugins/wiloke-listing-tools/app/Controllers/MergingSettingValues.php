<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\MetaBoxes\Listing;
use WilokeListingTools\Models\BookingCom;
use WilokeListingTools\Models\Coupon;
use WilokeListingTools\Models\EventModel;

trait MergingSettingValues {
	protected $bookingID = null;
	/**
	 * It's important step. He gets the addlisting configuration value and merge to vue options
	 */
	private function isDisable($aField){
		return isset($aField['toggle']) && $aField['toggle'] == 'disable';
	}

	protected function parseOption($option){
		$option = trim($option);
		if ( strpos($option, ':') !== false ){
			$aOption = explode(':', $option);
			return array(
				'value' => $aOption[0],
				'name'=> $aOption[1]
			);
		}
		return array(
			'name' => $option,
			'value'=> $option
		);
	}

	protected function getCouponVal($key){
		if ( empty($this->listingID) ){
			return '';
		}


		$val = Coupon::getCoupon($this->listingID, $key);

		if ( empty($val) ){
			return '';
		}

		if ( $key != 'popup_image' ){
			return $val;
		}

		if ( !empty($val) ){
			return array(
				array(
					'imgID' => $val,
					'src'   => wp_get_attachment_image_url($val, 'thumbnail')
				)
			);
		}
	}

	protected function getBookingComCreatorVal($key){
		if ( empty($this->listingID) || $this->bookingID === false ){
			return '';
		}

		if ( $this->bookingID === null ){
			$this->bookingID = BookingCom::getCreatorIDByParentID($this->listingID);
		}

		if ( $this->bookingID !== false ){
			return BookingCom::getBookingComCreatorVal($this->bookingID, $key);
		}

		return '';
	}

	final protected function mergeSettingValues(){
		global $wiloke;
		$postType = '';
		if ( !empty($this->listingID) ){
			$postType = get_post_type($this->listingID);
		}else if ( isset($_REQUEST['listing_type']) ){
			$postType = $_REQUEST['listing_type'];
		}

		foreach ($this->aSections as $sectionKey => $aSection){
			if ( isset($aSection['isCustomSection']) && $aSection['isCustomSection'] == 'yes' ){
				foreach ($aSection['fields'] as $fieldKey => $aField){
					if ( !empty($this->listingID) ){
						if ( $aSection['type'] == 'listing_type_relationships' ){
							$this->aSections[$sectionKey]['fields'][$fieldKey]['value'] = get_post_meta($this->listingID, wilokeListingToolsRepository()->get('addlisting:customMetaBoxPrefix') . $aSection['key'], true);
						}else{
							$this->aSections[$sectionKey]['fields'][$fieldKey]['value'] = GetSettings::getPostMeta($this->listingID, $aSection['key'], wilokeListingToolsRepository()->get('addlisting:customMetaBoxPrefix'));
						}
					}else{
						$this->aSections[$sectionKey]['fields'][$fieldKey]['value'] = '';
					}
					$this->aSections[$sectionKey]['fields'][$fieldKey]['key'] = $fieldKey;
					$this->aSections[$sectionKey]['fields'][$fieldKey]['errMsg'] = '';

					if ( in_array($aField['type'], array('select', 'checkbox', 'checkbox2')) ){
						if ( isset($aField['options']) ){
							$aRawOptions = explode(',', $aField['options']);
							$this->aSections[$sectionKey]['fields'][$fieldKey]['options'] = array();
							foreach ($aRawOptions as $option){
								$aOption = $this->parseOption($option);
								$optionName = explode('|', $aOption['name']);

								if ( $aField['type'] == 'select' ){
									$this->aSections[$sectionKey]['fields'][$fieldKey]['options'][] = array(
										'name'  => $optionName[0],
										'value' => $aOption['value']
									);
								}else{
									$this->aSections[$sectionKey]['fields'][$fieldKey]['options'][] = array(
										'value' => $aOption['value'],
										'label' => $optionName[0]
									);
								}
							}
						} else{
							if ( !empty($this->aSections[$sectionKey]['fields'][$fieldKey]['value']) ){
								$aParseOption = is_array($this->aSections[$sectionKey]['fields'][$fieldKey]['value']) ? $this->aSections[$sectionKey]['fields'][$fieldKey]['value'] : explode(',', $this->aSections[$sectionKey]['fields'][$fieldKey]['value']);
								foreach ($aParseOption as $postID){
									$this->aSections[$sectionKey]['fields'][$fieldKey]['options'][] = array(
										'name'  => get_the_title($postID),
										'value' => $postID
									);
								}
							}
						}
					}

					if ( $aField['type'] == 'single_image' ){
						$this->aSections[$sectionKey]['fields'][$fieldKey]['value'] = array();
						$imgID = GetSettings::getPostMeta($this->listingID, $aSection['key'].'_id', wilokeListingToolsRepository()->get('addlisting:customMetaBoxPrefix') );
						if ( !empty($imgID) ){
							$url = wp_get_attachment_image_url($imgID, 'thumbnail');
							$this->aSections[$sectionKey]['fields'][$fieldKey]['value'][] = array(
								'imgID' => $imgID,
								'src'   =>  !$url ? GetSettings::getPostMeta($this->listingID, $fieldKey) : $url
							);
						}

					}
				}
			}else{
				switch ($aSection['type']){
					case 'listing_title':
						foreach ($aSection['fields'] as $fieldKey => $aField){
							$this->aSections[$sectionKey]['fields'][$fieldKey]['value'] = !empty($this->listingID) ? html_entity_decode(get_the_title($this->listingID)) : '';
							$this->aSections[$sectionKey]['fields'][$fieldKey]['errMsg'] = '';
							$this->aSections[$sectionKey]['fields'][$fieldKey]['key'] = $fieldKey;
						}
						break;
					case 'custom_button':
						foreach ($aSection['fields'] as $fieldKey => $aField){
							$realKey = str_replace('custom_button_', '', $fieldKey);
							$val = GetSettings::getPostMeta($this->listingID, $realKey);
							$this->aSections[ $sectionKey ]['fields'][ $fieldKey ]['value'] = empty($val) ? '' : $val;
						}
						break;
					case 'bookingcombannercreator':
						foreach ($aSection['fields'] as $fieldKey => $aField){
							$bookingFieldKey = str_replace('bookingcombannercreator_', '', $fieldKey);
							if ( $fieldKey == 'bookingcombannercreator_bannerImg' ){
								$this->aSections[ $sectionKey ]['fields'][ $fieldKey ]['value'] = array(
									array(
										'src'   => $this->getBookingComCreatorVal($bookingFieldKey),
										'imgID' => ''
									)
								);
							}else{
								$this->aSections[ $sectionKey ]['fields'][ $fieldKey ]['value'] = $this->getBookingComCreatorVal($bookingFieldKey);
							}
						}
						break;
					case 'coupon':
						foreach ($aSection['fields'] as $fieldKey => $aField){
							$couponKey = str_replace('coupon_', '', $fieldKey);
							$this->aSections[$sectionKey]['fields'][$fieldKey]['value'] = $this->getCouponVal($couponKey);
						}
						break;
					case 'hosted_by':
						foreach ($aSection['fields'] as $fieldKey => $aField){
							$val = GetSettings::getPostMeta($this->listingID, $fieldKey);
							$this->aSections[ $sectionKey ]['fields'][ $fieldKey ]['value'] = !empty($val) ? $val : '';
						}
						break;
					case 'event_belongs_to_listing':
						foreach ($aSection['fields'] as $fieldKey => $aField){
							$parentID = !empty($this->listingID) ? wp_get_post_parent_id($this->listingID) : '';
							if ( empty($parentID) ){
								if ( isset($_REQUEST['parentID']) && !empty($_REQUEST['parentID']) ){
									$parentID = abs($_REQUEST['parentID']);
								}
							}
							$this->aSections[$sectionKey]['fields'][$fieldKey]['errMsg'] = '';
							$this->aSections[ $sectionKey ]['fields'][ $fieldKey ]['value'] = $parentID;
							if ( !empty($parentID) ){
								$this->aSections[ $sectionKey ]['fields'][ $fieldKey ]['options'] = array(
									array(
										'name' => get_the_title($parentID),
										'value'=>$parentID
									)
								);
							}
							$this->aSections[$sectionKey]['fields'][$fieldKey]['key'] = $fieldKey;
						}
						break;
					case 'my_products':
						foreach ($aSection['fields'] as $fieldKey => $aField){
							$aMyProducts = GetSettings::getPostMeta($this->listingID, $fieldKey);

							if ( !empty($aMyProducts) ){
								$aOptions = array();
								foreach ($aMyProducts as $productID){
									$aOptions[] = array(
										'name' => get_the_title($productID),
										'value'=>$productID
									);
								}
								$this->aSections[ $sectionKey ]['fields'][ $fieldKey ]['options'] = $aOptions;
								$this->aSections[ $sectionKey ]['fields'][ $fieldKey ]['value'] = implode(',', $aMyProducts);
							}
						}
						break;
					case 'my_posts':
						foreach ($aSection['fields'] as $fieldKey => $aField){
							$aMyPosts = GetSettings::getPostMeta($this->listingID, $fieldKey);

							if ( !empty($aMyPosts) ){
								$aOptions = array();
								foreach ($aMyPosts as $postID){
									$aOptions[] = array(
										'name' => get_the_title($postID),
										'value'=>$postID
									);
								}
								$this->aSections[ $sectionKey ]['fields'][ $fieldKey ]['options'] = $aOptions;
								$this->aSections[ $sectionKey ]['fields'][ $fieldKey ]['value'] = implode(',', $aMyPosts);
							}
						}

						break;
					case 'my_room':
						foreach ($aSection['fields'] as $fieldKey => $aField){
							$myRoom = GetSettings::getPostMeta($this->listingID, $fieldKey);

							if ( !empty($myRoom) ){
								$aOptions = array();
								$aOptions[] = array(
									'name' => get_the_title($myRoom),
									'value'=>$myRoom
								);
								$this->aSections[ $sectionKey ]['fields'][ $fieldKey ]['options'] = $aOptions;
								$this->aSections[ $sectionKey ]['fields'][ $fieldKey ]['value'] = $myRoom;
							}
						}
						break;
					case 'featured_image':
						foreach ($aSection['fields'] as $fieldKey => $aField){
							$imgID = !empty($this->listingID) ? get_post_thumbnail_id($this->listingID) : '';
							$this->aSections[ $sectionKey ]['fields'][$fieldKey]['value'] = array();
							$this->aSections[$sectionKey]['fields'][$fieldKey]['errMsg'] = '';
							if ( !empty($imgID) ){
								$this->aSections[$sectionKey]['fields'][$fieldKey]['value'][] = array(
									'imgID' => $imgID,
									'src'   => wp_get_attachment_image_url($imgID, 'thumbnail')
								);
							}
							$this->aSections[$sectionKey]['fields'][$fieldKey]['key'] = $fieldKey;
						}
						break;
					case 'header':
						foreach ($aSection['fields'] as $fieldKey => $aField){
							if ( $this->isDisable($aField) ){
								unset($this->aSections[$sectionKey]['fields'][$fieldKey]);
								continue;
							}

							switch ($fieldKey){
								case 'listing_title':
									$this->aSections[$sectionKey]['fields'][$fieldKey]['value'] = !empty($this->listingID) ? get_the_title($this->listingID) : '';
									break;
								case 'logo':
								case 'cover_image':
									$imgID = GetSettings::getPostMeta($this->listingID, $fieldKey.'_id');
									$this->aSections[$sectionKey]['fields'][$fieldKey]['value'] = array();

									if ( !empty($imgID) ){
										$url = wp_get_attachment_image_url($imgID);
										$this->aSections[$sectionKey]['fields'][$fieldKey]['value'][] = array(
											'imgID' => $imgID,
											'src'   =>  !$url ? GetSettings::getPostMeta($this->listingID, $fieldKey) : $url
										);
									}

									break;
								default:
									$this->aSections[$sectionKey]['fields'][$fieldKey]['value'] = !empty($this->listingID) ? GetSettings::getPostMeta($this->listingID, $fieldKey) : '';
									break;
							}

							$this->aSections[$sectionKey]['fields'][$fieldKey]['errMsg'] = '';
							$this->aSections[$sectionKey]['fields'][$fieldKey]['key'] = $fieldKey;
						}
						break;
					case 'listing_content':
						foreach ($aSection['fields'] as $fieldKey => $aField){
							$this->aSections[ $sectionKey ]['fields'][ $fieldKey ]['value'] = !empty($this->listingID) ? get_post_field('post_content', $this->listingID) : '';
							$this->aSections[$sectionKey]['fields'][$fieldKey]['errMsg'] = '';
							$this->aSections[$sectionKey]['fields'][$fieldKey]['key'] = $fieldKey;
						}
						break;
					case 'category':
						foreach ($aSection['fields'] as $fieldKey => $aField){
							$maximumCategory = isset($aField['maximum']) && $aField['maximum'] ? abs($aField['maximum']) : 1;
							$aRawTerms = GetSettings::getTaxonomyHierarchy(array(
								'taxonomy' => $fieldKey,
								'orderby'  => $aField['orderBy'],
								'order'    => isset($aField['order']) && !empty($aField['order']) ? $aField['order'] : '',
								'parent'   => 0
							), $postType);

							if ( ! $aRawTerms ) {
								$aTerms = array(
									'' => esc_html__( 'There are no terms', 'wiloke-listing-tools' )
								);
							} else {
								$aTerms = array();
								foreach ( $aRawTerms as $oTerm ) {
									$aTerm['value'] = $oTerm->term_id;
									$aTerm['name']  = $oTerm->name;
									$aTerms[]       = $aTerm;
								}
							}
							$this->aSections[ $sectionKey ]['fields'][ $fieldKey ]['options'] = $aTerms;

							if ( !empty( $this->listingID ) ) {
								$aRawCategories = GetSettings::getPostTerms( $this->listingID, 'listing_cat' );
								if ( $aRawCategories ) {
									if ( $maximumCategory < 2 ){
										$oLastCategory = end($aRawCategories);
										$this->aSections[ $sectionKey ]['fields']['listing_cat']['category'] = $oLastCategory->term_id;
										$this->aSections[ $sectionKey ]['fields']['listing_cat']['value'] = $oLastCategory->term_id;
									}else{
										$aParseCategories = array_slice($aRawCategories, 0, $maximumCategory);

										$aGetCategories = array();
										foreach ($aParseCategories as $oCategory){
											$aGetCategories[] = $oCategory->term_id;
										}

										$this->aSections[ $sectionKey ]['fields']['listing_cat']['category'] = implode(',', $aGetCategories);
										$this->aSections[ $sectionKey ]['fields']['listing_cat']['value'] = $this->aSections[ $sectionKey ]['fields']['listing_cat']['category'];
;									}
									$aTags = GetSettings::getPostTerms($this->listingID, 'listing_tag');
									if ( $aTags ){
										foreach ($aTags as $oTag){
											$this->aSections[$sectionKey]['fields']['listing_cat']['tags'][] = $oTag->term_id;
										}
									}
								}else{
									$this->aSections[ $sectionKey ]['fields']['listing_cat']['category'] = '';
									$this->aSections[ $sectionKey ]['fields']['listing_cat']['value'] = '';
								}
							}else{
								$this->aSections[ $sectionKey ]['fields']['listing_cat']['category'] = '';
								$this->aSections[ $sectionKey ]['fields']['listing_cat']['value'] = '';
							}

							$this->aSections[$sectionKey]['fields'][$fieldKey]['errMsg'] = '';
							$this->aSections[$sectionKey]['fields'][$fieldKey]['key'] = $fieldKey;
							if ( $maximumCategory > 1 ){
								$this->aSections[$sectionKey]['fields']['listing_cat']['isMultiple'] = 'yes';
							}
						}
						break;
					case 'listing_address':
						foreach ($aSection['fields'] as $fieldKey => $aField){
							if ( $this->isDisable($aField) ){
								unset($this->aSections[$sectionKey]['fields'][$fieldKey]);
								continue;
							}

							$maximumRegions = isset($aField['maximum']) && ($aField['maximum']) ? abs($aField['maximum']) : 1;

							if ( !isset($aField['isEnable']) || $aField['isEnable'] == 'no' ){
								unset($this->aSections[$sectionKey]['fields'][$fieldKey]);
								continue;
							}

							if ( $fieldKey == 'listing_location' ){
								if ( $maximumRegions > 1 ){
									$this->aSections[$sectionKey]['fields'][$fieldKey]['isMultiple'] = 'yes';
								}else{
									$this->aSections[$sectionKey]['fields'][$fieldKey]['isMultiple'] = 'no';
								}

								$aRawTerms = GetSettings::getTaxonomyHierarchy(array(
									'taxonomy' => $fieldKey,
									'orderby'  => isset($aField['orderBy']) ? $aField['orderBy'] : 'count',
									'parent'   => 0
								), $postType);

								if ( !$aRawTerms ){
									$aTerms = array(
										'' => esc_html__('There are no terms', 'wiloke-listing-tools' )
									);
								}else{
									$aTerms = array();
									foreach ($aRawTerms as $oTerm){
										$aTerm['value'] = $oTerm->term_id;
										$aTerm['name']  = $oTerm->name;
										$aTerms[] = $aTerm;
									}
								}
								$this->aSections[$sectionKey]['fields'][$fieldKey]['options'] = $aTerms;

								if ( !empty($this->listingID) ){
									$aRawPostTerms = wp_get_post_terms($this->listingID, 'listing_location');
									if ( !empty($aRawPostTerms) && !is_wp_error($aRawPostTerms) ) {
										if ( $maximumRegions < 2 ) {
											$aPostTerm                                                       = end( $aRawPostTerms );
											$this->aSections[$sectionKey]['fields'][$fieldKey]['value'] = $aPostTerm->term_id;
										} else {
											$aPostTerms = array_slice( $aRawPostTerms, 0, $maximumRegions );

											$aGetRegions = array();
											foreach ( $aPostTerms as $oRegion ) {
												$aGetRegions[] = $oRegion->term_id;
											}
											$this->aSections[$sectionKey]['fields'][$fieldKey]['value'] = implode( ',', $aGetRegions );
										}
									}
								}
							}else{
								$this->aSections[$sectionKey]['fields'][$fieldKey]['value'] = array();
								if ( !empty($this->listingID) ) {
									$aRawMap = GetSettings::getListingMapInfo( $this->listingID );
									if ( ! empty( $aRawMap ) ) {
										$aMap['latLng']  = $aRawMap['lat'] . ',' . $aRawMap['lng'];
										$aMap['lat']  = $aRawMap['lat'];
										$aMap['lng']  = $aRawMap['lng'];
										$aMap['address'] = stripslashes( $aRawMap['address'] );
										$this->aSections[ $sectionKey ]['fields'][ $fieldKey ]['value'] = $aMap;
									}
								}
							}

							$this->aSections[$sectionKey]['fields'][$fieldKey]['errMsg'] = '';
							$this->aSections[$sectionKey]['fields'][$fieldKey]['key'] = $fieldKey;
						}
						break;
					case 'listing_tag':
						foreach ($aSection['fields'] as $fieldKey => $aField){
							$this->aSections[$sectionKey]['fields'][$fieldKey]['key'] = $fieldKey;
							$aRawTerms = GetSettings::getTerms(array(
								'taxonomy'  => $fieldKey,
								'orderby'   => $aField['orderBy'],
								'order'     => isset($aField['order']) ? $aField['order'] : 'DESC',
								'hide_empty'=> false
							));
							if ( !$aRawTerms ){
								$aTerms = array(
									'' => esc_html__('There are no terms', 'wiloke-listing-tools' )
								);
							}else{
								$aTerms = array();
								foreach ($aRawTerms as $oTerm){
									$aTagsBelongsTo = GetSettings::getTermMeta($oTerm->term_id, 'belongs_to');
									if ( !empty($aTagsBelongsTo) && !in_array($postType, $aTagsBelongsTo) ){
										continue;
									}
									$aTerm['value'] = $oTerm->term_id;
									$aTerm['label']  = $oTerm->name;
									$aTerms[] = $aTerm;
								}
							}
							$this->aSections[$sectionKey]['fields'][$fieldKey]['options'] = $aTerms;
						}

						if ( !empty($this->listingID) ){
							$aTags = GetSettings::getPostTerms($this->listingID, 'listing_tag');
							if ( $aTags ){
								$this->aSections[$sectionKey]['fields']['listing_tag']['value'] = array();
								foreach ($aTags as $oTag){
									$this->aSections[$sectionKey]['fields']['listing_tag']['value'][] = $oTag->term_id;
								}
							}
						}
						break;
					case 'gallery':
						$aGallery = !empty($this->listingID) ? GetSettings::getPostMeta($this->listingID, 'gallery') : '';

						if ( !empty($aGallery) ){
							$aGalleryValues = array();
							foreach ($aGallery as $imgID => $src){
								$aGalleryValues[] = array(
									'imgID' => $imgID,
									'src'   => wp_get_attachment_image_url($imgID, 'thumbnail')
								);
							}
							$this->aSections[$sectionKey]['fields']['gallery']['value'] = $aGalleryValues;
						}
						$this->aSections[$sectionKey]['fields']['gallery']['errMsg'] = '';
						$this->aSections[$sectionKey]['fields']['gallery']['key'] = 'gallery';
						break;
					case 'video':
						$aVideos = !empty($this->listingID) ? GetSettings::getPostMeta($this->listingID, 'video_srcs') : '';
						if ( !empty($aVideos) ){
							$this->aSections[$sectionKey]['fields']['videos']['value'] = array();
							foreach ($aVideos as $aVideo){
								$this->aSections[$sectionKey]['fields']['videos']['value'][] = $aVideo;
							}
						}
						$this->aSections[$sectionKey]['fields']['videos']['errMsg'] = '';
						$this->aSections[$sectionKey]['fields']['videos']['key'] = 'videos';
						break;
					case 'business_hours':
						$aSetupBusinessHours = array();
						$aBusinessHours = !empty($this->listingID) ? Listing::getBusinessHoursOfListing($this->listingID) : '';
						if ( !empty($aBusinessHours) ){
							foreach ($aBusinessHours as $aBusinessHour){
								$aOperatingHour = array();
								$aOperatingHour[] = array(
									'from' => isset($aBusinessHour['firstOpenHour']) ? $aBusinessHour['firstOpenHour'] : '',
									'to' => isset($aBusinessHour['firstCloseHour']) ? $aBusinessHour['firstCloseHour'] : '',
								);

								if ( !empty($aBusinessHour['secondOpenHour']) && !empty($aBusinessHour['secondCloseHour']) ){
									$aOperatingHour[] = array(
										'from' => isset($aBusinessHour['secondOpenHour']) ? $aBusinessHour['secondOpenHour'] : '',
										'to' => isset($aBusinessHour['secondCloseHour']) ? $aBusinessHour['secondCloseHour'] : '',
									);
								}

								$aSetupBusinessHours[$aBusinessHour['dayOfWeek']]['operating_times'] = $aOperatingHour;
								$aSetupBusinessHours[$aBusinessHour['dayOfWeek']]['isOpen'] = isset($aBusinessHour['isOpen']) ? $aBusinessHour['isOpen'] : 'no';
							}

						}
						$this->aSections[$sectionKey]['fields']['business_hours']['value'] = array();
						$timeFormat = GetSettings::getPostMeta($this->listingID, 'timeFormat');
						$hourMode = GetSettings::getPostMeta($this->listingID, 'hourMode');

						$this->aSections[$sectionKey]['fields']['business_hours']['value']['timeFormat'] = empty($timeFormat) ? $wiloke->aThemeOptions['timeformat'] : $timeFormat;
						$this->aSections[$sectionKey]['fields']['business_hours']['value']['hourMode'] = empty($hourMode) ? '' : $hourMode;
						$this->aSections[$sectionKey]['fields']['business_hours']['value']['businessHours'] = $aSetupBusinessHours;
						$this->aSections[$sectionKey]['fields']['business_hours']['key'] = 'business_hours';
						break;
					case 'contact_info':
						foreach ($aSection['fields'] as $fieldKey => $aField){
							if ( $fieldKey == 'social_networks' ){
								$aParsedSocialNetworks = array();
								$aSocialNetworks = !empty($this->listingID) ? GetSettings::getPostMeta($this->listingID, $fieldKey) : '';
								if ( !empty($aSocialNetworks) ){
									foreach ($aSocialNetworks as $socialName => $socialUrl){
										if ( !empty($socialUrl) ){
											$aParsedSocialNetworks[] = array(
												'name' => $socialName,
												'url'  => $socialUrl
											);
										}
									}
									$this->aSections[$sectionKey]['fields'][$fieldKey]['value'] = $aParsedSocialNetworks;
								}
							}else{
								$val = !empty($this->listingID) ? GetSettings::getPostMeta($this->listingID, $fieldKey) : '';
								$this->aSections[$sectionKey]['fields'][$fieldKey]['value'] = empty($val) ? '' : $val;
							}
							$this->aSections[$sectionKey]['fields'][$fieldKey]['key'] = $fieldKey;
						}

						if ( isset($aSection['fields']['social_networks']['excludingSocialNetworks']) && !empty($aSection['fields']['social_networks']['excludingSocialNetworks']) ){
							if (class_exists('\WilokeSocialNetworks')){
								\WilokeSocialNetworks::$aSocialNetworks = array_diff(\WilokeSocialNetworks::$aSocialNetworks, $aSection['fields']['social_networks']['excludingSocialNetworks']);
							};
						}

						break;
					case 'single_price':
						$this->aSections[$sectionKey]['fields']['single_price']['value'] = '';
						$singlePrice = $priceRangeDesc = '';
						if ( !empty($this->listingID) ){
							$singlePrice = GetSettings::getPostMeta($this->listingID, 'single_price');
						}

						$this->aSections[$sectionKey]['fields']['single_price']['value'] = empty($singlePrice) ? '' : $singlePrice;
						$this->aSections[$sectionKey]['fields']['single_price']['key'] = 'single_price';
						$this->aSections[$sectionKey]['fields']['single_price']['type']= 'text';
						break;

					case 'price_range':
						$this->aSections[$sectionKey]['fields']['price_range']['value'] = array();

						$priceRange = $minPrice = $priceRangeDesc = $maxPrice = '';
						if ( !empty($this->listingID) ){
							$priceRange = GetSettings::getPostMeta($this->listingID, 'price_range');
							$priceRangeDesc = GetSettings::getPostMeta($this->listingID, 'price_range_desc');
							$minPrice = GetSettings::getPostMeta($this->listingID, 'minimum_price');
							$maxPrice = GetSettings::getPostMeta($this->listingID, 'maximum_price');
						}

						$this->aSections[$sectionKey]['fields']['price_range']['value']['price_range'] = empty($priceRange) ? '' : $priceRange;
						$this->aSections[$sectionKey]['fields']['price_range']['value']['price_range_desc'] = empty($priceRangeDesc) ? '' : $priceRangeDesc;
						$this->aSections[$sectionKey]['fields']['price_range']['value']['minimum_price'] = !empty($minPrice) ? $minPrice : '';
						$this->aSections[$sectionKey]['fields']['price_range']['value']['maximum_price'] = !empty($maxPrice) ? $maxPrice : '';
						$this->aSections[$sectionKey]['fields']['price_range']['key'] = 'price_range';
						break;
					case 'event_calendar':
						$dateFormat = apply_filters('wilcity_date_picker_format', 'mm/dd/yy');
						$dateFormat = \WilokeListingTools\Framework\Helpers\Time::convertJSDateFormatToPHPDateFormat($dateFormat);
						foreach ($aSection['fields'] as $fieldKey => $aField){
							$this->aSections[ $sectionKey ]['fields'][ $fieldKey ]['value'] = array();
							$aEventData = !empty($this->listingID) ? EventModel::getEventData($this->listingID) : '';
							if ( !empty($aEventData) ){
								$this->aSections[ $sectionKey ]['fields'][ $fieldKey ]['value']['frequency'] = $aEventData['frequency'];
								if ( !empty($aEventData['startsOn']) && $aEventData['startsOn'] !== '0000-00-00 00:00:00' ){
									$this->aSections[ $sectionKey ]['fields'][ $fieldKey ]['value']['starts'] = date($dateFormat, strtotime($aEventData['startsOn']));
									$this->aSections[ $sectionKey ]['fields'][ $fieldKey ]['value']['openingAt'] = date('H:i', strtotime($aEventData['startsOn']));
								}
								if ( !empty($aEventData['endsOn']) && $aEventData['endsOn'] !== '0000-00-00 00:00:00' ){
									$this->aSections[ $sectionKey ]['fields'][ $fieldKey ]['value']['endsOn'] = date($dateFormat, strtotime($aEventData['endsOn']));
									$this->aSections[ $sectionKey ]['fields'][ $fieldKey ]['value']['closedAt'] = date('H:i', strtotime($aEventData['endsOn']));
								}
								$this->aSections[ $sectionKey ]['fields'][ $fieldKey ]['value']['specifyDays'] = empty($aEventData['specifyDays']) ? array() : explode(',', $aEventData['specifyDays']);
							}
							$this->aSections[$sectionKey]['fields'][$fieldKey]['errMsg'] = '';
							$this->aSections[$sectionKey]['fields'][$fieldKey]['key'] = $fieldKey;
						}
						break;
					case 'group':
						$this->aSections[$sectionKey]['fields'] = array();
						$aFields = array();
						foreach ($aSection['fields'] as $aFieldSettings){
							$aField = array();
							$aField['settings'] = array();
							foreach ($aFieldSettings as $aSetting){
								switch ($aSetting['type']){
									case 'label':
										$aField['settings']['label'] = stripslashes(trim($aSetting['value']));
										break;
									case 'name':
										$aField['settings']['name'] = stripslashes(trim($aSetting['value']));
										break;
									case 'customField':
										if ( !empty($this->listingID) ){
											$aGroupVal = GetSettings::getPostMeta($this->listingID, $aSection['key']);
										}
										if ( $aSetting['value']['type'] == 'select2-multiple' ){
											$aField['component'] = 'wiloke-select2';
										}else{
											$aField['component'] = 'wiloke-'.$aSetting['value']['type'];
										}

										if ( $aField['component'] == 'wiloke-select2' ){
											$options = stripslashes($aSetting['value']['options']);
											$aRawOptions = explode(',', $options);

											$aOptions = array();
											foreach ($aRawOptions as $option){
												$aParseOption = $this->parseOption($option);
												$aOptions[] = array(
													'name' => $aParseOption['name'],
													'value'=> $aParseOption['value']
												);
											}
											$aField['settings']['options'] = $aOptions;

											if ( $aSetting['value']['type'] == 'select2-multiple' ){
												$aField['settings']['isMultiple']  = 'yes';
												$aField['settings']['value']   = [];
											}

										}
										if(isset($aGroupVal[$aField['settings']['name']])){
											$aField['settings']['value'] = $aGroupVal[$aField['settings']['name']];
										}
										break;
								}
							}
							$aFields[] = $aField;
						}
						$this->aSections[$sectionKey]['fields']['oFields'] = $aFields;
						break;
					case 'restaurant_menu':
						foreach ($aSection['fields'] as $fieldKey => $aField){
							$numberOfFields = GetSettings::getPostMeta($this->listingID, 'number_restaurant_menus');
							$aDefaultMenu = apply_filters('wiloke-listing-tools/addlisting/restaurant-menu/default', array(
								'group_title' => '',
								'group_description' => '',
								'group_icon' => 'fa fa-cutlery',
								'items' => array(
									array(
										'title' => esc_html__('My Title', 'wiloke-listing-tools'),
										'description' => esc_html__('Price is net per person, excluding drinks chef seasonal menu', 'wiloke-listing-tools'),
										'price' => esc_html__('$ 59.99 - $ 79.99', 'wiloke-listing-tools'),
										'link_to' => '',
										'is_open_new_window' => ''
									)
								)
							));
							if ( empty($this->listingID) || empty($numberOfFields) ){
								$this->aSections[$sectionKey]['fields'][$fieldKey]['value'] = empty($aDefaultMenu) ? array() : array($aDefaultMenu);
							}else{
								$aRestaurantMenus = array();
								for ($i=0; $i<$numberOfFields; $i++){
									$aItems = GetSettings::getPostMeta($this->listingID, 'restaurant_menu_group_'.$i);
									$aRestaurantMenus[$i] = array(
										'group_title'       => GetSettings::getPostMeta($this->listingID, 'group_title_'.$i),
										'group_description' => GetSettings::getPostMeta($this->listingID, 'group_description_'.$i),
										'group_icon'        => GetSettings::getPostMeta($this->listingID, 'group_icon_'.$i),
										'items'             => is_array($aItems) ? array_values($aItems) : array()
									);
								}
								$this->aSections[$sectionKey]['fields'][$fieldKey]['value'] = $aRestaurantMenus;
							}
						}
						break;
					default:
						foreach ($aSection['fields'] as $fieldKey => $aSetting){
							$this->aSections[$sectionKey]['fields'][$fieldKey]['key'] = $fieldKey;
						}
						add_filter('wiloke-listing-tools/addlisting/merging-setting-values/'.$aSection['type'], $this->aSections, $aSection, $this->listingID);
						add_filter('wiloke-listing-tools/addlisting/merging-setting-values', $this->aSections, $aSection, $this->listingID);
						break;
				}
			}
		}

		return $this->aSections;
	}
}
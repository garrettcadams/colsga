<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\Frontend\User;

trait Validation {
	protected $aListingData = array(
		'post_title'    => 'No Title 1',
		'post_status'   =>'unpaid'
	);
	protected $aCoupon;
	protected $aRawFeaturedImage;
	protected $aRawCoverImg;
	protected $aRawLogo;
	protected $myRoom;
	protected $myPosts;
	protected $aTags = array();
	protected $category = array();
	protected $location = array();
	protected $email;
	protected $website;
	protected $phone;
	protected $aGoogleAddress = array();
	protected $aPriceRange = array();
	protected $aSocialNetworks = array();
	protected $aBusinessHours = array();
	protected $aGallery = array();
	protected $aRawGallery = array();
	protected $aGeneralData = array();
	protected $aEventCalendar = array();
	protected $aCustomButton = array();
	protected $aVideos = array();
	protected $aGroupData = array();
	protected $aCustomSections;
	protected $aRestaurantMenu = array();
	protected $singlePrice;
	protected $aBookingComBannerCreator = array();
	protected $aMyProducts = array();

	protected static function sanitizeData($data){
		if ( !is_array($data) ){
			return sanitize_text_field($data);
		}

		return array_map(array(__CLASS__, 'sanitizeData'), $data);
	}

	protected function validation(){
		if ( !isset($this->aData) || empty($this->aData) ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('Please fill up all requirement fields.', 'wiloke-listing-tools')
				)
			);
		}

		$msg = esc_html__('The %s is required', 'wiloke-listing-tools');
		foreach ($this->aData as $index => $aSection){
			if ( !is_array($index) ){
				$index = sanitize_text_field($index);
			}
			foreach ($aSection as $fieldKey => $aFieldData){
					$fieldKey = sanitize_text_field($fieldKey);
					if ( isset($this->aPlanSettings['toggle_'.$fieldKey]) && $this->aPlanSettings['toggle_'.$fieldKey] == 'disable' ){
						continue;
					}

					if ( (!isset($aFieldData['isEnable']) || $aFieldData['isEnable'] == 'yes') && isset($aFieldData['isRequired']) && ($aFieldData['isRequired'] == 'yes') ){

						if ( empty($aFieldData['value']) ){
							$placeHolder = isset($aFieldData['label']) ? $aFieldData['label'] : $this->aData[$index]['heading'];
							$msg = sprintf($msg, $placeHolder);
							if ( isset($aOptions['isReturnDepthMsg']) && $aOptions['isReturnDepthMsg'] ){
								$aMessage['index']     = $index;
								$aMessage['fieldKey']  = $fieldKey;
								$aMessage['msg']       = $msg;
							}else{
								$aMessage['msg'] = $msg;
							}

							$aMessage = apply_filters('wilcity/addlisting/validation/required/message/' . $fieldKey, $aMessage);

							wp_send_json_error($aMessage);
						}
					}

					switch ($fieldKey){
						case 'custom_button_button_icon':
						case 'custom_button_button_name':
						case 'custom_button_button_link':
							$realKey = str_replace('custom_button_', '', $fieldKey);
							$this->aCustomButton[$realKey] = sanitize_text_field($aFieldData['value']);
							break;
						case 'listing_title':
							$this->aListingData['post_title'] = sanitize_text_field($aFieldData['value']);
							if ( empty($aFieldData['value']) ){
								wp_send_json_error(array(
									'msg' => esc_html__('The Title is required', 'wiloke-listing-tools')
								));
							}
							break;
						case 'event_calendar':
							if ( empty($aFieldData['value']['starts']) || empty($aFieldData['value']['endsOn']) ){
								wp_send_json_error(array(
									'msg' => esc_html__('Event Calendar is required', 'wiloke-listing-tools')
								));
							}
							$aMessage = array();
							$aMessage['index']     = $index;
							$aMessage['fieldKey']  = $fieldKey;

							$dateFormat = apply_filters('wilcity_date_picker_format', 'mm/dd/yy');
							$dateFormat = Time::convertJSDateFormatToPHPDateFormat($dateFormat);
							if ( Time::toTimestamp($dateFormat, $aFieldData['value']['starts']) > Time::toTimestamp($dateFormat, $aFieldData['value']['endsOn']) ){
								$aMessage['msg'] = esc_html__('The event starts must be smaller than Event End', 'wiloke-listing-tools');
								wp_send_json_error($aMessage);
							}else if ( Time::toTimestamp($dateFormat, $aFieldData['value']['starts']) == Time::toTimestamp($dateFormat, $aFieldData['value']['endsOn']) ){
								if ( strtotime($aFieldData['value']['openingAt']) >= strtotime($aFieldData['value']['closedAt']) ){
									$aMessage['msg'] = esc_html__('The event Closed At must be greater than Opening At', 'wiloke-listing-tools');
									wp_send_json_error($aMessage);
								}

								$aFieldData['value']['openingAt'] = Time::toTimeFormat($aFieldData['value']['openingAt']);
								$aFieldData['value']['closedAt'] = Time::toTimeFormat($aFieldData['value']['closedAt']);
							}

							if ( isset($aFieldData['value']['openingAt']) && !empty($aFieldData['value']['openingAt']) ){
								$aFieldData['value']['openingAt'] = Time::toTimeFormat($aFieldData['value']['openingAt']);
								$aFieldData['value']['closedAt'] = Time::toTimeFormat($aFieldData['value']['closedAt']);

							}

							$this->aEventCalendar = $aFieldData['value'];
							break;

						case 'listing_content':
							$this->aListingData['post_content'] = $aFieldData['value'];
							break;
						case 'logo':
							$this->aRawLogo = isset($aFieldData['value']) ? $aFieldData['value'] : '';
							break;
						case 'featured_image':
							$this->aRawFeaturedImage = isset($aFieldData['value']) ? $aFieldData['value'] : '';
							break;
						case 'cover_image':
							$this->aRawCoverImg = isset($aFieldData['value']) ? $aFieldData['value'] : '';
							break;
						case 'single_price':
							$this->singlePrice = $aFieldData['value'];
							if ( !empty($this->singlePrice) ){
								if ( !is_numeric($this->singlePrice) ){
									wp_send_json_error(array(
										'msg' => sprintf(esc_html__('The field %s must be a number', 'wiloke-listing-tools'), $aFieldData['label'])
									));
								}
							}
							break;
						case 'listing_cat':
							$catVal = null;
							if ( isset($aFieldData['category']) && !empty($aFieldData['category']) ){
								$catVal = $aFieldData['category'];
							}else if ( isset($aFieldData['value']) && !empty($aFieldData['value']) ){
								$catVal = $aFieldData['value'];
							}

							if ( !empty($catVal) ){
								if ( isset($aFieldData['maximum']) && $aFieldData['maximum'] > 1 ){
									$catVal = !is_array($catVal) ? explode(',', $catVal) : $catVal;
									$this->category = array_slice($catVal, 0, $aFieldData['maximum']);
									$this->category = array_map(function($val){
										return abs($val);
									}, $this->category);
								}else{
									if ( is_array($catVal) ){
										$this->category = absint($catVal[0]);
									}else{
										$this->category = absint($catVal);
									}
								}

								if ( isset($aFieldData['tags']) && !empty($aFieldData['tags']) ){
									$this->aTags = array_map(function($val){
										return abs($val);
									}, $aFieldData['tags']);
								}
							}
							break;
						case 'listing_location':
							if( !empty($aFieldData['value']) ) {
								if ( isset($aFieldData['maximum']) && $aFieldData['maximum'] > 1 ){
									$this->location = array_slice($aFieldData['value'], 0, $aFieldData['maximum']);
									$this->location = array_map(function($val){
										return abs($val);
									}, $this->location);
								}else{
									$this->location = absint($aFieldData['value']);
								}
								if ( isset($aFieldData['tags']) && !empty($aFieldData['tags']) ){
									$this->aTags = array_map(function($val){
										return abs($val);
									}, $aFieldData['tags']);
								}
							}
							break;
						case 'listing_tag':
							if ( isset($aFieldData['value']) && !empty($aFieldData['value']) ){
								if ( isset($aFieldData['maximum']) && !empty($aFieldData['maximum']) ){
									$this->aTags = array_slice($aFieldData['value'], 0, $aFieldData['maximum']);
								}else{
									$this->aTags = $aFieldData['value'];
								}
								$this->aTags = array_map('absint', $this->aTags);
							}

							break;
						case 'social_networks':
							if ( !empty($aFieldData['value']) ){
								foreach ($aFieldData['value'] as $aSocial){
									if ( in_array($aSocial['name'], \WilokeSocialNetworks::$aSocialNetworks) ){
										$this->aSocialNetworks[sanitize_text_field($aSocial['name'])] = sanitize_text_field($aSocial['url']);
									}
								}
							}
							break;
						case 'email':
							$this->email = sanitize_email($aFieldData['value']);
							break;
						case 'phone':
							$this->phone = sanitize_text_field($aFieldData['value']);
							break;
						case 'website':
							$this->website = sanitize_text_field($aFieldData['value']);
							break;
						case 'address':
						case 'listing_address':
							$checkStatus = false;
							$isRequired = isset($aFieldData['isRequired']) && $aFieldData['isRequired'] == 'yes';
							foreach ($aFieldData['value'] as $key => $val) {
								if( empty($val) && $isRequired ) {
									$checkStatus = true;
									break;
								}
								$this->aGoogleAddress[sanitize_text_field($key)] = sanitize_text_field($val);
							}

							if( $checkStatus ) {
								$placeHolder = isset($aFieldData['label']) ? $aFieldData['label']: $this->aData[$index]['heading'];
								$aMessage = apply_filters('wilcity/addlisting/validation/required/message/' . $fieldKey, array('msg'=> sprintf($msg, $placeHolder)));
								wp_send_json_error($aMessage);
							}

							break;
						case 'price_range':
							foreach ($aFieldData['value'] as $key => $val){
								$this->aPriceRange[sanitize_text_field($key)] = sanitize_text_field($val);
							}

							if ( isset($aFieldData['isRequired']) && $aFieldData['isRequired'] == 'yes' ){
								if ( empty($this->aPriceRange['minimum_price']) || empty($this->aPriceRange['maximum_price']) | empty($this->aPriceRange['price_range']) ){
									wp_send_json_error(array(
										'msg' => esc_html__('The Price Range is required', 'wiloke-listing-tools')
									));
								}
							}
							break;
						case 'business_hours':
							$this->aBusinessHours['timeFormat'] = sanitize_text_field($aFieldData['value']['timeFormat']);
							$this->aBusinessHours['hourMode']   = sanitize_text_field($aFieldData['value']['hourMode']);

							foreach ($aFieldData['value']['businessHours'] as $dayOfWeek => $aSettings){
								foreach ($aSettings['operating_times'] as $key => $aOperating){
									$this->aBusinessHours['businessHours'][$dayOfWeek]['operating_times'][$this->aConvertToRealBusinessHourKeys[$key]['to']]  = sanitize_text_field($aOperating['to']);
									$this->aBusinessHours['businessHours'][$dayOfWeek]['operating_times'][$this->aConvertToRealBusinessHourKeys[$key]['from']]  = sanitize_text_field($aOperating['from']);
								}

								$this->aBusinessHours['businessHours'][$dayOfWeek]['isOpen'] = isset($aSettings['isOpen']) ? $aSettings['isOpen'] : 'no';
							}
							break;
						case 'my_room':
							$myRoom = abs($aFieldData['value']);
							if ( !empty($myRoom) ){
								if ( !current_user_can('administrator') ){
									if ( get_post_type($myRoom) != 'product' ){
										break;
									}else{
										$postStatus = get_post_status($myRoom);
										if ( !in_array($postStatus, array('pending', 'publish')) ){
											break;
										}else{
											if ( get_post_field('post_author', $myRoom) != get_current_user_id() ){
												break;
											}
										}
									}
								}
								$this->myRoom = $myRoom;
							}
							break;
						case 'my_posts':
							$aMyPosts = $aFieldData['value'];
							if ( !empty($aMyPosts) ){
								if ( !current_user_can('administrator') ){
									foreach ($aMyPosts as $postOrder => $postID){
										if ( get_post_type($postID) != 'post' ){
											unset($aMyPosts[$postOrder]);
										}else{
											$postStatus = get_post_status($postID);
											if ( !in_array($postStatus, array('pending', 'publish')) ){
												unset($aMyPosts[$postOrder]);
											}else{
												if ( get_post_field('post_author', $postID) != User::getCurrentUserID() ){
													unset($aMyPosts[$postOrder]);
												}
											}
										}
									}
								}else{
									$aMyPosts = array_map('absint', $aMyPosts);
								}
								$this->myPosts = $aMyPosts;
							}
							break;
						case 'my_products':
							$aMyProducts = $aFieldData['value'];

							if ( !empty($aMyProducts) ){
								if ( !current_user_can('administrator') ){
									foreach ($aMyProducts as $productOrder => $productID){
										if ( get_post_type($productID) != 'product' ){
											unset($aMyProducts[$productOrder]);
										}else{
											$postStatus = get_post_status($productID);
											if ( !in_array($postStatus, array('pending', 'publish')) ){
												unset($aMyProducts[$productOrder]);
											}else{
												if ( get_post_field('post_author', $productID) != get_current_user_id() ){
													unset($aMyProducts[$productOrder]);
												}
											}
										}
									}
								}else{
									$aMyProducts = array_map('absint', $aMyProducts);
								}
								$this->aMyProducts = $aMyProducts;
							}
							break;
						case 'bookingcombannercreator_buttonName':
						case 'bookingcombannercreator_buttonColor':
						case 'bookingcombannercreator_buttonBg':
						case 'bookingcombannercreator_bannerImg':
						case 'bookingcombannercreator_bannerCopy':
						case 'bookingcombannercreator_bannerCopyColor':
						case 'bookingcombannercreator_bannerLink':
							if ( isset($this->aPlanSettings['toggle_bookingcombannercreator']) && $this->aPlanSettings['toggle_bookingcombannercreator'] == 'disable'  ){
								break;
							}

							if ( $fieldKey !== 'bookingcombannercreator_bannerImg' ){
								$bookingKey = str_replace('bookingcombannercreator_', '', $fieldKey);
								$this->aBookingComBannerCreator[$bookingKey] = sanitize_text_field($aFieldData['value']);
							}else{
								$this->aBookingComBannerCreator['bannerImg'] = $aFieldData['value'];
							}
							break;
						case 'videos':
							if ( isset($this->aPlanSettings['toggle_videos']) && $this->aPlanSettings['toggle_videos'] == 'disable'  ){
								break;
							}
							if ( !empty($aFieldData['value']) ){
								if ( isset($this->aPlanSettings['maximumVideos']) && $this->aPlanSettings['maximumVideos'] != 0  ) {
									$aFieldData['value'] = array_slice($aFieldData['value'], 0, $this->aPlanSettings['maximumVideos']);
								}

								foreach ($aFieldData['value'] as $key => $aVideo){
									if ( !empty($aVideo['src']) ){
										if ( strpos($aVideo['src'], 'youtube') !== -1 ){
											$aVideo['src'] = preg_replace_callback('/&.*/', function(){
												return '';
											}, $aVideo['src']);
										}

										$this->aVideos[$key]['src'] = sanitize_text_field($aVideo['src']);
										$this->aVideos[$key]['thumbnail'] = sanitize_text_field($aVideo['thumbnail']);
									}
								}
							}
							break;
						case 'gallery':
							if ( isset($this->aPlanSettings['toggle_gallery']) && $this->aPlanSettings['toggle_gallery'] == 'disable'  ){
								break;
							}
							$this->aRawGallery = $aFieldData['value'];
							$maxGallery = isset($this->aPlanSettings['maximumGalleryImages']) && $this->aPlanSettings['maximumGalleryImages']!=0 ? abs($this->aPlanSettings['maximumGalleryImages']) : 0;

							if ( !empty($maxGallery) && !empty($this->aRawGallery) ) {
								$this->aRawGallery = array_slice($this->aRawGallery, 0, $maxGallery);
							}

							break;
						case 'coupon_code':
						case 'coupon_title':
						case 'coupon_description':
						case 'coupon_highlight':
						case 'coupon_redirect_to':
						case 'coupon_popup_description':
						case 'coupon_popup_image':
						case 'coupon_expiry_date':
							if ( isset($this->aPlanSettings['toggle_coupon']) && $this->aPlanSettings['toggle_coupon'] == 'disable'  ){
								break;
							}
							$couponKey = str_replace('coupon_', '', $fieldKey);
							if ( $fieldKey != 'coupon_popup_image' ){
								$this->aCoupon[$couponKey] = sanitize_text_field($aFieldData['value']);
							}else{
								$this->aCoupon[$couponKey] = $aFieldData['value'];
							}
							break;
						case 'restaurant_menu':
							if ( isset($this->aPlanSettings['toggle_restaurant_menu']) && $this->aPlanSettings['toggle_restaurant_menu'] == 'disable'  ){
								break;
							}

							if ( is_array($aFieldData['value']) ){
								$aRestaurantMenus = $aFieldData['value'];
								if ( isset($this->aPlanSettings['maximumRestaurantMenus']) && !empty($this->aPlanSettings['maximumRestaurantMenus']) ){
									$aRestaurantMenus = array_splice($aRestaurantMenus, 0, $this->aPlanSettings['maximumRestaurantMenus']);
								}

								foreach ($aRestaurantMenus as $groupOrder => $aGroupMenu){
									$aSanitizedItems = array();
									// Handle Items
									if ( !isset($aGroupMenu['items']) || !is_array($aGroupMenu['items']) ){
										continue;
									}

									$aListMenus = $aGroupMenu['items'];
									unset($aGroupMenu['items']);

									if ( isset($this->aPlanSettings['maximumItemsInMenu']) && !empty($this->aPlanSettings['maximumItemsInMenu']) ){
										$aListMenus = array_splice($aListMenus, 0, $this->aPlanSettings['maximumItemsInMenu']);
									}

									foreach ($aListMenus as $listMenuOrder => $aListItems){
										if ( empty($aListItems['title']) ){
											wp_send_json_error(array('msg'=>esc_html__('The Item title is required', 'wiloke-listing-tools')));
										}
										foreach ($aListItems as $itemKey => $aItems){
											if ( $itemKey == 'gallery' ){
												if ( is_array($aItems) ){
													$aRawGallery = $aItems;
													$aGallery = array();

													if ( isset($this->aPlanSettings['maximum_restaurant_gallery_images']) && !empty($this->aPlanSettings['maximum_restaurant_gallery_images']) ){
														$aRawGallery = array_slice($aRawGallery, 0, $this->aPlanSettings['maximum_restaurant_gallery_images'], true);
													}
													foreach ($aRawGallery as $galleryID => $src){
														$aGallery[sanitize_text_field($galleryID)] = $src;
													}
													$aSanitizedItems[$listMenuOrder][$itemKey] = $aGallery;
												}else{
													$aSanitizedItems[$listMenuOrder][$itemKey] = array();
												}
											}else{
												$aSanitizedItems[$listMenuOrder][$itemKey] = sanitize_text_field($aItems);
											}
										}
									}

									$this->aRestaurantMenu[$groupOrder]['items'] = $aSanitizedItems;
									foreach ($aGroupMenu as $groupKey => $groupVal){
										$this->aRestaurantMenu[$groupOrder][$groupKey] = sanitize_text_field($groupVal);
									}
								}
							}else{
								$this->aRestaurantMenu = array();
							}
							break;
						case 'customField':
							if ( $aFieldData['type'] == 'select' ){
								$aFieldData['value'] = is_array($aFieldData['value']) ? implode(',', $aFieldData['value']) : $aFieldData['value'];
							}
							$this->aCustomSections[$index] = isset($aFieldData['value']) ? $aFieldData['value'] : '';
							break;
						case 'group':
							$this->aGroupData[$index] = array();
							if ( is_array($aFieldData) ){
								foreach ($aFieldData as $stackKey => $stackVal){
									$this->aGroupData[$index][sanitize_text_field($stackKey)] = is_array($stackVal) ? $stackVal : sanitize_text_field($stackVal);
								}
							}
							break;
						default:
							if (  has_filter('wilcity/addlisting/validation/'.$fieldKey) ){
								$this->aGeneralData[$fieldKey] = apply_filters('wilcity/addlisting/validation/'.$fieldKey, $this, $aFieldData, $fieldKey);
							}else{
								$this->aGeneralData[$fieldKey] = $aFieldData['value'];
							}
							break;
					}
				}
		}
	}
}

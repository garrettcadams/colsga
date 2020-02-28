<?php

namespace WilokeListingTools\Framework\Helpers;


use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Register\WilokeSubmission;

class GetWilokeSubmission {
	public static $aConfiguration = array();
	public static $isFocusNonRecurring;
	public static $aGateways;

	public static function getField($field, $isUrl=false){
		self::getAll();
		if ( isset(self::$aConfiguration[$field]) ){
			return $isUrl ? get_permalink(self::$aConfiguration[$field]) : self::$aConfiguration[$field];
		}
		return '';
	}

	public static function getDashboardUrl($field, $router){
		$dashboardURL = self::getField($field, true);
		return $dashboardURL . '#' . $router;
	}

	public static function getFreePlan($postType){
		$aPlans = self::getAddListingPlans($postType . '_plans');
		return is_array($aPlans) ? $aPlans[0] : '';
	}

	public static function getFreeClaimPlanID($listingID){
		$key = 'free_claim_' . get_post_type($listingID) . '_plan';
		return self::getField($key);
	}

	public static function isEnable($field){
		$toggle = self::getField($field);
		return $toggle == 'enable';
	}

	public static function isSystemEnable(){
		return self::isEnable('toggle');
	}

	public static function getAll(){
		if ( empty(self::$aConfiguration) ){
			self::$aConfiguration = get_option(WilokeSubmission::$optionKey);
			self::$aConfiguration = maybe_unserialize(self::$aConfiguration);
		}
		return self::$aConfiguration;
	}

	public static function getBillingType(){
		return self::getField('billing_type');
	}

	public static function getGatewaysWithName($excludeDirectBank=false){
		$aTranslations = array(
			'stripe' => esc_html__('Stripe', 'wiloke-listing-tools'),
			'banktransfer' => esc_html__('Bank Transfer', 'wiloke-listing-tools'),
			'paypal' => esc_html__('PayPal', 'wiloke-listing-tools'),
			'woocommerce' => esc_html__('WooCommerce', 'wiloke-listing-tools')
		);

		$aGateways = self::getAllGateways($excludeDirectBank);
		if ( empty($aGateways) ){
			return false;
		}

		$aWithName = array();
		foreach ($aGateways as $gateway){
			$aWithName[$gateway] = $aTranslations[$gateway];
		}
		return $aWithName;
	}

	public static function getAllGateways($excludeDirectBank=false){
		if ( !empty(self::$aGateways) ){
			return self::$aGateways;
		}

		$gateways = self::getField('payment_gateways');
		if ( empty($gateways) ){
			self::$aGateways = false;
			return self::$aGateways;
		}

		self::$aGateways = explode(',', $gateways);
		if ( $excludeDirectBank ){
			$key = array_search('banktransfer', self::$aGateways);
			$aGateways = self::$aGateways;
			if ( !empty($key) ){
				unset($aGateways[$key]);
			}
			return $aGateways;
		}
		return self::$aGateways;
	}

	public static function getAddListingPlans($planKey=''){
		$planKey = empty($planKey) ? 'listing_plans' : $planKey;
		$planIDs = self::getField($planKey);
		if ( empty($planIDs) ){
			return false;
		}

		$aPlanIDs = explode(',', $planIDs);
		$aPlanIDs = array_map('trim', $aPlanIDs);
		return $aPlanIDs;
	}

	public static function isGatewaySupported($gateway=''){
		self::getAllGateways();
		if ( !self::$aGateways ){
			return false;
		}

		return in_array($gateway, self::$aGateways);
	}

	public static function getPermalink($field){
		$val = self::getField($field);
		return get_permalink($val);
	}

	public static function getAddToCardUrl($productID){
		$aArgs = array(
			'add-to-cart' => $productID,
			'quantity'    => 1
		);

		return add_query_arg(
			$aArgs,
			get_permalink(get_option('woocommerce_checkout_page_id'))
		);
	}

	public static function isNonRecurringPayment($billingType=null){
		if ( self::$isFocusNonRecurring ){
			return true;
		}
		$billingType = empty($billingType) ? self::getBillingType() : $billingType;

		if ( empty($billingType) ){
			if ( empty($planID) ){
				Message::error( sprintf(esc_html__('Please provide the billing type: %s %s', 'wiloke-listing-tools'), __LINE__, __CLASS__) );
			}
			$billingType = self::getBillingType();
		}

		return $billingType == wilokeListingToolsRepository()->get('payment:billingTypes', true)->sub('nonrecurring');
	}

	public static function convertStripePrice($price){
		$zeroDecimal = self::getField('stripe_zero_decimal');
		$zeroDecimal =  empty($zeroDecimal) ? 1 : $zeroDecimal;

		return number_format($price/$zeroDecimal, 2);
	}

	public static function getSymbol($currency){
		return wilokeListingToolsRepository()->get('wiloke-submission:currencySymbol', true)->sub($currency);
	}

	public static function getPackageType($packageType){
		switch ($packageType){
			case 'promotion':
				$packageType = esc_html__('Promotion', 'wiloke-listing-tools');
				break;
			case 'listing_plan':
				$packageType = esc_html__('Listing Plan', 'wiloke-listing-tools');
				break;
			default:
				$packageType = str_replace('_', ' ', $packageType);
				$packageType = ucfirst($packageType);
				break;
		}
		return $packageType;
	}

	public static function canUserTrial($planID, $userID=null){
		if ( DebugStatus::status('WILOKE_ALWAYS_PAY') ){
			return true;
		}

		$userID = empty($userID) ? get_current_user_id() : $userID;
		$aPlansIDs = GetSettings::getUserMeta($userID, wilokeListingToolsRepository()->get('user:usedTrialPlans'));
		return empty($aPlansIDs) || !in_array($planID, $aPlansIDs);
	}

	public static function renderPrice($price, $currency='', $isNegative=false, $symbol=''){
		if ( empty($symbol) ){
			$currency   = empty($currency) ? GetWilokeSubmission::getField('currency_code') : $currency;
			$symbol     = self::getSymbol($currency);
		}
		$position   = self::getField('currency_position');

		if ( strpos($price, '-') !== false ){
			$price = str_replace('-', '', $price);
			$isNegative = true;
		}

		$symbol = apply_filters('wilcity/filter/symbol', $symbol);

		switch ($position){
			case 'left':
				$priceHTML = $symbol . $price;
				break;
			case 'right':
				$priceHTML = $price . $symbol;
				break;
			case 'left_space':
				$priceHTML = $symbol . ' ' . $price;
				break;
			default:
				$priceHTML = $price . ' ' . $symbol;
				break;
		}

		return $isNegative ? '-' . $priceHTML : $priceHTML;
	}

	public static function getSubmissionPlanID(){
		return Session::getSession(wilokeListingToolsRepository()->get('payment:storePlanID'));
	}

	public static function isCancelPage(){
		global $post;
		$cancelID = self::getField('cancel');
		return isset($post->ID) && $cancelID == $post->ID;
	}

	public static function isPlanExists($planID){
		$planType = get_post_type($planID) . '_plan';
		$aPlanIDs = self::getAddListingPlans($planType);
		return !empty($aPlanIDs) && in_array($planID, $aPlanIDs);
	}

	public static function isFreeAddListing(){
		$mode = GetWilokeSubmission::getField('add_listing_mode');
		return $mode == 'free_add_listing';
	}

	public static function getDefaultPostType(){
		$aPostTypes = GetSettings::getFrontendPostTypes();
		return $aPostTypes[0];
	}
}

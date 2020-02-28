<?php

namespace WilokeListingTools\Controllers;


use Stripe\Util\Set;
use WilokeListingTools\Framework\Helpers\FileSystem;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Framework\Payment\WooCommerce\WoocommerceNonRecurringPayment;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;
use WilokeListingTools\Models\PromotionModel;

class PromotionController extends Controller {
	protected $aPromotionPlans;
	protected $aWoocommercePlans;
	protected $expirationHookName = 'expiration_promotion';
	private $belongsToPromotionKey = 'belongs_to_promotion';

	public function __construct() {
		add_action( 'wp_ajax_wilcity_fetch_promotion_plans', array( $this, 'fetchPromotions' ) );
		add_action( 'wp_ajax_wilcity_get_payment_gateways', array( $this, 'getPaymentGateways' ) );
		add_action( 'wp_ajax_wilcity_boost_listing', array( $this, 'boostListing' ) );
		add_action( 'wiloke-listing-tools/payment-succeeded/promotion', array( $this, 'updatePayPalPostPromotion' ) );
		add_action( 'wiloke-listing-tools/woocommerce/order-created', array(
			$this,
			'updateWooCommercePromotion'
		), 10, 2 );
		add_action( 'wiloke-listing-tools/woocommerce/after-order-succeeded/promotion', array(
			$this,
			'updateWoocommercePostPromotion'
		), 10 );

		add_action( 'update_post_meta', array( $this, 'maybeChangeListingID' ), 10, 4 );
		add_action( 'updated_post_meta', array( $this, 'updatedListingPromotion' ), 10, 4 );
		add_action( 'added_post_meta', array( $this, 'updatedListingPromotion' ), 10, 4 );
		add_action( 'post_updated', array( $this, 'onChangedPromotionStatus' ), 10, 3 );
		add_action( 'before_delete_post', array( $this, 'deletePromotion' ), 100 );

		$aPromotionPlans = GetSettings::getOptions( 'promotion_plans' );
		if ( ! empty( $aPromotionPlans ) ) {
			foreach ( $aPromotionPlans as $aPlanSetting ) {
				add_action( $this->generateScheduleKey( $aPlanSetting['position'] ), array(
					$this,
					'deletePromotionValue'
				), 10, 2 );
			}
		}
		add_action( 'wiloke-listing-tools/payment-succeeded/promotion', array(
			$this,
			'updatePromotionPageStatusToPublish'
		) );
		add_action( 'wiloke-listing-tools/after-changed-payment-status/promotion', array(
			$this,
			'updateChangedPaymentStatus'
		) );

		add_action( 'wilcity/single-listing/sidebar-promotion', array( $this, 'printOnSingleListing' ), 10, 2 );

		add_action( 'admin_init', array( $this, 'isInPromotionAdminArea' ) );
		add_action( 'wp_ajax_wilcity_fetch_listing_promotions', array( $this, 'fetchPromotionDetails' ) );
	}

	public function fetchPromotionDetails() {
		if ( ! isset( $_POST['postID'] ) || empty( $_POST['postID'] ) ) {
			wp_send_json_error( array(
				'msg' => esc_html__( 'The post id is required.', 'wiloke-listing-tools' ),
			) );
		}

		$aRawPromotions = PromotionModel::getListingPromotions( $_POST['postID'] );
		if ( empty( $aRawPromotions ) ) {
			wp_send_json_error( array(
				'msg' => esc_html__( 'There are no promotions.', 'wiloke-listing-tools' ),
			) );
		}

		$aPromotions        = array();
		$aRawPromotionPlans = GetSettings::getPromotionPlans();

		$aPromotionPlans = array();
		foreach ( $aRawPromotionPlans as $promotionKey => $aPlan ) {
			$aPromotionPlans[ $promotionKey ] = $aPlan;
		}

		foreach ( $aRawPromotions as $aPromotion ) {
			$position      = str_replace( 'wilcity_promote_', '', $aPromotion['meta_key'] );
			$aPromotions[] = array(
				'name'     => $aPromotionPlans[ $position ]['name'],
				'position' => $position,
				'preview'  => $aPromotionPlans[ $position ]['preview'],
				'expiryOn' => date_i18n( get_option( 'date_format' ), $aPromotion['meta_value'] )
			);
		}

		wp_send_json_success( $aPromotions );
	}

	public function isInPromotionAdminArea() {
		$aTopOfSearchSettings = $this->getPromotionField( 'top_of_search' );
	}

	public function printOnSingleListing( $post, $aSidebarSetting ) {
		
		$aSidebarSetting = wp_parse_args( $aSidebarSetting, array(
			'name'        => '',
			'conditional' => '',
			'promotionID' => '',
			'style'       => 'slider'
		) );
		
		$belongsTo = GetSettings::getPostMeta( $post->ID, 'belongs_to' );

		if ( ! empty( $belongsTo ) && ! GetSettings::isPlanAvailableInListing( $post->ID, 'toggle_promotion' ) ) {
			return '';
		}

		$aPromotionSettings = GetSettings::getPromotionSetting( $aSidebarSetting['promotionID'] );

		if ( ! is_array( $aPromotionSettings ) ) {
			return $aPromotionSettings;
		}

		if( isset($aPromotionSettings['name']) ) {
			unset($aPromotionSettings['name']);
		}

		$aSidebarSetting = array_merge($aSidebarSetting, $aPromotionSettings);

		$aSidebarSetting['orderby']                       = 'rand';
		$aSidebarSetting['order']                         = 'DESC';
		$aSidebarSetting['postType']                      = General::getPostTypeKeys( false, false );
		$aSidebarSetting['aAdditionalArgs']['meta_query'] = array(
			array(
				'key'     => GetSettings::generateListingPromotionMetaKey( $aSidebarSetting, true ),
				'compare' => 'EXISTS',
			)
		);

		$aAtts = array(
			'atts' => $aSidebarSetting
		);
		
		echo wilcitySidebarRelatedListings( $aAtts );
	}

	public function deletePromotion( $postID ) {
		if ( get_post_type( $postID ) != 'promotion' ) {
			return false;
		}

		$listingID = GetSettings::getPostMeta( $postID, 'listing_id' );
		if ( empty( $listingID ) ) {
			return false;
		}
		SetSettings::deletePostMeta( $listingID, $this->belongsToPromotionKey );
	}

	private function focusUpdatePromotionStatus( $postID, $status ) {
		global $wpdb;
		$wpdb->update(
			$wpdb->posts,
			array(
				'post_status' => $status
			),
			array(
				'ID' => $postID
			),
			array(
				'%s'
			),
			array(
				'%s'
			)
		);
	}

	public function updateChangedPaymentStatus( $aData ) {
		if ( $aData['newStatus'] !== 'pending' ) {
			$aBoostPostData = PaymentMetaModel::get( $aData['paymentID'], 'boost_post_data' );
			SetSettings::deletePostMeta( $aBoostPostData['postID'], 'promotion_wait_for_bank_transfer' );
		} else {
			$aBoostPostData = PaymentMetaModel::get( $aData['paymentID'], 'boost_post_data' );
			SetSettings::setPostMeta( $aBoostPostData['postID'], 'promotion_wait_for_bank_transfer', $aData['paymentID'] );
		}
	}

	public function deleteWaitForBankTransferStatus( $aData ) {
		if ( PaymentMetaModel::get( $aData['paymentID'], 'packageType' ) !== 'promotion' ) {
			return false;
		}
	}

	public function updatePromotionPageStatusToPublish( $aData ) {
		$promotionPageID = PaymentMetaModel::get( $aData['paymentID'], 'promotion_page_id' );
		if ( empty( $promotionPageID ) ) {
			return false;
		}

		wp_update_post( array(
			'ID'          => $promotionPageID,
			'post_status' => 'draft'
		) );

		wp_update_post( array(
			'ID'          => $promotionPageID,
			'post_status' => 'publish'
		) );
	}

	public function deletePromotionValue( $listingID, $position ) {
		SetSettings::deletePostMeta( $listingID, 'promote_' . $position );

		if ( strpos( $position, 'top_of_search' ) !== false ) {
			$this->updateMenuOrder( $listingID, $position, false );
		}

		$promotionID = GetSettings::getPostMeta( $listingID, $this->belongsToPromotionKey );
		if ( empty( $promotionID ) ) {
			return false;
		}
		$aRawPromotionPlans = GetSettings::getOptions( 'promotion_plans' );

		$isExpiredAll = true;
		$now          = current_time( 'timestamp' );

		foreach ( $aRawPromotionPlans as $aPlanSetting ) {
			$val = GetSettings::getPostMeta( $promotionID, 'promote_' . $aPlanSetting['position'] );
			if ( ! empty( $val ) ) {
				$val = abs( $val );
				if ( $val > $now ) {
					$isExpiredAll = false;
				}
			}
		}

		if ( $isExpiredAll ) {
			$this->focusUpdatePromotionStatus( $promotionID, 'draft' );
		}
	}

	private function deleteAllPlansOfListing( $listingID ) {
		$aRawPromotionPlans = GetSettings::getOptions( 'promotion_plans' );
		foreach ( $aRawPromotionPlans as $aPlanSetting ) {
			SetSettings::deletePostMeta( $listingID, 'promote_' . $aPlanSetting['position'] );
			if ( strpos( $aPlanSetting['position'], 'top_of_search' ) == false ) {
				$this->updateMenuOrder( $listingID, $aPlanSetting['position'], false );
			}
		}
	}

	/*
	 * Generate Key where stores promotion duration to Listing ID
	 *
	 * @since 1.2.0
	 */
	private function generateListingPromoteKey( $aPromotion ) {
		return 'promote_' . GetSettings::generateSavingPromotionDurationKey( $aPromotion );
	}

	private function generateScheduleKey( $position ) {
		return 'trigger_promote_' . $position . '_expired';
	}

	public function getPromotionPlans() {
		$this->aPromotionPlans = GetSettings::getPromotionPlans();

		return $this->aPromotionPlans;
	}

	public function getPromotionField( $field ) {
		$this->getPromotionPlans();

		return isset( $this->aPromotionPlans[ $field ] ) ? $this->aPromotionPlans[ $field ] : false;
	}

	/*
	 * Updating Listing Order
	 *
	 * @since 1.0
	 */
	private function updateMenuOrder( $listingID, $promotionKey, $isPlus = true ) {
		$aTopOfSearchSettings = $this->getPromotionField( $promotionKey );
		if ( $aTopOfSearchSettings ) {
			$menuOrder = get_post_field( 'menu_order', $listingID );
			if ( $isPlus ) {
				$menuOrder = abs( $menuOrder ) + abs( $aTopOfSearchSettings['menu_order'] );
			} else {
				$menuOrder = abs( $menuOrder ) - abs( $aTopOfSearchSettings['menu_order'] );
			}

			global $wpdb;
			$wpdb->update(
				$wpdb->posts,
				array(
					'menu_order' => $menuOrder
				),
				array(
					'ID' => $listingID
				),
				array(
					'%d'
				),
				array(
					'%d'
				)
			);
		}
	}

	/**
	 * Set a expiry promotion cron job
	 *
	 * $position This var contains promotion ID already
	 * @since 1.2.0
	 * @return null
	 */
	public function updatedListingPromotion( $metaID, $objectID, $metaKey, $metaValue ) {
		if ( ( get_post_type( $objectID ) !== 'promotion' ) || GetSettings::getPostMeta( $objectID, 'canIgnoreHim' ) ) {
			return false;
		}

		if ( strpos( $metaKey, 'wilcity_promote_' ) !== false ) {
			$listingID = GetSettings::getPostMeta( $objectID, 'listing_id' );
   
			if ( empty( $listingID ) ) {
				return false;
			}

			$position = str_replace( 'wilcity_promote_', '', $metaKey );
			$this->clearExpirationPromotion( $position, $listingID );
   
			if ( is_numeric( $metaValue ) && ! empty( $metaValue ) ) {
				wp_clear_scheduled_hook( $this->generateScheduleKey( $position ), array( $listingID, $position ) );
				wp_schedule_single_event( $metaValue, $this->generateScheduleKey( $position ), array(
					$listingID,
					$position
				) );
    
				update_post_meta( $listingID, $metaKey, $metaValue );
				if ( strpos( $metaKey, 'top_of_search' ) !== false ) {
					$this->updateMenuOrder( $listingID, $metaKey );
				}
			}
		} else if ( $metaKey == 'wilcity_listing_id' ) {
			SetSettings::setPostMeta( $metaValue, $this->belongsToPromotionKey, $objectID );
		}
	}

	protected function getWooCommercePlanSettings( $productID ) {
		$aPromotionPlans = $this->getPromotionPlans();
		foreach ( $aPromotionPlans as $aPromotion ) {
			if ( $aPromotion['productAssociation'] == $productID ) {
				return $aPromotion;
			}
		}
	}

	protected function getWooCommercePlans() {
		if ( ! empty( $this->aWoocommercePlans ) ) {
			return $this->aWoocommercePlans;
		}

		$aPromotionPlans = $this->getPromotionPlans();
		foreach ( $aPromotionPlans as $aPromotion ) {
			$this->aWoocommercePlans[] = $aPromotion['productAssociation'];
		}

		return $this->aWoocommercePlans;
	}

	public function updateWooCommercePromotion( $aItem, $orderID ) {
		$aWooCommercePlans = $this->getWooCommercePlans();
		if ( ! in_array( $aItem['product_id'], $aWooCommercePlans ) ) {
			return false;
		}

		$aResponse = WooCommerceController::setupReceiptDirectly( array(
			'packageType' => 'promotion',
			'orderID'     => $orderID,
			'planName'    => get_the_title( $aItem['product_id'] ),
			'productID'   => $aItem['product_id'],
			'billingType' => wilokeListingToolsRepository()->get( 'payment:billingTypes', true )->sub( 'nonrecurring' )
		) );

		if ( ! isset( $aResponse['paymentID'] ) || empty( $aResponse['paymentID'] ) ) {
			wp_send_json_error( array(
				'msg' => esc_html__( 'We could not insert the payment id', 'wiloke-listing-tools' )
			) );
		}

		PaymentMetaModel::set( $aResponse['paymentID'], 'boost_post_data', array(
			'postID'    => Session::getSession( 'woocommerce_boost_listing_id' ),
			'plans'     => array( $this->getWooCommercePlanSettings( $aItem['product_id'] ) ),
			'productID' => $aItem['product_id']
		) );
	}

	public function cancelPostPromotion( $aInfo ) {
		$aBoostPostData = PaymentMetaModel::get( $aInfo['paymentID'], 'boost_post_data' );
		if ( empty( $aBoostPostData ) ) {
			return true;
		}
		$this->decreasePostPromotion( $aBoostPostData );
	}

	protected function clearExpirationPromotion( $position, $postID ) {
		wp_clear_scheduled_hook( $this->generateScheduleKey( $position ), array( $postID, $position ) );
		wp_clear_scheduled_hook( $this->generateScheduleKey( $position ), array( "$postID", "$position" ) );
	}

	public function decreasePostPromotion( $aBoostPostData ) {
		foreach ( $aBoostPostData['plans'] as $aInfo ) {
			$this->clearExpirationPromotion( $aInfo['position'], $aBoostPostData['postID'] );
			SetSettings::deletePostMeta( $aBoostPostData['postID'], $aInfo['position'] );
		}
	}

	public function updatePostPromotion( $aBoostPostData, $isSucceeded = false ) {
		$promotionID = GetSettings::getPostMeta( $aBoostPostData['postID'], $this->belongsToPromotionKey );

		if ( empty( $promotionID ) ) {
			$promotionID = wp_insert_post( array(
				'post_title'  => 'Promote ' . get_the_title( $aBoostPostData['postID'] ),
				'post_type'   => 'promotion',
				'post_status' => 'draft',
				'post_author' => User::getCurrentUserID()
			) );
		}

		SetSettings::setPostMeta( $promotionID, 'listing_id', $aBoostPostData['postID'] );
		foreach ( $aBoostPostData['plans'] as $aInfo ) {
			SetSettings::setPostMeta( $promotionID, $this->generateListingPromoteKey( $aInfo ), strtotime( '+ ' . $aInfo['duration'] . ' days' ) );
		}

		SetSettings::setPostMeta( $aBoostPostData['postID'], $this->belongsToPromotionKey, $promotionID );

		if ( $isSucceeded ) {
			wp_update_post( array(
				'ID'          => $promotionID,
				'post_status' => 'publish'
			) );
		}

		return $promotionID;
	}

	public function updateWoocommercePostPromotion( $aData ) {
		Session::destroySession( 'woocommerce_boost_listing_id' );
		$aPaymentIDs = PaymentModel::getPaymentIDsByWooOrderID( $aData['orderID'] );

		if ( empty( $aPaymentIDs ) ) {
			return true;
		}

		foreach ( $aPaymentIDs as $aPaymentID ) {
			$aBoostPostData = PaymentMetaModel::get( $aPaymentID['ID'], 'boost_post_data' );
			if ( ! empty( $aBoostPostData ) ) {
				$this->updatePostPromotion( $aBoostPostData, true );
			}
		}
	}

	public function cancelWooCommercePostPromotion( $aData ) {
		$aPaymentIDs = PaymentModel::getPaymentIDsByWooOrderID( $aData['orderID'] );
		if ( empty( $aPaymentIDs ) ) {
			return true;
		}

		foreach ( $aPaymentIDs as $aPaymentID ) {
			$aBoostPostData = PaymentMetaModel::get( $aPaymentID['ID'], 'boost_post_data' );
			if ( ! empty( $aBoostPostData ) ) {
				$this->decreasePostPromotion( $aBoostPostData );
			}
		}
	}

	public function updatePayPalPostPromotion( $aInfo ) {
		if ( $aInfo['gateway'] != 'paypal' && $aInfo['gateway'] != 'banktransfer' ) {
			return false;
		}

		$aBoostPostData = PaymentMetaModel::get( $aInfo['paymentID'], 'boost_post_data' );
		if ( empty( $aBoostPostData ) ) {
			$msg = 'The promotion plans are emptied. Please contact the theme author to solve this issue';
			if ( wp_doing_ajax() ) {
				wp_send_json_error( array( 'msg' => $msg ) );
			}
			throw new \Exception( $msg );
		}

		SetSettings::deletePostMeta( $aBoostPostData['postID'], 'promotion_wait_for_bank_transfer' );
		$this->updatePostPromotion( $aBoostPostData, true );
	}

	public function boostListing() {
		$this->middleware( [ 'isPublishedPost' ], array(
			'postID' => $_POST['postID']
		) );

		$noPlanMsg = esc_html__( 'You have to select 1 plan at least', 'wiloke-listing-tools' );

		if ( ! isset( $_POST['aPlans'] ) || empty( $_POST['aPlans'] ) ) {
			wp_send_json_error( array(
				'msg' => $noPlanMsg
			) );
		}

		$aSelectedPlans    = array();
		$aSelectedPlanKeys = array();

		$aPromotionPlans = $this->getPromotionPlans();

		foreach ( $_POST['aPlans'] as $aPlan ) {
			if ( isset( $aPlan['value'] ) && $aPlan['value'] == 'yes' ) {
				$aSelectedPlanKeys[] = GetSettings::generateSavingPromotionDurationKey( $aPlan );
			}
		}

		if ( empty( $aSelectedPlanKeys ) ) {
			wp_send_json_error( array(
				'msg' => $noPlanMsg
			) );
		}

		$total = 0;

		foreach ( $aPromotionPlans as $aPlan ) {
			if ( in_array( GetSettings::generateSavingPromotionDurationKey( $aPlan ), $aSelectedPlanKeys ) ) {
				$total            += floatval( $aPlan['price'] );
				$aSelectedPlans[] = $aPlan;
			}
		}

		if ( empty( $total ) ) {
			wp_send_json_error( array(
				'msg' => $noPlanMsg
			) );
		}

		Session::setSession( wilokeListingToolsRepository()->get( 'payment:category' ), 'promotion' );

		if ( isset( $_POST['gateway'] ) && ! empty( $_POST['gateway'] ) ) {
			$this->middleware( [ 'isGatewaySupported' ], array(
				'gateway' => $_POST['gateway']
			) );

			$aPlanSettings  = array(
				'total'       => $total,
				'planName'    => esc_html__( 'Promotion - ', 'wiloke-listing-tools' ) . get_the_title( $_POST['postID'] ),
				'billingType' => wilokeListingToolsRepository()->get( 'payment:billingTypes', true )->sub( 'nonrecurring' ),
				'packageType' => 'promotion',
				'oStripeData' => $_POST['oStripeData']
			);
			$aBoostPostData = array(
				'postID' => $_POST['postID'],
				'plans'  => $aSelectedPlans
			);

			switch ( $_POST['gateway'] ) {
				case 'paypal':
					$aResponse = PayPalController::setupReceiptDirectly( $aPlanSettings );

					if ( $aResponse['status'] !== 'success' ) {
						Session::destroySession( wilokeListingToolsRepository()->get( 'payment:category' ) );
						wp_send_json_error( array(
							'msg' => esc_html__( 'Oops! Something went wrong. The PayPal gateway could not execute', 'wiloke-listing-tools' )
						) );
					} else {
						PaymentMetaModel::set( $aResponse['paymentID'], 'boost_post_data', array(
							'postID' => $_POST['postID'],
							'plans'  => $aSelectedPlans
						) );
						wp_send_json_success( $aResponse );
					}
					break;

				case 'stripe':
					$aResponse = StripeController::setupReceiptDirectly( $aPlanSettings );

					if ( $aResponse['status'] == 'success' ) {
						PaymentMetaModel::set( $aResponse['paymentID'], 'boost_post_data', $aBoostPostData );
						$this->updatePostPromotion( $aBoostPostData, true );
						wp_send_json_success( array(
							'msg' => esc_html__( 'Congratulations! Your post has been boosted successfully', 'wiloke-listing-tools' )
						) );
					} else {
						Session::destroySession( wilokeListingToolsRepository()->get( 'payment:category' ) );
						wp_send_json_error( array(
							'msg' => $aResponse['msg']
						) );
					}

					break;

				case 'banktransfer':
					if ( GetSettings::getPostMeta( $_POST['postID'], 'promotion_wait_for_bank_transfer' ) ) {
						wp_send_json_error( array(
							'msg' => esc_html__( 'You have already submitted a request via Bank Transfer before, please complete the payment to boost your post.', 'wiloke-listing-tools' )
						) );
					} else {
						$aResponse = DirectBankTransferController::setupReceiptDirectly( $aPlanSettings );
						if ( $aResponse['status'] == 'error' ) {
							Session::destroySession( wilokeListingToolsRepository()->get( 'payment:category' ) );
							wp_send_json_error( array(
								'msg' => $aResponse['msg']
							) );
						} else {
							PaymentMetaModel::set( $aResponse['paymentID'], 'boost_post_data', $aBoostPostData );
							$promotionPageID = $this->updatePostPromotion( $aBoostPostData, false );
							PaymentMetaModel::set( $aResponse['paymentID'], 'promotion_page_id', $promotionPageID );
							do_action( 'wiloke/promotion/submitted', get_current_user_id(), $_POST['postID'] );

							wp_send_json_success( array(
								'msg' => esc_html__( 'Congratulations! Your submission has been approved. Please complete the payment to boost your post.', 'wiloke-listing-tools' )
							) );
						}
					}
					break;
			}
		} else {
			// Pay via WooCommerce
			$aProductIDs = array_map( function ( $aPlan ) {
				return $aPlan['productAssociation'];
			}, $aSelectedPlans );

			/*
			 * @WooCommerceController:removeProductFromCart
			 */
			global $woocommerce;
			do_action( 'wiloke-listing-tools/before-redirecting-to-cart', $aProductIDs );

			Session::setSession( 'woocommerce_boost_listing_id', $_POST['postID'] );

			global $woocommerce;
			wp_send_json_success( array(
				'productIDs' => $aProductIDs,
				'cartUrl'    => $woocommerce->cart->get_cart_url()
			) );
		}
	}

	public function getPaymentGateways() {
		$aPromotions = GetSettings::getOptions( 'promotion_plans' );
		if ( empty( $aPromotions ) ) {
			wp_send_json_error();
		}

		foreach ( $aPromotions as $aPromotion ) {
			if ( isset( $aPromotion['productAssociation'] ) && ! empty( $aPromotion['productAssociation'] ) ) {
				wp_send_json_error();
			}
		}

		$gateways = GetWilokeSubmission::getField( 'payment_gateways' );
		if ( empty( $gateways ) ) {
			wp_send_json_error( array(
				'msg' => esc_html__( 'You do not have any gateways. Please go to Wiloke Submission to set one.' )
			) );
		}

		$aGatewayKeys = explode(',', $gateways);
		$aGatewayNames = GetWilokeSubmission::getGatewaysWithName();

		$aGateways = array();
		foreach ( $aGatewayKeys as $gateway ){
			$aGateways[$gateway] = $aGatewayNames[$gateway];
		}

		wp_send_json_success( $aGateways );
	}

	public function fetchPromotions() {
		$aPromotions = GetSettings::getPromotionPlans();
		$currency    = GetWilokeSubmission::getField( 'currency_code' );
		$symbol      = GetWilokeSubmission::getSymbol( $currency );
		$position    = GetWilokeSubmission::getField( 'currency_position' );

		$promotionID = GetSettings::getPostMeta( $_POST['postID'], $this->belongsToPromotionKey );
		if ( ! empty( $promotionID ) && get_post_status( $promotionID ) === 'publish' ) {
			$now   = current_time( 'timestamp' );
			$order = 0;
			foreach ( $aPromotions as $key => $aPlanSetting ) {
				// Listing Sidebar without id won't display
				if ( $aPlanSetting['position'] == 'listing_sidebar' && empty( $aPlanSetting['id'] ) ) {
					continue;
				}
				$aReturnPromotions[ $order ] = $aPlanSetting;
				$val                         = GetSettings::getPostMeta( $promotionID, 'promote_' . $key );
				if ( ! empty( $val ) ) {
					$val = abs( $val );
					if ( $now < $val ) {
						$aReturnPromotions[ $order ]['isUsing'] = 'yes';
					}
				}
				$order ++;
			}
		} else {
			$aReturnPromotions = array_values( $aPromotions );
			foreach ( $aReturnPromotions as $key => $aPlanSetting ) {
				// Listing Sidebar without id won't display
				if ( $aPlanSetting['position'] == 'listing_sidebar' && empty( $aPlanSetting['id'] ) ) {
					unset( $aReturnPromotions[ $key ] );
				}
			}
		}

		wp_send_json_success(
			array(
				'plans'    => $aReturnPromotions,
				'position' => $position,
				'symbol'   => $symbol
			)
		);
	}

	public function onChangedPromotionStatus( $postID, $oPostAfter, $oPostBefore ) {
		if ( $oPostAfter->post_type !== 'promotion' || $oPostAfter->post_status == $oPostBefore->post_status ) {
			return false;
		}

		$listingID = GetSettings::getPostMeta( $postID, 'listing_id' );
		
		if ( empty( $listingID ) ) {
			return false;
		}

		$aPromotionPlans = $this->getPromotionPlans();
		if ( empty( $aPromotionPlans ) ) {
			return false;
		}

		$aPlanKeys = array_keys( $aPromotionPlans );
		if ( $oPostAfter->post_status == 'publish' ) {
			foreach ( $aPlanKeys as $position ) {
				$newVal = GetSettings::getPostMeta( $postID, 'promote_' . $position );
				if ( ! empty( $newVal ) ) {
					SetSettings::setPostMeta( $listingID, 'promote_' . $position, $newVal );
					if ( strpos( $position, 'top_of_search' ) !== false ) {
						$this->updateMenuOrder( $listingID, $position, true );
					}
				}
			}
			do_action( 'wiloke/promotion/approved', $listingID );
			SetSettings::setPostMeta( $listingID, $this->belongsToPromotionKey, $postID );
		} else {
			foreach ( $aPlanKeys as $position ) {
				$this->clearExpirationPromotion( $position, $listingID );
				if ( strpos( $position, 'top_of_search' ) !== false ) {
					$promotionExists = GetSettings::getPostMeta( $listingID, 'promote_' . $position );
					if ( ! empty( $promotionExists ) ) {
						$this->updateMenuOrder( $listingID, $position, false );
					}
				}
				SetSettings::deletePostMeta( $listingID, 'promote_' . $position );
			}
			SetSettings::deletePostMeta( $listingID, $this->belongsToPromotionKey );
		}
	}

	public function maybeChangeListingID( $metaID, $objectID, $metaKey, $metaValue ) {
		if ( ( get_post_type( $objectID ) !== 'promotion' ) || get_post_status( $objectID ) != 'publish' ) {
			return false;
		}

		if ( $metaKey == 'wilcity_listing_id' ) {
			$currentListing = GetSettings::getPostMeta( $metaValue, $this->belongsToPromotionKey );
			if ( $metaValue != $currentListing ) {
				SetSettings::setPostMeta( $objectID, 'canIgnoreHim', true );
				$this->deleteAllPlansOfListing( $currentListing );
				SetSettings::deletePostMeta( $objectID, 'canIgnoreHim' );
			}
		}
	}
}

<?php
namespace WilokeListingTools\Framework\Helpers;
use \WilokeListingTools\Framework\Helpers\FileSystem;
use WilokeListingTools\Frontend\User;

class QRCodeGenerator {
	private static function generateTicketName($orderId, $productID){
		return md5('ticket-'.$orderId . '-' . $productID ) . '.png';
	}

	public static function generateTicketUrl($userID, $fileName){
		$userURL = FileSystem::getUserFolderUrl($userID);
		return $userURL . $fileName;
	}

	public static function isSendQRCodeToEmail($productID){
		$status = GetSettings::getPostMeta($productID, 'is_send_qrcode');
		return $status == 'yes';
	}

	public static function generateTicket($orderID){
		$oOrder = wc_get_order($orderID);
		$aProducts = GetSettings::getDokanProductIDsByOrderID($orderID);

		if ( empty($aProducts) ){
			return false;
		}

		$name  = $oOrder->get_billing_first_name() . ' ' . $oOrder->get_billing_last_name();
		$purchasedDate = $oOrder->get_date_completed();
		$purchasedFrom = home_url('/');

		if ( !class_exists('\QRcode') ){
			include WILOKE_LISTING_TOOL_DIR . 'vendor/phpqrcode/phpqrcode.php';
		}

		$aQRCodes = array();

		foreach ($aProducts as $productID){
			if ( self::isSendQRCodeToEmail($productID) ){
				$userID = get_post_field('post_author', $productID);
				FileSystem::createUserFolder($userID);
				$dir = FileSystem::getUserFolderDir($userID);
				$url = FileSystem::getUserFolderUrl($userID);
				$aContents = array();

				$aContents['fullName'] = sprintf(esc_html__('Full name: %s', 'wiloke-listing-tools'), $name);
				if ( !empty($oOrder->get_billing_phone()) ){
					$aContents['phone'] = sprintf(esc_html__('Tel: %s', 'wiloke-listing-tools'), $oOrder->get_billing_phone());
				}

				$aContents['orderID'] = sprintf(esc_html__('Order ID: %s', 'wiloke-listing-tools'), $orderID);
				$aContents['productID'] = sprintf(esc_html__('Product ID: %s', 'wiloke-listing-tools'),  $productID);
				$aContents['productName'] = sprintf(esc_html__('Product name: %s', 'wiloke-listing-tools'),  get_the_title($productID));
				$aContents['purchasedDate'] = sprintf(esc_html__('Purchased date: %s', 'wiloke-listing-tools'), $purchasedDate);
				$aContents['purchasedFrom'] = sprintf(esc_html__('Purchased from: %s', 'wiloke-listing-tools'), $purchasedFrom);

				$aContents = apply_filters('wilcity/qrcode/my_products/content', $aContents, $productID, $orderID, $oOrder);

				$codeContents = implode("\n", $aContents);

				\QRcode::png($codeContents, $dir.self::generateTicketName($orderID,$productID), QR_ECLEVEL_L, 3);
				$aQRCodes[] = array(
					'dir' => $dir . self::generateTicketName($orderID,$productID),
					'url' => $url . self::generateTicketName($orderID,$productID)
				);
			}
		}

		return $aQRCodes;
	}
}
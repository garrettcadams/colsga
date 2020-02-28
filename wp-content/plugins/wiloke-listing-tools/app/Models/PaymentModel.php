<?php
namespace WilokeListingTools\Models;


use WilokeListingTools\AlterTable\AlterTablePaymentHistory;
use WilokeListingTools\Framework\Helpers\Time;

class PaymentModel {
	public static $aOrderOrderAndPaymentsRelationship = array();

	public static function getPackageTypeByOrderID($orderID){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		$packageType = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT packageType FROM $tblName WHERE wooOrderID=%d ORDER BY ID DESC",
				$orderID
			)
		);

		return $packageType;
	}

	public static function delete($paymentID){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		return $wpdb->delete(
			$tblName,
			array(
				'ID' => $paymentID
			),
			array(
				'%d'
			)
		);
	}

	public static function getPaymentIDsByWooOrderID($orderID, $isGetLatestPaymentID=false){
		if ( isset(self::$aOrderOrderAndPaymentsRelationship[$orderID]) ){
			if ( $isGetLatestPaymentID ){
				$aLastItem = end(self::$aOrderOrderAndPaymentsRelationship[$orderID]);
				return $aLastItem['ID'];
			}

			return self::$aOrderOrderAndPaymentsRelationship[$orderID];
		}else{
			global $wpdb;
			$tblName = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
			self::$aOrderOrderAndPaymentsRelationship[$orderID] = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT ID FROM $tblName WHERE wooOrderID=%d",
					$orderID
				),
				ARRAY_A
			);

			if ( $isGetLatestPaymentID ){
				$aLastItem = end(self::$aOrderOrderAndPaymentsRelationship[$orderID]);
				return $aLastItem['ID'];
			}

			return self::$aOrderOrderAndPaymentsRelationship[$orderID];
		}
	}

	public static function getPaymentIDByOrderIDAndPlanID($orderID, $planID){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM $tblName WHERE wooOrderID=%d AND planID=%d",
				$orderID, $planID
			)
		);
	}

	public static function getLastDirectBankTransferStatus($userID, $planID){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT status FROM $tblName WHERE userID=%d AND planID=%d and gateway=%s ORDER BY ID DESC",
				$userID, $planID, 'banktransfer'
			)
		);
	}

	public static function getLastDirectBankTransferID($userID, $planID){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM $tblName WHERE userID=%d AND planID=%d and gateway=%s ORDER BY ID DESC",
				$userID, $planID, 'banktransfer'
			)
		);
	}

	/**
	 * Get Payment Field by specifying payment id
	 *
	 * @param $paymentID @number
	 */
	public static function getField($fieldName, $paymentID){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT $fieldName FROM $tblName WHERE ID=%d",
				$paymentID
			)
		);
	}

	public static function getUserPaymentStatus($userID, $paymentID){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT status FROM $tblName WHERE userID=%d AND ID=%d",
				$userID, $paymentID
			)
		);
	}

	public static function isMyPaymentSession($userID, $paymentID){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		$id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM $tblName WHERE userID=%d AND ID=%d",
				$userID, $paymentID
			)
		);
		return empty($id) ? false : true;
	}

	/**
	 * Inserting a new data to wilcity_payment_history table
	 *
	 * @param $that object an instance of payment gateway
	 * @param $oReceipt object
	 *
	 * @return $sessionID
	 */
	public static function setPaymentHistory($that, $oReceipt, $orderID=null){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		$status = $wpdb->insert(
			$tblName,
			array(
				'userID'        => $oReceipt->userID,
				'planID'        => $oReceipt->planID,
				'packageType'   => $oReceipt->getPackageType(),
				'gateway'       => $that->gateway,
				'status'        => 'pending',
				'wooOrderID'    => !empty($orderID) ? $orderID : 0,
				'billingType'   => $that->getBillingType(),
				'updatedAt'     => Time::mysqlDateTime()
			),
			array(
				'%d',
				'%d',
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s'
			)
		);
		$paymentID = $wpdb->insert_id;
		if ( $status ){
			if ( empty($oReceipt->planID) ){
				PaymentMetaModel::set($paymentID, 'planName', $oReceipt->getPlanName());
			}
			return $paymentID;
		}

		return false;
	}

	/*
	 * Updating Payment Status
	 *
	 * @param $sessionID number
	 * @param $status string
	 *
	 * @return bool
	 */
	public static function updatePaymentStatus($status, $sessionID){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		$wpdb->update(
			$tblName,
			array(
				'status'    => $status,
				'updatedAt' => Time::mysqlDateTime(),
			),
			array(
				'ID' => $sessionID
			),
			array(
				'%s',
				'%s'
			),
			array(
				'%d'
			)
		);
	}

	public static function getLastSuspendedByPlan($planID, $userID){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		$id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM $tblName WHERE planID=%d AND userID=%d AND  status=%s ORDER BY ID DESC",
				$planID, $userID, 'suspended'
			)
		);
		return abs($id);
	}

	public static function getLastPaymentID($userID){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		$id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM $tblName WHERE userID=%d AND  status=%s ORDER BY ID DESC",
				$userID, 'active'
			)
		);
		return abs($id);
	}
}
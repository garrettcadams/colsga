<?php
namespace WilokeListingTools\Models;


use WilokeListingTools\AlterTable\AlterTablePaymentHistory;
use WilokeListingTools\AlterTable\AlterTablePlanRelationships;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\Time;
use WilokeListingTools\Framework\Store\Session;
use WilokeListingTools\Frontend\User;

final class PlanRelationshipModel {
	public static $tbl;

	/**
	 * For change plan
	 *
	 * var $userID Only administrator can use this param
	 * @since 1.2.0
	 */
	public static function updateNewPaymentID($newPaymentID, $oldPaymentID, $oldPlanID, $newPlanID, $userID=null){
		global $wpdb;

		$table = $wpdb->prefix . AlterTablePlanRelationships::$tblName;

		if ( !empty($userID) ){
			if ( !current_user_can('administrator') ){
				die;
			}
		}

		$userID = empty($userID) ? User::getCurrentUserID() : abs($userID);

		return $wpdb->update(
			$table,
			array(
				'planID' => $newPlanID,
				'paymentID' => $newPaymentID
			),
			array(
				'userID' => $userID,
				'planID' => $oldPlanID,
				'paymentID' => $oldPaymentID
			),
			array(
				'%d',
				'%d'
			),
			array(
				'%d',
				'%d',
				'%d'
			)
		);
	}

	public static function countListingsUserSubmittedInPlan($planID, $userID=''){
		$userID = empty($userID) ? get_current_user_id() : $userID;
		global $wpdb;
		$tbl = $wpdb->prefix . AlterTablePlanRelationships::$tblName;

		$total = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(ID) FROM $tbl WHERE planID=%d AND userID=%d",
				$planID, $userID
			)
		);
		return abs($total);
	}

	public static function getFirstObjectIDByPaymentID($paymentID){
		global $wpdb;
		$tbl = $wpdb->prefix . AlterTablePlanRelationships::$tblName;

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT objectID FROM $tbl WHERE paymentID=%d ORDER BY ID ASC",
				$paymentID
			)
		);
	}

	public static function getObjectIDsByPaymentID($paymentID){
		global $wpdb;
		$tbl = $wpdb->prefix . AlterTablePlanRelationships::$tblName;

		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT objectID FROM $tbl WHERE paymentID=%d",
				$paymentID
			),
			ARRAY_A
		);
	}

	public static function isPlanExisting($aInfo){
		global $wpdb;
		$tbl = $wpdb->prefix . AlterTablePlanRelationships::$tblName;

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM $tbl WHERE planID=%d AND objectID=%d AND paymentID=%d AND planID=%d AND userID=%d",
				$aInfo['planID'], $aInfo['objectID'], $aInfo['paymentID'], $aInfo['planID'], $aInfo['userID']
			)
		);
	}

	public static function getField($field, $planRelationshipID){
		global $wpdb;
		$tbl = $wpdb->prefix . AlterTablePlanRelationships::$tblName;

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT $field FROM $tbl WHERE ID=%d",
				$planRelationshipID
			)
		);
	}

	public static function getPlanIDByProductID($productID){
		global $wpdb;
		$tbl = $wpdb->postmeta;

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT post_id FROM $tbl WHERE meta_key=%s AND meta_value=%d",
				'wilcity_woocommerce_association', $productID
			)
		);
	}

	/*
	 * @param array $aInfo: planID, objectID, userID, paymentID
	 */
	public static function setPlanRelationship($aInfo){
		global $wpdb;
		$tbl = $wpdb->prefix . AlterTablePlanRelationships::$tblName;

		if (empty($aInfo['objectID'])) {
		    return false;
        }

		$isPlanExisting = self::isPlanExisting($aInfo);
		if ( $isPlanExisting ){
			$planRelationshipID = $isPlanExisting;
			$wpdb->update(
				$tbl,
				array(
					'updatedAtGMT'  => Time::getAtomUTCString()
				),
				array(
					'planID'        => $aInfo['planID'],
					'objectID'      => $aInfo['objectID'],
					'userID'        => $aInfo['userID'],
					'paymentID'     => $aInfo['paymentID']
				),
				array(
					'%s'
				),
				array(
					'%d',
					'%d',
					'%d',
					'%d'
				)
			);
		}else{
			$wpdb->insert(
				$tbl,
				array(
					'planID'        => $aInfo['planID'],
					'objectID'      => $aInfo['objectID'],
					'userID'        => $aInfo['userID'],
					'paymentID'     => $aInfo['paymentID'],
					'updatedAtGMT'  => Time::getAtomUTCString()
				),
				array(
					'%d',
					'%d',
					'%d',
					'%d',
					'%s'
				)
			);
			$planRelationshipID = $wpdb->insert_id;
		}

		Session::setSession(wilokeListingToolsRepository()->get('payment:sessionRelationshipStore'), $planRelationshipID);
		return $planRelationshipID;
	}

	public static function deletePlanRelationship($planID, $objectID, $userID){
		global $wpdb;
		$tbl = $wpdb->prefix . AlterTablePlanRelationships::$tblName;
		return $wpdb->delete(
			$tbl,
			array(
				'planID' => $planID,
				'objectID' => $objectID,
				'userID' => $userID
			),
			array(
				'%d',
				'%d',
				'%d'
			)
		);
	}

	public static function getUsedNonRecurringPlan(RemainingItems $that){
		global $wpdb;
		$planRelationshipTbl = $wpdb->prefix . AlterTablePlanRelationships::$tblName;
		$postTbl = $wpdb->posts;
		$historyTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT($planRelationshipTbl.objectID) FROM $planRelationshipTbl LEFT JOIN $postTbl ON ($postTbl
.ID=$planRelationshipTbl.objectID) LEFT JOIN $historyTbl ON ($historyTbl.ID=$planRelationshipTbl
.paymentID) WHERE $planRelationshipTbl.objectID != 0 AND $planRelationshipTbl.planID=%d AND 
$planRelationshipTbl.paymentID=%d AND $planRelationshipTbl.userID=%d",
				$that->getPlanID(), $that->getPaymentID(), $that->getUserID()
			)
		);
	}

	public static function getUsedRecurringPlan(RemainingItems $that){
		global $wpdb;
        $planRelationshipTbl = $wpdb->prefix . AlterTablePlanRelationships::$tblName;
		$timestampToDate = Time::toAtomUTC($that->getNextBillingDateGMT());

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT($planRelationshipTbl.objectID) FROM $planRelationshipTbl WHERE $planRelationshipTbl.objectID !=
 0 AND $planRelationshipTbl.planID=%d AND $planRelationshipTbl.paymentID=%d AND $planRelationshipTbl.userID=%d AND $planRelationshipTbl.updatedAtGMT >= (%s - INTERVAL %d DAY)",
				$that->getPlanID(), $that->getPaymentID(), $that->getUserID(), $timestampToDate, $that->getDuration()
			)
		);
	}
}

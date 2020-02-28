<?php

namespace WilokeListingTools\Models;


class ListingModel {
	private static $aCache = array();

	public static function listingBelongsToPromotion($listingID){
		global $wpdb;
		$key = 'promotion_'.$listingID;

		if ( isset(self::$aCache[$key]) ){
			return self::$aCache[$key];
		}
		$postTbl = $wpdb->posts;
		$postmetaTbl = $wpdb->postmeta;

		$aRawResults = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM $postmetaTbl LEFT JOIN $postTbl ON ($postTbl.ID = $postmetaTbl.post_id) WHERE $postmetaTbl.post_id=%d AND $postTbl.post_status='publish' AND $postmetaTbl.meta_key='wilcity_belongs_to_promotion'",
				$listingID
			),
			ARRAY_A
		);

		if ( empty($aRawResults) ){
			self::$aCache[$key] = false;
			return false;
		}

		$aResults = array();
		foreach ($aRawResults as $aValue){
			$aResults[] = array(
				'title' => get_the_title($aValue['meta_value']),
				'id'    => $aValue['meta_value']
			);
		}

		self::$aCache[$key] = $aResults;
		return $aResults;
	}
}
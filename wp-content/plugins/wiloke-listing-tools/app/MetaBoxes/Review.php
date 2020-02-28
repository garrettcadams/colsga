<?php
namespace WilokeListingTools\MetaBoxes;

use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Models\ReviewMetaModel;
use WilokeListingTools\Models\ReviewModel;

class Review {
	public function __construct() {
		add_action('cmb2_admin_init', array($this, 'renderMetaboxFields'));
		add_action('wiloke-listing-tools/save-meta-boxes', array($this, 'doNotSaveReviewCat'));
		add_action('save_post_review', array($this, 'saveReviewDetails'), 10, 1);
	}

	public function doNotSaveReviewCat($aData){
		if ( isset($aData['review_category']) ){
			unset($aData['review_category']);
		}
		return $aData;
	}

	public static function getParentID(){
		if ( isset($_GET['post']) ){
			return wp_get_post_parent_id($_GET['post']);
		}

		return '';
	}

	public function saveReviewDetails($reviewID){
		if ( !isset($_POST['wiloke_custom_field']) ){
			return false;
		}

		if ( !isset($_POST['wiloke_custom_field']['review_category']) ){
			return false;
		}

		foreach ($_POST['wiloke_custom_field']['review_category'] as $key => $score){
			$score = absint($score);
			$isSucceeded = ReviewMetaModel::setReviewMeta($reviewID, $key, $score);
			if ( !$isSucceeded ){
				$msg = esc_html__('We could not insert your review.', 'wiloke-listing-tools');
				if ( wp_doing_ajax() ){
					wp_send_json_error(array('msg'=>$msg));
				}else{
					wp_die($msg);
				}
			}
			$parentID = wp_get_post_parent_id($reviewID);
			if ( !empty($parentID) ){
				$averageScore = ReviewMetaModel::getAverageReviews($parentID);
				SetSettings::setPostMeta($parentID, 'average_reviews', $averageScore);
			}
		}
	}

	public function renderMetaboxFields(){
		if ( !General::isPostType('review') || (isset($_GET['post']) && is_array($_GET['post'])) ){
			return false;
		}

		$aReviews = wilokeListingToolsRepository()->get('reviews:metaBoxes', true)->sub('gallery');
		new_cmb2_box($aReviews);

		$aParent = wilokeListingToolsRepository()->get('reviews:metaBoxes', true)->sub('parent');
		new_cmb2_box($aParent);

		$parentID = isset($_GET['post']) ? wp_get_post_parent_id($_GET['post']) : '';

		if ( !empty($parentID) ){
			$aDetails = GetSettings::getOptions(General::getReviewKey('details', get_post_type($parentID)));

			if ( !empty($aDetails) ){
				$aFields = array();
				foreach ($aDetails as $aDetail){
					$score = '';
					if ( isset($_GET['post']) && !empty($_GET['post']) ){
						$score = ReviewMetaModel::getReviewMeta($_GET['post'], $aDetail['key']);
					}

					$aFields[] = array(
						'type'          => 'wiloke_field',
						'fieldType'     => 'input',
						'id'            => 'review_category:'.$aDetail['key'],
						'name'          => $aDetail['name'],
						'value'         => $score
					);
				}

				$aReviewDetails = array(
					'id'            => 'review_details',
					'title'         => 'Review Categories Score (Score Scale: ' . GetSettings::getOptions(General::getReviewKey('mode', get_post_type($parentID))) . ')',
					'object_types'  => array('review'),
					'context'       => 'normal',
					'priority'      => 'low',
					'save_fields'   => false,
					'show_names'    => true, // Show field names on the left
					'fields'        => $aFields
				);

				new_cmb2_box($aReviewDetails);
			}
		}
	}
}
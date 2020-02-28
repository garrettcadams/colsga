<?php
/*
 * Template Name: Wilcity AddListing
 */
use WilokeListingTools\Controllers\AddListingController;
use \WilokeListingTools\Framework\Helpers\General;

if ( class_exists('\WilokeListingTools\Controllers\AddListingController') ){
	AddListingController::saveListingIDToSession();
}

get_header();
use WilokeListingTools\Frontend\User as WilokeUser;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\Submission;

global $wiloke, $post;
$listingType = isset($_GET['listing_type']) && !empty($_GET['listing_type']) ? $_GET['listing_type'] : General::getDefaultPostTypeKey(false, true);
?>
	<div class="wil-content">
		<section class="wil-section bg-color-gray-2 pt-30">
            <?php do_action('wilcity/wiloke-submission/addlisting/before-container', array('postType'=>$listingType)); ?>
            <div class="container">
                <div id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-addlisting')); ?>" class="wil-addlisting row">
                    <?php
                    if ( !WilokeUser::canSubmitListing() ){
	                    do_action('wilcity/can-not-submit-listing');
                    }else{
	                    $aPostTypeSupported = Submission::getSupportedPostTypes();
	                    if ( !in_array($listingType, $aPostTypeSupported) ){
		                    WilokeMessage::message( array(
			                    'msg'        => sprintf( __( 'Oops! %s type is not supported.', 'wilcity' ), $_REQUEST['listing_type'] ),
			                    'msgIcon'    => 'la la-bullhorn',
			                    'status'     => 'danger',
			                    'hasMsgIcon' => true
		                    ) );
	                    }else {
		                    ?>
                            <div class="col-md-4 col-lg-4 md-hide js-sticky">
			                    <?php do_action('wiloke/wilcity/addlisting/print-sidebar-items', $post); ?>
                            </div>
                            <div class="col-md-8 col-lg-8 ">
			                    <?php
                                    do_action('wiloke/wilcity/addlisting/print-fields', $post);
                                ?>
                            </div>
		                    <?php
	                    }
                    }
                    ?>
                </div>
            </div>
		</section>
	</div>
<?php
get_footer();

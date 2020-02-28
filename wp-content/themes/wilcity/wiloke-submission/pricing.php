<?php
/*
 * Template Name: Wilcity Package Page
 */

get_header();
use WilokeListingTools\Frontend\User as WilokeUser;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Models\UserModel;
use WilokeListingTools\Framework\Helpers\DebugStatus;
use WilokeListingTools\Framework\Helpers\Submission;
use WilokeListingTools\Frontend\SingleListing;

global $wiloke;
?>
	<div class="wil-content">
        <section class="wil-section bg-color-gray-2">
			<div class="container">
				<div class="row" data-col-xs-gap="20">
					<?php
					if ( have_posts() ) {
						while ( have_posts() ) {
							the_post();
							the_content();
						}
					}
					?>
				</div>
			</div>
		</section>
	</div>
<?php
get_footer();

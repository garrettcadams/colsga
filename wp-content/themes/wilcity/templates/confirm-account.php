<?php
/*
 * Template Name: Wilcity Confirm Account
 */
use \WilokeListingTools\Framework\Helpers\SetSettings;
use \WilokeListingTools\Frontend\User as WilokeUser;

get_header();
global $wiloke;
?>
	<div class="wil-content">
		<section class="wil-section bg-color-gray-2 pt-30">
			<div class="container">
				<div id="wilcity-confirm-account" class="row">
					<?php
                        if ( have_posts() ){
                            while (have_posts()){
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
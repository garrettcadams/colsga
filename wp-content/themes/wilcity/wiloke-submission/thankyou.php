<?php
/*
 * Template Name: Wilcity Thank You
 */
use \WilokeListingTools\Framework\Store\Session;

get_header();
global $wiloke;
?>
	<div class="wil-content">
		<section class="wil-section bg-color-gray-2 pt-30">
			<div class="container">
				<div id="wilcity-thankyout" class="row">
					<?php do_action('wilcity/wiloke-submission/thankyou/before_content'); ?>
					<?php
                    if ( Session::getSession(wilokeListingToolsRepository()->get('addlisting:isPayViaDirectBankTransfer')) ){
                        ?>
                        <h3><?php esc_html_e('Thank for your order!', 'wilcity'); ?></h3>
                        <p><?php esc_html_e('Please send your cheque to the following bank account to complete this payment:', 'wilcity'); ?></p>
                        <div id="wilcity-our-bank-account">
		                    <?php echo do_shortcode('[wilcity_my_bank_accounts]'); ?>
                        </div>
                        <?php
                    }else{
	                    if ( have_posts() ){
		                    while (have_posts()){
			                    the_post();
			                    if ( $message = Session::getSession('errorPayment', true) ){
				                    Wiloke::ksesHTML($message);
			                    }else{
				                    the_content();
			                    }
		                    }
	                    }
                    }
                    ?>
					<?php do_action('wilcity/wiloke-submission/thankyou/after_content'); ?>
				</div>
			</div>
		</section>
	</div>
<?php
get_footer();

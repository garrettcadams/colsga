<?php
/*
 * Template Name: Wilcity Become an author
 */

use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Frontend\User as WilokeUser;

global $post;
get_header();
?>
	<div id="wilcity-become-an-author" class="wil-content">
		<div class="wil-section bg-color-gray-2">
			<div class="container">
                <?php
                if ( defined('ELEMENTOR_VERSION') && ( \Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode() ) ) :
                    ?>
                <div class="row">
                    <div class="col-md-8 col-lg-8 col-xs-offset-0 col-sm-offset-0 col-md-offset-2 col-lg-offset-2">
                    <?php
	                while (have_posts()) :
		                the_post();
		                the_content();
                    endwhile;
                    ?>
                    </div>
                </div>
                <?php
                else:
                    if ( !is_user_logged_in() ):
                        WilokeMessage::message(
                           array(
                               'msg' => esc_html__('You do not have permission to access this page', 'wilcity'),
                               'status' => 'danger'
                           )
                        );
                    elseif ( !WilokeUser::isAccountConfirmed() ):
                        do_action('wilcity/print-need-to-verify-account-message');
                    else:
                    ?>
                        <div class="row">
                            <div class="col-md-8 col-lg-8 col-xs-offset-0 col-sm-offset-0 col-md-offset-2 col-lg-offset-2">
                            <?php
                                if ( \WilokeListingTools\Frontend\User::canSubmitListing() ) :
                                    ?>
                                    <a class="wil-btn mt-20 wil-btn--secondary wil-btn--round wil-btn--lg wil-btn--block " href="<?php echo esc_url(GetWilokeSubmission::getField('package', true)); ?>"><i class="la la-home"></i> <?php esc_html_e('Take me to the Add Listing page', 'wilcity'); ?></a>
                                    <?php
                                else:
                            ?>
                                <?php if ( have_posts() ) : ?>
                                    <div v-if="!isConfirmed">
                                        <div class="content-box_module__333d9">
                                            <div class="content-box_body__3tSRB">
                                                <h1 class="mt-0 wil-text-center" style="font-size: 32px;"><?php the_title() ?></h1>
                                                <?php
                                                while (have_posts()) :
                                                    the_post();
                                                ?>
                                                    <div class="clearfix">
                                                        <?php the_content(); ?>
                                                    </div>
                                                    <form action="#" class="mt-20">
                                                        <?php do_action('wilcity/agree-to-terms-and-policy'); ?>
                                                        <button class="wil-btn mt-20 wil-btn--primary wil-btn--round wil-btn--lg wil-btn--block" :class="btnClass" @click.prevent="submitBecomeAnAuthor" type="submit"><?php esc_html_e('Submit', 'wilcity'); ?><div v-show="isSubmitting" class="pill-loading_module__3LZ6v"><div class="pill-loading_loader__3LOnT"></div></div></button>
                                                    </form>
                                                <?php  endwhile; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-show="isConfirmed" class="temporary-hidden">
                                        <div class="content-box_module__333d9">
                                            <div class="content-box_body__3tSRB">
                                                <h5 class="mt-0"><?php echo apply_filters('wilcity/become-an-author/congrats-message', esc_html__('Thanks for being an contributor. You can now upload your listing to the website.', 'wilcity')); ?></h5>
                                                <a class="wil-btn mt-20 wil-btn--secondary wil-btn--round wil-btn--lg wil-btn--block " href="<?php echo esc_url(GetWilokeSubmission::getField('package', true)); ?>"><i class="la la-home"></i> <?php esc_html_e('Take me to the Add Listing page', 'wilcity'); ?></a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; wp_reset_postdata(); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
			</div>
		</div>
	</div>
<?php
get_footer();
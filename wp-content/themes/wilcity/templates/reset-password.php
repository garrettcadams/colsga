<?php
$action = apply_filters('wilcity/reset-password/action', 'rp');
if ( !defined('WILCITY_FIX_COOKIE_ON_TEMPLATE') || !WILCITY_FIX_COOKIE_ON_TEMPLATE ){
	if ( isset( $_GET['action'] ) && $_GET['action'] == $action ) {
		list( $rp_path ) = explode( '?', wp_unslash( $_SERVER['REQUEST_URI'] ) );
		$rp_cookie = 'wp-resetpass-' . COOKIEHASH;
		if ( isset( $_GET['key'] ) ) {
			$value = sprintf( '%s:%s', wp_unslash( $_GET['login'] ), wp_unslash( $_GET['key'] ) );
			setcookie( $rp_cookie, $value, 0, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
			wp_safe_redirect( remove_query_arg( array( 'key', 'login' ) ) );
			exit;
		} else {
			if ( isset( $_COOKIE[ $rp_cookie ] ) && 0 < strpos( $_COOKIE[ $rp_cookie ], ':' ) ) {
				list( $rp_login, $rp_key ) = explode( ':', wp_unslash( $_COOKIE[ $rp_cookie ] ), 2 );
				$oUser = check_password_reset_key( $rp_key, $rp_login );
				if ( isset( $_POST['pass1'] ) && ! hash_equals( $rp_key, $_POST['rp_key'] ) ) {
					$oUser = false;
				}
			} else {
				$oUser = false;
			}

			if ( ! $oUser || is_wp_error( $oUser ) ) {
				setcookie( $rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
				$pageURL = get_permalink();
				if ( $oUser && $oUser->get_error_code() === 'expired_key' ) {
					$pageURL = add_query_arg(
						array(
							'action' => 'lostpassword',
							'error'  => 'expiredkey'
						),
						$pageURL
					);
					wp_redirect( $pageURL );
				} else {
					$pageURL = add_query_arg(
						array(
							'action' => 'lostpassword',
							'error'  => 'invalidkey'
						),
						$pageURL
					);
					wp_redirect( $pageURL );
				}
			}
		}
	}
}
/*
 * Template Name: Wilcity Reset Password
 */
use \WilokeListingTools\Framework\Helpers\GetSettings;
use \WilokeListingTools\Framework\Helpers\Cookie;

get_header();
if ( have_posts() ):
	while (have_posts()) : the_post();
		$imgBgID = GetSettings::getPostMeta($post->ID, 'background_image_id');
		$imgBg = wp_get_attachment_image_url($imgBgID, 'large');
		if ( empty($imgBg) ){
			$imgBg = GetSettings::getPostMeta($post->ID, 'background_image');
		}
		?>
        <div id="wilcity-reset-password" class="wil-content">
            <section class="wil-section bg-cover pd-0" style="background-image:url(<?php echo esc_url($imgBg); ?>);">
                <div class="wil-overlay"></div>
                <div class="container">
                    <div class="row">
                        <div class="col-sm-8 col-md-8 col-lg-4 col-xs-offset-0 col-sm-offset-2 col-md-offset-2 col-lg-offset-4 ">
                            <div class="wil-tb full">
                                <div class="wil-tb__cell">
									<?php
									the_content();
									if ( is_user_logged_in() ):
										WilokeMessage::message(array(
											'msg' => esc_html__('You are already logged in the site.', 'wilcity'),
											'status'=>'info'
										));
									else:
										if ( isset($_GET['action']) && $_GET['action'] == $action ){
											list( $rp_path ) = explode( '?', wp_unslash( $_SERVER['REQUEST_URI'] ) );
											$rp_cookie = 'wp-resetpass-' . COOKIEHASH;
											if ( !isset( $_GET['key'] ) ) {
												?>
                                                <message :status="msgStatus" :msg="msg"></message>
                                                <div v-show="!hideForm">
                                                    <form action="#" @submit.prevent="updatePassword">
                                                        <input type="hidden" id="wilcity-rp-username"
                                                               value="<?php echo isset($rp_login) ? esc_attr( $rp_login ) : Cookie::getCookie('rp_login'); ?>">
                                                        <input type="hidden" id="wilcity-rp-key"
                                                               value="<?php echo isset($rp_key) ? esc_attr( $rp_key ) : Cookie::getCookie('rp_key'); ?>">
                                                        <div :class="wrapperResetPasswordField">
                                                            <div class="field_wrap__Gv92k">
                                                                <input v-model="newPassword" class="field_field__3U_Rt"
                                                                       type="password" value=""/><span
                                                                        class="field_label__2eCP7 text-ellipsis required"><?php esc_html_e( 'New Password', 'wilcity' ); ?></span><span
                                                                        class="bg-color-primary"></span>
                                                            </div>
                                                        </div>
                                                        <div class="o-hidden ws-nowrap temporary-hidden">
                                                            <button v-cloak :class="resetPassWordLinkBtnClass"
                                                                    type="submit"><?php esc_html_e( 'Reset Password', 'wilcity' ); ?></button>
                                                        </div>
                                                    </form>
                                                </div>
												<?php
											}
										}else{
											?>
                                            <message :status="msgStatus" :msg="msg"></message>
                                            <div v-show="!hideForm">
												<?php
												if ( ( isset($_GET['action']) && $_GET['action'] == 'lostpassword' )  ) {
													WilokeMessage::message( array(
														'msg'    => esc_html__( 'Your password reset link appears to be invalid or expired. Please request a new link below.', 'wilcity' ),
														'status' => 'danger'
													) );
												}else{
													WilokeMessage::message( array(
														'msg'    => esc_html__( 'Please request a new link below.', 'wilcity' ),
														'status' => 'info'
													) );
												}
												?>
                                                <form action="#" @submit.prevent="sendResetPasswordLink">
                                                    <div class="field_module__1H6kT field_style2__2Znhe mb-15 js-field">
                                                        <div class="field_wrap__Gv92k">
                                                            <input v-model="usernameOrEmail" class="field_field__3U_Rt" type="text"/><span class="field_label__2eCP7 text-ellipsis required"><?php esc_html_e('Username or Email Address', 'wilcity'); ?></span><span class="bg-color-primary"></span>
                                                        </div>
                                                    </div>
                                                    <div class="o-hidden ws-nowrap temporary-hidden">
                                                        <button :class="getResetLinkBtnClass" type="submit"><?php esc_html_e('Get New Password', 'wilcity'); ?></button>
                                                    </div>
                                                </form>
                                            </div>
										<?php }
									endif;
									?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
		<?php
	endwhile;endif;wp_reset_postdata();
get_footer();

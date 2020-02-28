<?php
use \WilokeListingTools\Framework\Helpers\GetSettings;
use \WilokeListingTools\Framework\Helpers\HTML;
use \WilokeListingTools\Framework\Store\Session;

function wilcity_render_custom_login_sc($atts){
    global $wiloke;
    $loginRedirectTo = home_url('/');
    if($wiloke->aThemeOptions['login_redirect_type'] == 'specify_page') {
        $loginRedirectTo = get_permalink($wiloke->aThemeOptions['login_redirect_to']);
    } elseif( isset( $_GET['redirect_to'] ) ) {
        $loginRedirectTo = esc_url( $_GET['redirect_to'] );
    }

    $afterRegisteringRedirectTo = isset($wiloke->aThemeOptions['created_account_redirect_to']) && !empty($wiloke->aThemeOptions['created_account_redirect_to']) ? get_permalink($wiloke->aThemeOptions['created_account_redirect_to']) : home_url('/');

	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'login';
	$userLogin = '';
	$userEmail = '';
	$userPassword = '';

	$classWrapper = 'log-reg-template_module__2BZGH clearfix';
	if ( !empty($atts['extra_class']) ){
		$classWrapper .= ' ' .  $atts['extra_class'];
    }
	?>
	<div class="<?php echo esc_attr($classWrapper); ?>">
		<div class="log-reg-template_left__3D6wA">
			<div class="wil-tb full">
				<div class="wil-tb__cell">

					<!-- log-reg-action_module__h5MhW -->
					<div class="log-reg-action_module__h5MhW">
						<div class="log-reg-action_logo__37V3f">
                            <?php HTML::renderSiteLogo(); ?>
                        </div>
						<div class="log-reg-action_formWrap__1HP4n">
                            <?php switch($action){
                                case 'login': ?>
		                            <?php if ( !empty($atts['login_section_title']) ) : ?>
                                        <h2 class="log-reg-action_title__2932Y"><?php Wiloke::ksesHTML($atts['login_section_title']); ?></h2>
		                            <?php endif; ?>
		                            <?php break; ?>
                                <?php case 'register': ?>
	                                <?php if ( !empty($atts['register_section_title']) ) : ?>
                                        <h2 class="log-reg-action_title__2932Y"><?php Wiloke::ksesHTML($atts['register_section_title']); ?></h2>
	                                <?php endif; ?>
	                                <?php break; ?>
	                            <?php case 'rp': ?>
		                            <?php if ( !empty($atts['rp_section_title']) ) : ?>
                                        <h2 class="log-reg-action_title__2932Y"><?php Wiloke::ksesHTML($atts['rp_section_title']); ?></h2>
		                            <?php endif; ?>
		                            <?php break; ?>
                                <?php
                            }?>

                            <?php if ( $atts['social_login_type'] != 'off' ): ?>
                                <div id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-social-login')); ?>" class="mb-30">
                                    <?php if ( $atts['social_login_type'] == 'fb_default' ) : ?>
                                        <facebook></facebook>
                                    <?php else: ?>
                                        <?php echo do_shortcode($atts['social_login_shortcode']); ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <?php
                            switch ($action){
	                            case 'rp':
                                    $aRPStatus = Session::getSession('rp_status', true);
		                            $aRPStatus = empty($aRPStatus) ? $aRPStatus : maybe_unserialize($aRPStatus);
                                    if ( isset($aRPStatus['status']) && $aRPStatus['status'] == 'success' ){
	                                    WilokeMessage::message(array(
		                                    'status'       => 'success',
		                                    'hasRemoveBtn' => false,
		                                    'hasMsgIcon'   => false,
		                                    'msgIcon'      => 'la la-envelope-o',
		                                    'msg'          => $aRPStatus['msg']
	                                    ));
                                    }else{
                                        if ( isset($aRPStatus['status']) && $aRPStatus['status'] == 'error' ){
	                                        WilokeMessage::message(array(
		                                        'status'       => 'danger',
		                                        'hasRemoveBtn' => false,
		                                        'hasMsgIcon'   => false,
		                                        'msgIcon'      => 'la la-envelope-o',
		                                        'msg'          => $aRPStatus['msg']
	                                        ));
                                        }
                                        ?>
                                        <form name="loginform" id="loginform" action="<?php echo esc_url( GetSettings::getCustomLoginPage() ); ?>" method="post">
                                            <?php
                                            HTML::renderInputField(array(
                                                'name' => 'user_login',
                                                'type' => 'text',
                                                'label'=> esc_html__('Username or Email Address', 'wilcity-shortcodes'),
                                                'value'=> $userLogin
                                            ));

                                            HTML::renderHiddenField(array(
                                                'name' => 'action',
                                                'value'=> 'rp'
                                            ));
                                            ?>
                                            <button type="submit" class="wil-btn mb-20 wil-btn--gradient wil-btn--md wil-btn--round wil-btn--block"><?php esc_html_e('Get New Password', 'wilcity-shortcodes'); ?></button>
                                        </form>
                                        <?php
	                                }
	                                break;
                                case 'login':
	                                if ( Session::getSession('login_error') ){
		                                WilokeMessage::message(array(
			                                'status'       => 'danger',
			                                'hasRemoveBtn' => false,
			                                'hasMsgIcon'   => false,
			                                'msgIcon'      => 'la la-envelope-o',
			                                'msg'          => Session::getSession('login_error', true)
		                                ));
	                                }

	                                ?>
                                    <form name="loginform" id="loginform" action="<?php echo esc_url( GetSettings::getCustomLoginPage() ); ?>" method="post">
                                        <?php
                                        HTML::renderInputField(array(
	                                        'name' => 'user_login',
	                                        'type' => 'text',
	                                        'label'=> esc_html__('Username or Email Address', 'wilcity-shortcodes'),
	                                        'value'=> $userLogin
                                        ));
                                        HTML::renderInputField(array(
	                                        'name' => 'user_password',
	                                        'type' => 'password',
	                                        'label'=> esc_html__('Password', 'wilcity-shortcodes'),
	                                        'value'=> ''
                                        ));

                                        HTML::renderHiddenField(array(
	                                        'name' => 'testcookie',
	                                        'value'=> 1
                                        ));

                                        HTML::renderHiddenField(array(
	                                        'name' => 'redirect_to',
	                                        'value'=> $loginRedirectTo
                                        ));

                                        HTML::renderHiddenField(array(
	                                        'name' => 'action',
	                                        'value'=> 'login'
                                        ));

		                                do_action( 'login_form' );
		                                do_action( 'wilcity/wiloke-listing-tools/custom-login-form' );
                                        ?>
		                                <div class="o-hidden ws-nowrap">
                                            <?php
                                            HTML::renderCheckboxField(array(
                                                'name' => 'rememberme',
                                                'value'=> 'forever',
                                                'label'=> esc_html__('Remember Me', 'wilcity-shortcodes')
                                            ));
                                            ?>
                                            <a class="wil-float-right td-underline" href="<?php echo esc_url(add_query_arg(array('action'=>'rp'), GetSettings::getCustomLoginPage())); ?>"><?php esc_html_e('Lost password', 'wilcity-shortcodes'); ?></a>
                                        </div>
                                        <button type="submit" class="wil-btn mb-20 wil-btn--gradient wil-btn--md wil-btn--round wil-btn--block"><?php esc_html_e('Login', 'wilcity-shortcodes'); ?></button>
                                    </form>
		                            <?php break; ?>
                                <?php case 'register':
                                        if ( GetSettings::userCanRegister() ) :
                                            if ( Session::getSession('register_error') ){
                                                $userLogin = $_POST['user_login'];
                                                $userEmail = wp_unslash($_POST['user_email']);
                                                $userPassword = wp_unslash($_POST['user_password']);

                                                WilokeMessage::message(array(
                                                    'status'       => 'danger',
                                                    'hasRemoveBtn' => false,
                                                    'hasMsgIcon'   => false,
                                                    'msgIcon'      => 'la la-envelope-o',
                                                    'msg'          => Session::getSession('register_error', true)
                                                ));
                                            }
                                    ?>
                                            <form name="registerform" id="registerform" action="<?php echo esc_url(add_query_arg( array('action'=>'register'), GetSettings::getCustomLoginPage() ), 'login_post' ); ?>" method="post" novalidate="novalidate">
                                                <?php
                                                HTML::renderInputField(array(
                                                    'name' => 'user_login',
                                                    'type' => 'text',
                                                    'label'=> esc_html__('Username', 'wilcity-shortcodes'),
                                                    'value'=> $userLogin
                                                ));
                                                HTML::renderInputField(array(
                                                    'name' => 'user_email',
                                                    'type' => 'email',
                                                    'label'=> esc_html__('Email', 'wilcity-shortcodes'),
                                                    'value'=> $userEmail
                                                ));
                                                HTML::renderInputField(array(
                                                    'name' => 'user_password',
                                                    'type' => 'password',
                                                    'label'=> esc_html__('Password', 'wilcity-shortcodes'),
                                                    'value'=> $userPassword
                                                ));
                                                HTML::renderHiddenField(array(
                                                    'name' => 'redirect_to',
                                                    'value'=> $afterRegisteringRedirectTo
                                                ));
                                                HTML::renderHiddenField(array(
                                                    'name' => 'action',
                                                    'value'=> 'register'
                                                ));
                                                do_action( 'register_form' );
                                                do_action( 'wilcity/wiloke-listing-tools/custom-register-form' );
                                                do_action( 'wilcity/agree-to-terms-and-policy/php' );
                                                ?>
                                                <button type="submit" class="wil-btn mb-20 wil-btn--gradient wil-btn--md wil-btn--round wil-btn--block"><?php esc_html_e('Register', 'wilcity-shortcodes'); ?></button>
                                            </form>
                                        <?php endif; ?>
                                <?php break; ?>
                            <?php }; ?>
						</div>
                        <?php
                        if ( GetSettings::userCanRegister() ) {
                            switch ($action){
                                case 'login':
	                                echo esc_html__('Don’t have an account?', 'wilcity-shortcodes') . ' <a href="'.wp_registration_url(). '">'.esc_html__('Register', 'wilcity-shortcodes').'</a>';
                                    break;
                                case 'register':
	                                echo '<a href="'.GetSettings::getCustomLoginPage(). '">'.esc_html__('Login with username and password?', 'wilcity-shortcodes').'</a>';
                                    break;
                                case 'rp':
                                    if ( !isset($aRPStatus) || !isset($aRPStatus['status']) || $aRPStatus['status'] !== 'success' ):
	                                    echo '<a href="'.GetSettings::getCustomLoginPage(). '">'.esc_html__('Login', 'wilcity-shortcodes').'</a> | <a href="'.wp_registration_url(). '">'.esc_html__('Register', 'wilcity-shortcodes').'</a>';
                                    endif;
                                    break;
                            }

                        }
                        ?>

					</div><!-- End / log-reg-action_module__h5MhW -->

				</div>
			</div>
		</div>
		<div class="log-reg-template_right__3aFwI">
            <?php if ( !empty($atts['login_bg_color']) ) : ?>
			<div class="wil-overlay" style="background-color: <?php echo esc_attr($atts['login_bg_color']); ?>;"></div>
            <?php endif; ?>
            <?php if ( !empty($atts['login_bg_img']) ) : ?>
			<div class="log-reg-template_bg__7KwPs bg-cover" style="background-image: url(<?php echo esc_url($atts['login_bg_img']); ?>);"></div>
            <?php endif; ?>
			<div class="wil-tb full">
				<div class="wil-tb__cell">
					<div class="log-reg-features_module__1x06b">
                        <?php if ( is_array($atts['login_boxes']) ) : ?>
                            <?php foreach ($atts['login_boxes'] as $aBox) :
                                if ( is_object($aBox) ){
                                    $aBox = get_object_vars($aBox);
                                }
                            ?>
                            <div class="textbox-2_module__15Zpj textbox-2_style-3__1U-rY clearfix">
                                <div class="textbox-2_icon__1xt9q color-primary"><i style="color: <?php echo esc_attr($aBox['icon_color']); ?>" class="<?php echo esc_attr($aBox['icon']); ?>"></i></div>
                                <h3 class="textbox-2_title__301U3" style="color: <?php echo esc_attr($aBox['text_color']); ?>"><?php Wiloke::ksesHTML($aBox['description']); ?></h3>
						    </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
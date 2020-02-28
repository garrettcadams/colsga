<?php
use WilokeListingTools\Framework\Helpers\GetSettings;

add_shortcode('wilcity_sidebar_business_info', 'wicitySidebarBusinessInfo');

function wilcitySidebarBusinessInfoAreSocialsEmpty($aSocialNetworks){
	if ( empty($aSocialNetworks) ){
	    return true;
    }
	foreach ($aSocialNetworks as $icon => $link){
		if ( !empty($link) ){
			return false;
		}
    }
    return true;
}

function wicitySidebarBusinessInfo($aArgs){
    global $post;
	$aAtts = \WILCITY_SC\SCHelpers::decodeAtts($aArgs['atts']);
	$aAtts = wp_parse_args(
		$aAtts,
		array(
			'name'          => esc_html__('Business Info', WILOKE_LISTING_DOMAIN),
			'icon'          => 'la la-qq',
			'desc'          => '',
			'currencyIcon'  => 'la la-dollar'
		)
	);

	if ( isset($aAtts['isMobile']) ){
		return apply_filters('wilcity/mobile/sidebar/business_info', $post, $aAtts);
	}

	$itemWrapperClass = 'mt-20 mt-sm-15';
	$address = GetSettings::getAddress($post->ID, false);
	$email = GetSettings::getListingEmail($post->ID);
	$phone = GetSettings::getListingPhone($post->ID);
	$website = !GetSettings::isPlanAvailableInListing($post->ID, 'toggle_website') ? '' : GetSettings::getPostMeta($post->ID, 'website');
	$aSocialNetworks = !GetSettings::isPlanAvailableInListing($post->ID, 'toggle_social_networks') ? '' : GetSettings::getPostMeta($post->ID, 'social_networks');
	$wrapperClass = apply_filters('wilcity/filter/class-prefix', 'wilcity-sidebar-item-business-info content-box_module__333d9');
	if ( !empty($address) || !empty($phone) || !empty($email) || !empty($website) || !wilcitySidebarBusinessInfoAreSocialsEmpty($aSocialNetworks)  ) :
	ob_start();
	?>
	<div class="<?php echo esc_attr($wrapperClass); ?>">
		<?php wilcityRenderSidebarHeader($aAtts['name'], $aAtts['icon']); ?>
		<div class="content-box_body__3tSRB">

			<?php
            $aInfo = apply_filters('wilcity/sidebar/business_info/order_show', array('email', 'phone', 'website',  'address',  'social', 'inbox') );
				foreach($aInfo as $name) {
					switch($name) :
						case 'address':
							if  ( !empty($address)  ) {
								\WILCITY_SC\SCHelpers::renderIconAndLink( $address, 'la la-map-marker', $address, array(
									'wrapperClass'     => $itemWrapperClass . ' text-pre',
									'isGoogle'         => true,
									'iconWrapperClass' => 'rounded-circle'
								));
							}
							break;
						case 'phone':
							if ( !empty($phone) && \WilokeListingTools\Frontend\SingleListing::isClaimedListing($post->ID) ) {
								\WILCITY_SC\SCHelpers::renderIconAndLink($phone, 'la la-phone', $phone, array(
									'wrapperClass'     => $itemWrapperClass,
									'isPhone' => true,
									'iconWrapperClass' => 'rounded-circle'
								));
							}
							break;
						case 'email':
							if ( !empty($email) && \WilokeListingTools\Frontend\SingleListing::isClaimedListing($post->ID) ) {
								\WILCITY_SC\SCHelpers::renderIconAndLink($email, 'la la-envelope', $email, array(
									'wrapperClass'     => $itemWrapperClass,
									'isEmail' => true,
									'iconWrapperClass' => 'rounded-circle'
								));
							}
							break;
						case 'website':
							if ( $website ) {
								\WILCITY_SC\SCHelpers::renderIconAndLink($website, 'la la-globe', $website, array(
									'wrapperClass'     => $itemWrapperClass,
									'iconWrapperClass' => 'rounded-circle'
								));
							}
							break;
						case 'social':
							if ( !empty($aSocialNetworks) ) : ?>
								<div class="icon-box-1_module__uyg5F mt-20 mt-sm-15">
									<div class="social-icon_module__HOrwr social-icon_style-2__17BFy">
										<?php
										foreach ($aSocialNetworks as $icon => $link) :
											if ( empty($link) ){
												continue;
											}
											if ( $icon == 'whatsapp'  ){
												$link = \WilokeListingTools\Framework\Helpers\General::renderWhatsApp($link);
											}
										?>
										<a class="social-icon_item__3SLnb" href="<?php echo esc_url($link); ?>" target="_blank"><i class="fa fa-<?php echo esc_attr($icon); ?>"></i></a>
										<?php endforeach; ?>
									</div>
								</div>
								<?php 
							endif;
							break;
						case 'inbox':
                            if ( \WilokeListingTools\Frontend\SingleListing::isClaimedListing($post->ID) ): ?>
                                <message-btn btn-name="<?php esc_html_e('Inbox', 'wilcity-shortcodes'); ?>" wrapper-class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-inbox-btn wil-btn wil-btn--block mt-20 wil-btn--border wil-btn--round')); ?>"></message-btn>
						<?php endif;
                            break;
					endswitch;
				} 
			?>
            <?php do_action('wilcity/wilcity-shortcodes/wilcity-sidear-business-info/after-info', $post); ?>
		</div>
	</div>
	<?php
    $content = ob_get_contents();
    ob_end_clean();
    else:
    $content = '';
    endif;
    return $content;
}

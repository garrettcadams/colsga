<?php
add_shortcode('wilcity_sidebar_woocommerce_booking', 'wilcitySidebarWooCommerceBookingForm');

function wilcitySidebarWooCommerceBookingForm($aArgs){
	global $wpdb, $post;

	if ( !class_exists('WC_Bookings') ){
		return '';
	}
	$aArgs['atts'] = \WILCITY_SC\SCHelpers::decodeAtts($aArgs['atts']);

	$aArgs = shortcode_atts(
		array(
			'name' => isset($aArgs['name']) ? $aArgs['name'] : $aArgs['atts']['name'],
			'atts' => array(
				'name'      => '',
				'icon'      => 'la la-shopping-cart',
				'postID'    => ''
			)
		),
		$aArgs
	);

	$aAtts = $aArgs['atts'];
	if ( empty($aAtts['postID']) ){
		$aAtts['postID'] = $post->ID;
	}

	$productID = \WilokeListingTools\Framework\Helpers\GetSettings::getPostMeta($post->ID, 'my_room');

	if ( empty($productID) ){
		return '';
	}

	if ( isset($aAtts['isMobile']) ){
		return apply_filters('wilcity/mobile/woocommerce-booking', '', $productID, $aAtts);
	}

	ob_start();
	?>
	<div id="<?php echo esc_attr(apply_filters('wilcity/filter/id-prefix', 'wilcity-sidebar-woobooking')); ?>" class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-sidebar-item-woobooking')); ?> content-box_module__333d9">
		<?php wilcityRenderSidebarHeader($aArgs['name'], $aAtts['icon']); ?>
		<div class="content-box_body__3tSRB">
			<?php echo do_shortcode('[product_page id='.$productID.']'); ?>
		</div>
	</div>
	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

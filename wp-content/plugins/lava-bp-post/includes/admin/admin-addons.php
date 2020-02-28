<?php

add_filter( 'lava_' . self::getSlug() . '_admin_save_button',  '__return_false' );

wp_localize_script(
	lava_bpp()->enqueue->getHandleName( 'admin-addons.js' ),
	'lavaAddonsVariable',
	Array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'post_type' => self::SLUG,
		'strEmailEmpty' => __( "Please type your email account ", 'lvbp-bp-post' ),
		'strLicenseEmpty' => __( "Please type license key", 'lvbp-bp-post' ),
		'strLicenseRegErr' => __( "Failed to register your license key. please try again or contact to lava support team", 'lvbp-bp-post' ),
	)
);

wp_enqueue_script( lava_bpp()->enqueue->getHandleName( 'admin-addons.js' ) );

printf(
	'<div id="%s" class="%s">',
	lava_bpp()->getName() . '-admin-addons',
	'lava-manager-addons-wrap'
);

	printf(
		'<p>
			<a href="%3$s" class="button" target="_blank">%4$s</a>
			<button type="button" class="button button-primary %1$s">%2$s</button><span class="spinner"></span>
			<a href="%5$s" class="button" target="_blank">%6$s</a>
		</p>',
		'lava-addon-update-check',
		__( "Refresh & check update", 'lvbp-bp-post' ),
		esc_url_raw( 'http://lava-code.com/directory/document/how-to-activate-addons-and-add-your-licence-key/' ),
		__( "How to active addons and add your license key", 'lvbp-bp-post' ),
		esc_url_raw( 'http://lava-code.com/directory/shop/' ),
		__( "Visit lava code addon list", 'lvbp-bp-post' )
	);

	echo '<ol class="addons-wrap">';
	if( !empty( $arrAddons ) ) : foreach( $arrAddons as $slug => $addon  ) {

		$has_licensekey = !empty( $addon->license );
		$is_active			= isset( $addon->active ) && $addon->active;

		if( !$is_active )
			continue;

		echo "<li>";
		include "admin-loop-addons.php";
		echo "</li>";
	} endif;
	echo "</ol>";
echo "</div><!-- /.lava-manager-addons-wrap -->";
<?php

function jvbpd_header_enabled() {
	$header_id = Jvbpd_Listing_Elementor::get_settings( 'single_listing_page', '' );

	if ( '' !== $header_id ) {
		return true;
	}

	return false;
}

function jvbpd_footer_enabled() {
	$footer_id = Jvbpd_Listing_Elementor::get_settings( 'listing_search', '' );

	if ( '' !== $footer_id ) {
		return true;
	}

	return false;
}

function jvbpd_listing_archive_enabled() {
	$listing_archive_id = Jvbpd_Listing_Elementor::get_settings( 'type_listing_archive', '' );

	if ( '' !== $listing_archive_id ) {
		return true;
	}

	return false;
}

function get_jvbpd_header_id() {
	$header_id = Jvbpd_Listing_Elementor::get_settings( 'single_listing_page', '' );

	if ( '' !== $header_id ) {
		return $header_id;
	}

	return false;
}

function get_jvbpd_footer_id() {
	$footer_id = Jvbpd_Listing_Elementor::get_settings( 'listing_search', '' );

	if ( '' !== $footer_id ) {
		return $footer_id;
	}

	return false;
}

function get_jvbpd_listing_archive_id() {
	$listing_archive_id = Jvbpd_Listing_Elementor::get_settings( 'listing_archive', '' );

	if ( '' !== $listing_archive_id ) {
		return $listing_archive_id;
	}

	return false;
}

function get_jvbpd_listing_custom_header_id() {
	$header_id = Jvbpd_Listing_Elementor::get_settings( 'custom_header', '' );

	if ( '' !== $header_id ) {
		return $header_id;
	}

	return false;
}

function get_jvbpd_listing_custom_footer_id() {
	$footer_id = Jvbpd_Listing_Elementor::get_settings( 'custom_footer', '' );

	if ( '' !== $footer_id ) {
		return $footer_id;
	}

	return false;
}

function get_jvbpd_listing_custom_login_id() {
	$login_id = Jvbpd_Listing_Elementor::get_settings( 'custom_login', '' );

	if ( '' !== $login_id ) {
		return $login_id;
	}
	return false;
}

function get_jvbpd_listing_custom_signup_id() {
	$signup_id = Jvbpd_Listing_Elementor::get_settings( 'custom_signup', '' );

	if ( '' !== $signup_id ) {
		return $signup_id;
	}
	return false;
}




function jvbpd_single_core_render() {

	if ( false == apply_filters( 'enable_jvbpd_render_header', '__return_true' ) ) {
		return;
	}?>
		<div class="jv-content">
			<?php Jvbpd_Listing_Elementor::get_header_content(); ?>
		</div>
	<?php

}

function jvbpd_listing_header_content() {
	if ( false == apply_filters( 'enable_jvbpd_render_header_content', '__return_true' ) ) {
		return;
	}
	Jvbpd_Listing_Elementor::get_custom_header_content();
}

function jvbpd_listing_footer_content() {
	Jvbpd_Listing_Elementor::get_custom_footer_content();
}

function jvbpd_login_content() {
	Jvbpd_Listing_Elementor::get_custom_login_content();
}

function jvbpd_signup_content() {
	Jvbpd_Listing_Elementor::get_custom_signup_content();
}
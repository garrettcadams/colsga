<?php
$queried_object = get_queried_object();
$isEnableOption = true;
if( class_exists( '\Elementor\Plugin' ) && $queried_object instanceof WP_Post ) {
	$page = \Elementor\Plugin::$instance->documents->get( $queried_object->ID );
	$isEnableOption = $page->get_settings( 'search_shortcode_in_header' ) == 'yes';
}

if( apply_filters( 'jvbpd_display_search_shortcdoe_in_header', true ) ) :
	?>
	<div class="pull-left jvbpd-header-map-filter-container <?php echo $isEnableOption ? '' : 'hidden'; ?>">
		<div class="jvbpd-header-map-filter-wrap">
			<!-- div class="container" -->
				<?php
				echo do_shortcode( sprintf( '[jvbpd_search1 columns="2" keyword_auto="disable" column1="ajax_search" column2="listing_location_with_google_search" amenities_field="disable" break_submit=true query_requester="%1$s" strip_form="true"]', jvbpd_tso()->get( 'search_sesult_page' ) ) ); ?>
			<!-- /div -->
		</div>
	</div>
	<?php
endif;
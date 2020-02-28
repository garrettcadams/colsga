<?php
$jvbpdElementorHeaderID = 0;
$jvbpdHeaderWrapCSS = Array();
$jvbpdHeaderWrapCSS[] = 'header';
$jvbpdHeaderWrapCSS[] = 'header-elementor';
if(class_exists('Jvbpd_Listing_Elementor')) {
    $jvbpdElementorHeaderID = apply_filters( 'jvbpd_core/elementor/custom_header_id', get_jvbpd_listing_custom_header_id(), get_queried_object() );
    $jvbpdHeaderWrapCSS[] = 'header-id-' . $jvbpdElementorHeaderID;
}
$jvbpdHeaderWrapCSS = apply_filters( 'Javo/Header/Wrap/CustomCSS', $jvbpdHeaderWrapCSS, $jvbpdElementorHeaderID );
?>
<div id="wrapper">
    <div class="<?php echo esc_attr(join(' ', $jvbpdHeaderWrapCSS)); ?>" data-offset="true">
        <div class="header-elementor-wrap">
            <?php
            if( function_exists( 'jvbpd_listing_header_content' ) ){
                jvbpd_listing_header_content();
            } ?>
        </div>
    </div>
<?php
global $post;
$toggle = \WilokeListingTools\Controllers\ReportController::isAllowReport();
if ( !$toggle ){
	return '';
}
?>
<div class="listing-detail_rightItem__2CjTS wilcity-single-tool-report">
    <report-popup-btn wrapper-class="list_link__2rDA1 text-ellipsis color-primary--hover" target-id="<?php echo esc_attr($post->ID); ?>"><template slot="insideBtn"><i class="color-tertiary la la-flag-o"></i> <?php esc_html_e('Report', 'wilcity'); ?></template></report-popup-btn>
</div>

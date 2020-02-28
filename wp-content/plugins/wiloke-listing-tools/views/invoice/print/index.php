<?php
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Models\PaymentMetaModel;

$planName = get_the_title($aInvoice['paymentID']);
if ( empty($planName) ){
	$planName = PaymentMetaModel::get($aInvoice['paymentID'], 'planName');
}
?>
<h3><?php esc_html_e('INVOICE', 'wiloke-listing-tools'); ?></h3>
<p><strong><?php esc_html_e('Invoice ID', 'wiloke-listing-tools'); ?>:</strong> <?php echo esc_html($aInvoice['invoiceID']); ?></p>
<p><strong><?php esc_html_e('Invoice date', 'wiloke-listing-tools'); ?>:</strong> <?php echo date_i18n(get_option('date_format'), strtotime($aInvoice['created_at'])); ?></p>

<table width="100%">
	<thead>
		<tr>
			<th><?php esc_html_e('Description', 'wiloke-listing-tools'); ?></th>
			<th><?php esc_html_e('Total', 'wiloke-listing-tools'); ?></th>
			<th><?php esc_html_e('Sub Total', 'wiloke-listing-tools'); ?></th>
			<th><?php esc_html_e('Discount', 'wiloke-listing-tools'); ?></th>
			<th><?php esc_html_e('Tax', 'wiloke-listing-tools'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php echo esc_html($planName); ?></td>
			<td><?php echo GetWilokeSubmission::renderPrice($aInvoice['total'], $aInvoice['currency']); ?></td>
			<td><?php echo GetWilokeSubmission::renderPrice($aInvoice['subTotal'], $aInvoice['currency']); ?></td>
			<td><?php echo GetWilokeSubmission::renderPrice($aInvoice['discount'], $aInvoice['currency']); ?></td>
			<td><?php echo GetWilokeSubmission::renderPrice(0, $aInvoice['currency']); ?></td>
		</tr>
	</tbody>
</table>
<?php
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
?>
<tbody>
<?php if ( empty($this->aInvoices) ) : ?>
	<tr><td colspan="7" class="text-center"><strong><?php esc_html_e('There are no invoices yet.', 'wiloke-listing-tools'); ?></strong></td></tr>
<?php else: ?>
	<?php
	foreach ( $this->aInvoices as  $aInfo ) :
		$editLink = admin_url('admin.php') . '?page='.$this->detailSlug.'&paymentID='.$aInfo['ID'];
		?>
		<tr class="item">
            <td class="invoices-id check-column manage-column">
                <input class="wiloke_checkbox_item" type="checkbox" value="<?php echo esc_attr($aInfo['ID']); ?>" name="delete[]">
            </td>
			<td class="invoices-id check-column manage-column"><a href="#"><?php echo esc_html($aInfo['ID']); ?></a></td>
			<td class="invoices-customer manage-column column-primary" data-colname="<?php esc_html_e('Customer', 'wiloke-listing-tools'); ?>"><a title="<?php esc_html_e('View customer information', 'wiloke-listing-tools'); ?>" href="<?php echo esc_url(admin_url('user-edit.php?user_id='.$aInfo['userID'])); ?>"><?php echo esc_html(get_user_meta($aInfo['userID'], 'nickname', true)); ?></a></td>
			<td class="invoices-id check-column manage-column"><a href="<?php echo esc_url($editLink); ?>" title="<?php esc_html_e('View Session Details', 'wiloke-listing-tools'); ?>"><?php echo esc_html($aInfo['paymentID']); ?></a></td>
			<td class="invoices-id check-column manage-column"><a href="<?php echo esc_url($editLink); ?>" target="<?php echo esc_attr($target); ?>"><?php echo !empty($aInfo['planID']) ? get_the_title($aInfo['planID']) : \WilokeListingTools\Models\PaymentMetaModel::get($aInfo['paymentID'], 'planName'); ?></a></td>
            <td class="invoices-id check-column manage-column"><a href="<?php echo esc_url($editLink); ?>"><?php echo esc_html($aInfo['gateway']); ?></a></td>
			<td class="invoices-id check-column manage-column"><a href="<?php echo esc_url($editLink); ?>"><?php echo esc_html(GetWilokeSubmission::renderPrice($aInfo['subTotal'], $aInfo['currency'])); ?></a></td>
			<td class="invoices-id check-column manage-column"><a href="<?php echo esc_url($editLink); ?>"><?php echo esc_html(GetWilokeSubmission::renderPrice($aInfo['discount'], $aInfo['currency'])); ?></a></td>
			<td class="invoices-id check-column manage-column"><a href="<?php echo esc_url($editLink); ?>"><?php echo esc_html(GetWilokeSubmission::renderPrice($aInfo['total'], $aInfo['currency'])); ?></a></td>
			<td class="invoices-id check-column manage-column"><a href="<?php echo esc_url($editLink); ?>"><?php echo esc_html($aInfo['created_at']); ?></a></td>
		</tr>
	<?php endforeach; ?>
<?php endif; ?>
</tbody>
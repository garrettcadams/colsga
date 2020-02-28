<tbody>
<?php if ( empty($this->aSales) ) : ?>
	<tr><td colspan="6" class="text-center"><strong><?php esc_html_e('There are no sales yet.', 'wiloke'); ?></strong></td></tr>
<?php else: ?>
	<?php

	foreach ( $this->aSales as  $aInfo ) :
        if ( $aInfo['gateway'] == 'woocommerce' ){
	        $target = '_blank';
	        $editLink = add_query_arg(
		        array(
			        'post'   => $aInfo['wooOrderID'],
			        'action' => 'edit'
		        ),
		        admin_url('post.php')
	        );
        }else{
	        $target = '_self';
	        $editLink = add_query_arg(
		        array(
			        'page'      => $this->detailSlug,
			        'paymentID' => $aInfo['ID']
		        ),
		        admin_url('admin.php')
	        );
        }
		?>
		<tr class="item">
            <td class="invoices-id check-column manage-column">
                <input class="wiloke_checkbox_item" type="checkbox" value="<?php echo esc_attr($aInfo['ID']); ?>" name="delete[]">
            </td>
			<td class="invoices-id check-column manage-column">
                <a href="<?php echo esc_url($editLink); ?>" title="<?php esc_html_e('View Invoice Detail', 'wiloke-listing-tools'); ?>" target="<?php echo esc_attr($target); ?>"><?php echo esc_html($aInfo['ID']); ?></a>
            </td>
            <td class="invoices-customer manage-column column-primary" data-colname="<?php esc_html_e('Customer', 'wiloke-listing-tools'); ?>">
                <a title="<?php esc_html_e('View customer information', 'wiloke'); ?>" href="<?php echo esc_url(admin_url('user-edit.php?user_id='.$aInfo['userID'])); ?>" target="_blank"><?php echo esc_html(get_user_meta($aInfo['userID'], 'nickname', true)); ?></a>
            </td>

			<td class="invoices-package manage-column" data-colname="<?php esc_html_e('Plan Type', 'wiloke-listing-tools'); ?>">
                <a href="<?php echo esc_url($editLink); ?>" target="<?php echo esc_attr($target); ?>"><?php echo esc_html($aInfo['packageType']); ?></a>
            </td>

			<td class="invoices-package manage-column" data-colname="<?php esc_html_e('Plan Name', 'wiloke-listing-tools'); ?>">
                <a href="<?php echo esc_url($editLink); ?>" target="<?php echo esc_attr($target); ?>"><?php echo !empty($aInfo['planID']) ? get_the_title($aInfo['planID']) : \WilokeListingTools\Models\PaymentMetaModel::get($aInfo['ID'], 'planName'); ?></a>
            </td>

			<td class="invoices-date manage-column" data-colname="<?php esc_html_e('Status', 'wiloke-listing-tools'); ?>">
                <a href="<?php echo esc_url($editLink); ?>" target="<?php echo esc_attr($target); ?>"><?php echo esc_html($aInfo['status']);  ?></a>
            </td>

			<td class="invoices-date manage-column" data-colname="<?php esc_html_e('Gateway', 'wiloke-listing-tools'); ?>">
                <a href="<?php echo esc_url($editLink); ?>" target="<?php echo esc_attr($target); ?>"><?php echo esc_html($aInfo['gateway']);  ?></a>
            </td>

			<td class="invoices-date manage-column" data-colname="<?php esc_html_e('Date', 'wiloke-listing-tools'); ?>">
                <a href="<?php echo esc_url($editLink); ?>" target="<?php echo esc_attr($target); ?>"><?php echo esc_html($aInfo['createdAt']);  ?></a>
            </td>

		</tr>
	<?php endforeach; ?>
<?php endif; ?>
</tbody>
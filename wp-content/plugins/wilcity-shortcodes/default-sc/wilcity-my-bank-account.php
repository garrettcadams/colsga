<?php
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;

add_shortcode('wilcity_my_bank_accounts', 'wilcityMyBankAccounts');
function wilcityMyBankAccounts(){
	$aConfiguration = GetWilokeSubmission::getAll();
	$aBankAccounts = array();

	for ($i=1; $i<=4; $i++){
		if ( !empty($aConfiguration['bank_transfer_account_name_'.$i]) && !empty($aConfiguration['bank_transfer_account_number_'.$i]) && !empty($aConfiguration['bank_transfer_name_'.$i]) ){
			foreach (array('bank_transfer_account_name', 'bank_transfer_account_number', 'bank_transfer_name', 'bank_transfer_short_code', 'bank_transfer_iban', 'bank_transfer_swift') as $bankInfo){
				$aBankAccounts[$i][$bankInfo] = $aConfiguration[$bankInfo.'_'.$i];
			}
		}
	}

	if ( empty($aBankAccounts) ){
		return '';
	}

	$aFields = array(
		'bank_transfer_account_name'    => esc_html__('Account Name', 'wilcity-shortcodes'),
		'bank_transfer_account_number'  => esc_html__('Account Number', 'wilcity-shortcodes'),
		'bank_transfer_name'            => esc_html__('Bank Name', 'wilcity-shortcodes'),
		'bank_transfer_short_code'      => esc_html__('Short Code', 'wilcity-shortcodes'),
		'bank_transfer_iban'            => esc_html__('IBAN', 'wilcity-shortcodes'),
		'bank_transfer_swift'           => esc_html__('BIC/Swift', 'wilcity-shortcodes'),
	);

	$aFields = apply_filters('wilcity/wilcity-shortcodes/my-bank-account-fields', $aFields);

	ob_start();
	?>
	<div class="table-module">
		<table class="table-module__table wil-table-responsive-lg">
			<thead>
				<tr>
					<?php foreach ($aFields as $fieldName): ?>
						<th><?php echo esc_html($fieldName); ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($aBankAccounts as $order => $aAccountInfo): ?>
				<tr>
					<?php foreach ($aAccountInfo as $key => $val) :
						$val = empty($val) ? 'X' : $val;
					?>
						<td data-th="<?php echo isset($aFields[$key]) ? esc_html($aFields[$key]) : ''; ?>"><?php echo esc_html($val); ?></td>
					<?php endforeach; ?>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}
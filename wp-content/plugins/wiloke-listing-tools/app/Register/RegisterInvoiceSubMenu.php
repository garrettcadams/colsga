<?php

namespace WilokeListingTools\Register;

use WilokeListingTools\AlterTable\AlterTableInvoices;
use WilokeListingTools\AlterTable\AlterTablePaymentHistory;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\HTML;
use WilokeListingTools\Models\InvoiceModel;


class RegisterInvoiceSubMenu {
    use WilokeSubmissionConfiguration;

	public $slug = 'invoices';
	protected $aInvoices = array();
	public $pagination = 1;
	public $paged = 1;

	public function __construct() {
		add_action('admin_menu', array($this, 'register'), 20);
		add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
		add_action('wp_ajax_wiloke_submission_export_invoices', array($this, 'exportFile'));
		add_action('wp_ajax_wiloke_delete_invoices', array($this, 'deleteInvoices'));
	}

	public function deleteInvoices(){
		if ( !current_user_can('administrator') ){
			wp_send_json_error('You do not have permission to access this page');
		}

		if ( !isset($_POST['items']) || empty($_POST['items']) ){
			wp_send_json_error('There are no checked items.');
		}

		foreach ($_POST['items'] as $itemID){
			InvoiceModel::delete($itemID);
		}

		wp_send_json_success('Congratulations! The payment sessions have been deleted');
    }

	public function enqueueScripts($hook){
	    if ( strpos($hook, $this->slug) !== false ){
		    wp_enqueue_style( 'jquery-ui' );
		    wp_enqueue_script( 'jquery-ui-datepicker' );
	    }
    }

	protected function fetchInvoices($aQuery){
		if ( !current_user_can('edit_posts') ){
			return false;
		}

		global $wpdb;
		$this->paged = isset($aQuery['paged']) ? $aQuery['paged'] : 1;
		$invoicesTbl = $wpdb->prefix . AlterTableInvoices::$tblName;
		$sessionTbl  = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		$offset = ($this->paged - 1)*$this->postPerPages;
		$this->postPerPages = isset($aQuery['posts_per_page']) && !empty($aQuery['posts_per_page']) ? $aQuery['posts_per_page'] : $this->postPerPages;

		$sql = "SELECT SQL_CALC_FOUND_ROWS $invoicesTbl.*, $sessionTbl.ID as paymentID, $sessionTbl.userID, $sessionTbl.gateway, $sessionTbl.billingType, $sessionTbl.planID FROM $invoicesTbl LEFT JOIN $sessionTbl ON ($invoicesTbl.paymentID=$sessionTbl.ID)";
		$concat = " WHERE";

		if ( isset($aQuery['planID']) && $aQuery['planID'] !== 'any' ){
			$sql .= $concat . " $sessionTbl.planID=".abs($aQuery['planID']);
			$aParams[] = $aQuery['planID'];
			$concat = " AND";
		}

		$additionalQuery = '';

		if ( isset($aQuery['gateway']) && !empty($aQuery['gateway']) && ($aQuery['gateway'] != 'all') ){
			$additionalQuery .= $concat . " $sessionTbl.gateway='".$wpdb->_real_escape($aQuery['gateway'])."'";
			$concat = " AND";
		}

		if ( isset($aQuery['date']) && $aQuery['date'] !== 'any' ){
			if ( $aQuery['date'] === 'this_week' ){
				$additionalQuery .= $concat. " DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE()) DAY) <= $sessionTbl.createdAt";
			}elseif ( $aQuery['date'] === 'this_month' ){
				$additionalQuery .= $concat. " $sessionTbl.created_at >= DATE_SUB(CURDATE(), INTERVAL DAYOFMONTH(CURDATE()) DAY) ";
			}else{
				if ( empty($aQuery['from']) ){
					$from = date('Y-m-d');
				}else{
					$from = date('Y-m-d', strtotime($aQuery['from']));
				}

				if ( empty($aQuery['to']) ){
					$to = date('Y-m-d');
				}else{
					$to = date('Y-m-d', strtotime($aQuery['to']));
				}
				$additionalQuery .= $concat . " ($sessionTbl.createdAt BETWEEN '".$wpdb->_real_escape($from)."' AND '".$wpdb->_real_escape($to)."')";
			}
		}

		if ( !empty($additionalQuery) ){
			$sql .= $additionalQuery;
		}

		$sql .= " ORDER BY $invoicesTbl.ID DESC LIMIT ".abs($this->postPerPages)." OFFSET ".abs($offset);

		$this->aInvoices = $wpdb->get_results($sql,ARRAY_A);
		$this->total = $wpdb->get_var("SELECT FOUND_ROWS()");
	}

	public function exportFile(){
		if ( !current_user_can('edit_posts') ){
			return false;
		}
		$aQuery = json_decode(urldecode($_POST['args']), true);
		$this->fetchInvoices($aQuery);

		$now = gmdate("D, d M Y H:i:s");
		header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
		header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
		header("Last-Modified: {$now} GMT");

		// force download
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");

		// disposition / encoding on response body
		header("Content-Disposition: attachment;filename=wiloke-invoices-".date('Y-m-D') . '.csv');
		header("Content-Transfer-Encoding: binary");
		$csv_header = '';
		$csv_header .= 'ID, Customer Name, Customer Email, Payment ID, Plan Name, Sub Total, Discount, Total, Gateway, Billing Type, Created At' . "\n";
		$csv_row ='';
		foreach ( $this->aInvoices as $aInfo ){
			$oUser = get_userdata($aInfo['userID']);
			$csv_row .= $aInfo['ID'] . ', ' . $oUser->display_name . ',' . $oUser->user_email . ', ' . $aInfo['paymentID'] . ', ' .  get_the_title($aInfo['planID']) . ', ' . GetWilokeSubmission::renderPrice($aInfo['subTotal'], $aInfo['currency'], false) . ', ' . GetWilokeSubmission::renderPrice($aInfo['discount'], $aInfo['currency'], true) . ', ' . GetWilokeSubmission::renderPrice($aInfo['total'], $aInfo['currency'], false) . ', '  . $aInfo['gateway'] . ', ' . $aInfo['billingType'] . ', ' . $aInfo['created_at'] . "\n";
		}

		echo $csv_header . $csv_row;
		die();
	}

	public function register() {
		add_submenu_page($this->parentSlug, esc_html__('Invoices', 'wiloke'), esc_html__('Invoices', 'wiloke'), 'administrator', $this->slug, array($this, 'showInvoices'));
	}

	public function showInvoices(){
		$this->fetchInvoices($_REQUEST);
		$this->pagination = absint(ceil($this->total/$this->postPerPages));
		$this->paged = isset($_REQUEST['paged']) ? $_REQUEST['paged'] : 1;

		?>
		<div id="listgo-table-wrapper" style="margin: 30px auto">
			<h2><?php esc_html_e('Invoices', 'wiloke'); ?></h2>
            <?php $this->getPartial('form-filters.php'); ?>

			<table id="listgo-table" class="ui striped table">
				<?php
                $this->getPartial('thead.php');
                $this->getPartial('tbody.php');
                if ( $this->pagination !== 1 && $this->pagination !== 0 ) {
                    $this->getPartial( 'tfood.php' );
                }
				?>
			</table>

			<?php if ( !empty($this->aInvoices) ) : ?>
				<div class="ui segment">
					<form action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="POST" style="display: inline-block;">
						<input type="hidden" name="args" value="<?php echo esc_attr(urlencode(json_encode($_REQUEST))); ?>">
						<input type="hidden" name="action" value="wiloke_submission_export_invoices">
						<button class="js_export button ui basic purple"><?php esc_html_e('Export Payments', 'wiloke'); ?></button>
						<button id="js_delete_invoices" class="button ui basic red"><?php esc_html_e('Delete All Invoices', 'wiloke-listing-tools'); ?></button>
					</form>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
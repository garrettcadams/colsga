<?php
namespace WilokeListingTools\Register;


use WilokeListingTools\AlterTable\AlterTablePaymentHistory;
use WilokeListingTools\Models\PaymentModel;

class RegisterSaleSubMenu {
	use WilokeSubmissionConfiguration;
	public $slug = 'sales';
	protected $aSales = array();
	public $paged = 1;
	public $pagination = 1;
	public $gateway = '';

	public function __construct() {
		add_action('admin_menu', array($this, 'register'), 20);
		add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
		add_action('wp_ajax_wiloke_delete_sales', array($this, 'deleteItems'));
		add_action('wp_ajax_wiloke_submission_export_sales', array($this, 'exportFile'));
	}

	public function deleteItems(){
	    if ( !current_user_can('administrator') ){
	        wp_send_json_error('You do not have permission to access this page');
        }

        if ( !isset($_POST['items']) || empty($_POST['items']) ){
	        wp_send_json_error('There are no checked items.');
        }

        foreach ($_POST['items'] as $itemID){
            PaymentModel::delete($itemID);
        }

        wp_send_json_success('Congratulations! The payment sessions have been deleted');
    }

	public function enqueueScripts($hook){
		if ( strpos($hook, $this->slug) !== false ){
			wp_enqueue_style( 'jquery-ui' );
			wp_enqueue_script( 'jquery-ui-datepicker' );
		}
	}

	public function exportFile(){
		if ( !current_user_can('edit_posts') ){
			return false;
		}
		$aQuery = json_decode(urldecode($_POST['args']), true);
		$this->fetchItems($aQuery);

		$now = gmdate("D, d M Y H:i:s");
		header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
		header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
		header("Last-Modified: {$now} GMT");

		// force download
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");

		// disposition / encoding on response body
		header("Content-Disposition: attachment;filename=wiloke-sales-statistic-".date('Y-m-D') . '.csv');
		header("Content-Transfer-Encoding: binary");
		$csv_header = '';
		$csv_header .= 'ID, Customer Name, Customer Email, Plan Name, Status, Gateway, Created At' . "\n";
		$csv_row ='';
		foreach ( $this->aSales as $aInfo ){
			$oUser = get_userdata($aInfo['userID']);
			$csv_row .= $aInfo['ID'] . ', ' . $oUser->display_name . ', ' . $oUser->user_email . ', ' . get_the_title($aInfo['planID']) . ', ' . $aInfo['status'] . ', ' . $aInfo['gateway'] . ', '. $aInfo['createdAt'] . "\n";
		}
		echo $csv_header . $csv_row;
		die();
	}

	public function register() {
		add_submenu_page($this->parentSlug, esc_html__('Sales', 'wiloke'), esc_html__('Sales', 'wiloke'), 'administrator', $this->slug, array($this, 'showSales'));
	}

	protected function fetchItems($aQuery){
		if ( !current_user_can('edit_posts') ){
			return false;
		}

		global $wpdb;
		$tblSession = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
		$offset = ($this->paged - 1)*$this->postPerPages;

		$sql = "SELECT SQL_CALC_FOUND_ROWS $tblSession.* FROM $tblSession WHERE billingType='".wilokeListingToolsRepository()->get('payment:billingTypes', true)->sub('nonrecurring')."'";
		$concat = " AND";

		if ( isset($aQuery['package_id']) && !empty($aQuery['package_id']) && $aQuery['package_id'] !== 'any' ){
			$sql .= $concat . " $tblSession.planID=".abs($aQuery['package_id']);
			$aParams[] = $aQuery['package_id'];
			$concat = " AND";
		}

		$additionalQuery = '';
		if ( isset($aQuery['payment_status']) && !empty($aQuery['payment_status']) && ($aQuery['payment_status'] !== 'all') ){
			$additionalQuery .= $concat . " $tblSession.status='".$wpdb->_real_escape($aQuery['payment_status'])."'";
			$concat = " AND";
		}

		if ( isset($aQuery['gateway']) && !empty($aQuery['gateway']) && $aQuery['gateway'] !== 'all' ){
			$additionalQuery .= $concat . " $tblSession.gateway='".$wpdb->_real_escape($aQuery['gateway'])."'";
			$concat = " AND";
		}

		if ( isset($aQuery['date']) && $aQuery['date'] !== 'any' ){
			if ( $aQuery['date'] === 'this_week' ){
				$additionalQuery .= $concat. " DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE()) DAY) <= $tblSession.createdAt";
			}elseif ( $aQuery['date'] === 'this_month' ){
				$additionalQuery .= $concat. " $tblSession.createdAt >= DATE_SUB(CURDATE(), INTERVAL DAYOFMONTH(CURDATE()) DAY) ";
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
				$additionalQuery .= $concat . " ($tblSession.createdAt BETWEEN '".$wpdb->_real_escape($from)."' AND '".$wpdb->_real_escape($to)."')";
			}
		}

		if ( !empty($additionalQuery) ){
			$sql .= $additionalQuery;
		}

		$sql .= " ORDER BY $tblSession.ID DESC LIMIT ".$wpdb->_real_escape($this->postPerPages)." OFFSET ".abs($offset);

		$this->aSales = $wpdb->get_results($sql,ARRAY_A);
		if ( empty($this->aSales) ){
		    $this->paged = 1;
        }
		$this->total = $wpdb->get_var("SELECT FOUND_ROWS()");
	}

	public function showSales(){
		$this->paged = isset($_REQUEST['paged']) && !empty($_REQUEST['paged']) ? absint($_REQUEST['paged']) : 1;
		$this->postPerPages = isset($_REQUEST['posts_per_page']) && !empty($_REQUEST['posts_per_page']) ? $_REQUEST['posts_per_page'] : $this->postPerPages;
		$this->gateway = isset($_REQUEST['gateway']) && !empty($_REQUEST['gateway']) ? $_REQUEST['gateway'] : $this->gateway;
		$this->fetchItems($_REQUEST);
		$this->pagination = absint(ceil($this->total/$this->postPerPages));
		?>
		<div id="listgo-table-wrapper" style="margin: 30px auto">
			<h2><?php esc_html_e('Sales', 'wiloke-listing-tools'); ?></h2>
			<?php $this->getPartial('form-filters.php'); ?>

			<table id="listgo-table" class="ui striped table">
				<?php
				$this->getPartial('thead.php');
				$this->getPartial('tbody.php');
				$this->getPartial('tfood.php');
				?>
			</table>

			<?php if ( !empty($this->aSales) ) : ?>
				<div class="ui segment">
					<form action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="POST" style="display: inline-block;">
						<input type="hidden" name="args" value="<?php echo esc_attr(urlencode(json_encode($_REQUEST))); ?>">
						<input type="hidden" name="action" value="wiloke_submission_export_sales">
						<button class="js_export button ui basic purple"><?php esc_html_e('Export Payments', 'wiloke'); ?></button>
						<button class="js_delete_sales button ui basic red"><?php esc_html_e('Delete Checked Items', 'wiloke'); ?></button>
					</form>
				</div>
			<?php endif; ?>

		</div>
		<?php
	}
}
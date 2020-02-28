<?php

namespace WilcityPaidClaim\Register;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\SemanticUi;
use WilokeListingTools\Framework\Helpers\SetSettings;
use WilokeListingTools\Register\WilokeSubmissionConfiguration;

class RegisterClaimSubMenu {
	use WilokeSubmissionConfiguration;
	public $slug = 'claim';
	public static $optionKey = 'wilcity_pc_options';

	public function __construct() {
		add_action('admin_menu', array($this, 'register'), 20);
		add_action('admin_init', array($this, 'saveMenu'));
	}

	public function register(){
		add_submenu_page($this->parentSlug, esc_html__('Claim Settings', 'wilcity-paid-claim'), esc_html__('Claim Settings', 'wilcity-paid-claim'), 'administrator', $this->slug, array($this, 'claimSettings'));
	}

	public function saveMenu(){
        if ( !isset($_POST['wilcity_pc']) || empty($_POST['wilcity_pc']) && !current_user_can('administrator') ){
            return false;
        }

        if ( !wp_verify_nonce($_POST['wiloke_pc_nonce_field'], 'wiloke_pc_nonce_action') ){
            return false;
        }

        $aData = array();
        foreach ($_POST['wilcity_pc'] as $key => $val){
            if ( !is_array($val) ){
	            $aData[$key] = sanitize_text_field($val);
            }else{
	            $aData[$key] = $val;
            }
        }
        SetSettings::setOptions(self::$optionKey, $aData);
    }

	public function claimSettings(){
		$aOptions = GetSettings::getOptions(self::$optionKey);
		?>
		<div id="wiloke-pc-wrapper" class="wrap">
			<form class="form ui" action="<?php echo esc_url(admin_url('admin.php?page='.$this->slug)); ?>" method="POST">
				<?php wp_nonce_field('wiloke_pc_nonce_action', 'wiloke_pc_nonce_field'); ?>
				<?php
				foreach ( wilokeListingToolsRepository()->setConfigDir(WILCITY_PC_CONFIG_DIR)->get('settings:configuration', true)->sub('fields') as $aField ){
					if ( $aField['type'] == 'password' || $aField['type'] == 'text' || $aField['type'] == 'select_post' || $aField['type'] == 'select_ui' || $aField['type'] == 'select' || $aField['type'] == 'textarea' ){
						$name = str_replace(array('wilcity_pc', '[', ']'), array('', '', ''), $aField['name']);
						$aField['value'] = isset($aOptions[$name]) ? $aOptions[$name] : $aField['default'];
					}
					switch ($aField['type']){
						case 'open_segment';
							SemanticUi::renderOpenSegment($aField);
							break;
						case 'open_accordion';
							SemanticUi::renderOpenAccordion($aField);
							break;
						case 'open_fields_group';
							SemanticUi::renderOpenFieldGroup($aField);
							break;
						case 'close';
							SemanticUi::renderClose();
							break;
						case 'close_segment';
							SemanticUi::renderCloseSegment();
							break;
						case 'password':
							SemanticUi::renderPasswordField($aField);
							break;
						case 'text';
							SemanticUi::renderTextField($aField);
							break;
						case 'select_post';
						case 'select_ui';
							SemanticUi::renderSelectUiField($aField);
							break;
						case 'select':
							SemanticUi::renderSelectField($aField);
							break;
						case 'textarea':
							SemanticUi::renderTextareaField($aField);
							break;
						case 'submit':
							SemanticUi::renderSubmitBtn($aField);
							break;
						case 'header':
							SemanticUi::renderHeader($aField);
							break;
						case 'desc';
							SemanticUi::renderDescField($aField);
							break;
					}
				}
				?>
			</form>
		</div>
		<?php
	}
}
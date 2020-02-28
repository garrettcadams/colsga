<?php
namespace WilokeListingTools\Register;


use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\SemanticUi;

class WilokeSubmission {
    use WilokeSubmissionConfiguration;

	public static $optionKey = 'wiloke_submission_configuration';

	public function __construct() {
		add_action('admin_menu', array($this, 'register'));
		add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
	}

	public function register(){
		add_menu_page( esc_html__( 'Wiloke Submission', 'wiloke-listing-tools' ), esc_html__( 'Wiloke Submission', 'wiloke' ), 'administrator', 'wiloke-submission', array($this, 'submissionArea'), 'dashicons-hammer', 29 );
	}

	public function enqueueScripts($hook){
		if ( strpos($hook, $this->parentSlug) !== false ){
			wp_dequeue_script('semantic-selection-ui');
			wp_register_style('semantic-ui', WILOKE_LISTING_TOOL_URL . 'admin/assets/semantic-ui/form.min.css');
			wp_enqueue_style('semantic-ui');
			wp_register_script('semantic-ui', WILOKE_LISTING_TOOL_URL . 'admin/assets/semantic-ui/semantic.min.js', array('jquery'), null, true);
			wp_enqueue_script('semantic-ui');
			wp_enqueue_script('wiloke-submission-general',  WILOKE_LISTING_TOOL_URL . 'admin/source/js/wiloke-submission-general.js', array('jquery'), WILOKE_LISTING_TOOL_VERSION, true);
			wp_register_style( 'jquery-ui', 'http://code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css' );
		}
    }

    public static function isDashboard(){
	    global $post;
	    if ( !isset($post->ID) ){
	        return false;
        }

        return $post->ID == GetWilokeSubmission::getField('dashboard_page');
    }

	public function saveConfiguration(){
		if ( !current_user_can('administrator') ){
			return false;
		}

		if ( (isset($_POST['wilcity_submission']) && !empty($_POST['wilcity_submission'])) &&  isset($_POST['wiloke_nonce_field']) && !empty($_POST['wiloke_nonce_field']) && wp_verify_nonce($_POST['wiloke_nonce_field'], 'wiloke_nonce_action') ){
			$options = $_POST['wilcity_submission'];
			update_option(self::$optionKey, maybe_serialize($options));
			do_action('wiloke/wiloke-listgo-functionality/app/Register/RegisterWilokeSubmission/save', $_POST);
		}
	}

	public function submissionArea(){
		$this->saveConfiguration();
		$aOptions = get_option(self::$optionKey);
		$aOptions = maybe_unserialize($aOptions);
		?>
		<div id="wiloke-submission-wrapper" class="wrap">
			<form class="form ui" action="<?php echo esc_url(admin_url('admin.php?page='.$this->parentSlug)); ?>" method="POST">
				<?php wp_nonce_field('wiloke_nonce_action', 'wiloke_nonce_field'); ?>
				<?php
				$aCustomPostTypes = \WilokeListingTools\Framework\Helpers\GetSettings::getOptions(wilokeListingToolsRepository()->get('addlisting:customPostTypesKey'));
				$aCustomPostTypes = array_filter($aCustomPostTypes, function ($aInfo){
                    return ($aInfo['key'] !== 'listing' && $aInfo['key'] !== 'event');
                });
				$isAddedCustomPostType = false;

				$aFieldSettings = apply_filters('wilcity/wiloke-listing-tools/wiloke-submission-fields', wilokeListingToolsRepository()->get('wiloke-submission:configuration', true)->sub('fields'));

				foreach ( $aFieldSettings as $aField ){
					if ( $aField['type'] == 'password' || $aField['type'] == 'text' || $aField['type'] == 'select_post' || $aField['type'] == 'select_ui' || $aField['type'] == 'select' || $aField['type'] == 'textarea' ){
						$name = str_replace(array('wilcity_submission', '[', ']'), array('', '', ''), $aField['name']);
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

					if ( !$isAddedCustomPostType ){
						if ( isset($aField['id']) && $aField['id'] == 'event_plans' && !empty($aCustomPostTypes) ){
							$isAddedCustomPostType = true;
							foreach ($aCustomPostTypes as $aCustomPostType){
							    $planKey = $aCustomPostType['key'].'_plans';
							    SemanticUi::renderSelectUiField(array(
									'type'      => 'select_post',
									'heading'   => $aCustomPostType['name'] . ' Plans',
									'name'      => 'wilcity_submission['.$planKey.']',
									'id'        => $planKey,
									'post_type' => 'listing_plan',
									'multiple'  => true,
									'value'     => isset($aOptions[$planKey]) ? $aOptions[$planKey] : '',
									'default'   => ''
								));

								SemanticUi::renderSelectUiField(array(
									'type'      => 'select_post',
									'heading'   => 'Default Plan For Free '.$aCustomPostType['name'].' Claim',
									'desc'=> 'If you are using Free Claim, this setting is required. Once a listing claim is approved, this plan will be assigned to this listing',
									'name'      => 'wilcity_submission[free_claim_'.$aCustomPostType['key'].'_plan]',
									'id'        => 'free_claim_'.$aCustomPostType['key'].'_plan',
									'post_type' => 'listing_plan',
									'multiple'  => true,
									'value'     => isset($aOptions['free_claim_'.$aCustomPostType['key'].'_plan']) ? $aOptions['free_claim_'.$aCustomPostType['key'].'_plan'] : '',
									'default'   => ''
								));
							}
						}
                    }

				}
				?>
			</form>
		</div>
		<?php
	}
}
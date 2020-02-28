<?php
namespace WilokeListingTools\MetaBoxes;


use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\SetSettings;

class EventPlan {
	public function __construct() {
		add_action('cmb2_admin_init', array($this, 'renderMetaboxFields'));
	}

	public function renderMetaboxFields(){
		foreach (wilokeListingToolsRepository()->get('eventplan') as $aSettings){
			new_cmb2_box($aSettings);
		}
	}

	public static function renderProductAlias(){
		$aPosts = get_posts(
			array(
				'post_type'         => 'product',
				'posts_per_page'    => -1,
				'post_status'       => 'publish'
			)
		);

		$aOptions = array(
			'' => '----'
		);

		foreach ($aPosts as $post){
			$aOptions[$post->ID] = $post->post_title;
		}
		return $aOptions;
	}

	public function saveListingPlan($postID){
		if ( isset($_POST['listing_plan']) && !empty($_POST['listing_plan'])  ){
			SetSettings::setPostMeta($postID, 'listing_plan', $_POST['listing_plan']);
		}
	}

	public function addListingPricingTab($aProductDataTabs){
		$aProductDataTabs['listing-pricing'] = array(
			'label'  => esc_html__( 'Listing pricing', 'wiloke-listing-tools' ),
			'target' => 'wilcity_listing_pricing',
		);
		return $aProductDataTabs;
	}

	public function getPricing(){
		$query = new \WP_Query(
			array(
				'post_type'     => 'event_plan',
				'post_status'   => 'publish',
				'posts_per_page'=> -1
			)
		);

		if ( !$query->have_posts() ){
			wp_reset_postdata();
			return false;
		}

		$aOptions = array(
			'' => '----'
		);
		while ($query->have_posts()){
			$query->the_post();
			$aOptions[$query->post->ID] = get_the_title($query->post->ID);
		}
		wp_reset_postdata();

		return $aOptions;
	}

	public function addListingPricingContent(){
		global $woocommerce, $post;
		$productID = $post->ID;
		?>
		<!-- id below must match target registered in above add_my_custom_product_data_tab function -->
		<div id="wilcity_listing_pricing" class="panel woocommerce_options_panel">
			<?php
			$aOptions = $this->getPricing();
			if ( !$aOptions ){
				?>
				<strong><?php esc_html_e('There is no Listing pricing', 'wiloke-listing-tools'); ?></strong>
				<?php
			}else{
				$val = GetSettings::getPostMeta($productID, 'add_event_plan');

				woocommerce_form_field( 'event_plan', array(
					'type'          => 'select',
					'class'         => array( 'wps-drop' ),
					'label'         => esc_html__( 'Select a Event Pricing', 'wiloke-listing-tools' ),
					'options'       => $aOptions
				), $val);
			}
			?>
		</div>
		<?php
	}
}
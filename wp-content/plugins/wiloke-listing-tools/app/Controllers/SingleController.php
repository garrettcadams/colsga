<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Routing\Controller;

class SingleController extends Controller {

	/*
	 * @tabKey: string
	 * @type: nav or viewAll
	 * @extraClass: string
	 * @tabName: string
	 */
	public static function renderTab($tabKey, $tabName = '', $type='nav', $extraClass=''){
		$class = '';
		if ( $type == 'nav' ){
			$class = 'list_link__2rDA1 text-ellipsis color-primary--hover';
		}else if ( $type == 'viewAll' ){
			$class = 'content-box_link__2K0Ib wil-text-center';
		}

		$class .= empty($extraClass) ? '' :  ' ' . $extraClass;

		?>
		<a class="<?php echo esc_attr($class); ?>" href="#" data-tab="<?php echo esc_attr($tabKey); ?>" @click="switchTab"><?php echo $tabName; ?></a>
		<?php
	}
}
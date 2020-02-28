<?php

namespace WilokeListingTools\Frontend;


class CheckoutPage {
	public function __construct() {
		add_action('wiloke/wilcity/addlisting/print-checkout', array($this, 'frontend'));
	}

	public function frontend(){
		?>

		<?php
	}
}
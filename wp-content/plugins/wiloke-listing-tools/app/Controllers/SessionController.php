<?php

namespace WilokeListingTools\Controllers;


use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Framework\Store\Session;

class SessionController extends Controller {
	public function __construct() {
		add_action('wiloke-submission/payment-succeeded-and-updated-everything', array($this, 'deletePaymentsSession'));
	}

	public function deletePaymentsSession(){
		Session::destroySession(wilokeListingToolsRepository()->get('payment:storePlanID'));
		Session::destroySession(wilokeListingToolsRepository()->get('payment:sessionObjectStore'));
		Session::destroySession(wilokeListingToolsRepository()->get('payment:associateProductID'));
		Session::destroySession(wilokeListingToolsRepository()->get('addlisting:isAddingListingSession'));
		Session::destroySession(wilokeListingToolsRepository()->get('payment:paypalTokenAndStoreData'));
		Session::destroySession('errorPayment');
	}
}
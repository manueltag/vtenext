<?php
/* crmv@42707 */

class TouchGetMenuList extends TouchWSClass {

	public function process(&$request) {
		global $touchUtils, $current_user;

		$response = $touchUtils->wsRequest($current_user->id,'getmenulist', array());
		$response = $response['result'];

		// TODO: success
		return $this->output($response);
	}
}


<?php
/* crmv@91082 */

require_once('modules/Users/LoginHistory.php'); 

class TouchLogout extends TouchWSClass {


	function process(&$request) {
		global $touchInst, $current_user;
		
		// crmv@91082 - login history
		$loghistory = LoginHistory::getInstance();
		$loghistory->user_logout($current_user->user_name);
		// crmv@91082e
		
		$touchInst->destroyWSSession();
		
		return $this->success();
	}
	
}


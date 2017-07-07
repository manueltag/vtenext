<?php
/* crmv@42537 - retrieves informations about the email config (accounts & folders) */

class TouchGetMessagesCount extends TouchWSClass {

	function process(&$request) {
		global $adb, $table_prefix, $touchInst;

		if (in_array('Messages', $touchInst->excluded_modules)) return $this->error('Module not permitted');

		// very stupid!
		ob_start();
		include('modules/SDK/src/Notifications/plugins/MessagesCheckChanges.php');
		$result = ob_get_clean();
		ob_end_clean();

		return $this->success(array('total'=>$result));
	}
}

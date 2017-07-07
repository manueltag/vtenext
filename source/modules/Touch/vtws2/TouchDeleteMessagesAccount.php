<?php
/* crmv@107655 */

class TouchDeleteMessagesAccount extends TouchWSClass {

	public function process(&$request) {
		global $current_user, $touchInst, $touchUtils;
		
		if (in_array('Messages', $touchInst->excluded_modules)) return $this->error('Module not permitted');
		
		$accountid = $request['accountid'];
		
		$focus = $touchUtils->getModuleInstance('Messages');

		if ($focus->canUserDeleteAccount($current_user->id, $accountid)) {
			$focus->deleteAccount($accountid);
		} else {
			return $this->error('Operation non permitted');
		}
		
		return $this->success();
	}
	
}


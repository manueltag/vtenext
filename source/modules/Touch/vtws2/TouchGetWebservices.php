<?php
/* crmv@93148 */

class TouchGetWebservices extends TouchWSClass {

	function process(&$request) {
		global $touchInst;
		
		$ws = array_keys($touchInst->webservices);
		
		return $this->success(array('webservices' => $ws));
	}

}


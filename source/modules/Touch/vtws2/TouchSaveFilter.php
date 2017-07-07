<?php
/* crmv@34559 */

class TouchSaveFilter extends TouchWSClass {

	public $validateModule = true;

	function process(&$request) {
	
		$module = $request['module'];
		$viewid = intval($request['viewid']);
		$recordid = intval($request['record']);
		$relRecordid = intval($request['relrecord']);
		
		// TODO:
		
		return $this->success();
		
	}
	
}


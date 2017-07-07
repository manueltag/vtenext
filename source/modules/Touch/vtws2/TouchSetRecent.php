<?php

class TouchSetRecent extends TouchWSClass {

	public $validateModule = true;

	function process(&$request) {
		global $log, $adb, $table_prefix, $touchInst, $touchUtils, $current_user, $currentModule;
		
		$module = $request['module'];
		$recordid = intval($request['record']);
		
		$trackMod = $module;
		if ($module == 'Events') $trackMod = 'Calendar';

		require_once('data/Tracker.php');

		try {
			$focus = $touchUtils->getModuleInstance($trackMod);
			$focus->track_view($current_user->id, $trackMod, $recordid);
		} catch (Exception $e) {
			return $this->error($e->getMessage());
		}
		
		return $this->success();
	}

}


<?php

class TouchGetGroups extends TouchWSClass {

	public function process(&$request) {
		global $adb, $table_prefix;

		$groupid = intval($request['groupid']);	// if set, retrieve only 1 group

		// get groups
		$groups = array();
		$res = $adb->pquery("select groupid, groupname from {$table_prefix}_groups".($groupid > 0 ? " WHERE groupid = ?" : ""), array($groupid));
		if ($res) {
			while ($row = $adb->fetchByAssoc($res, -1, false)) {
				$groups[] = $row;
			}
		}

		return $this->success(array('groups'=>$groups, 'total'=>count($groups)));
	}
}


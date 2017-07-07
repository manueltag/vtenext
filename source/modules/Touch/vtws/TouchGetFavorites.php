<?php
/* crmv@31780 */

global $adb, $table_prefix;
global $login, $userId;

if (!$login || !$userId) {
	echo 'Login Failed';
} else {

	require_once('modules/SDK/src/Favorites/Utils.php');

	$favs = getFavoritesList($userId);
	$favout = array();

	foreach ($favs as $k=>$v) {
		//if (in_array($v['module'], $touchInst->excluded_modules)) continue;
		// fix for Calendar (Events/Tasks)
		if ($v['module'] == 'Calendar') {
			$activityType = getSingleFieldValue($table_prefix.'_activity', 'activitytype', 'activityid', $v['crmid']);
			if ($activityType != 'Task') {
				// Events uses the "Events" module
				$v['module'] = 'Events';
			}
		}
		$newfav = $v;
		$newfav['entityname'] = $v['name'];
		$newfav['favourite'] = 1;
		$newfav['tabid'] = getTabId($v['module']);
		unset($newfav['name']);
		$favout[] = $newfav;
	}

	echo Zend_Json::encode($favout);
}
?>
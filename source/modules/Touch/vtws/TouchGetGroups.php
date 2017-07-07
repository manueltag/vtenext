<?php
/* crmv@31780 */

global $adb, $table_prefix, $login, $userId;

if (!$login || !$userId) {
	echo 'Login Failed';
} else {

	// get groups
	$groups = array();
	$res = $adb->query("select groupid, groupname from {$table_prefix}_groups");
	if ($res) {
		while ($row = $adb->fetchByAssoc($res)) {
			$groups[] = $row;
		}
	}

	echo Zend_Json::encode($groups);
}
?>
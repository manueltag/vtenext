<?php
/* crmv@34559 */
global $login, $userId, $current_user, $currentModule;

$module = $_REQUEST['module'];
$viewid = intval($_REQUEST['viewid']);
$recordid = intval($_REQUEST['record']);
$relRecordid = intval($_REQUEST['relrecord']);


if (!$login || empty($userId)) {
	echo 'Login Failed';
} elseif (in_array($module, $touchInst->excluded_modules)) {
	echo "Module not permitted";
} else {


	// non fa nulla per ora
}
?>
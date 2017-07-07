<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/
//crmv@35153
include_once('../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
global $adb, $table_prefix;
$user_name = vtlib_purify($_REQUEST['user_name']);
$result = $adb->query("SELECT user_name FROM {$table_prefix}_users WHERE id = 1");
if ($result && $adb->num_rows($result) > 0) {
	if ($user_name == $adb->query_result($result,0,'user_name')) {
		echo 'yes';
		exit;
	}
}
echo 'no';
exit;
//crmv@35153e
?>
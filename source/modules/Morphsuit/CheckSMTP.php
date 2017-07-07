<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/

$installation_mode = false;
if (empty($_SESSION)) {
	session_start();
}
if ($_SESSION['morph_mode'] == 'installation') {
	$installation_mode = true;
	if (!isset($root_directory)) {
		require_once('../../config.inc.php');
	}
	chdir($root_directory);
	require_once('include/utils/utils.php');
}
 
global $adb,$table_prefix;
$sql="select * from ".$table_prefix."_systems where server_type = ?";
$result = $adb->pquery($sql, array('email'));
$mail_server = $adb->query_result($result,0,'server');
if ($mail_server != '') {
	die('ok');
}
die('no');
?>
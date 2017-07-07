<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/

/* crmv@56023 */

include_once('../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
global $adb, $table_prefix;

$mailkey = vtlib_purify($_REQUEST['k']);

$error = false;
if (empty($mailkey)) {
	$error = true;
} else {
	$focus = CRMEntity::getInstance('Users');
	$result = $adb->pquery("select * from {$focus->track_login_table} where mailkey = ?",array($mailkey));
	if ($result && $adb->num_rows($result) > 0) {
		$id = $adb->query_result($result,0,'id');
		$ip = $adb->query_result($result,0,'ip');
		$userid = $adb->query_result($result,0,'userid');
		$status = $adb->query_result($result,0,'status');
		if ($status == 'W') {
			$error = true;
		}
	} else {
		$error = true;
	}
}
if ($error) {
	header('HTTP/1.0 403 Forbidden');
	include('themes/LoginHeader.php');
	include('modules/Users/403error.html');
} else {
	require_once('Smarty_setup.php');
	global $default_charset, $current_language;
	$result = $adb->pquery("select default_language from {$table_prefix}_users where id = ?",array($userid));
	if ($result && $adb->num_rows($result) > 0) {
		$language = $adb->query_result($result,0,'default_language');
		if (!empty($language)) $current_language = $language;
	}
	
	$adb->pquery("update {$focus->track_login_table} set status = ?, date_whitelist = ? where id = ?",array('W',date('Y-m-d H:i:s'),$id));
	
	$description = '
	<table border="0" cellpadding="20" cellspacing="0" width="100%" align="center" class="small">
	<tr><td colspan="2">'.sprintf(getTranslatedString('LBL_LOCKED_LOGIN_RESTORED','Users'),$ip).'</td></tr>
	</table>';
	
	header('Content-Type: text/html; charset='. $default_charset);
	$smarty = new vtigerCRM_Smarty;
	$smarty->assign('PATH','../../');
	$smarty->assign('CURRENT_LANGUAGE',$current_language);
	$smarty->assign('BODY',$description);
	$smarty->display('NoLoginMsg.tpl');
}
exit;
?>
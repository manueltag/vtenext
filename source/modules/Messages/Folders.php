<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/

if ($_REQUEST['file'] == 'Folders') {
	global $currentModule, $app_strings, $mod_strings;
	$focus = CRMEntity::getInstance($currentModule);
	require_once('Smarty_setup.php');
	$smarty = new vtigerCRM_Smarty();
	$smarty->assign('MOD', $mod_strings);
	$smarty->assign('APP', $app_strings);
	$smarty->assign('FOCUS', $focus);
	$focus->setAccount($_REQUEST['account']);
	$current_account = $_REQUEST['account'];
}

$smarty->assign('DIV_DIMENSION', array('Folders'=>'0%','ListViewContents'=>'24%','PreDetailViewContents'=>'60%','DetailViewContents'=>'61%','TurboliftContents'=>'15%'));
$smarty->assign('VIEW', 'list');

try {
	if ($current_account != 'all' && $focus->getZendMailStorageImap()) {
		$smarty->assign('FOLDERS', $focus->getFoldersList());
	}
} catch (Exception $e) {}

if ($_REQUEST['file'] == 'Folders') {
	$smarty->display("modules/Messages/Folders.tpl");
}
?>
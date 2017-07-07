<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/

global $currentModule, $table_prefix;
$mode = vtlib_purify($_REQUEST['mode']);
$account = vtlib_purify($_REQUEST['account']);
$folder = vtlib_purify($_REQUEST['folder']);
$record = vtlib_purify($_REQUEST['record']);
$current_folder = vtlib_purify($_REQUEST['current_folder']);
$thread = vtlib_purify($_REQUEST['thread']);
$focus = CRMEntity::getInstance($currentModule);

if (in_array($_REQUEST['view'],array('display','create'))) {
	require_once('Smarty_setup.php');
	$smarty = new vtigerCRM_Smarty();
	if (!empty($record)) {
		$account = getSingleFieldValue($table_prefix.'_messages', 'account', 'messagesid', $record);
	}
	$focus->setAccount($account);
	$focus->getZendMailStorageImap();
	if ($_REQUEST['view'] == 'display') {
		$view = 'move';
	} else {
		$view = $_REQUEST['view'];
	}
	$smarty->assign('FOLDERS', $focus->getFoldersList($view,$current_folder,$mode));
	$smarty->assign('VIEW', $view);
	$smarty->assign('MODE', $mode);
	$smarty->assign('ID', $record);
	$smarty->assign('FOCUS', $focus);
	$smarty->display("modules/Messages/Folders.tpl");
} else {
	if ($mode == 'single') {
		$focus->id = $record;
		$focus->retrieve_entity_info($record, $currentModule);
		$focus->setAccount($focus->column_fields['account']);
		$focus->moveMessage($folder);
	} elseif ($mode == 'mass') {
		$focus->massMoveMessage($account,$current_folder,$folder);
		$viewid = vtlib_purify($_REQUEST['viewname']);
		$return_module = $currentModule;
		$return_action = 'ListView';
		$url = getBasic_Advance_SearchURL();
		$rstart = "&start=".getLVSDetails($currentModule,$viewid,'start').'&load_all=true';	//crmv@48307
		$parenttab = getParentTab();
		header("location: index.php?module=$return_module&action={$return_module}Ajax&file=$return_action&ajax=true&parenttab=$parenttab$rstart&account=$account&folder=$current_folder&thread=$thread");
	} elseif ($mode == 'folders') {
		if ($focus->folderMove($account,$current_folder,$folder) === false) {
			die('FAILED');
		}
	} elseif ($mode == 'create') {
		if ($focus->folderCreate($account,$folder,$current_folder) === false) {
			die('FAILED');
		}
	}
	die('SUCCESS');
}
?>
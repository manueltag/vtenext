<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('modules/Documents/Documents.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');

global $adb;
global $current_user;
global $table_prefix;
if($current_user->is_admin != 'on')
{
	echo 'NOT_PERMITTED';
	die;
}
else
{
	$local_log =& LoggerManager::getLogger('index');
	if(isset($_REQUEST['folderid']) && $_REQUEST['folderid'] != '')
		$folderId = $_REQUEST['folderid'];
	else
	{
		echo 'FAILURE';
		die;
	}
	if(isset($_REQUEST['deletechk']) && $_REQUEST['deletechk'] == 'true')
	{
		$query = "select notesid from ".$table_prefix."_notes INNER JOIN ".$table_prefix."_crmentity ON ".$table_prefix."_crmentity.crmid = ".$table_prefix."_notes.notesid WHERE ".$table_prefix."_notes.folderid = ? and ".$table_prefix."_crmentity.deleted = 0";
		$result = $adb->pquery($query,array($folderId));
		if($adb->num_rows($result) > 0)
		{
			echo 'FAILURE';
		}
		else
		{
			header("Location: index.php?action=DocumentsAjax&file=ListView&mode=ajax&module=Documents");
			exit;
		}
	}
	else
	{
		if ($folderId != 1)	deleteEntityFolder($folderId); //crmv@30967
		header("Location: index.php?action=DocumentsAjax&file=ListView&mode=ajax&module=Documents");
		exit;
	}
}
?>
<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('config.php');
require_once('include/database/PearDatabase.php');

global $adb;
global $fileId;
global $current_user;
global $table_prefix;
$fileid = vtlib_purify($_REQUEST['fileid']);
$folderid = vtlib_purify($_REQUEST['folderid']);

$returnmodule='Documents';
$noteQuery = $adb->pquery("select crmid from ".$table_prefix."_seattachmentsrel where attachmentsid = ?",array($fileid));
$noteid = $adb->query_result($noteQuery,0,'crmid');
$dbQuery = "SELECT * FROM ".$table_prefix."_notes WHERE notesid = ? and folderid= ?";
$result = $adb->pquery($dbQuery,array($noteid,$folderid)) or die("Couldn't get file list");
if($adb->num_rows($result) == 1)
{
	$fileType = @$adb->query_result($result, 0, "filetype");
	$name = @$adb->query_result($result, 0, "filename");
	$name = html_entity_decode($name, ENT_QUOTES, $default_charset);
	$pathQuery = $adb->pquery("select path from ".$table_prefix."_attachments where attachmentsid = ?",array($fileid));
	$filepath = $adb->query_result($pathQuery,0,'path');
		
	$saved_filename = $fileid."_".$name;
	if(!$filepath.$saved_filename)
	$saved_filename = $fileid."_".$name;
	
	$filesize = filesize($filepath.$saved_filename);
	if(!fopen($filepath.$saved_filename, "r"))
	{
		echo 'unable to open file';
		$log->debug('Unable to open file');
	}
	else
	{
		$fileContent = fread(fopen($filepath.$saved_filename, "r"), $filesize);
	}
	if($fileContent != '')
	{
		$log->debug('About to update download count');
		$sql = "select filedownloadcount from ".$table_prefix."_notes where notesid= ?";
		$download_count = $adb->query_result($adb->pquery($sql,array($fileid)),0,'filedownloadcount') + 1;
		$sql="update ".$table_prefix."_notes set filedownloadcount= ? where notesid= ?";
		$res=$adb->pquery($sql,array($download_count,$fileid));
	}

	header("Content-type: $fileType");
	header("Content-length: $filesize");
	header("Cache-Control: private");
	header("Content-Disposition: attachment; filename=$name");
	header("Content-Description: PHP Generated Data");
	echo $fileContent;
}
else
{
	echo "Record doesn't exist.";
}
?>
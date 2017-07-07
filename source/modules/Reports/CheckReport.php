<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
// crmv@38798
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');

global $default_charset;
global $adb, $table_prefix;

$check = $_REQUEST['check'];

if ($check == 'reportCheck') {
	$reportName = $_REQUEST['reportName'];
	$reportId = intval($_REQUEST['reportid']);
	$isDuplicate = $_REQUEST['isDuplicate'];
	$sSQL = "select reportid from ".$table_prefix."_report where reportname=?";
	if ($reportId > 0 && $isDuplicate != 'true') $sSQL .= " and reportid != ?";

	$sqlresult = $adb->pquery($sSQL, array(trim($reportName), $reportId));
	echo $adb->num_rows($sqlresult);

} elseif ($check == 'folderCheck') {
	$folderName = function_exists(iconv) ? @iconv("UTF-8",$default_charset, $_REQUEST['folderName']) : $_REQUEST['folderName'];
	$folderName =str_replace(array("'",'"'),'',$folderName);
	if($folderName == "" || !$folderName) {
		echo "999";
	} else {
		// crmv@30967
		$SQL="select folderid from ".$table_prefix."_crmentityfolder where tabid = ? and foldername=?";
		$sqlresult = $adb->pquery($SQL, array(getTabid('Reports'), trim($folderName)));
		// crmv@30967e
		$id = $adb->query_result($sqlresult,0,"folderid");
		echo trim($adb->num_rows($sqlresult)."::".$id);
	}
}
exit;
?>
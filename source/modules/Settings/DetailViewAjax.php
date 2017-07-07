<?php
/*+********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*********************************************************************************/
/* crmv@92272 */

global $currentModule, $current_user;

$ajaxaction = $_REQUEST["ajxaction"];
if($ajaxaction == "DETAILVIEW")
{
	$crmid = $_REQUEST["recordid"];
	$tablename = $_REQUEST["tableName"];
	$fieldname = $_REQUEST["fldName"];
	$fieldvalue = utf8RawUrlDecode($_REQUEST["fieldValue"]); 
	
	if($crmid != "" && is_admin($current_user) && strpos($fieldname,'pm_') === 0)
	{
		require_once('modules/Settings/ProcessMaker/ProcessMakerUtils.php');
		$PMUtils = ProcessMakerUtils::getInstance();
		$PMUtils->edit($crmid,array(str_replace('pm_','',$fieldname)=>$fieldvalue));
		echo ":#:SUCCESS";
	} else {
		echo ":#:FAILURE";
	}
}
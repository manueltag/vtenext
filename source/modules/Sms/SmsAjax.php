<?php
/*+********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: crmvillage.biz Open Source
* The Initial Developer of the Original Code is crmvillage.biz* 
* Portions created by crmvillage.biz are Copyright (C) crmvillage.biz*.
* *All Rights Reserved.
********************************************************************************/

require_once('include/logging.php');
require_once('modules/Sms/Sms.php');
require_once('include/database/PearDatabase.php');
global $adb;

$local_log =& LoggerManager::getLogger('SmsAjax');

$ajaxaction = $_REQUEST["ajxaction"];
if($ajaxaction == "DETAILVIEW")
{
	$crmid = $_REQUEST["recordid"];
	$tablename = $_REQUEST["tableName"];
	$fieldname = $_REQUEST["fldName"];
	$fieldvalue = $_REQUEST["fieldValue"];
	if($crmid != "")
	{
		$modObj = CRMEntity::getInstance('Sms');
		$modObj->retrieve_entity_info($crmid,"Sms");
		$modObj->column_fields[$fieldname] = $fieldvalue;
		$modObj->id = $crmid;
		$modObj->mode = "edit";
		$modObj->save("Sms");
		if($modObj->id != "")
		{
			echo ":#:SUCCESS";
		}else
		{
			echo ":#:FAILURE";
		}   
	}else
	{
		echo ":#:FAILURE";
	}
}
elseif($_REQUEST['ajaxmode'] == 'qcreate')
{
        require_once('quickcreate.php');
}
else
{
        require_once('include/Ajax/CommonAjax.php');
}
?>

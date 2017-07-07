<?php
//crmv@17001
/*+********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*********************************************************************************/
// crmv@67410

global $currentModule, $current_user;
$modObj = CRMEntity::getInstance($currentModule);

$ajaxaction = $_REQUEST["ajxaction"];
if($ajaxaction == "DETAILVIEW")
{
	$crmid = $_REQUEST["recordid"];
	$tablename = $_REQUEST["tableName"];
	$fieldname = $_REQUEST["fldName"];
	$fieldvalue = utf8RawUrlDecode($_REQUEST["fieldValue"]); 
	if($crmid != "")
	{
		// crmv@101312
		$isTask = (getActivityType($crmid) == 'Task');
		$activityModule = ($isTask ? 'Calendar' : 'Events');
		
		$permEdit = isPermitted($activityModule, 'DetailViewAjax', $crmid);
		$permField = getFieldVisibilityPermission($activityModule, $current_user->id, $fieldname);
		// crmv@101312e
		
		if ($permEdit == 'yes' && $permField == 0) {
			$modObj->retrieve_entity_info($crmid,$activityModule);
			$modObj->column_fields[$fieldname] = $fieldvalue;

			$modObj->id = $crmid;
			$modObj->mode = "edit";
			$modObj->save($activityModule);
			if($modObj->id != "") {
				echo ":#:SUCCESS";
			} else {
				echo ":#:FAILURE";
			}   
		} else {
			echo ":#:FAILURE";
		}
	} else {
		echo ":#:FAILURE";
	}
} elseif($ajaxaction == "LOADRELATEDLIST" || $ajaxaction == "DISABLEMODULE"){
	require_once 'include/ListView/RelatedListViewContents.php';
}
//crmv@17001e
?>
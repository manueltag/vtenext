<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
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
		$permEdit = isPermitted($currentModule, 'DetailViewAjax', $crmid);
		$permField = getFieldVisibilityPermission($currentModule, $current_user->id, $fieldname);
		
		if ($permEdit == 'yes' && $permField == 0) {
			$modObj->retrieve_entity_info($crmid,$currentModule);
			$modObj->column_fields[$fieldname] = $fieldvalue;

			$modObj->id = $crmid;
			$modObj->mode = "edit";
			$modObj->save($currentModule);
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
?>
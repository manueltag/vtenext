<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $currentModule, $current_user;
$modObj = CRMEntity::getInstance($currentModule);

$ajaxaction = $_REQUEST["ajxaction"];
if($ajaxaction == 'DETAILVIEW')
{
	// crmv@67410
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
	// crmv@67410e
// crmv@43194
} elseif ($ajaxaction == "GETNOTIFICATION") {
	$record = intval($_REQUEST['record']);
	$setSeen = $_REQUEST['seen'];

	if ($record > 0 && isPermitted($currentModule, 'DetailView', $record) == 'yes') {
		if ($setSeen != '') {
			$adb->pquery("update {$table_prefix}_modnotifications set seen = ? where modnotificationsid = ?", array(($setSeen == '1' ? 1 : 0), $record));
			$adb->pquery("UPDATE {$table_prefix}_crmentity SET modifiedtime = ? WHERE crmid = ?",array(date('Y-m-d H:i:s'), $record));	// crmv@57366
		}

		$modObj->retrieve_entity_info($record, $currentModule);
		$modObj->id = $record;

		$widgetInstance = $modObj->getWidget('DetailViewBlockCommentWidget');
		$unseenCount = $modObj->getUnseenCount();	//crmv@64325
		echo $unseenCount.':#:SUCCESS'.$widgetInstance->processItem($widgetInstance->getModel($record));
	} else {
		echo ':#:FAILURE';
	}

// crmv@43194e
} elseif($ajaxaction == "LOADRELATEDLIST" || $ajaxaction == "DISABLEMODULE"){
	require_once 'include/ListView/RelatedListViewContents.php';
}
?>
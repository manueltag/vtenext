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

require_once('include/database/PearDatabase.php');
require_once('include/utils/utils.php');
global $current_user,$currentModule;
global $table_prefix;
global $adb;

$tabid=getTabid($currentModule);

//crmv@25255
if(isset($_REQUEST['selectedColumnsString'])) {
	$selected_col_string = rtrim($_REQUEST['selectedColumnsString'],",");
	$merge_criteria_cols = explode(',',$selected_col_string);

	// Drop all the existing merge field selections
	$adb->pquery("DELETE FROM {$table_prefix}_user2mergefields WHERE tabid=? AND userid=?", array($tabid, $current_user->id));
	
	if(!empty($merge_criteria_cols)) {
		// Update the new merge field selections
		foreach($merge_criteria_cols as $merge_fieldid) {
			$adb->pquery("INSERT INTO {$table_prefix}_user2mergefields (userid, tabid, fieldid, visible) VALUES (?,?,?,?)", 
			array($current_user->id, $tabid, $merge_fieldid, 1));
		}
	}
}
//crmv@25255e

unset($_SESSION['merge_user_fields']);	//crmv@26280
?>
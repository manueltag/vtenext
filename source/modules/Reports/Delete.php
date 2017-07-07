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
require_once('modules/Reports/Reports.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');

/* crmv@97237 */

require("user_privileges/user_privileges_".$current_user->id.".php");
global $current_user,$adb,$is_admin,$table_prefix;

$reports = Reports::getInstance();

if(isset($_REQUEST['idlist']) && $_REQUEST['idlist']!= '')
{
	$id_array = Array();
	$id_array = explode(':',$_REQUEST['idlist']);

	$query = $adb->pquery("select userid from ".$table_prefix."_user2role inner join ".$table_prefix."_users on ".$table_prefix."_users.id=".$table_prefix."_user2role.userid inner join ".$table_prefix."_role on ".$table_prefix."_role.roleid=".$table_prefix."_user2role.roleid where ".$table_prefix."_role.parentrole like '".$current_user_parent_role_seq."::%'",array());
	$subordinate_users = Array();
	for($i=0;$i<$adb->num_rows($query);$i++){
		$subordinate_users[] = $adb->query_result($query,$i,'userid');
	}
	
	for($i=0;$i<count($id_array);$i++) // crmv@30967
	{
		$own_query = $adb->pquery("SELECT reportname,owner FROM ".$table_prefix."_report WHERE reportid=?",array($id_array[$i]));
		$owner = $adb->query_result($own_query,0,"owner");
		if($is_admin==true || in_array($owner,$subordinate_users) || $owner==$current_user->id){
			$reports->deleteReport($id_array[$i]);
		} else {
			$del_failed []= $adb->query_result($own_query,0,"reportname");
		}
	}

	// crmv@30967
	if(!empty($del_failed))
		die('ERROR::Denied');
	else
		die('SUCCESS');
	// crmv@30967e

}elseif(isset($_REQUEST['record']) && $_REQUEST['record']!= '') {
	$id = vtlib_purify($_REQUEST["record"]);
	$reports->deleteReport($id);
	die('SUCCESS');
	//header("Location: index.php?action=ReportsAjax&file=ListView&mode=ajaxdelete&module=Reports");
}

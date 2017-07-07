<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

/* crmv@104283 */
require_once('Smarty_setup.php');
require_once('include/utils/utils.php');

global $app_strings, $mod_strings;
global $theme,$default_charset, $current_language;
global $adb, $table_prefix;

$delete_group_id = intval($_REQUEST['groupid']);
if (empty($delete_group_id)) die('No group id specified');

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
$mod_strings_users = return_module_language($current_language, 'Users');

$smarty = new vtigerCRM_Smarty();

$smarty->assign("MOD", $mod_strings_users);
$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);


$delete_group_name = fetchGroupName($delete_group_id);
$groupnameHtml = htmlentities($delete_group_name,ENT_QUOTES,$default_charset);

$otherGroups = array();
$result = $adb->pquery("select groupid,groupname from ".$table_prefix."_groups WHERE groupid != ?", array($delete_group_id));
if ($result && $adb->num_rows($result) > 0) {
	while ($row = $adb->FetchByAssoc($result, -1, false)) {
		$row['groupname'] = htmlentities($row["groupname"],ENT_QUOTES,$default_charset);
		$otherGroups[] = $row;
	}
}

$otherUsers = array();
$result1= $adb->query("select id,user_name from ".$table_prefix."_users where deleted=0");
if ($result1 && $adb->num_rows($result1) > 0) {
	while ($row = $adb->FetchByAssoc($result1, -1, false)) {
		$otherUsers[] = $row;
	}
}

$smarty->assign("GROUPID", $delete_group_id);
$smarty->assign("GROUPNAME", $groupnameHtml);
$smarty->assign("GROUPS", $otherGroups);
$smarty->assign("USERS", $otherUsers);

$smarty->display("modules/Users/GroupDelete.tpl");

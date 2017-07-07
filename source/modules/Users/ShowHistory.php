<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

/* crmv@91082 */
 
require_once('Smarty_setup.php');
require_once('data/Tracker.php');
require_once('modules/Users/LoginHistory.php');
require_once('modules/Users/Users.php');
require_once('include/logging.php');
require_once('include/utils/utils.php');

global $theme, $app_strings, $mod_strings, $app_list_strings;
global $current_language, $current_user, $currentModule;
global $adb, $table_prefix;

global $list_max_entries_per_page;
global $urlPrefix;

$log = LoggerManager::getLogger('login_list');

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$focus = LoginHistory::getInstance();
$LVU = ListViewUtils::getInstance();

$smarty = new vtigerCRM_Smarty;

$category = getParenttab();

$userid = intval($_REQUEST['record']);
$username = getUserName($userid);
$qry_result = $adb->pquery("SELECT COUNT(*) as cnt FROM {$focus->table_name} WHERE user_name= ?", array($username));
$no_of_rows = intval($adb->query_result_no_html($qry_result, 0, 'cnt'));

//Retreiving the start value from request
if(isset($_REQUEST['start']) && $_REQUEST['start'] != '') {
	$start = vtlib_purify($_REQUEST['start']);
} else {
	$start=1;
}

//Retreive the Navigation array
$navigation_array = $LVU->getNavigationValues($start, $no_of_rows, '10');

$start_rec = $navigation_array['start'];
$end_rec = $navigation_array['end_val'];
$record_string= $app_strings[LBL_SHOWING]." " .$start_rec." - ".$end_rec." " .$app_strings[LBL_LIST_OF] ." ".$no_of_rows;

$navigationOutput = $LVU->getTableHeaderNavigation($navigation_array, $url_string,"Users","ShowHistory",'');

$smarty->assign("CMOD", $mod_strings);
$smarty->assign("MOD", return_module_language($current_language, "Settings"));
$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("LIST_HEADER",$focus->getHistoryListViewHeader());
$smarty->assign("LIST_ENTRIES",$focus->getHistoryListViewEntries($username, $navigation_array, $sorder, $sortby));
$smarty->assign("RECORD_COUNTS", $record_string);
$smarty->assign("NAVIGATION", $navigationOutput);
$smarty->assign("CATEGORY",$category);

$smarty->display("ShowHistoryContents.tpl");
?>
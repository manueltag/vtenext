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
require_once("data/Tracker.php");
require_once('Smarty_setup.php');
require_once('include/logging.php');
require_once('include/utils/utils.php');
require_once('modules/Reports/Reports.php');

global $log;
global $app_strings;
global $app_list_strings;
global $mod_strings;
$current_module_strings = return_module_language($current_language, 'Reports');

global $list_max_entries_per_page;
global $urlPrefix,$current_user;

$log = LoggerManager::getLogger('report_list');

global $currentModule;

global $image_path;
global $theme;

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
// focus_list is the means of passing data to a ListView.
global $focus_list;

$folderid = intval($_REQUEST['folderid']); // crmv@30967

$list_report_form = new vtigerCRM_Smarty;
//sk@2
$dateFormat = parse_calendardate('');
$list_report_form->assign("dateFormat", $dateFormat);
$script_date = getConvertDateFormat();
$list_report_form->assign("SCRIPTDATE", $script_date);
//sk@2e
$list_report_form->assign("MOD", $mod_strings);
$list_report_form->assign("APP", $app_strings);
$list_report_form->assign("APPLIST", $app_list_strings);
$list_report_form->assign("THEME", $theme);
$list_report_form->assign("IMAGE_PATH", $image_path);

$list_report_form->assign("CATEGORY",getParentTab());
$list_report_form->assign("MODULE",$currentModule);
$list_report_form->assign("NEWRPT_BUTTON",$newrpt_button);
$list_report_form->assign("NEWRPT_FLDR_BUTTON",$newrpt_fldr_button);
$repObj = CRMEntity::getInstance('Reports');

// crmv@30967
$folderlist = $repObj->sgetRptFldr('SAVED', $folderid);
$cusFldrDtls = $repObj->sgetRptFldr('CUSTOMIZED', $folderid);
$folderlist = array_merge($folderlist, $cusFldrDtls);
//crmv@sdk-25785
$sdkfolders = SDK::getReportFolders();
if (!empty($sdkfolders) && (empty($folderid) || array_key_exists($folderid, $sdkfolders))) {
	//crmv@65492 - 25
	if (empty($folderid)) {
		$folderlist = array_merge($folderlist, $sdkfolders);
	} else {
		$folderlist = array_merge($folderlist, array($sdkfolders[$folderid])); //crmv@63341
	}
	//crmv@65492e - 25
}
// order the list of folders by name
usort($folderlist, function($a, $b) {
	return strcasecmp($a['name'], $b['name']);
});
//crmv@sdk-25785e
$list_report_form->assign("REPT_FLDR", $folderlist);
$list_report_form->assign("FOLDERID", $folderid);
// crmv@30967e

//sk@2
If (isProjectAdmin()) {
   $list_report_form->assign("ISPROJECTADMIN", "yes");
} elseif (isProjectLeader()) {
   $list_report_form->assign("ISPROJECTLEADER", "yes");
}
$list_report_form->assign("WORKERID", $current_user->id);
//sk@2e

foreach($cusFldrDtls as $entries)
{
	$fldrids_lists [] =$entries['id'];
}

// crmv@97862
if(count($fldrids_lists) > 0) {
	$list_report_form->assign("FOLDE_IDS",implode(',',$fldrids_lists));
}
// crmv@97862e

// crmv@30967
$allfolders = $repObj->sgetRptFldr();
// remove sdk folders from move list
$allfolders = array_filter($allfolders, create_function('$v', 'return $v["state"] != "SDK";'));
$list_report_form->assign("REPT_FOLDERS", $allfolders);
// crmv@30967e


$list_report_form->assign("DEL_DENIED",vtlib_purify($_REQUEST['del_denied']));

if($_REQUEST['mode'] == 'ajax')
	$list_report_form->display("ReportsCustomize.tpl");
elseif($_REQUEST['mode'] == 'ajaxdelete')
	$list_report_form->display("ReportContents.tpl");
else
	$list_report_form->display("Reports.tpl");

?>

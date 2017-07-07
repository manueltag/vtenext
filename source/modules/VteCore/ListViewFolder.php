<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ************************************************************************************/
/* crmv@30967 - listview a cartelle */

global $adb, $table_prefix;
global $app_strings, $mod_strings, $current_language, $currentModule, $theme;

require_once('Smarty_setup.php');
require_once('include/ListView/ListView.php');
require_once('modules/CustomView/CustomView.php');
require_once('modules/Reports/Reports.php');

$smarty = new vtigerCRM_Smarty();

$tool_buttons = Button_Check($currentModule);

$list_buttons = Array();

if (isPermitted($currentModule,'Delete','') == 'yes') $list_buttons['del'] = $app_strings['LBL_MASS_DELETE'];


$folderlist = array();
$focus = CRMEntity::getInstance($currentModule);

// get list of folders
if (method_exists($focus, 'getFolderList')) {
	$folderlist = $focus->getFolderList();
} else {
	$folderlist = getEntityFoldersByName(null, $currentModule);
}

// get elements info for each folder
if (method_exists($focus, 'getFolderContent')) {
	foreach ($folderlist as $key=>$fcont) {
		$foldercontent = $focus->getFolderContent($fcont['folderid']);
		$folderlist[$key]['content'] = $foldercontent;
	}
}

$customView = new CustomView($currentModule);

$viewid = $customView->getViewId($currentModule);

$queryGenerator = QueryGenerator::getInstance($currentModule, $current_user);
if ($viewid != "0") {
	$queryGenerator->initForCustomViewById($viewid);
} else {
	$queryGenerator->initForDefaultCustomView();
}


$smarty->assign('MOD', $mod_strings);
$smarty->assign('APP', $app_strings);
$smarty->assign("VIEWID", $viewid);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', getTranslatedString('SINGLE_'.$currentModule));
$smarty->assign("THEME", $theme);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('BUTTONS', $list_buttons);
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign("DATEFORMAT",$current_user->date_format);
$smarty->assign("OWNED_BY",getTabOwnedBy($currentModule));
//$smarty->assign("CRITERIA", $criteria);
//$smarty->assign("FIELDNAMES", $fieldnames);
//$smarty->assign("ALPHABETICAL", $alphabetical);
//$smarty->assign("SEARCHLISTHEADER", $listview_header_search);
$smarty->assign("HIDE_BUTTON_SEARCH", ($currentModule == 'Reports'));	//crmv@107103

$smarty->assign('FOLDERS_PER_ROW', 6);
$smarty->assign('FOLDERLIST', $folderlist);

// specific modules
if ($currentModule == 'Reports' || $currentModule == 'Charts') {
	$smarty->assign('HIDE_BUTTON_CREATE', true); // crmv@97862
}

$smarty_template = 'ListViewFolder.tpl';

$sdk_custom_file = 'ListViewFolderCustomisations';
if (isModuleInstalled('SDK')) {
    $tmp_sdk_custom_file = SDK::getFile($currentModule,$sdk_custom_file);
    if (!empty($tmp_sdk_custom_file)) {
    	$sdk_custom_file = $tmp_sdk_custom_file;
    }
}
@include("modules/$currentModule/$sdk_custom_file.php");

$smarty->display($smarty_template);
?>
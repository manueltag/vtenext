<?php
/***************************************************************************************
* The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is:  CRMVILLAGE.BIZ VTECRM
* The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
* Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
* All Rights Reserved.
***************************************************************************************/
/* crmv@42752 crmv@43050 crmv@43864 crmv@43942 */

require_once('Smarty_setup.php');
require_once('modules/Popup/Popup.php');
require_once('modules/Area/Area.php');

global $adb, $table_prefix;
global $mod_strings, $app_strings, $theme;
global $currentModule, $current_user;

$from_module = vtlib_purify($_REQUEST['from_module']);
$from_crmid = intval($_REQUEST['from_crmid']);
$mode = $_REQUEST['mode'];
$show_module = vtlib_purify($_REQUEST['show_module']);

if (!empty($from_module)) {
	$focus = CRMEntity::getInstance($from_module);
}

$popup = Popup::getInstance();

$smarty = new vtigerCRM_Smarty();
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('THEME', $theme);

$pageTitle = getTranslatedString('LBL_AREAS_SETTINGS');

$action = 'SettingsArea';

$areaManager = AreaManager::getInstance();
$areaList = $areaManager->getAreaList();
$linkMods = array();
foreach($areaList as $areaLis) {
	$linkMods[] = array('module'=>$areaLis['areaid'],'action'=>$action,'label'=>$areaLis['label']);
}
$smarty->assign('LINK_MODULES', $linkMods);

$smarty->assign('BROWSER_TITLE', $pageTitle);
$smarty->assign('PAGE_TITLE', $pageTitle);
$smarty->assign('HEADER_Z_INDEX', 10);

$extraInputs = array(
	'show_module' => $show_module,
	'popup_mode' => $mode,
	'from_module' => $from_module,
	'from_crmid' => $from_crmid,
);
$smarty->assign('EXTRA_INPUTS', $extraInputs);

// crmv@43050
// add extra js default js for the parent module
$extraJs = array();
$smarty->assign('EXTRA_JS', array_unique($extraJs));
// crmv@43050e

$smarty->display('modules/Popup/SettingsAreas.tpl');
?>
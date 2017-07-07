<?php
/***************************************************************************************
* The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is:  CRMVILLAGE.BIZ VTECRM
* The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
* Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
* All Rights Reserved.
***************************************************************************************/
/* crmv@43942 */

global $current_user, $theme;

require_once('Smarty_setup.php');
require_once('modules/Area/Area.php');

$areaid = intval($_REQUEST['mod']);

$areaManager = AreaManager::getInstance();
$areaList = $areaManager->getModuleList($areaid);
$areaList = $areaList['info'];
if ($areaid == -1) {
	usort($areaList, create_function('$a, $b','return ($a[\'translabel\'] > $b[\'translabel\']);'));
}
$allList = $areaManager->getSelectableModuleList($areaid,$areaList);

$smarty = new vtigerCRM_Smarty();
$smarty->assign('THEME', $theme);
$smarty->assign('AREAID', $areaid);
$smarty->assign('MODE', 'edit');
if ($areaid == -1 || $areaid == 0) {
	$smarty->assign('PERMISSION_DELETE', false);
} else {
	$smarty->assign('PERMISSION_DELETE', true);
}
$smarty->assign('CURRENTMODULES', $areaList);
$smarty->assign('OTHERMODULES', $allList);
$smarty->assign('HIGHTLIGHT_FIXED_MODULES', $areaManager->hightlight_fixed_modules);
$smarty->assign('HIDE_FIXED_MODULES', $areaManager->hide_fixed_modules);
$smarty->display('modules/Popup/SettingsArea.tpl');
?>
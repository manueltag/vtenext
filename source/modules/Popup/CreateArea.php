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

global $theme;

require_once('Smarty_setup.php');
require_once('modules/Area/Area.php');

$areaid = intval($_REQUEST['mod']);

$areaManager = AreaManager::getInstance();
$areaList = array();
$allList = $areaManager->getSelectableModuleList($areaid,$areaList);

$smarty = new vtigerCRM_Smarty();
$smarty->assign('THEME', $theme);
$smarty->assign('AREAID', '');
$smarty->assign('MODE', 'create');
$smarty->assign('PERMISSION_DELETE', false);
$smarty->assign('CURRENTMODULES', $areaList);
$smarty->assign('OTHERMODULES', $allList);
$smarty->assign('HIGHTLIGHT_FIXED_MODULES', $areaManager->hightlight_fixed_modules);
$smarty->assign('HIDE_FIXED_MODULES', $areaManager->hide_fixed_modules);
$smarty->display('modules/Popup/SettingsArea.tpl');
?>
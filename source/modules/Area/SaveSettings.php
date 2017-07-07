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

global $current_user;

$areaid = intval(vtlib_purify($_REQUEST['area']));

require_once('modules/Area/Area.php');
$areaManager = AreaManager::getInstance();

$modules = $_REQUEST['modules'];
$other_modules = $_REQUEST['other_modules'];

// if user don't have customisations duplicate default settings
if (!$areaManager->getSearchByUser()) {
	$areaManager->forceDefaultSettings($current_user->id);
}

if ($_REQUEST['mode'] == 'create') {
	$areaid = $areaManager->createArea($_REQUEST['areaname'],$modules);
} elseif ($_REQUEST['mode'] == 'edit') {
	$areaManager->editArea($areaid,$modules);
} elseif ($_REQUEST['mode'] == 'delete') {
	$areaManager->deleteArea($areaid);
}

header("location: index.php?module=Popup&action=PopupAjax&file=SettingsAreas&show_module=$areaid");
?>
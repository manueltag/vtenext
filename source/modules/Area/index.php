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

$areaid = vtlib_purify($_REQUEST['area']);

require_once('modules/Area/Area.php');
$area = Area::getInstance();
$area->constructById($areaid);
$area->setSessionVars();
$areaid = $area->getId();

if (empty($areaid)) {
	die('This page do not exists.');
}

global $theme, $app_strings, $currentModule;

require_once('Smarty_setup.php');
$smarty = new vtigerCRM_Smarty;
$smarty->assign('APP',$app_strings);
$smarty->assign('THEME',$theme);
$smarty->assign('MODULE',$currentModule);
$smarty->assign('REQUEST_ACTION',$_REQUEST['action']);
$smarty->assign('AREAID',$areaid);
$smarty->assign('AREANAME',$area->getName());
$smarty->assign('AREALABEL',$area->getLabel());
$smarty->assign('MODULES',$area->getModules());
if ($_REQUEST['query'] == 'true' || $_REQUEST['search'] == 'true') { // fix parameter
	$smarty->assign('QUERY_SCRIPT',$_REQUEST['search_text']);
	if (empty($_REQUEST['ajax'])) {
		$smarty->assign('AJAXCALL',true);
	} else {
		$list = $area->search($_REQUEST['search_text']);
	}
} else {
	$list = $area->getLastModified();
}
$smarty->assign('AREAMODULELIST',$list);
$smarty->display('modules/Area/Area.tpl');
?>
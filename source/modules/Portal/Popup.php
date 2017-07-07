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

require_once('Smarty_setup.php');
require_once('modules/Portal/Portal.php');

global $app_strings,$mod_strings,$theme;
global $adb, $table_prefix;

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty();

$smarty->assign("APP", $app_strings);
$smarty->assign("MOD", $mod_strings);

$portalid = intval($_REQUEST['record']);
$portalname = '';
$portalurl = '';

if ($portalid > 0) {
	$result = $adb->pquery("select * from ".$table_prefix."_portal where portalid = ?", array($portalid));
	$portalname = $adb->query_result($result,0,'portalname');
	$portalurl = $adb->query_result($result,0,'portalurl');	
	/* to remove http:// from portal url*/
	$portalurl = preg_replace("/http:\/\//i","",$portalurl);	
}

$smarty->assign('PORTALID', $portalid);
$smarty->assign('PORTALNAME', $portalname);
$smarty->assign('PORTALURL', $portalurl);

$smarty->display('modules/Portal/EditPopup.tpl');

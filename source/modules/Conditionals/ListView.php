<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is crmvillage.biz.
 * Portions created by crmvillage.biz are Copyright (C) crmvillage.biz.
 * All Rights Reserved.
 *******************************************************************************/

require_once('include/utils/utils.php');
require_once('Smarty_setup.php');
require_once('modules/Conditionals/Conditionals.php');

global $app_strings;
global $currentModule, $current_user;

if($current_user->is_admin != 'on')
{
	die("<br><br><center>".$app_strings['LBL_PERMISSION']." <a href='javascript:window.history.back()'>".$app_strings['LBL_GO_BACK'].".</a></center>");
}

$log = LoggerManager::getLogger('workflow_list');

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
$mod_strings = return_module_language($current_language, 'Conditionals');
$category = getParentTab();
//Display the mail send status
$smarty = new vtigerCRM_Smarty;
$conditionals_obj = CRMEntity::getInstance('Conditionals'); //crmv@36505
$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("CMOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("CURRENT_USERID", $current_user->id);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("CATEGORY",$category);
$smarty->assign("THEME", $theme);
$list_header = $conditionals_obj->wui_getFpofvListViewHeader(); //crmv@36505
$smarty->assign("LIST_HEADER", $list_header);
$ListEntries = $conditionals_obj->wui_getFpofvListViewEntries($fields_columnnames); //crmv@36505
$smarty->assign("LIST_ENTRIES", $ListEntries);
$smarty->assign("USER_COUNT",$no_of_users);
$smarty->assign("RECORD_COUNTS", $record_string);

// crmv@77249
if ($_REQUEST['included'] == 'true') {
	$smarty->assign("INCLUDED",true);
	$smarty->assign("FORMODULE",$_REQUEST['formodule']);
	$smarty->assign("STATUSFIELD",$_REQUEST['statusfield']);
}
// crmv@77249e

if($_REQUEST['ajax'] !='')
	$smarty->display("modules/Conditionals/ListViewContents.tpl");
else
	$smarty->display("modules/Conditionals/ListView.tpl");
 
?>

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
require_once('include/utils/utils.php');
require_once('include/utils/UserInfoUtil.php');
global $mod_strings;
global $app_strings;
global $app_list_strings;

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty;

// Look up for modules for which sharing access is enabled.
// NOTE: Accounts and Contacts has been couple, so we need to elimiate Contacts also
//crmv@13979	crmv@47243
$othermodules = getSharingModuleList(Array('Messages'));
//crmv@13979e	crmv@47243e
if(!empty($othermodules)) {
	foreach($othermodules as $moduleresname) {
		$custom_access[$moduleresname] = getAdvSharingRuleList($moduleresname);
	}
}

$smarty->assign("MODSHARING", $custom_access);

$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("CMOD", $mod_strings);
$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("THEME", $theme);
$smarty->display("AdvRuleDetailView.tpl");
?>

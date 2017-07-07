<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

global $app_strings, $mod_strings, $current_language, $currentModule, $current_user, $theme, $adb,$table_prefix;

$selected_modules = array();
if(!empty($_SESSION['__UnifiedSearch_SelectedModules__']) && is_array($_SESSION['__UnifiedSearch_SelectedModules__'])) {
	$selected_modules = $_SESSION['__UnifiedSearch_SelectedModules__'];
}

$allowed_modules = array();
$sql = 'select distinct '.$table_prefix.'_field.tabid,name from '.$table_prefix.'_field inner join '.$table_prefix.'_tab on '.$table_prefix.'_tab.tabid='.$table_prefix.'_field.tabid where '.$table_prefix.'_tab.tabid not in (16,29) and '.$table_prefix.'_tab.presence != 1 and isentitytype = 1 and '.$table_prefix.'_field.presence in (0,2)';	//crmv@27911
$moduleres = $adb->query($sql);
while($modulerow = $adb->fetch_array($moduleres)) {
	if(is_admin($current_user) || isPermitted($modulerow['name'], 'DetailView') == 'yes') {
		$modulename = $modulerow['name'];
		$allowed_modules[$modulename] = array(
			'label' => getTranslatedString($modulename, $modulename),
			'selected' => in_array($modulename, $selected_modules)
		);
	}
}
uasort($allowed_modules, create_function('$a,$b', 'return strcasecmp($a["label"], $b["label"]);')); // crmv@26485

require_once('Smarty_setup.php');

$smarty = new vtigerCRM_Smarty();
$smarty->assign('MOD', $mod_strings);
$smarty->assign('APP', $app_strings);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('ALLOWED_MODULES', $allowed_modules);

$smarty->display('UnifiedSearchModules.tpl');

?>
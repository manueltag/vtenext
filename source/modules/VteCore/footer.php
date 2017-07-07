<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/

// crmv@119414

require_once('Smarty_setup.php');

global $mod_strings, $app_strings, $currentModule, $theme;

$smarty = new vtigerCRM_Smarty();
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('CATEGORY', $category);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);

$smarty_template = 'Footer.tpl';

$sdk_custom_file = 'FooterCustomisations';
if (isModuleInstalled('SDK')) {
	$tmp_sdk_custom_file = SDK::getFile($currentModule,$sdk_custom_file);
	if (!empty($tmp_sdk_custom_file)) {
		$sdk_custom_file = $tmp_sdk_custom_file;
	}
}
@include("modules/$currentModule/$sdk_custom_file.php");

$smarty->display($smarty_template);

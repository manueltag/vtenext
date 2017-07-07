<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

require_once('Smarty_setup.php');
require_once('include/utils/CommonUtils.php');

global $mod_strings;
global $app_strings;
global $app_list_strings;
global $current_user,$default_charset;

global $import_mod_strings;

$focus = 0;

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$log->info($mod_strings['LBL_MODULE_NAME'] . " Upload Step 1");

$smarty = new vtigerCRM_Smarty;

$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("IMP", $import_mod_strings);

$smarty->assign("CATEGORY", htmlspecialchars($_REQUEST['parenttab'],ENT_QUOTES,$default_charset));

$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("PRINT_URL", "phprint.php?jt=".session_id().$GLOBALS['request_string']);

$smarty->assign("MODULE", $_REQUEST['module']);
$smarty->assign("MODULELABEL", getTranslatedString($_REQUEST['module'],$_REQUEST['module']));

$smarty->display("modules/PDFMaker/ImportPDFTemplate.tpl");

?>

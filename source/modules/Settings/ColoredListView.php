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

/* crmv@105538 */

require_once('Smarty_setup.php');
require_once('include/database/PearDatabase.php');
require_once('include/CustomFieldUtil.php');
require_once('include/utils/crmv_utils.php');
require_once('include/utils/EntityColorUtils.php');

$ECU = EntityColorUtils::getInstance();

global $mod_strings,$app_strings,$theme;
$smarty=new vtigerCRM_Smarty;
$smarty->assign("MOD",$mod_strings);
$smarty->assign("APP",$app_strings);
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
$smarty->assign("IMAGE_PATH", $image_path);
$module_array = $ECU->getSupportedModules();

$smarty->assign("MODULES",$module_array);
$smarty->assign("CFTEXTCOMBO",$cftextcombo);
$smarty->assign("CFIMAGECOMBO",$cfimagecombo);
$smarty->assign("THEME", $theme);
//crmv@36562
if($_REQUEST['clv_module'] !='')
	$clv_module = $_REQUEST['clv_module'];
else{
	// get first key
	foreach ($module_array as $k=>$v){
		$clv_module = $k;
		break;
	}
}	
//crmv@36562 e
$smarty->assign("MODULE",$clv_module);

$statusfield_array = $ECU->getStatusFields($clv_module);
$smarty->assign("STATUS_FIELD_ARRAY",$statusfield_array);

$statusfield = $ECU->getUsedStatusField($clv_module);
$smarty->assign("STATUS_FIELD",$statusfield);


if($_REQUEST['mode'] !='')
	$mode = $_REQUEST['mode'];
$smarty->assign("MODE", $mode);

if($_REQUEST['ajax'] != 'true')
	$smarty->display('ColoredListView.tpl');	
else
	$smarty->display('ColoredListContent.tpl');

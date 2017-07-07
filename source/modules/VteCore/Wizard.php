<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/

/* crmv@OPER6317 crmv@96233 crmv@98866 */

require_once('Smarty_setup.php');
require_once 'include/utils/WizardUtils.php';
// webservices
require_once 'include/Webservices/Utils.php';
require_once("include/Webservices/VtigerCRMObject.php");
require_once("include/Webservices/VtigerCRMObjectMeta.php");
require_once("include/Webservices/DataTransform.php");
require_once("include/Webservices/WebServiceError.php");
require_once('include/Webservices/ModuleTypes.php');
require_once("include/Webservices/Retrieve.php");
require_once("include/Webservices/DescribeObject.php");

global $adb, $table_prefix;
global $mod_strings, $app_strings, $theme;
global $currentModule, $current_user;

$smarty = new vtigerCRM_Smarty();
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', return_module_language($current_language, 'Newsletter'));
$smarty->assign('MODULE', $currentModule);
$smarty->assign('THEME', $theme);
$smarty->assign("CALENDAR_LANG", $app_strings['LBL_JSCALENDAR_LANG']);

$JSGlobals = ( function_exists('getJSGlobalVars') ? getJSGlobalVars() : array() );
$smarty->assign('JS_GLOBAL_VARS',Zend_Json::encode($JSGlobals));

$wizardid = intval($_REQUEST['wizardid']);
$parentModule = vtlib_purify($_REQUEST['parentModule']);
$parentId = intval($_REQUEST['parentId']);

$extraParams = vtlib_purify($_REQUEST['params']);

$WU = WizardUtils::getInstance();
$WG = WizardGenerator::getInstance();
$wizardInfo = $WU->getWizardInfo($wizardid);

$pageTitle = getTranslatedString('Wizard');

$wizardFile = $wizardInfo['src'];
$wizardTpl = $wizardInfo['template'];
$wizardCfg = $wizardInfo['config'];

if ($wizardCfg) {
	$params = array();
	if ($parentModule && $parentId) {
		$params['parentModule'] = $parentModule;
		$params['parentId'] = $parentId;
		$smarty->assign('PARENT_MODULE', $parentModule);
		$smarty->assign('PARENT_ID', $parentId);
	}
	$wizardSteps = $WG->generateWizardSteps($wizardid, $wizardCfg, $params);
	$smarty->assign('WIZARD', $wizardSteps);
}

if ($wizardFile && is_readable($wizardFile)) {
	require($wizardFile);
}

$smarty->assign('HEADER_Z_INDEX', 10);

$smarty->assign('WIZARD_ID', $wizardid);
$smarty->assign('BROWSER_TITLE', $pageTitle);
$smarty->assign('PAGE_TITLE', $pageTitle);

if ($wizardTpl) {
	$smarty->display($wizardTpl);
} else {
	$smarty->display('Wizard.tpl');
}
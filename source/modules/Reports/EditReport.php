<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
// crmv@38798 - Rename Report, Duplicate Report
/* crmv@98764 */

require_once('Smarty_setup.php');
require_once("data/Tracker.php");
require_once('include/logging.php');
require_once('include/utils/utils.php');
require_once('modules/Reports/Reports.php');

global $app_strings, $app_list_strings, $mod_strings;
global $currentModule, $current_language, $current_user;
global $theme, $image_path;

$log = LoggerManager::getLogger('report_list');

$mode = '';
$recordid = intval($_REQUEST['record']);
$folderid = intval($_REQUEST['folder']);
$duplicate = intval($_REQUEST['duplicate']);
$formodule = $_REQUEST['formodule'];

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty;
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("THEME_PATH", $theme_path);
$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);

$CU = CRMVUtils::getInstance();
$repObj = Reports::getInstance();

if ($duplicate > 0) $recordid = $duplicate;

if ($recordid > 0){
	$mode = ($duplicate > 0 ? 'create' : 'edit');
	
	// access control
	if (!$duplicate && !$repObj->isEditable($recordid)) {
		$CU->showAccessDenied(getTranslatedString('LBL_NO_ACCESS'), false);
		die();
	} elseif ($duplicate > 0 && !$repObj->isViewable($recordid)) {
		$CU->showAccessDenied(getTranslatedString('LBL_NO_ACCESS'), false);
		die();
	}
	
	$repinfo = $repObj->loadReport($recordid);
	$smarty->assign("REPORTID",($duplicate > 0 ? '' : $recordid));
	$smarty->assign("FOLDERID",$repinfo['folderid']);
	
	$repObj->prepareForEdit($repinfo);
	
	$smarty->assign("PRIMARYMODULE",$repinfo['module']);
	$smarty->assign("PRIMARYMODULE_LABEL",getTranslatedString($repinfo['module'], $repinfo['module']));
	$smarty->assign("REPORTNAME", ($duplicate > 0 ? '' : $repinfo['reportname']));
	$smarty->assign("REPORTDESC", $repinfo['description']);
	
	$smarty->assign("REPORT_TYPE", $repinfo['reporttype']);
	$smarty->assign("SHARING_TYPE", $repinfo['sharingtype']);
	
	//$smarty->assign("RELATIONS", $repinfo['relations']);
	$smarty->assign("STDFILTERS", $repinfo['stdfilters']);
	$smarty->assign("ADVFILTERS", $repinfo['advfilters']);
	$smarty->assign("FIELDS", $repinfo['fields']);
	$smarty->assign("TOTALS", $repinfo['totals']);
	$smarty->assign("SUMMARY", $repinfo['summary']);
	$smarty->assign("SHARING", $repinfo['sharing']);
	
	
	
	// preload some relations and fields
	$preloadChain = array($repinfo['module']);
	$preload_js = array(
		array(
			'type' => 'modules',
			'chain' => $preloadChain,
			'data' => $repObj->getModulesListForChain($recordid, $preloadChain),
		),
		array(
			'type' => 'fields',
			'fieldstype' => '',
			'chain' => $preloadChain,
			'data' => $repObj->getFieldsListForChain($recordid, $preloadChain),
		),
		array(
			'type' => 'fields',
			'fieldstype' => 'stdfilter',
			'chain' => $preloadChain,
			'data' => $repObj->getStdFiltersFieldsListForChain($recordid, $preloadChain),
		),
		array(
			'type' => 'fields',
			'fieldstype' => 'advfilter',
			'chain' => $preloadChain,
			'data' => $repObj->getAdvFiltersFieldsListForChain($recordid, $preloadChain),
		),
		array(
			'type' => 'fields',
			'fieldstype' => 'total',
			'chain' => $preloadChain,
			'data' => $repObj->getTotalsFieldsListForChain($recordid, $preloadChain),
		),
	);
	$smarty->assign("PRELOAD_JS", Zend_Json::encode($preload_js));
	
} else {
	$mode = 'create';
	$smarty->assign("FOLDERID",$folderid);
	if (!empty($formodule)) {
		$smarty->assign("PRIMARYMODULE",$formodule);
		$smarty->assign("PRIMARYMODULE_LABEL",getTranslatedString($formodule, $formodule));
	}
	
}

$repModules = $repObj->getAvailableModules();
unset($repModules['ProductsBlock']);

// Report assigned to
$report_assigned_to = ($recordid > 0 ? $repinfo['owner'] : $current_user->id);
$bck_mod = $_REQUEST['module'];
$_REQUEST['module'] = $repinfo['module'] ?: 'Accounts';
$usersToView = get_user_array(false, "Active", $report_assigned_to, ($current_user->is_admin != 'on' ? 'private': ''));
$_REQUEST['module'] = $bck_mod;

$smarty->assign("REP_ASSIGNED_TO", $report_assigned_to);
$smarty->assign("ASSIGNED_TO_USERS",$usersToView);

$smarty->assign("REP_FOLDERS", $repObj->sgetRptFldr(array('SAVED', 'CUSTOMIZED')));
$smarty->assign("REPT_MODULES",$repModules);
$smarty->assign("COMPARATORS",$repObj->getAdvFilterOptions());

$smarty->assign("DATEFORMAT",$current_user->date_format);
$smarty->assign("JS_DATEFORMAT",parse_calendardate(getTranslatedString('NTC_DATE_FORMAT', 'APP_STRINGS')));

$availFuncs = $repObj->get_available_functions();
$smarty->assign("FIELD_FUNCTIONS",$availFuncs);
$smarty->assign("FIELD_FUNCTIONS_JS",Zend_Json::encode($availFuncs));

// values for standard filters
$BLOCKJS = $repObj->getCriteriaJS();
$smarty->assign("BLOCKJS",$BLOCKJS);
$smarty->assign("STDFILTEROPTIONS",$repObj->getStdFilterOptions($recordid));

if ($mode == 'create' && Vtlib_isModuleActive('Charts') && isPermitted('Charts', 'EditView') == 'yes') {
	$smarty->assign("CAN_CREATE_CHARTS",true);
	$smarty->assign("CHARTS_LANG", return_module_language($current_language, 'Charts'));
	$chartInst = CRMEntity::getInstance('Charts');
	$smarty->assign("CHART_TYPES", $chartInst->getChartTypes());
	
	$qcreate_array = QuickCreate("Charts");
	$col_fields = $chartInst->getQuickCreateDefault('Charts', $qcreate_array, '', '');
	if (!empty($col_fields)) {
		$qcreate_array = QuickCreate("Charts", $col_fields);
	}
	$smarty->assign("QUICKCREATE", $qcreate_array['form']);
	
} else {
	$smarty->assign("CAN_CREATE_CHARTS",false);
}

$visiblecriteria = $repObj->getVisibleCriteria($recordid);
$smarty->assign("VISIBLECRITERIA", $visiblecriteria);
$smarty->assign("SHAREUSERS_JS", Zend_Json::encode($repObj->getSubordinateUsers()));
$smarty->assign("SHAREGROUPS_JS", Zend_Json::encode($repObj->getUserGroups()));

// crmv@100905
$JSGlobals = ( function_exists('getJSGlobalVars') ? getJSGlobalVars() : array() );
$smarty->assign('JS_GLOBAL_VARS',Zend_Json::encode($JSGlobals));
// crmv@100905e

$smarty->display("modules/Reports/EditReport.tpl");

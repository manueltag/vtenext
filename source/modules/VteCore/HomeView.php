<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/

/* crmv@83340 crmv@98431 crmv@102334 */

require_once('include/utils/ModuleHomeView.php');
require_once('Smarty_setup.php');

global $currentModule, $current_user, $adb, $table_prefix, $theme, $mod_strings, $app_strings;

$category = getParentTab();
$smarty = new vtigerCRM_Smarty();

$smarty->assign('MOD', $mod_strings);
$smarty->assign('APP', $app_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', getTranslatedString('SINGLE_'.$currentModule));
$smarty->assign('CATEGORY', $category);
$smarty->assign("THEME", $theme);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");

$smarty->assign('CHECK', Button_Check($currentModule));

$MHW = ModuleHomeView::getInstance($currentModule, $current_user->id);
$views = $MHW->getViews(true);
//crmv@105882
if (empty($views)) {
	$MHW->setDefaultListViewTab();
	$views = $MHW->getViews(true);
}
//crmv@105882e
$modhomeid = $MHW->getModHomeId($views);

$smarty->assign("MODHOMEID", $modhomeid);
$smarty->assign("MODHOMEVIEWS", $views);

$smarty->assign("EDITMODE", $_REQUEST['editmode']); // crmv@105193
$smarty->assign("CAN_ADD_HOME_BLOCKS", true);
$smarty->assign("CAN_ADD_HOME_VIEWS", true);
$smarty->assign("CAN_DELETE_HOME_VIEWS", true);
$smarty->assign("HOME_BLOCK_TYPES", $MHW->getSupportedBlocks());

$currentview = null;
foreach ($views as $view) {
	if ($view['modhomeid'] == $modhomeid) {
		$currentview = $view;
		break;
	}
}
if ($currentview['cvid'] > 0) {
	$smarty->assign("MODHOMEVIEWTYPE", 'ListView');
	$module = $currentModule;
	$action = 'ListView';
	if (isset($_REQUEST['viewmode'])) {
		if ($_REQUEST['viewmode'] == 'KanbanView') $action = 'KanbanView';
		$MHW->setKanabaView($modhomeid,($action=='KanbanView')?1:0);	//crmv@106616
	} else {
		$lvs_viewmode = getLVSDetails($currentModule,$currentview['cvid'],'viewmode');
		if (!empty($lvs_viewmode)) $action = $lvs_viewmode;
		elseif ($currentview['kanban'] == '1') $action = 'KanbanView';	//crmv@106616
	}
	setLVSDetails($currentModule,$currentview['cvid'],$action,'viewmode');

	$is_action = false;
	$in_core = false;
	$in_dir = @scandir($root_directory.'modules/'.$module);
	$res_arr = @array_intersect($in_dir,$temp_arr);
	if(count($res_arr) == 0 && !preg_match("/[\/.]/",$module)) {
		if(@in_array($action.".php",$in_dir))
		$is_action = true;
	}
	if(!$is_action) {
		$in_dir = @scandir($root_directory.'modules/VteCore');
		$res_arr = @array_intersect($in_dir,$temp_arr);
		if(count($res_arr) == 0 && !preg_match("/[\/.]/",'VteCore')) {
			if(@in_array($action.".php",$in_dir)) {
				$is_action = true;
				$in_core = true;
			}
		}
	}
	$sdk_action = '';
	if (isModuleInstalled('SDK')) {
		$sdk_action = SDK::getFile($module,$action);
	}
	$call_sdk = true;
	if ($sdk_action == '') {
		$sdk_action = $action;
		$call_sdk = false;
	}
	if ($in_core && !$call_sdk) {
		$listViewFile = 'modules/VteCore/'.$sdk_action.'.php';
	} else {
		$listViewFile = 'modules/'.$module.'/'.$sdk_action.'.php';
	}
	$homepage = true;
	include($listViewFile);
	$smarty->assign("LISTVIEWTPL", $smarty_template);
	$smarty->assign("REQUEST_ACTION", $action);
	
} elseif ($currentview['reportid'] > 0) {
	/*$CU = CRMVUtils::getInstance();
	$ogReport = Reports::getInstance();

	if ($ogReport->reportExists($reportid)) {

		if ($ogReport->isViewable($reportid)) {
			$config = $ogReport->loadReport($reportid);
			$oReportRun = ReportRun::getInstance($reportid);
		}
	}*/
}

$lbl_no_home_blocks = sprintf($app_strings['LBL_NO_HOME_BLOCKS'], '<a href="javascript:void(0);" onclick="ModuleHome.chooseNewBlock(\''.$modhomeid.'\')">'.$app_strings['LBL_HERE'].'</a>');
$smarty->assign("LBL_NO_HOME_BLOCKS", $lbl_no_home_blocks);

$smarty_template = 'ModuleHomeView.tpl';

$sdk_custom_file = 'HomeViewCustomisations';
if (isModuleInstalled('SDK')) {
    $tmp_sdk_custom_file = SDK::getFile($currentModule,$sdk_custom_file);
    if (!empty($tmp_sdk_custom_file)) {
    	$sdk_custom_file = $tmp_sdk_custom_file;
    }
}
@include("modules/$currentModule/$sdk_custom_file.php");

if(isset($_REQUEST['ajax']) && $_REQUEST['ajax'] != '')
	$smarty->display($smarty_ajax_template);
else
	$smarty->display($smarty_template);

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
 
/* crmv@104568 */

require_once('Smarty_setup.php');
require_once('include/database/PearDatabase.php');
require_once('include/CustomFieldUtil.php');
require_once('include/utils/UserInfoUtil.php');
require_once('include/utils/utils.php');
require_once('include/ComboUtil.php');
require_once('modules/Settings/LayoutBlockListUtils.php'); // crmv@64542

global $mod_strings,$app_strings,$log,$theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once('modules/VteCore/layout_utils.php');	//crmv@30447
$smarty=new vtigerCRM_Smarty;

$subMode = $_REQUEST['sub_mode'];
$smarty->assign("MOD",$mod_strings);
$smarty->assign("APP",$app_strings);

if($_REQUEST['formodule'] !='')
	$fld_module = $_REQUEST['formodule'];
elseif($_REQUEST['fld_module'] != ''){
	$fld_module = $_REQUEST['fld_module'];
}else
	$fld_module = 'Accounts';
	
$panelid = intval($_REQUEST['panelid']);
if (!$panelid) {
	$panelid = getCurrentPanelId($fld_module);
} else {
	// check if exists
	$tab = Vtecrm_Panel::getInstance($panelid);
	if (!$tab) {
		$panelid = getCurrentPanelId($fld_module);
	}
}
$smarty->assign("PANELID",$panelid);

// crmv@39110
if ($_REQUEST['mobile'] == '1') {
	$forMobile = true;
	$mobileProfiles = getAllProfileInfo(true);
	$smarty->assign("MOBILE_PROFILES",$mobileProfiles);
	if (count($mobileProfiles) > 0) {
		// get the first only - multiple profiles not suppoerted
		$mobileProfileId = array_keys($mobileProfiles);
		$mobileProfileId = $mobileProfileId[0];
		// align fields with profiles
		if (empty($subMode)) {
			alignFieldsForProfile($fld_module, $mobileProfileId);
			alignRelatedForProfile($fld_module, $mobileProfileId);
			alignMobileInfoForProfile($fld_module, $mobileProfileId);
		}
	}
}
$smarty->assign("FORMOBILE",$forMobile);

$smarty->assign("MODULELIST",getModuleList());

if ($subMode == 'addTab') {
	createTab($fld_module);
} elseif ($subMode == 'editTab') {
	editTab($fld_module);
} elseif ($subMode == 'deleteTab') {
	deleteTab();
} elseif ($subMode == 'reorderTabs') {
	reorderTabs($fld_module);
	die();
} elseif ($subMode == 'moveBlockToTab') {
	moveBlockToTab();
} elseif ($subMode == 'updateFieldProperties')
	updateFieldProperties($forMobile, $mobileProfileId);
elseif($subMode == 'deleteCustomField')
	deleteCustomField();
elseif($subMode == 'changeOrder')
	changeFieldOrder($forMobile, $mobileProfileId);
elseif($subMode == 'addBlock')
	$duplicate = addblock();
elseif($subMode == 'deleteCustomBlock')
	deleteBlock();
elseif($subMode == 'addCustomField')
	$duplicate = addCustomField();
elseif($subMode == 'movehiddenfields' || $subMode == 'showhiddenfields')
	show_move_hiddenfields($subMode, $forMobile, $mobileProfileId);
elseif($subMode == 'changeRelatedInfoOrder')
	changeRelatedListOrder($forMobile, $mobileProfileId);
elseif($subMode == 'changeRelatedInfoVisibility')
	changeRelatedListVisibility($forMobile, $mobileProfileId);
// crmv@104568
elseif($subMode == 'changeRelatedOption') {
	changeRelatedListOption($forMobile, $mobileProfileId);
// crmv@104568e
} elseif ($subMode == 'addRelatedToTab') {
	addRelatedToTab($fld_module);
} elseif ($subMode == 'removeTabRelated') {
	removeTabRelated($fld_module);
} elseif ($subMode == 'reorderTabRelateds') {
	reorderTabRelateds($fld_module);
	die();
} elseif($subMode == 'saveMobileInfo') {
	saveMobileInfo($fld_module, $mobileProfileId);
}
// crmv@39110e

$smarty->assign("THEME", $theme);
$module_array=getCustomFieldSupportedModules();

$cfimagecombo = Array(
	$image_path."text.gif",
	$image_path."number.gif",
	$image_path."percent.gif",
	$image_path."currency.gif",
	$image_path."date.gif",
	$image_path."email.gif",
	$image_path."phone.gif",
	$image_path."picklist.gif",
	$image_path."url.gif",
	$image_path."checkbox.gif",
	$image_path."text.gif",
	$image_path."picklist.gif"
	);

$cftextcombo = Array(
	$mod_strings['Text'],
	$mod_strings['Number'],
	$mod_strings['Percent'],
	$mod_strings['Currency'],
	$mod_strings['Date'],
	$mod_strings['Email'],
	$mod_strings['Phone'],
	$mod_strings['PickList'],
	$mod_strings['LBL_URL'],
	$mod_strings['LBL_CHECK_BOX'],
	$mod_strings['LBL_TEXT_AREA'],
	$mod_strings['LBL_MULTISELECT_COMBO']
	);

$smarty->assign("MODULES",$module_array);
$smarty->assign("CFTEXTCOMBO",$cftextcombo);
$smarty->assign("CFIMAGECOMBO",$cfimagecombo);

$tabs = getPanelsAndBlocks($fld_module);
$smarty->assign("TABS",$tabs);
$smarty->assign("TABS_JSON",Zend_Json::encode($tabs));

$tabRelids = array();
if ($panelid > 0) {
	$currentPanel = Vtecrm_Panel::getInstance($panelid);
	$panelRelated = $currentPanel->getRelatedLists();
	foreach ($panelRelated as $rel) {
		$tabRelids[] = $rel['id'];
	}
	$smarty->assign("TAB_RELATED", $panelRelated);
}
$smarty->assign("TAB_RELIDS", $tabRelids);

$block_array = getModuleBlocks($fld_module);

$smarty->assign("BLOCKS",$block_array);
$smarty->assign("MODULE",$fld_module);

$smarty->assign("CFENTRIES",getFieldListEntries($fld_module, $mobileProfileId));
$smarty->assign("RELATEDLIST",getRelatedListInfo($fld_module, $mobileProfileId));
$smarty->assign("RELATEDLISTCONFIG",getRelatedListOptions($fld_module, $mobileProfileId)); // crmv@104568
if ($forMobile) {
	$cvFocus = CRMEntity::getInstance('CustomView', $fld_module);
	$smarty->assign("CVLIST",$cvFocus->getPublicFilters());
	$smarty->assign("MOBILEINFO",getMobileInfo($fld_module, $mobileProfileId));
}

if(isset($_REQUEST["duplicate"]) && $_REQUEST["duplicate"] == "yes" || $duplicate == 'yes')
{
	echo "ERROR";
	exit;
}
if($duplicate == 'LENGTH_ERROR'){
	echo "LENGTH_ERROR";
	exit;
}
if($_REQUEST['mode'] !='')
	$mode = $_REQUEST['mode'];

$smarty->assign("MODE", $mode);

//crmv@101683
$smarty->assign('NEWFIELDS', getNewFields());
$smarty->assign('USERSLIST', get_user_array(true, "Active"));
//crmv@101683e

if($_REQUEST['ajax'] != 'true'){
	$smarty->display('Settings/LayoutBlockList.tpl');
}
elseif(in_array($subMode, array('getRelatedInfoOrder', 'changeRelatedInfoOrder', 'changeRelatedInfoVisibility', 'changeRelatedOption')) && $_REQUEST['ajax'] == 'true') { // crmv@104568
	$smarty->display('Settings/OrderRelatedList.tpl');
}
else{
	$smarty->display('Settings/LayoutBlockEntries.tpl');
}


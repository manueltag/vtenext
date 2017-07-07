<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is:  CRMVILLAGE.BIZ VTECRM
* The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
* Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
* All Rights Reserved.
***************************************************************************************/
/* crmv@43611 */

require_once('Smarty_setup.php');

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

$pageTitle = getTranslatedString('NewsletterWizard', 'Campaigns');

$smarty->assign('BROWSER_TITLE', $pageTitle);
$smarty->assign('PAGE_TITLE', $pageTitle);


$campaignid = intval($_REQUEST['from_record']);
$newsletterid = intval($_REQUEST['newsletterid']);

if ($campaignid > 0) {
	// retrieve targets
	$RM = RelationManager::getInstance();
	$targets = $RM->getRelatedIds('Campaigns', $campaignid, 'Targets');
	$tlist = array();
	foreach ($targets as $tid) {
		$tname = getEntityName('Targets', $tid);
		$tlist[] = array('crmid'=> $tid, 'entityname'=>$tname[$tid]);
	}
	$smarty->assign('SEL_TARGETS', $tlist);
}


$smarty->assign('CAMPAIGNID', $campaignid);
$smarty->assign('NEWSLETTERID', $newsletterid);

$smarty->assign('TESTEMAILADDRESS', getUserEmail($current_user->id));

require_once('include/ListView/SimpleListView.php');

$Slv = SimpleListView::getInstance('EmailTemplates'); // fake module, but works as well
$Slv->listid = 200;
$Slv->maxFields = 2;
$Slv->entriesPerPage = 5;
$Slv->showCreate = true;	//crmv@55230	is_admin($current_user);
$Slv->selectFunction = 'nlwTemplateSelect';
$Slv->createFunction = 'nlwTemplateEdit';
$Slv->showCheckboxes = false;

$lv = $Slv->render();
$smarty->assign('TPLLIST', $lv);
$smarty->assign('CAN_EDIT_TEMPLATES', $Slv->showCreate);

$allOptions = getEmailTemplateVariables();
$smarty->assign('TPLVARIABLES', $allOptions);

// get newsletter fields
$nlfields = array('newslettername', 'from_name', 'from_address', 'description');
// retrieve them with webservices
$wsmodule = vtws_describe('Newsletter', $current_user);
$fields = array();
foreach ($wsmodule['fields'] as $f) {
	if (in_array($f['name'], $nlfields)) {
		if ($f['name'] == 'from_name') {
			$f['value'] = trim(getUserFullName($current_user->id));
		} elseif ($f['name'] == 'from_address') {
			$f['value'] = getUserEmail($current_user->id);
		}
		$fields[] = $f;
	}
}
$smarty->assign('NLFIELDS', $fields);

$target_mods = array('Targets', 'Accounts', 'Contacts', 'Leads');
$target_modinfo = array();
foreach ($target_mods as $tmod) {
	if (!vtlib_isModuleActive($tmod)) continue; //crmv@48990
	if (isPermitted($tmod, 'index') != 'yes') continue;
	$cv = new CustomView($tmod);
	$filterlist = $cv->getCustomViewCombo();

	$Slv = SimpleListView::getInstance($tmod);
	$Slv->entriesPerPage = 10;
	$Slv->showCreate = false;
	$Slv->showSuggested = false;
	$Slv->showCheckboxes = false;

	if ($tmod != 'Targets') {
		$Slv->extraButtonsHTML = '<input type="button" class="crmbutton" value="'.getTranslatedString('LBL_ADD_ALL').'" onclick="nlwFilterSelect(\''.$Slv->listid.'\', \''.$tmod.'\', jQuery(\'#SLVContainer_'.$Slv->listid.'\').find(\'#viewname\').val())" >';
	}

	$Slv->selectFunction = 'nlwRecordSelect';

	$list = $Slv->render();

	$modinfo = array(
		'filters' => $filterlist,
		'list' => $list,
		'listid' => $Slv->listid
	);
	$target_modinfo[$tmod] = $modinfo;
}

$smarty->assign('TARGET_MODS', $target_modinfo);

$smarty->assign('HEADER_Z_INDEX', 100);

$smarty->display('modules/Campaigns/NewsletterWizard.tpl');

?>
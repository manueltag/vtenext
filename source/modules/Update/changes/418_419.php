<?php
global $adb;
$result = $adb->query("SELECT presence FROM vtiger_tab WHERE name = 'Morphsuit'");
if ($result && $adb->num_rows($result)>0) {
	if ($adb->query_result($result,0,'presence') == 2) {
		vtlib_toggleModuleAccess('Morphsuit',true);
	}
}

$_SESSION['modules_to_update']['Assets'] = 'packages/vte/mandatory/Assets.zip';
$_SESSION['modules_to_update']['Ddt'] = 'packages/vte/mandatory/Ddt.zip';
$_SESSION['modules_to_update']['Fax'] = 'packages/vte/mandatory/Fax.zip';
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['Morphsuit'] = 'packages/vte/mandatory/Morphsuit.zip';
$_SESSION['modules_to_update']['PBXManager'] = 'packages/vte/mandatory/PBXManager.zip';
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
$_SESSION['modules_to_update']['Projects'] = 'packages/vte/mandatory/Projects.zip';
$_SESSION['modules_to_update']['ProjectsStandard'] = Array('location'=>'packages/vte/mandatory/ProjectsStandard.zip','modules'=>Array('ProjectPlan','ProjectMilestone','ProjectTask'));
$_SESSION['modules_to_update']['ServiceContracts'] = 'packages/vte/mandatory/ServiceContracts.zip';
$_SESSION['modules_to_update']['Services'] = 'packages/vte/mandatory/Services.zip';
$_SESSION['modules_to_update']['Sms'] = 'packages/vte/mandatory/Sms.zip';
$_SESSION['modules_to_update']['Timecards'] = 'packages/vte/mandatory/Timecards.zip';
$_SESSION['modules_to_update']['Visitreport'] = 'packages/vte/mandatory/Visitreport.zip';

if (!isModuleInstalled('SDK')) {
	require_once('modules/SDK/InstallTables.php');
	$sdkModule = new Vtiger_Module();
	$sdkModule->name = 'SDK';
	$sdkModule->isentitytype = false;
	$sdkModule->save();
 	//crmv@33465 fix sdk
 	$adb->pquery('UPDATE vtiger_tab SET customized=0 WHERE name=?', array($sdkModule->name));
	Vtiger_Link::addLink($sdkModule->id,'HEADERSCRIPT','SDKScript','modules/SDK/SDK.js');
	Vtiger_Link::addLink($sdkModule->id,'HEADERSCRIPT','SDKScript','modules/SDK/LoadJsLang.js');
	SDK::setUtil('modules/SDK/LangUtils.php');
	SDK::setUtil('modules/SDK/src/Utils.php');
	include_once('modules/SDK/LangUtils.php');
	$langinfo = vtlib_getToggleLanguageInfo();
	$languages = array_keys($langinfo);
 	if (empty($languages)) {
		$languages = array('en_us','it_it');
	}
	foreach ($languages as $language){
		SDK::importPhpLanguage($language);
		//l'import della lingua js viene fatto in Header.tpl
	}	
	//Vtiger_Module::fireEvent($sdkModule->name, Vtiger_Module::EVENT_MODULE_POSTINSTALL);
	//crmv@33465e
}
?>
<?php
include_once('vtlib/Vtiger/Module.php');
$modulename = 'Visitreport';
$moduleInstance=Vtiger_Module::getInstance($modulename);
$productmodule=Vtiger_Module::getInstance('Products');
$productmodule->setRelatedList($moduleInstance, $modulename, Array('ADD','SELECT'),'get_related_list');
$_SESSION['modules_to_update']['Visitreport'] = 'packages/vte/mandatory/Visitreport.zip';

global $adb;
$res = $adb->query("SELECT tabid FROM vtiger_tab WHERE name = 'SLA'");
if ($res && $adb->num_rows($res)>0) {
	$adb->query("UPDATE vtiger_field SET typeofdata = 'I~O' WHERE tabid = 13 AND fieldname IN ('time_elapsed','time_remaining')");
}

$result = $adb->pquery("select tabid from vtiger_tab where name=?", array('ProjectPlan'));
$tabid =  $adb->query_result($result,0,"tabid");
$adb->pquery("UPDATE vtiger_links SET linkurl=? WHERE tabid=? and linklabel=?",array('index.php?module=ProjectTask&action=EditView&projectid=$RECORD$&return_module=ProjectPlan&return_action=DetailView&return_id=$RECORD$',$tabid,'Add Project Task'));
$adb->pquery("UPDATE vtiger_links SET linkurl=? WHERE tabid=? and linklabel=?",array('index.php?module=Documents&action=EditView&return_module=ProjectPlan&return_action=DetailView&return_id=$RECORD$&parent_id=$RECORD$',$tabid,'Add Note'));

$moduleInstance = Vtiger_Module::getInstance('ProjectPlan');
$docModuleInstance = Vtiger_Module::getInstance('Documents');
$docModuleInstance->setRelatedList($moduleInstance,'Project Plans',array('select'),'get_documents_dependents_list');
$moduleInstance = Vtiger_Module::getInstance('ProjectMilestone');
$docModuleInstance = Vtiger_Module::getInstance('Documents');
$docModuleInstance->setRelatedList($moduleInstance,'Project Milestones',array('select'),'get_documents_dependents_list');
$moduleInstance = Vtiger_Module::getInstance('ProjectTask');
$docModuleInstance = Vtiger_Module::getInstance('Documents');
$docModuleInstance->setRelatedList($moduleInstance,'Project Tasks',array('select'),'get_documents_dependents_list');
$_SESSION['modules_to_update']['ProjectsStandard'] = Array('location'=>'packages/vte/mandatory/ProjectsStandard.zip','modules'=>Array('ProjectPlan','ProjectMilestone','ProjectTask'));

$_SESSION['modules_to_update']['Transitions'] = 'packages/vte/mandatory/Transitions.zip';

$res = $adb->query("SELECT tabid FROM vtiger_tab WHERE name = 'Ddt'");
if ($res && $adb->num_rows($res)>0) {
	$_SESSION['modules_to_update']['Ddt'] = 'packages/vte/mandatory/Ddt.zip';
}
?>
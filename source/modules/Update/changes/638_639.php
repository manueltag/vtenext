<?php
$_SESSION['modules_to_update']['Morphsuit'] = 'packages/vte/mandatory/Morphsuit.zip';
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
$_SESSION['modules_to_update']['ProjectsStandard'] = Array('location'=>'packages/vte/mandatory/ProjectsStandard.zip','modules'=>Array('ProjectPlan', 'ProjectMilestone', 'ProjectTask'));

global $adb, $table_prefix;

$CalendarInstance = Vtiger_Module::getInstance('Calendar');
$ProjectPlanInstance = Vtiger_Module::getInstance('ProjectPlan');
$ProjectMilestoneInstance = Vtiger_Module::getInstance('ProjectMilestone');
$ProjectTaskInstance = Vtiger_Module::getInstance('ProjectTask');

$result = $adb->pquery("SELECT * FROM {$table_prefix}_relatedlists WHERE tabid = ? AND related_tabid = ?",array($ProjectPlanInstance->id,9));
if (!$result || $adb->num_rows($result) == 0) {
	$ProjectPlanInstance->setRelatedList($CalendarInstance, 'Activities', Array('ADD,SELECT'),'get_activities');
	$ProjectPlanInstance->setRelatedList($CalendarInstance, 'Activity History', Array('ADD'),'get_history');
}
$result = $adb->pquery("SELECT * FROM {$table_prefix}_relatedlists WHERE tabid = ? AND related_tabid = ?",array($ProjectMilestoneInstance->id,9));
if (!$result || $adb->num_rows($result) == 0) {
	$ProjectMilestoneInstance->setRelatedList($CalendarInstance, 'Activities', Array('ADD,SELECT'),'get_activities');
	$ProjectMilestoneInstance->setRelatedList($CalendarInstance, 'Activity History', Array('ADD'),'get_history');
}
$result = $adb->pquery("SELECT * FROM {$table_prefix}_relatedlists WHERE tabid = ? AND related_tabid = ?",array($ProjectTaskInstance->id,9));
if (!$result || $adb->num_rows($result) == 0) {
	$ProjectTaskInstance->setRelatedList($CalendarInstance, 'Activities', Array('ADD,SELECT'),'get_activities');
	$ProjectTaskInstance->setRelatedList($CalendarInstance, 'Activity History', Array('ADD'),'get_history');
}

$result = $adb->pquery("SELECT * FROM {$table_prefix}_fieldmodulerel WHERE module = ? AND relmodule = ?",array('ProjectTask','SalesOrder'));
if (!$result || $adb->num_rows($result) == 0) {
	$fields = array();
	$fields[] = array('module'=>'ProjectTask','block'=>'LBL_PROJECT_TASK_INFORMATION','name'=>'salesorderid','label'=>'SalesOrder','uitype'=>'10','columntype'=>'I(19)','typeofdata'=>'V~O','quickcreate'=>'1','masseditable'=>'0','relatedModules'=>array('SalesOrder'),'relatedModulesAction'=>array('SalesOrder'=>array('ADD','SELECT')));
	include('modules/SDK/examples/fieldCreate.php');
}
SDK::setLanguageEntries('ProjectTask', 'SalesOrder', array('it_it'=>'Ordine di Vendita','en_us'=>'Sales Order','pt_br'=>'Pedido de Vendas'));

global $enterprise_current_version,$enterprise_mode;
SDK::setLanguageEntries('APP_STRINGS', 'LBL_BROWSER_TITLE', array(
'it_it'=>"$enterprise_mode $enterprise_current_version",
'en_us'=>"$enterprise_mode $enterprise_current_version",
'pt_br'=>"$enterprise_mode $enterprise_current_version",
'de_de'=>"$enterprise_mode $enterprise_current_version"
));
?>
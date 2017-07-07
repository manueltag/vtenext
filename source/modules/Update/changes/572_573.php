<?php
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';

global $adb;

$modulename = 'ChangeLog';
$moduleInstance = Vtiger_Module::getInstance($modulename);
if ($moduleInstance) {
	$moduleInstance->hide(array('hide_module_manager'=>1,'hide_profile'=>1));
	$adb->pquery('UPDATE vtiger_tab SET customized=0 WHERE name=?', array($modulename));
}

$modulename = 'ModComments';
$moduleInstance = Vtiger_Module::getInstance($modulename);
if ($moduleInstance) {
	$moduleInstance->hide(array('hide_module_manager'=>1,'hide_profile'=>1));
	$adb->pquery('UPDATE vtiger_tab SET customized=0 WHERE name=?', array($modulename));
}

$columns = array_keys($adb->datadict->MetaColumns('vte_hide_tab'));
if (!in_array(strtoupper('hide_report'),$columns)) {
	$sqlarray = $adb->datadict->AddColumnSQL('vte_hide_tab','hide_report I(1) DEFAULT 0');
	$adb->datadict->ExecuteSQLArray($sqlarray);
}
$adb->query('update vte_hide_tab set hide_report = 1');
?>
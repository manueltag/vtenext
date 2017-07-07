<?php
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['ServiceContracts'] = 'packages/vte/mandatory/ServiceContracts.zip';
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';

global $table_prefix;
require_once("modules/Update/Update.php");
Update::change_field($table_prefix.'_loginhistory','logout_time','T','');

$ServiceContractsInstance = Vtiger_Module::getInstance('ServiceContracts');
$HelpDeskInstance = Vtiger_Module::getInstance('HelpDesk');
$result = $adb->pquery("SELECT * FROM {$table_prefix}_relatedlists WHERE tabid = ? AND related_tabid = ?",array($HelpDeskInstance->id,$ServiceContractsInstance->id));
if (!$result || $adb->num_rows($result) == 0) {
	$HelpDeskInstance->setRelatedList($ServiceContractsInstance, 'Service Contracts', '', 'get_related_list');
}

$AssetsInstance = Vtiger_Module::getInstance('Assets');
$HelpDeskInstance = Vtiger_Module::getInstance('HelpDesk');
$result = $adb->pquery("SELECT * FROM {$table_prefix}_relatedlists WHERE tabid = ? AND related_tabid = ?",array($HelpDeskInstance->id,$AssetsInstance->id));
if (!$result || $adb->num_rows($result) == 0) {
	$HelpDeskInstance->setRelatedList($AssetsInstance, 'Assets', array('ADD','SELECT'), 'get_related_list');
}
?>
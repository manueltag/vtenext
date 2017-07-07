<?php
global $adb, $table_prefix;

$calendarInstance = Vtiger_Module::getInstance('Calendar');
$eventsInstance = Vtiger_Module::getInstance('Events');
$vendorsInstance = Vtiger_Module::getInstance('Vendors');

$fieldInstance = Vtiger_Field::getInstance('parent_id',$calendarInstance);
$fieldInstance->setRelatedModules(array('Vendors'));
$fieldInstance = Vtiger_Field::getInstance('parent_id',$eventsInstance);
$fieldInstance->setRelatedModules(array('Vendors'));

$result = $adb->pquery("SELECT * FROM {$table_prefix}_relatedlists WHERE tabid = ? AND related_tabid = ?",array($vendorsInstance->id,$calendarInstance->id));
if (!$result || $adb->num_rows($result) == 0) {
	$vendorsInstance->setRelatedList($calendarInstance, 'Activities', Array('SELECT','ADD'), 'get_activities');
}
<?php
global $adb, $table_prefix;

$result = $adb->pquery("SELECT picklist_valueid FROM {$table_prefix}_ticketstatus WHERE ticketstatus = ?", array('Maintain'));
if ($result && $adb->num_rows($result) > 0) {
	$picklist_valueid = $adb->query_result($result,0,'picklist_valueid');
	$adb->pquery("delete from {$table_prefix}_ticketstatus where picklist_valueid = ?", array($picklist_valueid));
	$adb->pquery("delete from {$table_prefix}_role2picklist where picklistvalueid = ?", array($picklist_valueid));

	$fieldInstance = Vtiger_Field::getInstance('ticketstatus',Vtiger_Module::getInstance('HelpDesk'));
	$fieldInstance->setPicklistValues(array('Maintain'));
}
?>
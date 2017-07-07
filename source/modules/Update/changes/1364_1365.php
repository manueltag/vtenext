<?php 
global $adb, $table_prefix;
$ChangeLogModuleInstance = Vtiger_Module::getInstance('ChangeLog');
$adb->pquery("DELETE FROM {$table_prefix}_relatedlists WHERE related_tabid = ?", array($ChangeLogModuleInstance->id));
?>
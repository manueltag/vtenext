<?php
global $adb, $table_prefix;
if (Vtiger_Utils::CheckTable($table_prefix.'_notification_summary')) {
	$sql = $adb->datadict->RenameColumnSQL($table_prefix.'_notification_summary','notification_summaryid','notify_summaryid','notification_summaryid I(19)');
	if ($sql){
		$adb->datadict->ExecuteSQLArray($sql);
	}
	$sql = $adb->datadict->RenameColumnSQL($table_prefix.'_notification_summary','notification_summary','notify_summary','notification_summary C(200)');
	if ($sql){
		$adb->datadict->ExecuteSQLArray($sql);
	}
	$sql = $adb->datadict->RenameTableSQL($table_prefix.'_notification_summary',$table_prefix.'_notify_summary');
	if ($sql){
		$adb->datadict->ExecuteSQLArray($sql);
	}
	$sql = $adb->datadict->RenameColumnSQL($table_prefix.'_users','notification_summary','notify_summary','notification_summary C(255)');
	if ($sql){
		$adb->datadict->ExecuteSQLArray($sql);
	}
}
$sql = "update {$table_prefix}_field set columnname = ?, fieldname = ? where tabid = ? and fieldname = ?";
$params = Array('notify_summary','notify_summary',29,'notification_summary');
$adb->pquery($sql,$params);
$sql = "update {$table_prefix}_picklist set name = ? where name = ?";
$params = Array('notify_summary','notification_summary');
$adb->pquery($sql,$params);
?>
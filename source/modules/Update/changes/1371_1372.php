<?php
global $adb, $table_prefix;

$tablename = $table_prefix.'_running_processes_timer';
$columnname = 'occurrence';
$cols = $adb->getColumnNames($tablename);
if (!in_array($columnname, $cols)) {
	$adb->alterTable($tablename, $columnname.' I(19) DEFAULT 0', 'Add_Column');
	$adb->query("update $tablename set $columnname = 0");
}
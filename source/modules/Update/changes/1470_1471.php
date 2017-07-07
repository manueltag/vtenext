<?php
global $adb, $table_prefix;
if (!function_exists('addColumnToTable')) {
	function addColumnToTable($tablename, $columnname, $type, $extra = '') {
		global $adb;
	
		// check if already present
		$cols = $adb->getColumnNames($tablename);
		if (in_array($columnname, $cols)) {
			return;
		}
	
		$col = $columnname.' '.$type.' '.$extra;
		$adb->alterTable($tablename, $col, 'Add_Column');
	}
}
$table = $table_prefix.'_process_gateway_conn';
if (Vtiger_Utils::CheckTable($table)) {
	addColumnToTable($table, 'conditionssons', 'X');
}
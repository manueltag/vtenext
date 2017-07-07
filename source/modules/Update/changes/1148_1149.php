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
addColumnToTable($table_prefix.'_mailscanner_ids', 'status', 'I(1) DEFAULT 0');
addColumnToTable($table_prefix.'_mailscanner_ids', 'attempts', 'I(11) DEFAULT 0');

$adb->query("update {$table_prefix}_mailscanner_ids set status = 1");
?>
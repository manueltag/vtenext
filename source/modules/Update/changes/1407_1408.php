<?php

// some functions
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

// add missing column
if ($adb->table_exist($table_prefix.'_myfiles')) {
	addColumnToTable($table_prefix.'_myfiles', 'backend_name', 'C(63)');
}
<?php 

// crmv@106521

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

// add column
if ($adb->table_exist($table_prefix.'_touch_requests')) {
	addColumnToTable($table_prefix.'_touch_requests', 'return_data', 'XL');
}
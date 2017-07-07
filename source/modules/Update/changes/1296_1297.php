<?php

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

// crmv@98778

$table = 'vte_modnot_follow_cv';
if (Vtiger_Utils::CheckTable($table)) {
	addColumnToTable($table, 'last_processed', 'T');
}
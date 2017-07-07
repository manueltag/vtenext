<?php
/* crmv@101057 */
global $adb, $table_prefix;
if (!function_exists('addColumnToTable')) {
	function addColumnToTable($tablename, $columnname, $type, $extra = '') {
		global $adb;

		// check if already present
		$cols = $adb->getColumnNames($tablename);
		if (in_array($columnname, $cols)) {
			return false;
		}

		$col = $columnname.' '.$type.' '.$extra;
		$adb->alterTable($tablename, $col, 'Add_Column');
		
		return true;
	}
}
// add column xml_version to _processmaker and init to 1
$done = addColumnToTable($table_prefix.'_processmaker', 'xml_version', 'I(19)', 'DEFAULT 1');
if ($done === true) {
	$adb->query("update {$table_prefix}_processmaker set xml_version = 1");
}
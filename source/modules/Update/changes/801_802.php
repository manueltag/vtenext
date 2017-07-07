<?php
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';

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
	
if (isModuleInstalled('Messages')) {
	global $adb, $table_prefix;
	
	$adb->pquery("update {$table_prefix}_field set readonly = ? where tablename = ? and fieldname = ?",array(100,$table_prefix.'_messages','mvisibility'));
	
	$tablename = $table_prefix.'_messages_cron';
	if (Vtiger_Utils::CheckTable($tablename)) {
		// if already there, just add the missing column
		addColumnToTable($tablename, 'ctime', 'T', 'DEFAULT 0000-00-00 00:00:00');
		$adb->pquery("update $tablename set ctime = ?",array(date('Y-m-d H:i:s')));
	}
}
?>
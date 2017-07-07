<?php
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['PBXManager'] = 'packages/vte/mandatory/PBXManager.zip';

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
	$tablename = $table_prefix.'_messages_pop3';
	if (Vtiger_Utils::CheckTable($tablename)) {
		// if already there, just add the missing column
		addColumnToTable($tablename, 'accountid', 'I(10)');
	}
}

$adb->pquery("update sdk_uitype set old_style = ? where uitype in (?,?,?,?,?)",array(0,201,202,203,205,208));
SDK::clearSessionValues();
?>
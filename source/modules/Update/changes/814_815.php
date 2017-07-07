<?php
$_SESSION['modules_to_update']['Fax'] = 'packages/vte/mandatory/Fax.zip';
$_SESSION['modules_to_update']['Sms'] = 'packages/vte/mandatory/Sms.zip';
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';

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
if (isModuleInstalled('Messages')) {
	$tablename = $table_prefix.'_messages_account';
	if (Vtiger_Utils::CheckTable($tablename)) {
		// if already there, just add the missing column
		addColumnToTable($tablename, 'signature', 'X');
	}
	$result = $adb->query("select id, signature from {$table_prefix}_users");
	if ($result && $adb->num_rows($result) > 0) {
		while($row=$adb->fetchByAssoc($result)) {
			$adb->pquery("update {$table_prefix}_messages_account set signature = ? where userid = ?",array(nl2br($row['signature']),$row['id']));
		}
	}
}
$fieldInstance = Vtiger_Field::getInstance('signature',Vtiger_Module::getInstance('Users'));
$fieldInstance->delete();
$sql = $adb->datadict->DropColumnSQL($table_prefix.'_users','signature');
$adb->datadict->ExecuteSQLArray($sql);

SDK::setLanguageEntries('APP_STRINGS', 'LBL_POPUP_RECORDS_NOT_SELECTABLE', array(
	'it_it'=>'Non è possibile selezionare %s esistenti ma puoi crearne uno ora.',
	'en_us'=>'Is not possibile to select existents %s but yuo can create one now.'
));
?>
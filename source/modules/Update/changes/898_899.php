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
addColumnToTable("{$table_prefix}_messages_account", 'email', 'C(100)');

$adb->query("update {$table_prefix}_messages_account set email = username");

SDK::setLanguageEntry('Settings', 'en_us', 'LBL_COLORED_LISTVIEW_EDITOR', 'List View Colours');
SDK::setLanguageEntry('Targets', 'en_us', 'LBL_TARGETS_INFORMATION', 'Target Information');
SDK::setLanguageEntry('Targets', 'en_us', 'LBL_CUSTOM_INFORMATION', 'Custom Information');
SDK::setLanguageEntry('Targets', 'en_us', 'LBL_DESCRIPTION_INFORMATION', 'Description Information');
SDK::setLanguageEntry('Targets', 'en_us', 'Created Time', 'Created Time');

$result = $adb->query("SELECT * FROM {$table_prefix}_language WHERE prefix = 'nl_nl'");
if ($result && $adb->num_rows($result) > 0) {
	$languageInstance = new Vtiger_Language();
	$languageInstance->update($languageInstance, 'packages/vte/optional/Dutch.zip', true);
}
?>
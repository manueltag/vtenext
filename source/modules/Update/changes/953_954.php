<?php
SDK::setLanguageEntries('Leads', 'LBL_LEADS_FIELD_MAPPING', array(
	'it_it'=>'Mappatura campi',
	'en_us'=>'Leads Custom Field Mapping'
));
SDK::setLanguageEntries('Leads', 'LBL_FOLLOWING_ARE_POSSIBLE_REASONS', array(
	'it_it'=>'Queste possono essere le ragioni possibili',
	'en_us'=>'This can be be one of the possible reasons'
));
SDK::setLanguageEntries('Leads', 'LBL_MANDATORY_FIELDS_ARE_EMPTY', array(
	'it_it'=>'Alcuni dei campi obbligatori sono vuoti',
	'en_us'=>'Some of the mandatory field value are empty'
));
SDK::setLanguageEntries('Leads', 'LBL_LEADS_FIELD_MAPPING_INCOMPLETE', array(
	'it_it'=>'Tutti i campi obbligatori non sono mappati',
	'en_us'=>'The mandatory fields are not mapped'
));

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

// add column for main_account
addColumnToTable($table_prefix.'_messages_cron_uid', 'attempts', 'I(1)', 'DEFAULT 0');
addColumnToTable($table_prefix.'_messages_cron_uidi', 'attempts', 'I(1)', 'DEFAULT 0');
$adb->query("update {$table_prefix}_messages_cron_uid set attempts = 0");
$adb->query("update {$table_prefix}_messages_cron_uidi set attempts = 0");
?>
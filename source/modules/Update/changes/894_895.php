<?php
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';

SDK::setLanguageEntries('Settings', 'LBL_FIELDS_TO_BE_SHOWN', array('it_it'=>'Campi','en_us'=>'Fields'));
SDK::setLanguageEntries('Settings', 'LBL_TOOLS_TO_BE_SHOWN', array('it_it'=>'Strumenti','en_us'=>'Tools'));
SDK::setLanguageEntries('Settings', 'LBL_PROFILE_FIELD_VISIBLE', array('it_it'=>'Visibile','en_us'=>'Visible'));
SDK::setLanguageEntries('Settings', 'LBL_PROFILE_FIELD_MANDATORY', array('it_it'=>'Obbligatorio','en_us'=>'Mandatory'));

global $adb, $table_prefix;
$tablename = "{$table_prefix}_profile2field";
$columnname = 'mandatory';
$type = 'N(1)';
$extra = 'DEFAULT 1';
$cols = $adb->getColumnNames($tablename);
if (!in_array($columnname, $cols)) {
	$adb->alterTable($tablename, $columnname.' '.$type.' '.$extra, 'Add_Column');
	$adb->datadict->ExecuteSQLArray((Array)$adb->datadict->CreateIndexSQL('profile2field_mandatory_idx', "{$table_prefix}_profile2field", 'mandatory'));
	$adb->query("update {$table_prefix}_profile2field set mandatory = 1");
}

$adb->query("update {$table_prefix}_messages_cron_uid set status = 0 where status in (1,2)");
$adb->query("update {$table_prefix}_messages_cron_uidi set status = 0 where status in (1,2)");
?>
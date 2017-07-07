<?php

// crmv@98431

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


$table = $table_prefix.'_modulehome';
if (Vtiger_Utils::CheckTable($table)) {
	addColumnToTable($table, 'reportid', 'INT(19)');
}


$trans = array(
	// most of these are existing
	'APP_STRINGS' => array(
		'it_it' => array(
			'NewModuleHomeView' => 'Nuovo tab',
			'AddModuleHomeView' => 'Nuovo tab',
			'AddModuleHomeViewReport' => 'Nuovo tab con report',
			'LBL_REMOVE_MODHOME_VIEW' => 'Rimuovi tab',
		),
		'en_us' => array(
			'NewModuleHomeView' => 'New tab',
			'AddModuleHomeView' => 'New tab',
			'AddModuleHomeViewReport' => 'New tab with report',
			'LBL_REMOVE_MODHOME_VIEW' => 'Remove tab',
		),
	),
	'Calendar' => array(
		'it_it' => array(
			'LBL_TASK_STATUS'=>'Stato compito',
			'LBL_ACTIVITY_STATUS'=>'Stato evento',
		),
		'en_us' => array(
			'LBL_TASK_STATUS'=>'Task status',
			'LBL_ACTIVITY_STATUS'=>'Activity status',
		),
	),
	'ALERT_ARR' => array(
		'it_it' => array(
			'LBL_CHOOSE_A_REPORT' => 'Scegli un report',
			'LBL_BACK' => 'Indietro',
		),
		'en_us' => array(
			'LBL_CHOOSE_A_REPORT' => 'Choose a report',
			'LBL_BACK' => 'Back',
		),
	),
);
foreach ($trans as $module=>$modlang) {
	foreach ($modlang as $lang=>$translist) {
		foreach ($translist as $label=>$translabel) {
			SDK::setLanguageEntry($module, $lang, $label, $translabel);
		}
	}
}
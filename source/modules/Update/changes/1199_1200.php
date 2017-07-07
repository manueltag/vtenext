<?php

global $adb, $table_prefix;

/* crmv@91082 */

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

// add some columns
addColumnToTable($table_prefix.'_loginhistory', 'type', 'C(15)');
addColumnToTable($table_prefix.'_loginhistory', 'sessionid', 'C(63)');
addColumnToTable($table_prefix.'_loginhistory', 'deviceid', 'C(63)');
addColumnToTable($table_prefix.'_loginhistory', 'last_activity', 'T');

// remove unused ones
$adb->datadict->ExecuteSQLArray((Array)$adb->datadict->DropIndexSQL('loginhistory_idx1', "{$table_prefix}_loginhistory"));
$adb->datadict->ExecuteSQLArray((Array)$adb->datadict->DropIndexSQL('loginhistory_idx2', "{$table_prefix}_loginhistory"));
$adb->datadict->ExecuteSQLArray((Array)$adb->datadict->DropIndexSQL('loginhistory_idx3', "{$table_prefix}_loginhistory"));

// create some indexes
$adb->datadict->ExecuteSQLArray((Array)$adb->datadict->CreateIndexSQL('loginhistory_type_idx', "{$table_prefix}_loginhistory", 'type'));
$adb->datadict->ExecuteSQLArray((Array)$adb->datadict->CreateIndexSQL('loginhistory_usertype_idx', "{$table_prefix}_loginhistory", array('user_name', 'type', 'status')));



$trans = array(
	'Users' => array(
		'it_it' => array(
			'LBL_LOGOUT_REASON_CONCURRENT' => 'E\' stato effettuato un altro login con il tuo utente e il numero massimo di accessi contemporanei è stato raggiunto',
			'LBL_LOGOUT_REASON_EXPIRED' => 'La sessione è scaduta, effettua nuovamente il login.',
		),
		'en_us' => array(
			'LBL_LOGOUT_REASON_CONCURRENT' => 'Another login with your user has been detected and the maximum allowed number of concurrent sessions has been reached.',
			'LBL_LOGOUT_REASON_EXPIRED' => 'The session has expired, please log in again.',
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
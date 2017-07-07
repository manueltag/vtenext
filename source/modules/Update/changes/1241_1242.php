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

/* crmv@97862 */

// get report owners
$repowners = array();
$res = $adb->query("SELECT reportid, owner FROM {$table_prefix}_report");
if ($res && $adb->num_rows($res) > 0) {
	while ($row = $adb->FetchByAssoc($res, -1, false)) {
		$repowners[$row['reportid']] = intval($row['owner']);
	}
}

// add userid to report summary columns
for ($i=1; $i<=7; ++$i) {
	$table = "vte_rep_count_liv{$i}";
	if (Vtiger_Utils::CheckTable($table)) {
		if ($adb->isMysql()) {
			if (!in_array('userid', $adb->getColumnNames($table))) {
				$adb->query("ALTER TABLE $table ADD COLUMN userid INT(19) NOT NULL AFTER reportid");
			}
		} else {
			addColumnToTable($table, 'userid', 'INT(19) NOT NULL AFTER reportid');
		}
	}
	// update the users
	foreach ($repowners as $reportid => $owner) {
		$adb->pquery("UPDATE $table SET userid = ? WHERE reportid = ?", array($owner, $reportid));
	}
	// rebuild the index
	$idxs = array_keys($adb->database->MetaIndexes($table));
	$index = "vte_rep_count_liv{$i}_idx_1";
	if (in_array($index, $idxs)) {
		$adb->datadict->ExecuteSQLArray((Array)$adb->datadict->DropIndexSQL($index, $table));
		$sql = $adb->datadict->CreateIndexSQL($index, $table, array('reportid', 'userid'));
		if ($sql) $adb->datadict->ExecuteSQLArray($sql);
	}
}
$table = "vte_rep_count_levels";
if (Vtiger_Utils::CheckTable($table)) {
	if ($adb->isMysql()) {
		if (!in_array('userid', $adb->getColumnNames($table))) {
			$adb->query("ALTER TABLE $table ADD COLUMN userid INT(19) NOT NULL AFTER reportid");
		}
	} else {
		addColumnToTable($table, 'userid', 'INT(19) NOT NULL AFTER reportid');
	}
	// update the users
	foreach ($repowners as $reportid => $owner) {
		$adb->pquery("UPDATE $table SET userid = ? WHERE reportid = ?", array($owner, $reportid));
	}
	// rebuild the index
	$idxs = array_keys($adb->database->MetaIndexes($table));
	$index = "vte_rep_count_levels_idx_1";
	if (in_array($index, $idxs)) {
		$adb->datadict->ExecuteSQLArray((Array)$adb->datadict->DropIndexSQL($index, $table));
		$sql = $adb->datadict->CreateIndexSQL($index, $table, array('reportid', 'userid'));
		if ($sql) $adb->datadict->ExecuteSQLArray($sql);
	}
}

// hide the emails module
require_once('vtlib/Vtecrm/Module.php');
$modinst = Vtecrm_Module::getInstance('Emails');
if ($modinst) $modinst->hide(array('hide_report' => 1));


$trans = array(
	'Reports' => array(
		'it_it' => array(
			'LBL_ADD_CHART_TO_REPORT' => 'Aggiungi un grafico al report',
			'LBL_WANT_TO_CREATE_CHART' => 'Vuoi creare un grafico per questo report?',
			'LBL_CHART_NEEDS_SUMMARY' => 'Per creare un grafico devi creare un report di tipo riassuntivo',
			'LBL_TEMPORAL_FILTER' => 'Filtro temporale',
			'LBL_USE_THE_FIELDS_OF' => 'Usa i campi di ',
			'LBL_COMPARE_WITH_FIELD' => 'Confronta con un altro campo',
			'LBL_STDFILTER_EDITABLE' => 'Questo filtro sarà modificabile direttamente dal report'
		),
		'en_us' => array(
			'LBL_ADD_CHART_TO_REPORT' => 'Add a chart to the report',
			'LBL_WANT_TO_CREATE_CHART' => 'Do you want to create a chart for this report?',
			'LBL_CHART_NEEDS_SUMMARY' => 'To create a chart you should create a summary report',
			'LBL_TEMPORAL_FILTER' => 'Time filter',
			'LBL_USE_THE_FIELDS_OF' => 'Use the fields of ',
			'LBL_COMPARE_WITH_FIELD' => 'Compare with another field',
			'LBL_STDFILTER_EDITABLE' => 'This filter will be editable directly from the report page'
		),
	),
	'ALERT_ARR' => array(
		'it_it' => array(
			'LBL_SEARCH' => 'Cerca',
		),
		'en_us' => array(
			'LBL_SEARCH' => 'Search',
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


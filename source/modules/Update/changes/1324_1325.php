<?php
global $adb, $table_prefix;

$tablename = $table_prefix.'_mailscanner_rules';
$columnname = 'match_field';
$type = 'C(50)';

// check if already present
$cols = $adb->getColumnNames($tablename);
if (!in_array($columnname, $cols)) {
	$col = $columnname.' '.$type.' '.$extra;
	$adb->alterTable($tablename, $col, 'Add_Column');

	// set default value for previous configurations
	if ($adb->isMssql()) {
		$adb->pquery("UPDATE r
			SET r.match_field = ?
			FROM {$table_prefix}_mailscanner_rules r
			INNER JOIN {$table_prefix}_mailscanner_ruleactions ra ON r.ruleid = ra.ruleid
			INNER JOIN {$table_prefix}_mailscanner_actions a ON a.actionid = ra.actionid
			WHERE a.actiontype = ? AND r.subjectop = ?", array('crmid','UPDATE','Regex'));
	} else {
		$adb->pquery("UPDATE {$table_prefix}_mailscanner_rules r
			INNER JOIN {$table_prefix}_mailscanner_ruleactions ra ON r.ruleid = ra.ruleid
			INNER JOIN {$table_prefix}_mailscanner_actions a ON a.actionid = ra.actionid
			SET r.match_field = ?
			WHERE a.actiontype = ? AND r.subjectop = ?", array('crmid','UPDATE','Regex'));
	}
}

SDK::setLanguageEntries('Settings', 'LBL_MAILCONV_MATCH_FIELD', array(
	'it_it'=>'Chiave di confronto',
	'en_us'=>'Match field',
));
SDK::setLanguageEntries('Settings', 'LBL_MAILCONV_MATCH_FIELD_CRMID', array(
	'it_it'=>'Ticket Id',
	'en_us'=>'Ticket Id',
));
<?php

// fix the block for calendar module
$res = $adb->pquery("SELECT blockid FROM {$table_prefix}_blocks WHERE tabid = ? AND blocklabel = ?", array(getTabid('Events'), 'LBL_EVENT_INFORMATION'));
$blockno = $adb->query_result_no_html($res, 0, 'blockid');
if ($blockno > 0) {
	// get old block
	$res = $adb->pquery("SELECT block FROM {$table_prefix}_field WHERE tabid = ? AND fieldname = ?", array(getTabid('Events'), 'subject'));
	$oldblock = $adb->query_result_no_html($res, 0, 'block');
	if ($oldblock > 0 && $oldblock != $blockno) {
		$adb->pquery("UPDATE {$table_prefix}_field set block = ? WHERE tabid = ? AND block = ? AND fieldname NOT IN (?)", array($blockno, getTabid('Events'), $oldblock, 'description'));
	}
}

// add the entityname for Events module
$res = $adb->pquery("SELECT tabid FROM {$table_prefix}_entityname WHERE tabid = ?", array(getTabid('Events')));
if ($res && $adb->num_rows($res) == 0) {
	$params = array(getTabid('Events'), 'Events', $table_prefix.'_activity', 'subject', 'activityid', 'activityid');
	$adb->pquery("INSERT INTO {$table_prefix}_entityname (tabid, modulename, tablename, fieldname, entityidfield, entityidcolumn) VALUES (".generateQuestionMarks($params).")", $params);
}

// translations
$trans = array(
	'Reports' => array(
		'it_it' => array(
			'This Month Tasks'=>'Compiti di questo mese',
		),
		'en_us' => array(
			'This Month Tasks'=>'This Month Tasks',
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

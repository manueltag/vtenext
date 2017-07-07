<?php

global $adb, $table_prefix;

// crmv@101312

if (!function_exists('changeFieldsOrder')) {

function changeFieldsOrder($tabid, $blockid, array $order) {
	global $adb, $table_prefix;

	// increment the sequence by 1000,
	$adb->pquery("UPDATE {$table_prefix}_field SET sequence = sequence+1000 WHERE tabid = ? AND block = ?", array($tabid, $blockid));
	
	// then set the sequence for the desired fields
	$seq = 1;
	foreach ($order as $ofield) {
		$adb->pquery("UPDATE {$table_prefix}_field SET sequence = ? WHERE tabid = ? AND block = ? AND fieldname = ?", array($seq++, $tabid, $blockid, $ofield));
	}
	
	// now restore the other fields, preserving their previous ordering
	$otherfields = array();
	$res = $adb->pquery("SELECT fieldname FROM {$table_prefix}_field WHERE tabid = ? AND block = ? AND sequence > 1000 ORDER BY sequence ASC", array($tabid, $blockid));
	if ($res) {
		while ($row = $adb->FetchByAssoc($res, -1, false)) {
			$otherfields[] = $row['fieldname'];
		}
		foreach ($otherfields as $ofield) {
			$adb->pquery("UPDATE {$table_prefix}_field SET sequence = ? WHERE tabid = ? AND block = ? AND fieldname = ?", array($seq++, $tabid, $blockid, $ofield));
		}
	}
}

}

// hide some fields from detailview

// task
$hideDetail = array('ical_uuid', 'recurr_idx');
$adb->pquery("UPDATE {$table_prefix}_field SET displaytype = 3 WHERE tabid = 9 AND fieldname IN (".generateQuestionMarks($hideDetail).")", $hideDetail);

// events
$hideDetail = array('ical_uuid', 'recurr_idx', 'notime', 'duration_hours');
$adb->pquery("UPDATE {$table_prefix}_field SET displaytype = 3 WHERE tabid = 16 AND fieldname IN (".generateQuestionMarks($hideDetail).")", $hideDetail);


// move the description in the first block
$adb->pquery("UPDATE {$table_prefix}_field SET block = 19 WHERE tabid = ? AND block = ? AND fieldname = ?", array(9, 20, 'description'));
$adb->pquery("UPDATE {$table_prefix}_field SET block = 39 WHERE tabid = ? AND block = ? AND fieldname = ?", array(16, 41, 'description'));


// now reorder the fields to respect the old layout (more or less)
$orderEvent = array(
	'activitytype', 'visibility',
	'subject',
	'description',
	'date_start', 'due_date', 
	'location', 'eventstatus', 
	'assigned_user_id', 'taskpriority', 
	'createdtime', 'modifiedtime',
);
	
$res = $adb->pquery("SELECT blockid FROM {$table_prefix}_blocks WHERE tabid = ? AND blocklabel = ?", array(16, 'LBL_EVENT_INFORMATION'));
if ($res && $adb->num_rows($res) > 0) {
	$blockid = $adb->query_result_no_html($res, 0, 'blockid');
	changeFieldsOrder(16, $blockid, $orderEvent);
}

// now reorder the fields to respect the old layout (more or less)
$orderTask = array(
	'subject', 'taskpriority', 
	'description',
	'date_start', 'due_date', 
	'taskstatus', 'eventstatus', 'assigned_user_id',
	'createdtime', 'modifiedtime',
);
	
$res = $adb->pquery("SELECT blockid FROM {$table_prefix}_blocks WHERE tabid = ? AND blocklabel = ?", array(9, 'LBL_TASK_INFORMATION'));
if ($res && $adb->num_rows($res) > 0) {
	$blockid = $adb->query_result_no_html($res, 0, 'blockid');
	changeFieldsOrder(9, $blockid, $orderTask);
}

<?php 

// fix the main seq table
if (Vtiger_Utils::CheckTable("{$table_prefix}_report_seq")) {
	$res = $adb->query("SELECT MAX(reportid) as id FROM {$table_prefix}_report");
	$maxid = $adb->query_result_no_html($res, 0, 'id');
	if ($maxid > 0) {
		$res = $adb->pquery("UPDATE {$table_prefix}_report_seq SET id = ?", array($maxid));
	}
} else {
	// create the table
	$adb->getUniqueID("{$table_prefix}_report");
	// and now fix the sequence
	if (Vtiger_Utils::CheckTable("{$table_prefix}_report_seq")) {
		$res = $adb->query("SELECT MAX(reportid) as id FROM {$table_prefix}_report");
		$maxid = $adb->query_result_no_html($res, 0, 'id');
		if ($maxid > 0) {
			$res = $adb->pquery("UPDATE {$table_prefix}_report_seq SET id = ?", array($maxid));
		}
	}
}
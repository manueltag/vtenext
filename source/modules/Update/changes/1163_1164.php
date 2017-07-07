<?php

$check = false;
$indexes = $adb->database->MetaIndexes("com_{$table_prefix}_workflowtasks");
foreach($indexes as $name => $index) {
	if (count($index['columns']) == 1 && $index['columns'][0] == 'workflow_id') {
		$check = true;
		break;
	}
}
if (!$check) {
	$sql = $adb->datadict->CreateIndexSQL('com_vte_workflowtasks_wf_idx', "com_{$table_prefix}_workflowtasks", 'workflow_id');
	if ($sql) $adb->datadict->ExecuteSQLArray($sql);
}

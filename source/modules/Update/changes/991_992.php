<?php
global $adb, $table_prefix;

$idxs = $adb->database->MetaIndexes($table_prefix.'_messages');
$idx_found = false;
foreach($idxs as $idx) {
	if (count($idx['columns']) == 1 && $idx['columns'][0] == 'xuid') {
		$idx_found = true;
		break;
	}
}
if (!$idx_found) $adb->datadict->ExecuteSQLArray((Array)$adb->datadict->CreateIndexSQL("{$table_prefix}_messages_xuid_idx", "{$table_prefix}_messages", 'xuid'));
?>
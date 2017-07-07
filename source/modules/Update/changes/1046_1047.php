<?php
$_SESSION['modules_to_update']['MyNotes'] = 'packages/vte/mandatory/MyNotes.zip';

global $adb, $table_prefix;

$indexes = $adb->database->MetaIndexes($table_prefix.'_users');
$check = false;
foreach($indexes as $name => $index) {
	if (count($index['columns']) == 1 && $index['columns'][0] == 'status') {
		$check = true;
		break;
	}
}
if (!$check) {
	$sql = $adb->datadict->CreateIndexSQL('users_status_idx', $table_prefix.'_users', 'status');
	if ($sql) $adb->datadict->ExecuteSQLArray($sql);
}

$indexes = $adb->database->MetaIndexes($table_prefix.'_field');
$check = false;
foreach($indexes as $name => $index) {
	if (count($index['columns']) == 1 && $index['columns'][0] == 'presence') {
		$check = true;
		break;
	}
}
if (!$check) {
	$sql = $adb->datadict->CreateIndexSQL('field_presence_idx', $table_prefix.'_field', 'presence');
	if ($sql) $adb->datadict->ExecuteSQLArray($sql);
}

$indexes = $adb->database->MetaIndexes($table_prefix.'_ws_referencetype');
$check = false;
foreach($indexes as $name => $index) {
	if (count($index['columns']) == 1 && $index['columns'][0] == 'fieldtypeid') {
		$check = true;
		break;
	}
}
if (!$check) {
	$sql = $adb->datadict->CreateIndexSQL('ws_referencetype_fieldtypeid_idx', $table_prefix.'_ws_referencetype', 'fieldtypeid');
	if ($sql) $adb->datadict->ExecuteSQLArray($sql);
}

$indexes = $adb->database->MetaIndexes($table_prefix.'_modnotifications');
$check = false;
foreach($indexes as $name => $index) {
	if (count($index['columns']) == 1 && $index['columns'][0] == 'seen') {
		$check = true;
		break;
	}
}
if (!$check) {
	$sql = $adb->datadict->CreateIndexSQL('modnotifications_seen_idx', $table_prefix.'_modnotifications', 'seen');
	if ($sql) $adb->datadict->ExecuteSQLArray($sql);
}

if ($adb->isMysql()) {
	$columns = $adb->datadict->MetaColumns($table_prefix.'_fieldmodulerel');
	if ($columns['FIELDID']->max_length != 19) {
		$adb->query("ALTER TABLE {$table_prefix}_fieldmodulerel CHANGE fieldid fieldid INT(19) NOT NULL");
	}
} else {
	echo "CHANGE column fieldid in {$table_prefix}_fieldmodulerel to INTEGER 19 NOT NULL<br />\n";
}

$indexes = $adb->database->MetaIndexes($table_prefix.'_fieldmodulerel');
$check = false;
foreach($indexes as $name => $index) {
	if (count($index['columns']) == 1 && $index['columns'][0] == 'fieldid') {
		$check = true;
		break;
	}
}
if (!$check) {
	$sql = $adb->datadict->CreateIndexSQL('fieldmodulerel_fieldid_idx', $table_prefix.'_fieldmodulerel', 'fieldid');
	if ($sql) $adb->datadict->ExecuteSQLArray($sql);
}
?>
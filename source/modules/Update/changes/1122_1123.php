<?php
$_SESSION['modules_to_update']['MyNotes'] = 'packages/vte/mandatory/MyNotes.zip';

global $adb, $table_prefix;
if ($adb->isMssql()) {
	$adb->query("UPDATE {$table_prefix}_crmentity SET deleted = 1 FROM {$table_prefix}_crmentity INNER JOIN {$table_prefix}_messages ON crmid = messagesid WHERE messageid LIKE '%_eml' AND subject = ''");
} else {
	$adb->query("UPDATE {$table_prefix}_crmentity INNER JOIN {$table_prefix}_messages ON crmid = messagesid SET deleted = 1 WHERE messageid LIKE '%_eml' AND subject = ''");
}
?>
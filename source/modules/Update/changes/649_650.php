<?php
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['Morphsuit'] = 'packages/vte/mandatory/Morphsuit.zip';
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';

global $adb, $table_prefix;
if (file_exists('hash_version.txt')) {
	$hash_version = file_get_contents('hash_version.txt');
	$adb->updateClob($table_prefix.'_version','hash_version','id=1',$hash_version);
	@unlink('hash_version.txt');
} else {
	$result = $adb->query('select hash_version from '.$table_prefix.'_version where id=1');
	if ($result) {
		$hash_version = $adb->query_result($result,0,'hash_version');
	}
}
$result = $adb->pquery('SELECT id FROM '.$table_prefix.'_users WHERE status = ?',array('Active'));
if ($result && $adb->num_rows($result) > 0) {
	$adb->pquery('delete from '.$table_prefix.'_reload_session where session_var = ?',array('vtiger_hash_version'));
	while($row=$adb->fetchByAssoc($result)) {
		$adb->pquery('insert into '.$table_prefix.'_reload_session (userid,session_var) values (?,?)',array($row['id'],'vtiger_hash_version'));
	}
}
$adb->pquery("delete from {$table_prefix}_settings_field where name = ?",array('LBL_BACKUP_SERVER_SETTINGS'));

$use_table_prefix = true;
include('modules/Update/changes/445_446.php');
unset($use_table_prefix);
?>
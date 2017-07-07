<?php
global $adb, $table_prefix;
$moduleInstance = Vtecrm_Module::getInstance('Processes');
$adb->pquery("UPDATE {$table_prefix}_field SET uitype = ? WHERE tabid = ? AND fieldname = ?", array(51,$moduleInstance->id,'process_actor'));
$result = $adb->pquery("SELECT MIN(fieldid) AS \"fieldid\" FROM {$table_prefix}_field WHERE tabid = ? AND fieldname = ? GROUP BY fieldname HAVING COUNT(*) > 1", array($moduleInstance->id,'process_actor'));
if ($result && $adb->num_rows($result) > 0) {
	$adb->pquery("DELETE FROM {$table_prefix}_field WHERE tabid = ? AND fieldid = ?", array($moduleInstance->id,$adb->query_result($result,0,'fieldid')));
}
$result = $adb->pquery("SELECT MIN(fieldid) AS \"fieldid\" FROM {$table_prefix}_field WHERE tabid = ? AND fieldname = ? GROUP BY fieldname HAVING COUNT(*) > 1", array($moduleInstance->id,'process_status'));
if ($result && $adb->num_rows($result) > 0) {
	$adb->pquery("DELETE FROM {$table_prefix}_field WHERE tabid = ? AND fieldid = ?", array($moduleInstance->id,$adb->query_result($result,0,'fieldid')));
}
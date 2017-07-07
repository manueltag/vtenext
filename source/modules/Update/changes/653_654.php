<?php
/* 31780 - patch varie per nuovo Mobile */
$_SESSION['modules_to_update']['ChangeLog'] = 'packages/vte/mandatory/ChangeLog.zip';
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';

global $adb, $table_prefix;

// rimozione webservice obsoleti
$res = $adb->query("select operationid from {$table_prefix}_ws_operation where name like 'touch.%' and handler_path like 'modules/Touch/vtws/%'");
if ($res && $adb->num_rows($res) > 0) {
	$wsids = array();
	while ($row = $adb->fetchByAssoc($res, -1, false)) {
		$wsids[] = $row['operationid'];
	}
	$wsids = array_filter($wsids);

	$adb->pquery("delete from {$table_prefix}_ws_operation where operationid in (".generateQuestionMarks($wsids).")", $wsids);
	$adb->pquery("delete from {$table_prefix}_ws_operation_parameters where operationid in (".generateQuestionMarks($wsids).")", $wsids);
}

// aggiungo un utile fieldtype (TODO: anche in installazione)
$res = $adb->query("select * from {$table_prefix}_ws_fieldtype where uitype = 71");
if ($res && $adb->num_rows($res) == 0) {
	$tid = $adb->getUniqueId("{$table_prefix}_ws_fieldtype");
	if ($tid > 0)
		$adb->pquery("insert into {$table_prefix}_ws_fieldtype (fieldtypeid, uitype, fieldtype) values (?,?,?)", array($tid, 71, 'currency'));
}

// pulizia del parametro "add" per le related "Activity history"
$res = $adb->query("update {$table_prefix}_relatedlists set actions = '' where related_tabid = 9 and name = 'get_history'");

?>
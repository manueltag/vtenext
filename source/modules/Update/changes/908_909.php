<?php
global $adb, $table_prefix;

$arr = array('ChangeLog','ModComments','ModNotifications','MyNotes','MyFiles');
$adb->pquery("update {$table_prefix}_def_org_share
			inner join {$table_prefix}_tab ON {$table_prefix}_tab.tabid = {$table_prefix}_def_org_share.tabid
			set editstatus = ?
			where {$table_prefix}_tab.name in (".generateQuestionMarks($arr).")",
array(2,$arr));

$adb->pquery('UPDATE '.$table_prefix.'_tab SET customized=0 WHERE name=?', array('MyFiles'));
?>
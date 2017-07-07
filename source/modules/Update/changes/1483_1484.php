<?php
global $adb, $table_prefix;
$result = $adb->pquery("select * from {$table_prefix}_ws_fieldtype where uitype = ?", array(54));
if ($adb->num_rows($result) == 0) {
	$fieldtypeid = $adb->getUniqueId($table_prefix.'_ws_fieldtype');
	$result = $adb->pquery("insert into {$table_prefix}_ws_fieldtype(fieldtypeid,uitype,fieldtype) values(?,?,?)",array($fieldtypeid,54,'reference'));
	$result = $adb->pquery("insert into {$table_prefix}_ws_referencetype(fieldtypeid,type) values(?,?)",array($fieldtypeid,'Groups'));
}
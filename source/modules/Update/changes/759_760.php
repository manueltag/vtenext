<?php
global $adb, $table_prefix;

$adb->pquery("update {$table_prefix}_field set uitype = ? where uitype = ?",array(1,2));
$adb->pquery("delete from {$table_prefix}_ws_fieldtype where uitype = ?",array(2));

$adb->pquery("update {$table_prefix}_field set uitype = ? where uitype = ?",array(15,111));
$adb->pquery("delete from {$table_prefix}_ws_fieldtype where uitype = ?",array(111));

$adb->pquery("update {$table_prefix}_field set uitype = ? where uitype = ?",array(19,20));
$adb->pquery("delete from {$table_prefix}_ws_fieldtype where uitype = ?",array(20));

$adb->pquery("update {$table_prefix}_field set uitype = ? where uitype in (?,?)",array(21,22,24));
$adb->pquery("delete from {$table_prefix}_ws_fieldtype where uitype in (?,?)",array(22,24));
?>
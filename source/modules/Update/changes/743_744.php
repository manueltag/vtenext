<?php
global $adb, $table_prefix;
$adb->pquery("UPDATE {$table_prefix}_field SET presence = 2 WHERE tablename = ? AND fieldname = ?",array($table_prefix.'_quotesshipads','ship_pobox'));
?>
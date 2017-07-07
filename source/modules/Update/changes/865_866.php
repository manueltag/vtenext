<?php
global $adb, $table_prefix;
$adb->pquery("update {$table_prefix}_cronjobs set timeout = ? where cronname = ?",array(600,'Messages'));
?>
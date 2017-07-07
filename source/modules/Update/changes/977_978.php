<?php
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';

global $adb, $table_prefix;

// disable report-based filters for mobile
$adb->query("update {$table_prefix}_customview set setmobile = 0 where reportid > 0");

?>
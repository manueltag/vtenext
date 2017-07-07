<?php
/* crmv@33097 - patch varie per nuovo Mobile */
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';
$_SESSION['modules_to_update']['Ddt'] = 'packages/vte/mandatory/Ddt.zip';

global $adb, $table_prefix;

// sistema data di ddt
$res = $adb->query("update {$table_prefix}_field set typeofdata = 'D~M' where tabid = 54 and fieldname = 'ddt_data' and typeofdata = 'V~M'");

?>
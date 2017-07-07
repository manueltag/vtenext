<?php
global $adb, $table_prefix;

$res = $adb->query("SELECT tabid FROM {$table_prefix}_tab WHERE name = 'RecycleBin'");
if ($res && $adb->num_rows($res)>0) $_SESSION['modules_to_update']['RecycleBin'] = 'packages/vte/optional/RecycleBin.zip';

?>
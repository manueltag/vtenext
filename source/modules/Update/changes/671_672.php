<?php
global $adb, $table_prefix;
$res = $adb->query("SELECT tabid FROM {$table_prefix}_tab WHERE name = 'Projects'");
if ($res && $adb->num_rows($res)>0) $_SESSION['modules_to_update']['Projects'] = 'packages/vte/optional/Projects.zip';

SDK::setUitype(207,'modules/SDK/src/207/207.php','modules/SDK/src/207/207.tpl','modules/SDK/src/207/207.js');
?>
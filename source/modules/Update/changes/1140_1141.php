<?php
global $adb, $table_prefix;
$result = $adb->pquery("SELECT * FROM {$table_prefix}_crmentityfolder WHERE tabid = ? AND foldername = ?", array(8,'Message attachments'));
if ($result && $adb->num_rows($result) > 0) {} else addEntityFolder('Documents', 'Message attachments', 'Contains message attachments', 1, '', 2);
?>
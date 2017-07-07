<?php
folderDetete('vtlib/ModuleDir/5.0.4');
folderDetete('vtlib/ModuleDir/5.1.0');
folderDetete('vtlib/ModuleDir/5.2.0');

global $adb, $table_prefix;
$adb->pquery("update tbl_s_menu set type = ? where type = ?",array('modules','areas'));
?>
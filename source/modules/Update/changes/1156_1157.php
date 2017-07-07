<?php
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';

global $adb, $table_prefix;
$adb->pquery("update {$table_prefix}_field set uitype = 1 where fieldname = ? and tablename = ?", array('folder', $table_prefix.'_messages'));
?>
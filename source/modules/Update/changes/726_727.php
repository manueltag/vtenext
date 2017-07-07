<?php
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';

// uitype htmltext
SDK::setUitype(210, 'modules/SDK/src/210/210.php', 'modules/SDK/src/210/210.tpl', 'modules/SDK/src/210/210.js', 'text');

// change documents and timecards type
$adb->pquery("update {$table_prefix}_field set uitype = 210 where tabid = ? and fieldname = ?", array(getTabid('Documents'), 'notecontent'));
$adb->pquery("update {$table_prefix}_field set uitype = 210 where tabid = ? and fieldname = ?", array(getTabid('Timecards'), 'description'));
?>
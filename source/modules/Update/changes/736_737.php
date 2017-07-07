<?php
$_SESSION['modules_to_update']['Morphsuit'] = 'packages/vte/mandatory/Morphsuit.zip';
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';

global $adb, $table_prefix;
$adb->pquery("UPDATE {$table_prefix}_ws_entity_name SET table_name = ? WHERE table_name = ?",array("{$table_prefix}_crmentityfolder","{$table_prefix}_attachmentsfolder"));
$adb->pquery("UPDATE {$table_prefix}_ws_entity_fieldtype SET table_name = ? WHERE table_name = ?",array("{$table_prefix}_crmentityfolder","{$table_prefix}_attachmentsfolder"));
$adb->pquery("UPDATE {$table_prefix}_ws_entity_tables SET table_name = ? WHERE table_name = ?",array("{$table_prefix}_crmentityfolder","{$table_prefix}_attachmentsfolder"));
?>
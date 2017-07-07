<?php
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';

// crmv@80653 - update uitype 207 -> new URL uitype
$adb->pquery("UPDATE sdk_uitype SET old_style = 0 WHERE uitype = ?", array(207));

// add the ws field data type
$res = $adb->pquery("SELECT fieldtypeid FROM {$table_prefix}_ws_fieldtype WHERE uitype = ?", array(207));
if ($res && $adb->num_rows($res) == 0) {
	$fid = $adb->getUniqueID("{$table_prefix}_ws_fieldtype");
	$adb->pquery("INSERT INTO {$table_prefix}_ws_fieldtype (fieldtypeid, uitype, fieldtype) VALUES (?,?,?)", array($fid, 207, 'url'));
}
// crmv@80653e

<?php
global $adb;
$columns = array_keys($adb->datadict->MetaColumns('sdk_popup_query'));
if (!in_array(strtoupper('hidden_rel_fields'),$columns)) {
	$sqlarray = $adb->datadict->AddColumnSQL('sdk_popup_query','hidden_rel_fields X');
	$adb->datadict->ExecuteSQLArray($sqlarray);
}
$_SESSION['modules_to_update']['Services'] = 'packages/vte/mandatory/Services.zip';
?>
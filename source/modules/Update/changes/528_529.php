<?php
global $adb;
$columns = array_keys($adb->datadict->MetaColumns('vtiger_users'));
if (!in_array(strtoupper('reload_session'),$columns)) {
	$sqlarray = $adb->datadict->AddColumnSQL('vtiger_users','reload_session I(1) DEFAULT 0');
	$adb->datadict->ExecuteSQLArray($sqlarray);
}
?>
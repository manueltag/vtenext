<?php
require_once('vtlib/Vtiger/Package.php');
$Vtiger_Utils_Log = true;
global $adb;

if ($adb->table_exist('crmv_inventorytoacc') == 0) {
	$flds = "	
		accountid I(19) NOTNULL PRIMARY,
		sorderid I(19) NOTNULL PRIMARY,
		id I(19) NOTNULL PRIMARY,
		type C(255) DEFAULT NULL
	";
	$sqlarray = $adb->datadict->CreateTableSQL('crmv_inventorytoacc', $flds);
	$adb->datadict->ExecuteSQLArray($sqlarray);
}
$_SESSION['modules_to_update']['Timecards'] = 'packages/vte/mandatory/Timecards.zip';
?>
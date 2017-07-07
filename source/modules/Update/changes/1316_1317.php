<?php

/* removed unused tables */

if (Vtiger_Utils::CheckTable("{$table_prefix}_failtype")) {
	$sqlarray = $adb->datadict->DropTableSQL("{$table_prefix}_failtype");
	$adb->datadict->ExecuteSQLArray($sqlarray);
}

if (Vtiger_Utils::CheckTable("{$table_prefix}_failtype_permisions")) {
	$sqlarray = $adb->datadict->DropTableSQL("{$table_prefix}_failtype_permisions");
	$adb->datadict->ExecuteSQLArray($sqlarray);
}

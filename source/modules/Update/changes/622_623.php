<?php
global $adb;
$sqlarray = $adb->datadict->AddColumnSQL('crmv_squirrelmailrel','folder C(255)');
$adb->datadict->ExecuteSQLArray($sqlarray);
?>
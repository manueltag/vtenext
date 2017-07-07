<?php
global $adb;
$sqlarray = $adb->datadict->AddColumnSQL('vtiger_emailtemplates','templatetype C(255)');
$adb->datadict->ExecuteSQLArray($sqlarray);
$adb->query("UPDATE vtiger_emailtemplates SET templatetype = 'Email'");
?>
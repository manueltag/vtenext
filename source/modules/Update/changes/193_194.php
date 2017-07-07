<?php
global $adb;
$adb->query("UPDATE vtiger_field SET typeofdata='N~O' WHERE fieldid='653'");
$adb->query("UPDATE vtiger_field SET typeofdata='N~O' WHERE fieldid='654'");
$adb->query("UPDATE vtiger_field SET typeofdata = 'C~O' WHERE uitype = 56");
$adb->query("UPDATE vtiger_relatedlists SET actions='select' WHERE relation_id='30'");
?>
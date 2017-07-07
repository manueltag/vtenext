<?php
global $adb;
$adb->query("UPDATE vtiger_field SET readonly='1' WHERE fieldname='webmail_structure' AND tabid = 29");
$adb->query("UPDATE vtiger_field SET presence='3' WHERE fieldname='webmail_structure' AND tabid = 29");
?>
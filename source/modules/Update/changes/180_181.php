<?php
global $adb;
$adb->query('UPDATE vtiger_profile2field SET visible = 0 WHERE profileid = 1');
?>
<?php
global $adb;
$adb->query('ALTER TABLE vtiger_users_last_import CHANGE id id INT(36) NOT NULL');
?>
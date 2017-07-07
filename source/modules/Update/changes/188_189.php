<?php
global $adb;
$adb->query('ALTER TABLE vtiger_users ADD COLUMN accesskey VARCHAR(36) NULL AFTER reminder_next_time');
?>
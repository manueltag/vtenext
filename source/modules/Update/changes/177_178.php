<?php
global $adb;
//$this->change_field('vtiger_users','id','I','11','NOTNULL',true);
$adb->query("ALTER TABLE vtiger_users CHANGE id id INT(11) NOT NULL");
$adb->query("ALTER TABLE vtiger_cvstdfilter  ADD COLUMN only_month_and_day INT(1) DEFAULT '0' NULL AFTER enddate");
$adb->query("ALTER TABLE vtiger_import_maps  CHANGE id id INT(19) NOT NULL");
?>
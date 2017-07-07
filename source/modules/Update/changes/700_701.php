<?php
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter','Targets'));

global $adb, $table_prefix;
$focusNewsletter = Vtiger_Module::getInstance('Newsletter');
$adb->pquery("update {$table_prefix}_field set typeofdata = ? where tabid = ? and fieldname = ?",array('V~M',$focusNewsletter->id,'campaignid'));
?>
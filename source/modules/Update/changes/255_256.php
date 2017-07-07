<?php
require_once("modules/Update/Update.php");
global $adb;

$adb->query("UPDATE vtiger_field SET typeofdata='N~O' WHERE fieldname='hours' and tabid = 13");
$adb->query("UPDATE vtiger_field SET typeofdata='N~O' WHERE fieldname='days' and tabid = 13");
Update::change_field('vtiger_troubletickets','hours','N','5.2');
Update::change_field('vtiger_troubletickets','days','N','5.2');
//crmv@18160
$_SESSION['modules_to_update']['Timecards'] = 'packages/vte/mandatory/Timecards.zip';
//crmv@18160 end
?>
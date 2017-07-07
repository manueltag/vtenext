<?php
require_once('vtlib/Vtiger/Package.php');
require_once('vtlib/Vtiger/Language.php');
global $adb;

$adb->pquery('UPDATE vtiger_tab SET customized=0 WHERE name=?', array('Visitreport'));

require_once("modules/Update/Update.php");
Update::change_field('vtiger_crmentity','createdtime','T','',"NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER description");
//crmv@18160
$_SESSION['modules_to_update']['M'] = 'packages/vte/mandatory/M.zip';
//crmv@18160 end
?>
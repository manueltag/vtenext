<?php
//crmv@18160
$_SESSION['modules_to_update']['Transitions'] = 'packages/vte/mandatory/Transitions.zip';
$_SESSION['modules_to_install']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_install']['FieldFormulas'] = 'packages/vte/mandatory/FieldFormulas.zip';
$_SESSION['modules_to_install']['Visitreport'] = 'packages/vte/mandatory/Visitreport.zip';
//crmv@18160 end
global $adb;
$sqlarray = $adb->datadict->DropTableSQL('vtiger_fieldformulas');
$adb->datadict->ExecuteSQLArray($sqlarray);
Update::change_field('vtiger_crmentity','createdtime','T','',"DEFAULT '0000-00-00 00:00:00'");
?>
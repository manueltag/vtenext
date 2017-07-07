<?php
//crmv@18160
$_SESSION['modules_to_update']['Conditionals'] = 'packages/vte/mandatory/Conditionals.zip';
$_SESSION['modules_to_update']['ProjectsStandard'] = Array('location'=>'packages/vte/mandatory/ProjectsStandard.zip','modules'=>Array('ProjectPlan'));
$_SESSION['modules_to_update']['Transitions'] = 'packages/vte/mandatory/Transitions.zip';

global $adb;
$res = $adb->query("SELECT tabid FROM vtiger_tab WHERE name = 'CustomerPortal'");
if ($res && $adb->num_rows($res)>0) $_SESSION['modules_to_update']['CustomerPortal'] = 'packages/vte/optional/CustomerPortal.zip';
//crmv@18160 end
?>
<?php
global $adb;
$_SESSION['modules_to_update']['Assets'] = 'packages/vte/mandatory/Assets.zip';
$res = $adb->query("SELECT tabid FROM vtiger_tab WHERE name = 'Ddt'");
if ($res && $adb->num_rows($res)>0) {
	$_SESSION['modules_to_update']['Ddt'] = 'packages/vte/mandatory/Ddt.zip';
} else {
	$_SESSION['modules_to_install']['Ddt'] = 'packages/vte/mandatory/Ddt.zip';
}
$res = $adb->query("SELECT tabid FROM vtiger_tab WHERE name = 'SLA'");
if ($res && $adb->num_rows($res)>0) {
	//do nothing
} else {
	$_SESSION['modules_to_install']['SLA'] = 'packages/vte/mandatory/SLA.zip';
}
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['ProjectsStandard'] = Array('location'=>'packages/vte/mandatory/ProjectsStandard.zip','modules'=>Array('ProjectPlan','ProjectMilestone','ProjectTask'));
$_SESSION['modules_to_update']['Projects'] = 'packages/vte/mandatory/Projects.zip';
$_SESSION['modules_to_update']['ServiceContracts'] = 'packages/vte/mandatory/ServiceContracts.zip';
$_SESSION['modules_to_update']['Services'] = 'packages/vte/mandatory/Services.zip';
$_SESSION['modules_to_update']['Timecards'] = 'packages/vte/mandatory/Timecards.zip';
$_SESSION['modules_to_update']['Visitreport'] = 'packages/vte/mandatory/Visitreport.zip';
?>
<?php
global $adb;
$res = $adb->query("SELECT tabid FROM vtiger_tab WHERE name = 'RecycleBin'");
if ($res && $adb->num_rows($res)>0)
	$_SESSION['modules_to_update']['RecycleBin'] = 'packages/vte/optional/RecycleBin.zip';
$_SESSION['modules_to_update']['Projects'] = 'packages/vte/mandatory/Projects.zip';

$adb->query("UPDATE vtiger_organizationdetails SET logoname = 'logo.jpg' WHERE organizationname = 'Crmvillage' AND logoname = 'logo.gif'");
?>
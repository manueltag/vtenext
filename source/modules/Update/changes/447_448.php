<?php
global $adb;
$res = $adb->query("SELECT tabid FROM vtiger_tab WHERE name = 'Newsletter'");
if ($res && $adb->num_rows($res)>0) {
	$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Targets','Newsletter'));
} else {
	$_SESSION['modules_to_install']['Newsletters'] = 'packages/vte/mandatory/Newsletters.zip';
}
?>
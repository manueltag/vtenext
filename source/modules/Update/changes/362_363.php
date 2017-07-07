<?php
global $adb;
$_SESSION['modules_to_update']['Projects'] = 'packages/vte/mandatory/Projects.zip';
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
$res = $adb->query("SELECT tabid FROM vtiger_tab WHERE name = 'RecycleBin'");
if ($res && $adb->num_rows($res)>0)
	$_SESSION['modules_to_update']['RecycleBin'] = 'packages/vte/optional/RecycleBin.zip';
?>
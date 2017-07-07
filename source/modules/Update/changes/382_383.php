<?php
$_SESSION['modules_to_update']['Projects'] = 'packages/vte/mandatory/Projects.zip';
@unlink('themes/softed/images/showUp.gif');
@unlink('themes/softed/images/showDown.gif');
global $adb;
$res = $adb->query("SELECT blockid FROM vtiger_blocks WHERE tabid = 13 AND blocklabel = 'LBL_SLA'");
if ($res && $adb->num_rows($res)>0) {
	$blockid = $adb->query_result($res,0,'blockid');
	$adb->query("UPDATE vtiger_field SET quickcreate = 1 WHERE tabid = 13 AND block = $blockid");
}
?>
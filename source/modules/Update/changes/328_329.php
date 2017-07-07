<?php
global $adb;
$res = $adb->query("SELECT tabid FROM vtiger_tab WHERE name = 'Ddt'");
if ($res && $adb->num_rows($res)>0) {
	$tabid = $adb->query_result($res,0,'tabid');
	$adb->query("UPDATE vtiger_relatedlists SET actions = '' WHERE tabid = $tabid AND related_tabid = 23");
}
?>
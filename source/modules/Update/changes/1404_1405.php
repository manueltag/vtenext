<?php
global $adb, $table_prefix;

$result = $adb->query("select * from ".$table_prefix."_organizationdetails");
if ($result && $adb->num_rows($result) > 0) {
	$organization_name = $adb->query_result($result,0,'organizationname');
	if ($organization_name == 'Acme') {
		$adb->pquery("update ".$table_prefix."_organizationdetails set logoname = ? where organizationname = ?", array('logo.png',$organization_name));
	}
}
<?php
global $adb;
$result = $adb->pquery("select areaid from tbl_s_areas where area = ?", array('HightlightArea'));
if ($result && $adb->num_rows($result) > 0) {
	$area0 = $adb->query_result($result,0,'areaid');
	$adb->pquery("delete from tbl_s_areas where areaid = ?", array($area0));
	$adb->pquery("delete from tbl_s_menu_areas where areaid = ?", array($area0));
}
<?php
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter','Targets'));
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';

global $adb, $table_prefix;

// chiave primaria (disable die on error)
$oldDieOnError = $adb->dieOnError;
$adb->dieOnError = false;
$adb->query("ALTER TABLE {$table_prefix}_newsletter ADD PRIMARY KEY (newsletterid)");
$adb->query("ALTER TABLE {$table_prefix}_targets ADD PRIMARY KEY (targetsid)");
$adb->query("ALTER TABLE {$table_prefix}_changelog ADD PRIMARY KEY (changelogid)");
$adb->query("ALTER TABLE {$table_prefix}_pbxmanager ADD PRIMARY KEY (pbxmanagerid)");
$adb->query("ALTER TABLE {$table_prefix}_timecards ADD PRIMARY KEY (timecardsid)");
$adb->query("ALTER TABLE {$table_prefix}_visitreport ADD PRIMARY KEY (visitreportid)");
$adb->dieOnError = $oldDieOnError;

$result = $adb->pquery("SELECT tbl_s_areas.areaid, MAX(tbl_s_menu_areas.sequence) AS seq FROM tbl_s_menu_areas
						INNER JOIN tbl_s_areas ON tbl_s_areas.areaid = tbl_s_menu_areas.areaid
						WHERE tbl_s_areas.area = ?
						GROUP BY tbl_s_areas.areaid",array('Inventory'));
if ($result && $adb->num_rows($result) > 0) {
	$areaid = $adb->query_result($result,0,'areaid');
	$sequence = $adb->query_result($result,0,'seq');
	$moduleInstance = Vtiger_Module::getInstance('ProductLines');
	$tabid = $moduleInstance->id;
	$adb->pquery("insert into tbl_s_menu_areas (areaid,tabid,userid,sequence) values (?,?,?,?)",array($areaid,$tabid,0,$sequence));
}
?>
<?php
global $adb, $table_prefix;

@unlink('modules/Reports/ViewReportPW.php');

$result = $adb->pquery("SELECT folderid FROM {$table_prefix}_crmentityfolder WHERE foldername = ? AND tabid = ?", array('LBL_REPORT_FOLDER_PROJECTS',getTabid('Reports')));
if ($result && $adb->num_rows($result) > 0) {
	$folderid = $adb->query_result($result,0,'folderid');
	$result = $adb->pquery("SELECT * FROM {$table_prefix}_report WHERE folderid = ?", array($folderid));
	if ($result && $adb->num_rows($result) == 0) {
		$adb->pquery("DELETE FROM {$table_prefix}_crmentityfolder WHERE folderid = ?", array($folderid));
	}
}

if (!extension_loaded('imagick')) {
	echo "Now VTE supports tiff images in the module Message. You have to install the php extension imagick to activate the new functionality.<br>\n";
}
?>
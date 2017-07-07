<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/
//crmv@31775
global $adb, $table_prefix;
$reportid = vtlib_purify($_REQUEST['report']);
if ($reportid != '' && $reportid != 0) {
	$result = $adb->pquery("SELECT {$table_prefix}_report.reportid FROM {$table_prefix}_report WHERE {$table_prefix}_report.reportid = ? AND {$table_prefix}_report.sharingtype = ?",array($reportid,'Public'));
	if (!$result || $adb->num_rows($result) == 0) {
		echo getTranslatedString('LBL_ERROR_PUBLIC_REPORT','Reports');
		exit;
	}
}
echo 'SUCCESS';
exit;
//crmv@31775e
?>
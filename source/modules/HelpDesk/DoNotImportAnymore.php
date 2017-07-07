<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/

/* crmv@56233 */

global $currentModule;
$mode = vtlib_purify($_REQUEST['mode']);
$error = '';

if ($mode == 'spam') {
	
	$record = vtlib_purify($_REQUEST['record']);
	
	$focus = CRMEntity::getInstance($currentModule);
	$focus->retrieve_entity_info($record, $currentModule);
	
	if (empty($focus->column_fields['mailscanner_action']) || isPermitted($currentModule, 'EditView', '') != 'yes') exit;
	
	require_once('modules/Settings/MailScanner/core/MailScannerSpam.php');
	$mailScannerSpam = new Vtecrm_MailScannerSpam();
	$mailScannerSpam->spam($record);
	
} elseif ($mode == 'mass_spam') {
	
	if (isPermitted($currentModule, 'EditView', '') != 'yes') exit;
	
	$idlist = getListViewCheck($currentModule);
	if (empty($idlist)) {
		$error = getTranslatedString('SELECT_ATLEAST_ONE');
	} else {
		global $adb, $atble_prefix;
		$result = $adb->pquery("SELECT crmid, mailscanner_action FROM {$table_prefix}_troubletickets
			INNER JOIN {$table_prefix}_crmentity ON {$table_prefix}_crmentity.crmid = {$table_prefix}_troubletickets.ticketid
			WHERE {$table_prefix}_crmentity.deleted = 0 AND crmid in (".generateQuestionMarks($idlist).")
			AND mailscanner_action IS NOT NULL AND mailscanner_action <> '' AND mailscanner_action <> 0",
			array($idlist));
		if ($adb->num_rows($result) == 0) {
			$error = getTranslatedString('SELECT_ATLEAST_ONE_MAILSCANNER','Settings');
		} else {
			require_once('modules/Settings/MailScanner/core/MailScannerSpam.php');
			foreach ($idlist as $id) {
				$mailScannerSpam = new Vtecrm_MailScannerSpam();
				$mailScannerSpam->spam($id);
			}
		}
	}
}

if (!empty($error)) {
	echo 'ERROR::'.$error;
}
exit;
?>
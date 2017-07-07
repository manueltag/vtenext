<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

/* crmv@95157 */

require_once('include/logging.php');
require_once('modules/Documents/Documents.php');
require_once('modules/Documents/storage/StorageBackendUtils.php');

if ($_REQUEST['act'] == 'updateDldCnt') {

	$crmid = intval($_REQUEST['file_id']);
	$SBU = StorageBackendUtils::getInstance();
	$SBU->incrementDownloadCount('Documents', $crmid);

} elseif ($_REQUEST['act'] == 'checkFileIntegrityDetailView') {	

	$crmid = intval($_REQUEST['noteid']);
	$SBU = StorageBackendUtils::getInstance();
	$integrity = $SBU->checkIntegrity('Documents', $crmid);
	
	switch ($integrity) {
		case 0:
			echo "file_available";
			break;
		case 1:
			echo "lost_integrity";
			break;
		case 2:
			echo "file_not_available";
			break;
		case 3:
			echo "internal_error";
			break;
		default:
			echo "unknown_error";
			break;
	}
	
}

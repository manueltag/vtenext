<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
********************************************************************************/
require_once('modules/Myfiles/Myfiles.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');

global $adb;
global $table_prefix;


$local_log =& LoggerManager::getLogger('index');
$folderid = $_REQUEST['record'];
$foldername = utf8RawUrlDecode($_REQUEST["foldername"]);
$folderdesc = utf8RawUrlDecode($_REQUEST["folderdesc"]);

if(isset($_REQUEST['savemode']) && $_REQUEST['savemode'] == 'Save')
{
	$obj = CRMEntity::getInstance('Myfiles');
	if($folderid == "")
	{
		// crmv@30967
		// check if it exists
		$folderinfo = $obj->getEntityFoldersByName($foldername, 'Myfiles');

		if (empty($folderinfo) && $foldername != '') {
			$sqlseq = "select max(sequence) as max from ".$table_prefix."_crmentityfolder where tabid = ?";
			$sequence = $adb->query_result($adb->pquery($sqlseq,array(getTabId('Myfiles'))),0,'max') + 1;
			$result = addEntityFolder('Myfiles', $foldername, $folderdesc, $current_user->id, '', $sequence);
			if (!$result) {
				echo "Failure";
			} else {
				header("Location: index.php?action=MyfilesAjax&file=ListView&mode=ajax&module=Myfiles");
			}
		} else {
			echo "DUPLICATE_FOLDERNAME";
		}
		// crmv@30967e

	} elseif($folderid != "") {

		// crmv@30967
		// check if creating a duplicate
		$folderinfo = $obj->getEntityFoldersByName($foldername, 'Myfiles');

		if (empty($folderinfo) || $folderinfo[0]['folderid'] == $folderid) {
			$result = editEntityFolder($folderid, $foldername);
			if(!$result) {
				echo "Failure";
			} else {
				echo 'Success';
			}
		} else {
			echo "DUPLICATE_FOLDERNAME";
		}
		// crmv@30967e

	}
}

?>

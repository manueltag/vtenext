<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*
********************************************************************************/
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');

/* crmv@97237 */

global $adb,$table_prefix;

$local_log =& LoggerManager::getLogger('index');
$rfid = intval($_REQUEST['record']);
if($rfid != "") {
	$records_in_folder = $adb->pquery("SELECT reportid from ".$table_prefix."_report WHERE folderid=?",array($rfid));
	if($adb->num_rows($records_in_folder)>0){
		echo getTranslatedString('LBL_FLDR_NOT_EMPTY',"Reports");
	} else {
		// crmv@30967
		$result = deleteEntityFolder($rfid);
		// crmv@30967e
		if ($result) {
			header("Location: index.php?action=ReportsAjax&mode=ajax&file=ListView&module=Reports");
		} else {
			include('modules/VteCore/header.php');	//crmv@30447
			$errormessage = "<font color='red'><B>Error Message<ul>
			<li><font color='red'>Error while deleting the folder</font>
			</ul></B></font> <br>" ;
			echo $errormessage;
		}
	}
}


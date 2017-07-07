<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@90004 */

global $adb, $table_prefix;

$folderid = intval($_REQUEST['folderid']);
$mode = $_REQUEST['mode'];

if ($mode == 'save') {
	$foldername = $_REQUEST['foldername'];
	$description = $_REQUEST['description'];
	$up_info_folder = "UPDATE {$table_prefix}_crmentityfolder SET foldername = ?, description = ? WHERE folderid = ? ";
	$array = array('foldername'=>$foldername,'description'=>$description);
 	$adb->pquery($up_info_folder,array($foldername,$description,$folderid));
} else {
	$sql_info_folder = "SELECT foldername, description FROM {$table_prefix}_crmentityfolder WHERE folderid = ? ";
	$ris_info_folder = $adb->pquery($sql_info_folder, array($folderid));
	
	while($row_info_folder = $adb->fetchByAssoc($ris_info_folder)){
		$foldername = $row_info_folder['foldername'];
		$description = $row_info_folder['description'];
	}
	$array = array('foldername'=>$foldername,'description'=>$description);
}

echo json_encode($array);
exit();
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
global $adb, $table_prefix;
$idlist = $_REQUEST['idlist'];
$id_array = array_filter(explode(';', $idlist));

//crmv@37290
if ($_REQUEST['mode'] == 'check') {
	$result = $adb->query("SELECT newsletterid FROM {$table_prefix}_newsletter WHERE templateemailid IN (".implode(',',$id_array).")");
	if ($result && $adb->num_rows($result) > 0) {
		echo 'NOT_SUCCESS';
	} else {
		echo 'SUCCESS';
	}
	exit;
}
//crmv@37290e

for($i=0;$i<=count($id_array);$i++) {
	$sql = "delete from {$table_prefix}_emailtemplates where templateid = ?";
	$adb->pquery($sql,array($id_array[$i]));
	//crmv@37290
	$sql = "update {$table_prefix}_newsletter set templateemailid = ? where templateemailid = ?";
	$adb->pquery($sql,array(0,$id_array[$i]));
	//crmv@37290e
}

header("Location:index.php?module=Settings&action=listemailtemplates");
?>
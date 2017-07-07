<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  crmvillage.biz
 * The Initial Developer of the Original Code is crmvillage.biz.
 * Portions created by vtiger are Copyright (C) crmvillage.biz.
 * All Rights Reserved.
 *
 ********************************************************************************/

function get_active_languages($associative=false){
	global $adb,$table_prefix;
	$query = "select prefix,label from {$table_prefix}_language where active = 1";
	$result = $adb->pquery($query, array());
	while($row = $adb->fetch_array($result)){
		$language[] = Array('prefix'=>$row['prefix'],'label'=>$row['label']);
	}
	return $language;

}
function get_active_languages_prefix(){
	global $adb,$table_prefix;
	$query = "select prefix from {$table_prefix}_language where active = 1";
	$result = $adb->pquery($query, array());
	while($row = $adb->fetch_array($result)){
		$language[] = $row['prefix'];
	}
	return $language;

}
?>

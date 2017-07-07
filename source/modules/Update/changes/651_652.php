<?php
$_SESSION['modules_to_update']['WSAPP'] = 'packages/vte/mandatory/WSAPP.zip';

global $adb, $table_prefix;

$createOperationQuery = "insert into {$table_prefix}_ws_operation(operationid,name,handler_path,handler_method,type,prelogin) values (?,?,?,?,?,?)";
$createOperationParamsQuery = "insert into {$table_prefix}_ws_operation_parameters(operationid,name,type,sequence) values (?,?,?,?)";

$result = $adb->pquery("SELECT * FROM {$table_prefix}_ws_operation WHERE name = ?",array('revise'));
if (!$result || $adb->num_rows($result) == 0) {
	$operationId = $adb->getUniqueID($table_prefix."_ws_operation");
	$adb->pquery($createOperationQuery,array($operationId,'revise','include/Webservices/Revise.php','vtws_revise','POST',0));
	$adb->pquery($createOperationParamsQuery,array($operationId,'element','Encoded',1));
}
$result = $adb->pquery("SELECT * FROM {$table_prefix}_ws_operation WHERE name = ?",array('get_labels'));
if (!$result || $adb->num_rows($result) == 0) {
	$operationId = $adb->getUniqueID($table_prefix."_ws_operation");
	$adb->pquery($createOperationQuery,array($operationId,'get_labels','include/Webservices/Language.php','vte_get_labels','POST',0));
	$adb->pquery($createOperationParamsQuery,array($operationId,'username','string',1));
	$adb->pquery($createOperationParamsQuery,array($operationId,'language','string',2));
	$adb->pquery($createOperationParamsQuery,array($operationId,'module','string',3));
}
$result = $adb->pquery("SELECT * FROM {$table_prefix}_ws_operation WHERE name = ?",array('get_langs'));
if (!$result || $adb->num_rows($result) == 0) {
	$operationId = $adb->getUniqueID($table_prefix."_ws_operation");
	$adb->pquery($createOperationQuery,array($operationId,'get_langs','include/Webservices/Language.php','vte_get_langs','POST',0));
	$adb->pquery($createOperationParamsQuery,array($operationId,'language','string',1));
}
?>
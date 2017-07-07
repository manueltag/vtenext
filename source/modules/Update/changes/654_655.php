<?php
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';

global $adb, $table_prefix;

$createOperationQuery = "insert into {$table_prefix}_ws_operation(operationid,name,handler_path,handler_method,type,prelogin) values (?,?,?,?,?,?)";
$createOperationParamsQuery = "insert into {$table_prefix}_ws_operation_parameters(operationid,name,type,sequence) values (?,?,?,?)";

$result = $adb->pquery("SELECT * FROM {$table_prefix}_ws_operation WHERE name = ?",array('login_pwd'));
if (!$result || $adb->num_rows($result) == 0) {
	$operationId = $adb->getUniqueID($table_prefix."_ws_operation");
	$adb->pquery($createOperationQuery,array($operationId,'login_pwd','include/Webservices/Login.php','vtws_login_pwd','POST',1));
	$adb->pquery($createOperationParamsQuery,array($operationId,'username','string',1));
	$adb->pquery($createOperationParamsQuery,array($operationId,'password','string',2));
}
?>
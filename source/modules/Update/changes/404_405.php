<?php
global $adb;
$result = $adb->query('SELECT fieldtypeid FROM vtiger_ws_fieldtype WHERE uitype = 357');
if ($result && $adb->num_rows($result) > 0) {
	$fieldtypeid = $adb->query_result($result,0,'fieldtypeid');
	$adb->pquery('insert into vtiger_ws_referencetype (fieldtypeid,type) values (?,?)',array($fieldtypeid,'Potentials'));
}
$operationId = $adb->getUniqueID("vtiger_ws_operation");
$adb->query("INSERT INTO vtiger_ws_operation (operationid,name,handler_path,handler_method,type,prelogin) VALUES ($operationId,'upload_files_ws','modules/Emails/ZTVFunctions.php','upload_files_ws','POST',0)");
$adb->query("INSERT INTO vtiger_ws_operation_parameters (operationid,name,type,sequence) VALUES ($operationId,'record','string',1)");
$adb->query("INSERT INTO vtiger_ws_operation_parameters (operationid,name,type,sequence) VALUES ($operationId,'module','string',2)");
$adb->query("INSERT INTO vtiger_ws_operation_parameters (operationid,name,type,sequence) VALUES ($operationId,'userid','string',3)");
$adb->query("INSERT INTO vtiger_ws_operation_parameters (operationid,name,type,sequence) VALUES ($operationId,'file','encoded',4)");
$adb->query("INSERT INTO vtiger_ws_operation_parameters (operationid,name,type,sequence) VALUES ($operationId,'email_id','string',5)");
$adb->query("INSERT INTO vtiger_ws_operation_parameters (operationid,name,type,sequence) VALUES ($operationId,'zimbra_url','string',6)");
$adb->query("INSERT INTO vtiger_ws_operation_parameters (operationid,name,type,sequence) VALUES ($operationId,'zimbra_user','string',7)");
?>
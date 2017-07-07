<?php
global $adb, $table_prefix;
require_once('include/Webservices/Utils.php');
$res = $adb->pquery("SELECT operationid FROM {$table_prefix}_ws_operation WHERE name = ?", array('describe_all'));
if ($res && $adb->num_rows($res) == 0) {
	$operationId = vtws_addWebserviceOperation('describe_all', 'include/Webservices/DescribeObject.php', 'vtws_describe_all', 'GET');
	vtws_addWebserviceOperationParam($operationId,'elementType','string',1);
}
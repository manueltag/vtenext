<?php

//crmv@5687 - add some new webservices
require_once('include/Webservices/Utils.php');

global $adb, $table_prefix;
$res = $adb->pquery("SELECT operationid FROM {$table_prefix}_ws_operation WHERE name = ?", array('ol_get_filters'));
if ($res && $adb->num_rows($res) == 0) {
	$operationId = vtws_addWebserviceOperation('ol_get_filters', 'include/Webservices/OutlookWS.php', 'ol_get_filters', 'POST');
	vtws_addWebserviceOperationParam($operationId,'module','string',1);
}

$res = $adb->pquery("SELECT operationid FROM {$table_prefix}_ws_operation WHERE name = ?", array('ol_clientsearch'));
if ($res && $adb->num_rows($res) == 0) {
	$operationId = vtws_addWebserviceOperation('ol_clientsearch', 'include/Webservices/OutlookWS.php', 'ol_clientsearch', 'POST');
	vtws_addWebserviceOperationParam($operationId,'modules','encoded',1);
	vtws_addWebserviceOperationParam($operationId,'search_text','string',2);
}

$res = $adb->pquery("SELECT operationid FROM {$table_prefix}_ws_operation WHERE name = ?", array('ol_is_sdk'));
if ($res && $adb->num_rows($res) == 0) {
	$operationId = vtws_addWebserviceOperation('ol_is_sdk', 'include/Webservices/OutlookWS.php', 'ol_is_sdk', 'POST');
	vtws_addWebserviceOperationParam($operationId,'client_version','string',1);
}

$res = $adb->pquery("SELECT operationid FROM {$table_prefix}_ws_operation WHERE name = ?", array('ol_doquery'));
if ($res && $adb->num_rows($res) == 0) {
	$operationId = vtws_addWebserviceOperation('ol_doquery', 'include/Webservices/OutlookWS.php', 'ol_doquery', 'POST');
	vtws_addWebserviceOperationParam($operationId,'module','string',1);
	vtws_addWebserviceOperationParam($operationId,'search_fields','encoded',2);
	vtws_addWebserviceOperationParam($operationId,'search_value','string',3);
}
//crmv@5687e

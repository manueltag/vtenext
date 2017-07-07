<?php
/* crmv@42707 */

global $adb, $table_prefix, $login, $userId;

if (!$login || !$userId) {
	echo 'Login Failed';
} else {

	$response = wsRequest($userId,'getmenulist', array());
	$response = $response['result'];

	echo Zend_Json::encode($response);
}
?>
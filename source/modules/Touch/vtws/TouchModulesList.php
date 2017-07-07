<?php
/* crmv@31780 - fix vari */
/* crmv@33097 */
global $login, $userId;

if (!$login || empty($userId)) {
	echo 'Login Failed';
} else {

	$recordReturn = touchModulesList();
	$record = Zend_Json::encode($recordReturn);
	echo $record;
}
?>
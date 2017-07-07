<?php
//crmv@35153
$installation_mode = false;
if (empty($_SESSION)) {
	session_start();
}
if ($_SESSION['morph_mode'] == 'installation') {
	$installation_mode = true;
	if (!isset($root_directory)) {
		require_once('../../config.inc.php');
	}
	chdir($root_directory);
	require_once('include/utils/utils.php');
}
//crmv@35153e
//crmv@28327
require_once('data/CRMEntity.php');
$focus = CRMEntity::getInstance('Users');
if ($_REQUEST['row'] != '') {
	$row = Zend_Json::decode($_REQUEST['row']);
} else {
	$focus->retrieve_entity_info($_REQUEST['record'],'Users');
	$row = $focus->column_fields;
}
if (!$focus->checkPasswordCriteria($_REQUEST['password'],$row)) {
	echo 'no';
}
exit;
//crmv@28327e
?>
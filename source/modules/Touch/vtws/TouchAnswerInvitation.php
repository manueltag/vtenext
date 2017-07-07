<?php
global $adb, $table_prefix;
global $login, $userId, $current_user, $currentModule;

$recordid = intval($_REQUEST['recordid']);
$value = intval($_REQUEST['participation']);

if (!$login || empty($userId)) {
	echo 'Login Failed';
} else {

	$success = false;

	$from = 'users';
	$_REQUEST['partecipation'] = $value;
	$_REQUEST['activityid'] = $recordid;
	$_REQUEST['userid'] = $current_user->id;

	try {
		require('modules/Calendar/SavePartecipation.php');
		$success = true;
	} catch (Exception $e) {
		$success = false;
	}

	echo Zend_Json::encode(array('success' => $success, 'invitation_answer' => $value));
}
?>
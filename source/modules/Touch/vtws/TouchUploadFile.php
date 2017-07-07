<?php
/* crmv@71388 */
global $login, $userId, $current_user, $currentModule;

if (!$login || empty($userId)) {
	echo 'Login Failed';
} else {

	$wsclass = new TouchUploadFile();
	$wsclass->execute($_REQUEST);
}


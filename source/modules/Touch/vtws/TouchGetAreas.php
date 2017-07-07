<?php
/* crmv@42537 */
global $adb, $table_prefix;
global $login, $userId, $current_user;


if (!$login || empty($userId)) {
	echo 'Login Failed';
} else {

	$wsclass = new TouchGetAreas();
	$wsclass->execute($_REQUEST);

}
?>
<?php
/* crmv@42537 - retrieves informations about the email config (accounts & folders) */
global $login, $userId, $current_user, $currentModule;
global $adb, $table_prefix;

if (!$login || empty($userId)) {
	echo 'Login Failed';
} elseif (in_array('Messages', $touchInst->excluded_modules)) {
	echo "Module not permitted";
} else {

	// very stupid!

	include('modules/SDK/src/Notifications/plugins/MessagesCheckChanges.php');

}

?>
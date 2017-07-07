<?php
/* crmv@71387 */
global $adb, $table_prefix;
global $login, $userId, $current_user;


if (!$login || empty($userId)) {
	echo 'Login Failed';
} elseif (in_array('Leads', $touchInst->excluded_modules)) {
	echo "Module Leads not permitted";
} else {

	$wsclass = new TouchConvertLead();
	$wsclass->execute($_REQUEST);

}

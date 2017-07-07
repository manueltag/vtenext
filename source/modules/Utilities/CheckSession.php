<?php
/*+*************************************************************************************
* The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: CRMVILLAGE.BIZ VTECRM
* The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
* Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
* All Rights Reserved.
***************************************************************************************/
/* crmv@91082 crmv@106590 */

global $current_user;

$SV = SessionValidator::getInstance();

$reason = '';
$username = $current_user ? $current_user->user_name : 0;
if ($SV->isStarted()) {
	$valid = $SV->isValid(null, $reason);
	$output = array('success' => true, 'valid' => $valid, 'updated' => false, 'user_name' => $username, 'reason' => $reason);
} else {
	$SV->refresh();
	$valid = $SV->isValid(null, $reason);
	$output = array('success' => true, 'valid' => $valid, 'updated' => true, 'user_name' => $username, 'reason' => $reason);
}

$SV->ajaxOutput($output);

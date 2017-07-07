<?php

if (isModuleInstalled('CustomerPortal')) {
	$_SESSION['modules_to_update']['CustomerPortal'] = 'packages/vte/optional/CustomerPortal.zip';
}


// enable the contacts module, otherwise the login won't work
if (isModuleInstalled('CustomerPortal') && vtlib_isModuleActive('CustomerPortal')) {
	$contactId = getTabid('Contacts');
	if ($contactId > 0) {
		$adb->pquery("UPDATE {$table_prefix}_customerportal_tabs SET visible = 1 WHERE tabid = ? AND visible = 0", array($contactId));
	}
}
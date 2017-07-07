<?php
$_SESSION['modules_to_update']['Fax'] = 'packages/vte/mandatory/Fax.zip';
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['Sms'] = 'packages/vte/mandatory/Sms.zip';
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';
$_SESSION['modules_to_update']['ServiceContracts'] = 'packages/vte/mandatory/ServiceContracts.zip';

global $adb, $table_prefix;

$tabid = getTabid('Timecards');
if ($tabid > 0) {
	$adb->pquery("UPDATE {$table_prefix}_field SET uitype = '52' WHERE tabid = ? AND fieldname = ?", array($tabid, 'newresp'));
}
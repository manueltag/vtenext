<?php
global $adb, $table_prefix;

$_SESSION['modules_to_update']['MyNotes'] = 'packages/vte/mandatory/MyNotes.zip';
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter','Targets'));
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';

SDK::setLanguageEntry('Users', 'en_us', 'LBL_NOT_SAFETY_PASSWORD', 'The password does not meet the safety criteria: use at least %s characters, with no reference to User Name, Name or Last name.');

if (isModuleInstalled('MyNotes')) {
	$moduleInstance = Vtiger_Module::getInstance('MyNotes');
	$adb->pquery("UPDATE {$table_prefix}_def_org_share SET editstatus = ? WHERE tabid = ?",array(0,$moduleInstance->id));
}
?>
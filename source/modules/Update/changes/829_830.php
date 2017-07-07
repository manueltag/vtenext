<?php
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';
$_SESSION['modules_to_update']['Myfiles'] = 'packages/vte/mandatory/Myfiles.zip';
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';

global $adb, $table_prefix;

$adb->pquery("update {$table_prefix}_entityname set fieldname = ? where modulename = ?", array('filename', 'Myfiles'));

// translations
SDK::setLanguageEntries('Calendar', 'LBL_INVITEES', array('it_it'=>'Invitati','en_us'=>'Invitees'));
SDK::setLanguageEntries('Calendar', 'LBL_INVITED', array('it_it'=>'Invitati','en_us'=>'Invited'));
SDK::setLanguageEntries('Calendar', 'LBL_INVITED_CONTACTS', array('it_it'=>'Contatti invitati','en_us'=>'Invited contacts'));
SDK::setLanguageEntries('Calendar', 'LBL_INVITED_USERS', array('it_it'=>'Utenti invitati','en_us'=>'Invited users'));

?>
<?php
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';

$moduleInstance = Vtiger_Module::getInstance('SDK');
Vtiger_Link::addLink($moduleInstance->id,'HEADERSCRIPT','VTELocalStorageScript','modules/SDK/src/VTELocalStorage.js');
?>
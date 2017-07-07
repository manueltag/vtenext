<?php
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
SDK::setLanguageEntries('APP_STRINGS','LBL_VIEW',array('it_it'=>'Filtro:','en_us'=>'Filter:','pt_br'=>'Filtro:'));

global $adb;
$ModCommentsInstance = Vtiger_Module::getInstance('ModComments');
$adb->pquery('UPDATE vtiger_field SET quickcreate = 3, masseditable = 0 WHERE tabid = ?',array($ModCommentsInstance->id));
?>
<?php
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';

global $adb, $table_prefix;
$hDInstance = Vtiger_Module::getInstance('HelpDesk');
$sCInstance = Vtiger_Module::getInstance('ServiceContracts');
$adb->pquery("UPDATE {$table_prefix}_relatedlists SET actions = ? WHERE (tabid = ? AND related_tabid = ?) OR (tabid = ? AND related_tabid = ?)",array('ADD,SELECT',$hDInstance->id,$sCInstance->id,$sCInstance->id,$hDInstance->id));

SDK::setLanguageEntry('Calendar', 'it_it', 'Will begin', 'Comincerà');
?>
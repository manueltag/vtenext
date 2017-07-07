<?php
global $adb, $table_prefix;

$adb->pquery("delete from {$table_prefix}_relatedlists where tabid = ? and name = ?", array(2,'get_stage_history'));
$adb->pquery("delete from {$table_prefix}_relatedlists where tabid = ? and name = ?", array(13,'get_ticket_history'));
$adb->pquery("delete from {$table_prefix}_relatedlists where tabid = ? and name = ?", array(20,'get_quotestagehistory'));
$adb->pquery("delete from {$table_prefix}_relatedlists where tabid = ? and name = ?", array(21,'get_postatushistory'));
$adb->pquery("delete from {$table_prefix}_relatedlists where tabid = ? and name = ?", array(22,'get_sostatushistory'));
$adb->pquery("delete from {$table_prefix}_relatedlists where tabid = ? and name = ?", array(23,'get_invoicestatushistory'));

SDK::setLanguageEntries('ChangeLog', 'LBL_HAS_CHANGED_THE_RECORD', array('it_it'=>'ha modificato il record','en_us'=>'has changed the record'));
SDK::setLanguageEntries('ChangeLog', 'LBL_HAS_CREATED_THE_RECORD', array('it_it'=>'ha creato il record','en_us'=>'has created the record'));
SDK::setLanguageEntries('ChangeLog', 'LBL_HAS_LINKED_THE_RECORD', array('it_it'=>'ha collegato %s','en_us'=>'has linked %s'));
SDK::setLanguageEntries('ChangeLog', 'LBL_HAS_REMOVED_LINK_WITH_RECORD', array('it_it'=>'ha rimosso il legame con %s','en_us'=>'has removed the link with %s'));

$sdkInstance = Vtiger_Module::getInstance('SDK');
$sdkInstance->addLink('HEADERSCRIPT', 'HistoryScript', 'include/js/HistoryTab.js');
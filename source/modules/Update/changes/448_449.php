<?php
global $adb;
$adb->query("DELETE FROM vtiger_seproductsrel WHERE crmid = productid");
$adb->query("DELETE FROM vtiger_crmentityrel WHERE crmid = relcrmid");
$adb->query("DELETE FROM vtiger_senotesrel WHERE crmid = notesid");
SDK::setLanguageEntry('Calendar', 'it_it', 'LBL_ACTIVITY_NOTIFICATION', 'Questa e` la notifica che un`attivita` a te assegnata e` stata');
SDK::setLanguageEntry('Calendar', 'it_it', 'LBL_ACTIVITY_INVITATION', 'Sei invitato ad una attivita` che e` stata');
?>
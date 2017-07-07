<?php
global $adb, $table_prefix;

$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['PBXManager'] = 'packages/vte/mandatory/PBXManager.zip';

SDK::setLanguageEntries('Messages', 'LBL_SPAM_ACTION', array('it_it'=>'Indesiderata','en_us'=>'Junk'));
SDK::setLanguageEntries('Messages', 'LBL_UNSPAM_ACTION', array('it_it'=>'Non indesiderata','en_us'=>'Not junk'));
SDK::setLanguageEntries('Messages', 'LBL_CONFIGURE_SPAM', array('it_it'=>'La cartella Spam non è configurata. Vuoi farlo adesso?','en_us'=>'Spam folder is not configure. Do you want to configure it now?'));

SDK::setLanguageEntry('PDFMaker', 'en_us', 'LBL_ARTICLE', 'Products block');
SDK::setLanguageEntry('PDFMaker', 'en_us', 'LBL_NOPRODUCT_BLOC', 'Current module does not contain products block.');
SDK::setLanguageEntry('PDFMaker', 'en_us', 'LBL_PRODUCT_BLOC_TPL', 'Template');
SDK::setLanguageEntry('PDFMaker', 'en_us', 'LBL_PRODUCT_FIELD_INFO', '* the fields will be repeated for each product/service.');

// alcune mail (es. quelle della Apple) sono state salvate con il corpo del messaggio come se fosse un allegato. se il cron è attivo le cancello così verranno riscaricate correttamente.
$result = $adb->pquery("select * from {$table_prefix}_cronjobs WHERE cronname = ? and active = ?",array('Messages',1));
if ($result && $adb->num_rows($result) > 0) {
	$adb->pquery("UPDATE {$table_prefix}_crmentity
	INNER JOIN {$table_prefix}_messages_attach ON {$table_prefix}_crmentity.crmid = {$table_prefix}_messages_attach.messagesid
	SET {$table_prefix}_crmentity.deleted = ?
	WHERE {$table_prefix}_messages_attach.contenttype IN (?,?) AND {$table_prefix}_messages_attach.contentdisposition = ?"
	,array(1,'text/plain','text/html','inline'));
}

$moduleInstance = Vtiger_Module::getInstance('ProjectPlan');
if ($moduleInstance) {
	$fieldInstance = Vtiger_Field::getInstance('linktoaccountscontacts',$moduleInstance);
	if ($fieldInstance) {
		$fieldInstance->setRelatedModules(array('Vendors'));
	}
	$relInstance = Vtiger_Module::getInstance('Vendors');
	$result = $adb->pquery("SELECT * FROM {$table_prefix}_relatedlists WHERE tabid = ? AND related_tabid = ? AND name = ?",array($relInstance->id,$moduleInstance->id,'get_dependents_list'));
	if ($result && $adb->num_rows($result) > 0) {}
	else { 
		$relInstance->setRelatedList($moduleInstance, 'ProjectPlan', array('ADD'), 'get_dependents_list');
	}
}

$moduleInstance = Vtiger_Module::getInstance('Visitreport');
if ($moduleInstance) {
	$fieldInstance = Vtiger_Field::getInstance('accountid',$moduleInstance);
	if ($fieldInstance) {
		$fieldInstance->setRelatedModules(array('Contacts'));
		$fieldInstance->setRelatedModules(array('Leads'));
		$adb->pquery("update {$table_prefix}_field set fieldlabel = ? where fieldid = ?",array('Related To',$fieldInstance->id));
		SDK::setLanguageEntries('Visitreport', 'Related To', array('it_it'=>'Collegato a','en_us'=>'Related to'));
	}
	$relInstance = Vtiger_Module::getInstance('Contacts');
	$result = $adb->pquery("SELECT * FROM {$table_prefix}_relatedlists WHERE tabid = ? AND related_tabid = ? AND name = ?",array($relInstance->id,$moduleInstance->id,'get_dependents_list'));
	if ($result && $adb->num_rows($result) > 0) {}
	else { 
		$relInstance->setRelatedList($moduleInstance, 'Visitreport', array('ADD'), 'get_dependents_list');
	}
	$relInstance = Vtiger_Module::getInstance('Leads');
	$result = $adb->pquery("SELECT * FROM {$table_prefix}_relatedlists WHERE tabid = ? AND related_tabid = ? AND name = ?",array($relInstance->id,$moduleInstance->id,'get_dependents_list'));
	if ($result && $adb->num_rows($result) > 0) {}
	else { 
		$relInstance->setRelatedList($moduleInstance, 'Visitreport', array('ADD'), 'get_dependents_list');
	}
}

$adb->pquery("UPDATE {$table_prefix}_field SET info_type = ?",array('BAS'));
?>
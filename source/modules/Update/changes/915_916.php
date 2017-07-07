<?php
global $adb, $table_prefix;

SDK::setLanguageEntries('APP_STRINGS', 'Newsletter Emails', array('it_it'=>'Newsletter','en_us'=>'Newsletter Emails'));

if (isModuleInstalled('Newsletter')) {
	$instanceNewsletter = Vtiger_Module::getInstance('Newsletter');
	$result = $adb->pquery("SELECT relation_id FROM {$table_prefix}_relatedlists WHERE related_tabid = ? AND name = ?",array($instanceNewsletter->id,'get_newsletter_emails'));
	if ($result && $adb->num_rows($result) > 0) {
		while($row=$adb->fetchByAssoc($result)) {
			SDK::setTurboliftCount($row['relation_id'], 'get_newsletter_emails_count');
		}
	}
}

// if is installed plugin MailConverterInfo
$result = $adb->pquery("select * from {$table_prefix}_links where linklabel = ?",array('ReplyMailConverter'));
if ($result && $adb->num_rows($result) > 0) {
	$HelpDeskInstance = Vtiger_Module::getInstance('HelpDesk');
	Vtiger_Link::addLink($HelpDeskInstance->id, 'DETAILVIEWBASIC', 'LBL_DO_NOT_IMPORT_ANYMORE', "javascript:doNotImportAnymore('\$MODULE\$',\$RECORD\$);", 'themes/images/small_spam.png',0,'checkMailScannerInfoRule:include/utils/crmv_utils.php');
	
	SDK::setLanguageEntries('Settings', 'LBL_DO_NOT_IMPORT_ANYMORE', array('it_it'=>'Indesiderata','en_us'=>'Junk','pt_br'=>'Junco','de_de'=>'Müll'));
	SDK::setLanguageEntries('HelpDesk', 'LBL_DO_NOT_IMPORT_ANYMORE', array('it_it'=>'Indesiderata','en_us'=>'Junk','pt_br'=>'Junco','de_de'=>'Müll'));
}
?>
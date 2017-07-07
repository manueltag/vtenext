<?php
SDK::setLanguageEntries('Settings', 'leadCustomFieldDescription', array(
	'it_it'=>'Mappa ciascuno dei tuoi campi personalizzati per i Lead verso il relativo campo personalizzato in azienda, contatto o opportunita`. Questa mappatura sara` utilizzata durante la fase di conversione del lead',
	'en_us'=>'Map each of your lead custom fields to each of your custom account, contact and potential.'
));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_SHARE_INSERT_EMAIL', array(
	'it_it'=>'Per caricare una revisione scrivi il tuo indirizzo email e premi su Procedi.',
	'en_us'=>'To upload a revision enter your email address and click to Next.'
));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_EMAIL_SENT', array(
	'it_it'=>'Ti è stata inviata una mail',
	'en_us'=>'It was sent an email to you'
));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_UPLOAD_SUCCESS', array(
	'it_it'=>'File caricato con successo',
	'en_us'=>'Upload completed'
));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_SHARE_NEXT', array(
	'it_it'=>'Procedi',
	'en_us'=>'Next'
));
@unlink('modules/PDFMaker/checkInstall.php');
@unlink('Smarty/templates/modules/PDFMaker/install.tpl');
/*
global $adb, $table_prefix;

// delete tables if I update a version older than 723
if ($this->from_version >= 723) {
	$tables = array(
		'tbl_s_newsletter_tl_click',
		"{$table_prefix}_emaildetails",
		"{$table_prefix}_email_access",
		"{$table_prefix}_email_track",
		"{$table_prefix}_mailcache_folders",
		"{$table_prefix}_mailcache_list",
		"{$table_prefix}_mailcache_messages",
		'crmv_squirrelmailrel',
	);
	foreach($tables as $table) {
		if (Vtiger_Utils::CheckTable($table)) {
			$result = $adb->query("select * from $table");
			if ($result && $adb->num_rows($result) > 0) {
				$adb->query("drop table $table");
			}
		}
	}
}*/
?>
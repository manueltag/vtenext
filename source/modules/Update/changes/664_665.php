<?php
/*
global $adb, $table_prefix;
//@$adb->datadict->ExecuteSQLArray((Array)$adb->datadict->CreateIndexSQL('idx_module_lang_label', 'sdk_language', 'module,language,label(255)'));
SDK::setLanguageEntries('Settings','LBL_TRANS_ALL',Array('en_us'=>'All','it_it'=>'Tutti'));
SDK::setLanguageEntries('Settings','LBL_TRANS_LANGUAGE',Array('en_us'=>'Language','it_it'=>'Lingua'));
SDK::setLanguageEntries('Settings','LBL_TRANS_MANDATORY',Array('en_us'=>'Fields marked with * are required','it_it'=>'I campi contrassegnati con * sono obbligatori'));
SDK::setLanguageEntries('Settings','LBL_TRANS_MODULE',Array('en_us'=>'Module','it_it'=>'Modulo'));
SDK::setLanguageEntries('Settings','LBL_TRANS_LABEL',Array('en_us'=>'System label','it_it'=>'Etichetta di sistema'));
SDK::setLanguageEntries('Settings','LBL_TRANS_SEARCH',Array('en_us'=>'Search','it_it'=>'Cerca'));
SDK::setLanguageEntries('Settings','LBL_TRANS_ACTIONS',Array('en_us'=>'Actions','it_it'=>'Azioni'));
SDK::setLanguageEntries('Settings','LBL_TRANS_SHOW_ONLY_FIELDS',Array('en_us'=>'Show only module fields/fieldvalues','it_it'=>'Visualizza solamente i campi/valori dei moduli'));
SDK::setLanguageEntries('Settings','LBL_TRANS_PICKLIST_FIELDS',Array('en_us'=>'Picklist fields','it_it'=>'Campi picklist'));
SDK::setLanguageEntries('Settings','LBL_TRANS_DUPLICATE_MESSAGE_BEFORE',Array('en_us'=>'The Label you selected is already translated with values','it_it'=>'L\'etichetta che hai selezionato risulta gia\' tradotta con valori'));
SDK::setLanguageEntries('Settings','LBL_TRANS_DUPLICATE_MESSAGE_AFTER',Array('en_us'=>'Do you want to overwrite?','it_it'=>'Vuoi sovrascrivere?'));
SDK::setLanguageEntries('Settings','LBL_TRANS_ERR',Array('en_us'=>'Generic Error','it_it'=>'Errore generico'));
SDK::setLanguageEntries('Settings','LBL_TRANS_APP_STRINGS',Array('en_us'=>'General','it_it'=>'Generale'));
SDK::setLanguageEntries('Settings','LBL_TRANS_APP_LIST_STRINGS',Array('en_us'=>'JSON Strings','it_it'=>'Liste JSON'));
SDK::setLanguageEntries('Settings','LBL_TRANS_APP_CURRENCY_STRINGS',Array('en_us'=>'Currency strings','it_it'=>'Stringhe valuta'));
SDK::setLanguageEntries('Settings','LBL_TRANS_NONE',Array('en_us'=>'None','it_it'=>'Nessuno'));
SDK::setLanguageEntries('Settings','LBL_TRANS_SHOW_ONLY_NOT_TRANSLATED',Array('en_us'=>'Show untranslated entries','it_it'=>'Visualizza voci non tradotte'));
SDK::setLanguageEntries('Settings','LBL_TRANS_FILTER_NAME',Array('en_us'=>'Restrictions','it_it'=>'Restrizioni'));
SDK::setLanguageEntries('Settings','LBL_TRANS_FILTER_ALL',Array('en_us'=>'None','it_it'=>'Nessuna'));
SDK::setLanguageEntries('Settings','LBL_TRANS_FILTER_FIELDS',Array('en_us'=>'Show only fields','it_it'=>'Mostra solamente i campi'));
SDK::setLanguageEntries('Settings','LBL_TRANS_FILTER_FIELDVALUES',Array('en_us'=>'Show only fieldvalues','it_it'=>'Mostra solamente i valori delle picklist dei campi'));
SDK::setLanguageEntries('Settings','LBL_TRANS_FILTER_OTHER',Array('en_us'=>'Show other','it_it'=>'Mostra altro'));
SDK::setLanguageEntries('Settings','LBL_TRANS_LANGUAGEEDITOR',Array('en_us'=>'Languages Editor','it_it'=>'Editor Lingue'));
SDK::setLanguageEntries('Settings','LBL_TRANS_LANGUAGEEDITOR_DES',Array('en_us'=>'Manage translation of all entries in CRM','it_it'=>'Gestisci le traduzioni di tutte le voci presenti nel CRM'));

$fieldid = $adb->getUniqueID($table_prefix.'_settings_field');
$blockid = getSettingsBlockId('LBL_STUDIO');

$seq_res = $adb->query("SELECT max(sequence) AS max_seq FROM ".$table_prefix."_settings_field");
$seq = 1;
if ($adb->num_rows($seq_res) > 0) {
	$cur_seq = $adb->query_result($seq_res, 0, 'max_seq');
	if ($cur_seq != null)	$seq = $cur_seq + 1;
}
$adb->pquery('INSERT INTO '.$table_prefix.'_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence) VALUES (?,?,?,?,?,?,?)', array($fieldid, $blockid, 'LBL_TRANS_LANGUAGEEDITOR', 'themes/images/LanguageEditor.png', 'LBL_TRANS_LANGUAGEEDITOR_DES', 'index.php?module=Settings&action=LanguageEditor&parenttab=Settings', $seq));
//Aggiungere anche in modules/Users/DefaultDataPopulator.php
*/
?>
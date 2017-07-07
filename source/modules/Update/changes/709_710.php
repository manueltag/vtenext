<?php
if (isModuleInstalled('WSAPP')) {
	$moduleInstance = Vtiger_Module::getInstance('WSAPP');
	$moduleInstance->hide(array('hide_profile'=>1));
}

SDK::setLanguageEntries('Import','LBL_NEXT_BUTTON_LABEL',Array('it_it'=>'Avanti','en_us'=>'Next'));
SDK::setLanguageEntries('Import','LBL_FILE_UPLOAD_FAILED',Array('it_it'=>'Problemi durante il caricamento del file csv. Verifica i permessi di scrittura della cartella di cache.','en_us'=>'Encountered issues on loading csv file. Check your cache folder permissions.'));

$language = new Vtiger_Language();
$languages = $language->getAll();
if (array_key_exists('pt_br',$languages)) {
	$unzip = new Vtiger_Unzip('packages/vte/optional/PTBrasil.zip');
	$tmp_dir = "packages/vte/optional/tmp";
	mkdir($tmp_dir);
	$unzip->unzipAllEx($tmp_dir);
	if($unzip) $unzip->close();
	if (copy($tmp_dir.'/modules/Import/language/pt_br.lang.php','modules/Import/language/pt_br.lang.php')) {
		SDK::deleteLanguage('Import','pt_br');
		SDK::file2DbLanguage('Import','pt_br');
	}
	folderDetete($tmp_dir);
}

global $adb, $table_prefix;
$res = $adb->pquery("select fieldtype from {$table_prefix}_ws_fieldtype where uitype = ?",array(1014));
if ($res && $adb->num_rows($res) == 0) {
	$id = $adb->getUniqueId("{$table_prefix}_ws_fieldtype");
	$adb->pquery("insert into {$table_prefix}_ws_fieldtype (fieldtypeid, uitype, fieldtype) values (?,?,?)",array($id, 1014, 'phone'));
}
?>
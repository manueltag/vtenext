<?php
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['Morphsuit'] = 'packages/vte/mandatory/Morphsuit.zip';
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';

global $adb, $table_prefix;
$result = $adb->query("SELECT * FROM {$table_prefix}_language WHERE prefix = 'de_de'");
if ($result && $adb->num_rows($result) > 0) {
	$languageInstance = new Vtiger_Language();
	$languageInstance->update($languageInstance, 'packages/vte/optional/Deutsch.zip', true);
}
$result = $adb->query("SELECT * FROM {$table_prefix}_language WHERE prefix = 'nl_nl'");
if ($result && $adb->num_rows($result) > 0) {
	$languageInstance = new Vtiger_Language();
	$languageInstance->update($languageInstance, 'packages/vte/optional/Dutch.zip', true);
}
SDK::setLanguageEntries('Home','Myfiles',Array(
	'it_it'=>'File personali',
	'en_us'=>'My files',
	'br_br'=>'Meus arquivos',
	'de_de'=>'Meine Dateien',
	'nl_nl'=>'Mijn bestanden',
));

@unlink('cron/jobstartwindows.bat');
if (is_dir('cron/modules/VtigerBackup')) {
	folderDetete('cron/modules/VtigerBackup');
}

SDK::setLanguageEntry('APP_STRINGS', 'en_us', 'LBL_POPUP_RECORDS_NOT_SELECTABLE', 'It\'s not possible to select existing %s but you can create one now.');
?>
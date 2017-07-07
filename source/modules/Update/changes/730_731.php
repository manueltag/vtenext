<?php
$_SESSION['modules_to_update']['ChangeLog'] = 'packages/vte/mandatory/ChangeLog.zip';
$_SESSION['modules_to_update']['Timecards'] = 'packages/vte/mandatory/Timecards.zip';

SDK::setLanguageEntry('Leads', 'it_it', 'Numero Dipendenti', 'Numero dipendenti');
SDK::setLanguageEntry('Import', 'it_it', 'LBL_UNDO_LAST_IMPORT', 'Annulla l\'importazione');

global $adb, $table_prefix;
if (isModuleInstalled('Timecards')) {
	$moduleInstance = Vtiger_Module::getInstance('Timecards');
	$fieldInstance = Vtiger_Field::getInstance('description',$moduleInstance);
	$blockInstance = new Vtiger_Block();
	$blockInstance->label = 'LBL_DESCRIPTION_INFORMATION';
	$blockInstance->save($moduleInstance);
	$blockInstance->moveHereFields(array('description'));
	$adb->pquery("UPDATE {$table_prefix}_blocks SET sequence = sequence+1 WHERE tabid = ? AND sequence >= ?",array($moduleInstance->id,2));
	$adb->pquery("UPDATE {$table_prefix}_blocks SET sequence = ? WHERE tabid = ? AND blocklabel = ?",array(2,$moduleInstance->id,$blockInstance->label));
	SDK::setLanguageEntries('Timecards', 'LBL_DESCRIPTION_INFORMATION', array(
		'it_it' => 'Informazioni Descrizione',
		'en_us'=>'Description Information',
		'de_de'=>'Zusatzinformationen',
		'pt_br' => utf8_encode('Informaчуo Descriчуo'),
	));
}
?>
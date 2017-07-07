<?php
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
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
$result = $adb->query("SELECT * FROM {$table_prefix}_language WHERE prefix = 'pt_br'");
if ($result && $adb->num_rows($result) > 0) {
	$languageInstance = new Vtiger_Language();
	$languageInstance->update($languageInstance, 'packages/vte/optional/PTBrasil.zip', true);
}
?>
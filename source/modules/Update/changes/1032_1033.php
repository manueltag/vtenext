<?php
global $adb, $table_prefix;

$result = $adb->query("SELECT * FROM {$table_prefix}_language WHERE prefix = 'de_de'");
if ($result && $adb->num_rows($result) > 0) {
	$languageInstance = new Vtiger_Language();
	$languageInstance->update($languageInstance, 'packages/vte/optional/Deutsch.zip', true);
}

?>
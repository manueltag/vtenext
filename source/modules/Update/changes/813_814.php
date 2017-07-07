<?php
$_SESSION['modules_to_update']['MyNotes'] = 'packages/vte/mandatory/MyNotes.zip';

global $adb, $table_prefix;

if (isModuleInstalled('MyNotes')) {
	$MyNotesInstance = Vtiger_Module::getInstance('MyNotes');
	$adb->pquery("UPDATE {$table_prefix}_relatedlists SET actions = ? WHERE actions = ? AND name = ? AND tabid = ?",array('SELECT,ADD','SELECT','get_related_list',$MyNotesInstance->id));
}

SDK::setLanguageEntries('APP_STRINGS', 'LBL_CONVERT_ACTION', array('it_it'=>'Converti','en_us'=>'Convert'));

@unlink('Smarty/templates/modules/Update/Login.tpl');

// change value of $calculate_response_time in config.inc
$configInc = file_get_contents('config.inc.php');
if (empty($configInc)) {
	echo "\nWARNING: Unable to get config.inc.php contents, please modify it manually.\n";
} else {
	// backup it (only if it doesn't exist
	$newConfigInc = 'config.inc.813.php';
	if (!file_exists($newConfigInc)) {
		file_put_contents($newConfigInc, $configInc);
	}
	// change value
	$configInc = preg_replace('/^\$default_theme.*$/m', "\$default_theme = 'softed';", $configInc);
	$configInc = str_replace("\$calculate_response_time = true;","\$calculate_response_time = false;",$configInc);
	if (is_writable('config.inc.php')) {
		file_put_contents('config.inc.php', $configInc);
	} else {
		echo "\nWARNING: Unable to update config.inc.php, please modify it manually.\n";
	}
}

$adb->pquery("update {$table_prefix}_users set default_theme = ?",array('softed'));
?>
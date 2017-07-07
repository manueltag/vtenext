<?php
$_SESSION['modules_to_update']['WSAPP'] = 'packages/vte/mandatory/WSAPP.zip';

if (isModuleInstalled('Webforms')) {
	SDK::setLanguageEntry('Webforms','it_it','LBL_NEUTRALIZEDFIELD','Campo nel webform');
	SDK::setLanguageEntry('Webforms','pt_br','LBL_NEUTRALIZEDFIELD','Campo no webform');
	SDK::setLanguageEntry('Webforms','en_us','Webform','Webform');
	SDK::setLanguageEntry('Webforms','pt_br','Webform','Webform');
	SDK::setLanguageEntries('Webforms','LBL_HIDDEN',array('it_it'=>'Nascosto','en_us'=>'Hidden','pt_br'=>'Ocultar'));
	
	global $adb, $table_prefix;
	$columns = array_keys($adb->datadict->MetaColumns($table_prefix.'_webforms_field'));
	if (!in_array(strtoupper('hidden'),$columns)) {
		$sqlarray = $adb->datadict->AddColumnSQL($table_prefix.'_webforms_field','hidden I(1) DEFAULT 0');
		$adb->datadict->ExecuteSQLArray($sqlarray);
		$result = $adb->query("update {$table_prefix}_webforms_field set hidden = 0");
	}
}
?>
<?php
$_SESSION['modules_to_update']['Ddt'] = 'packages/vte/mandatory/Ddt.zip';
$_SESSION['modules_to_update']['Fax'] = 'packages/vte/mandatory/Fax.zip';
$_SESSION['modules_to_update']['SLA'] = 'packages/vte/mandatory/SLA.zip';
$_SESSION['modules_to_update']['PBXManager'] = 'packages/vte/mandatory/PBXManager.zip';
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
$_SESSION['modules_to_update']['Sms'] = 'packages/vte/mandatory/Sms.zip';
$_SESSION['modules_to_update']['Transitions'] = 'packages/vte/mandatory/Transitions.zip';

SDK::file2DbLanguages('PickList');
SDK::setLanguageEntry('Users', 'it_it', 'LBL_PASSWORD', 'Password');
SDK::setLanguageEntry('Users', 'it_it', 'LBL_THEME', 'Tema');
SDK::setLanguageEntry('Users', 'it_it', 'LBL_LANGUAGE', 'Lingua');

if (Vtiger_Module::getInstance('SLA')){
	require_once('modules/SLA/SLA.php');
	// copy language files to database
	SDK::file2DbLanguages('SLA');
	// gets all the SLA languages
	$slalangs = array_keys(vtlib_getToggleLanguageInfo());
	$langstrings = array();
	if (!empty($slalangs)) {
		foreach ($slalangs as $lang) {
			$langstrings[$lang] = get_lang_strings('SLA', $lang);
		}
	}
	// register them for modules associated with SLA
	$slacfg = SLA::get_config();
	if (is_array($slacfg) && !empty($langstrings)) {
		$slamod = array_keys($slacfg);
		foreach ($slamod as $mod) {
			foreach ($langstrings as $lang=>$mod_strings) {
				foreach ($mod_strings as $key => $value) {
					SDK::setLanguageEntry($mod, $lang, $key, $value);
				}
			}
		}
	}
}
?>
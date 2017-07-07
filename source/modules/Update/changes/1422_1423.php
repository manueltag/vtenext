<?php

global $adb;

global $table_prefix;

// switch to https
$adb->query("UPDATE {$table_prefix}_home_iframe SET url = 'https://help-vtecrm-com.vtecrm.net/news/index.php?lang=\$CURRENT_LANGUAGE\$' WHERE hometype = 'CRMVNEWS'");
$adb->query("UPDATE {$table_prefix}_home_iframe SET url = 'https://help-vtecrm-com.vtecrm.net/index.php?lang=\$CURRENT_LANGUAGE\$' WHERE hometype = 'HELPVTE'");


/* crmv@108207 */

require_once('modules/Settings/ModuleMaker/ModuleMakerUtils.php');

// get installed modules
$MMUtils = new ModuleMakerUtils();
$res = $adb->query("SELECT id FROM {$table_prefix}_modulemaker WHERE installed = 1");
if ($res && $adb->num_rows($res) > 0) {
	while ($row = $adb->FetchByAssoc($res, -1, false)) {
		$mid = $row['id'];
		$names = $MMUtils->getScriptFileNames($mid);
		if ($names && is_file($names['uninstall_script']) && is_writable($names['uninstall_script'])) {
			// patch the file!
			patchModuleUninstallScript($names['uninstall_script']);
		}
		
	}
}

function patchModuleUninstallScript($file) {
	$data = @file_get_contents($file);
	if (!$data) return; // false or 0, I don't care
	
	$search = "/protected function deleteReports(.*?)protected function/s";
	$replace = <<<'REPL'
protected function deleteReports($modname) {
		global $adb, $table_prefix;
		
		require_once('modules/Reports/Reports.php');
		$reports = Reports::getInstance();
		
		$this->log('Deleting all reports...');
		
		$res = $adb->pquery("SELECT r.reportid
			FROM {$table_prefix}_report r
			INNER JOIN {$table_prefix}_reportconfig rc ON rc.reportid = r.reportid
			WHERE rc.module = ?", array($modname));
		if ($res && $adb->num_rows($res) > 0) {
			while ($row = $adb->FetchByAssoc($res, -1, false)) {
				$id = intval($row['reportid']);
				if ($id > 0) $reports->deleteReport($id);
			}
		}
	}
	// crmv@99794e
	
	protected function
REPL;
	if (preg_match($search, $data, $matches)) {
		// check if already patched
		if (strpos($matches[1], '_reportmodules') !== false) {
			$data = preg_replace($search, $replace, $data);
			// save the file!
			file_put_contents($file, $data);
		}
	}
}


$trans = array(
	'Home' => array(
		'it_it' => array(
			'News CRMVILLAGE.BIZ' => 'News da VTECRM',
		),
		'en_us' => array(
			'News CRMVILLAGE.BIZ' => 'News from VTECRM',
		),
	),	
);
foreach ($trans as $module=>$modlang) {
	foreach ($modlang as $lang=>$translist) {
		foreach ($translist as $label=>$translabel) {
			SDK::setLanguageEntry($module, $lang, $label, $translabel);
		}
	}
}

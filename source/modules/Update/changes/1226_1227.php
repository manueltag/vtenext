<?php

global $adb, $table_prefix;

// disable modules M and Mobile, but keep them installed, so the tabids won't change
$hideModules = array('Mobile', 'M');
foreach ($hideModules as $mod) {
	$module = Vtecrm_Module::getInstance($mod);
	if ($module && $module->id > 0) {
		$module->hide(array(
			'hide_module_manager' => 1,
			'hide_profile' => 1,
			'hide_report' => 1,
		));
		// now disable (I can't skip it, otherwise the tabids would change)
		$adb->pquery("UPDATE {$table_prefix}_tab SET presence = 1 WHERE tabid = ?", array($module->id));
		$adb->pquery("DELETE FROM tbl_s_menu_modules WHERE tabid = ?", array($module->id));
		// recreate tabdata
		$module->syncfile();
	}
}
// and clear the cache
if (class_exists('Cache')) {
	$cache = Cache::getInstance('installed_modules');
	if ($cache) $cache->clear();
}

// and drop a table
if (Vtiger_Utils::CheckTable("{$table_prefix}_mobile_alerts")) {
	$sqlarray = $adb->datadict->DropTableSQL("{$table_prefix}_mobile_alerts");
	$adb->datadict->ExecuteSQLArray($sqlarray);
}

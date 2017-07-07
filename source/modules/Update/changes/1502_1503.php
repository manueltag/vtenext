<?php

// crmv@120738

global $adb, $table_prefix;

$moduleInstance = Vtiger_Module::getInstance('SDK');
Vtiger_Link::addLink($moduleInstance->id, 'HEADERSCRIPT', 'SearchUtils', 'include/js/SearchUtils.js');

$adb->query("DELETE FROM tbl_s_menu_modules");

$cache = Cache::getInstance('getMenuModuleList');
$cache->clear();

$fastModules = array('Home', 'Processes', 'Leads', 'Accounts', 'Contacts', 'Campaigns', 'HelpDesk', 'Potentials', 'Reports');
$i = 0;
foreach ($fastModules as $module) {
	if (vtlib_isModuleActive($module)) {
		$params = array(getTabid($module), 1, $i);
		$adb->pquery("INSERT INTO tbl_s_menu_modules(tabid, fast, sequence) VALUES(?,?,?)", $params);
		$i++;
	}
}

$moduleQ = "SELECT {$table_prefix}_tab.tabid,
	{$table_prefix}_tab.name
	FROM {$table_prefix}_tab
	INNER JOIN (SELECT DISTINCT tabid FROM {$table_prefix}_parenttabrel) parenttabrel
	ON parenttabrel.tabid = {$table_prefix}_tab.tabid
	WHERE {$table_prefix}_tab.presence = 0";

$moduleR = $adb->query($moduleQ);

$i = 0;
if ($moduleR && $adb->num_rows($moduleR)) {
	while ($row = $adb->fetchByAssoc($moduleR, -1, false)) {
		if (vtlib_isModuleActive($row['name']) && !in_array($row['name'], $fastModules)) {
			$params = array($row['tabid'], 0, $i);
			$adb->pquery("INSERT INTO tbl_s_menu_modules(tabid,fast,sequence) VALUES(?,?,?)", $params);
			$i++;
		}
	}
}

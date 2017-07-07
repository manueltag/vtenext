<?php

global $adb, $table_prefix;

/* crmv@102956 */

if (!function_exists('addColumnToTable')) {
	function addColumnToTable($tablename, $columnname, $type, $extra = '') {
		global $adb;

		// check if already present
		$cols = $adb->getColumnNames($tablename);
		if (in_array($columnname, $cols)) {
			return;
		}

		$col = $columnname.' '.$type.' '.$extra;
		$adb->alterTable($tablename, $col, 'Add_Column');
	}
}

$table = $table_prefix.'_cronjobs';
if ($adb->isMysql()) {
	if (!in_array('last_duration', $adb->getColumnNames($table))) {
		$adb->query("ALTER TABLE $table ADD COLUMN last_duration INT(21) AFTER lastrun");
	}
} else {
	addColumnToTable($table, 'last_duration', 'I(21)');
}


/* crmv@103054 */

if (!isModuleInstalled('Geolocalization')) {
	// install the new geolocation module
	require_once('vtlib/Vtecrm/Package.php');
	$package = new Vtiger_Package();
	$package->importByManifest('Geolocalization');
} else {
	// already installed, run the post install and register the handler
	
	$em = new VTEventsManager($adb);
	$em->registerHandler('vtiger.entity.aftersave', 'modules/Geolocalization/GeolocalizationHandler.php', 'GeolocalizationHandler');
	
	// convert the old links
	$adb->pquery(
		"UPDATE {$table_prefix}_links SET linkurl = ? WHERE linktype = ? AND linklabel = ? and linkurl LIKE ?", 
		array('VTEGeolocalization.getLocalization(\'$MODULE$\');', 'LISTVIEWBASIC', 'Geolocalization', 'getLocalization%')
	);
	
	require_once("modules/Geolocalization/Geolocalization.php");
	$instance = Geolocalization::getInstance();
	if ($instance) {
		$instance->vtlib_handler('Geolocalization', Vtiger_Module::EVENT_MODULE_POSTINSTALL);
	}
}

// translations
$trans = array(
	'ALERT_ARR' => array(
		'it_it' => array(
			'NO_ADDRESS_SELECTED' => 'Nessun indirizzo selezionato',
		),
		'en_us' => array(
			'NO_ADDRESS_SELECTED' => 'No address selected',
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


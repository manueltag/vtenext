<?php
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
global $table_prefix;
$table = $table_prefix.'_process_dynaform';
if (Vtiger_Utils::CheckTable($table)) {
	addColumnToTable($table, 'done', 'I(1)', 'DEFAULT 0');
}

$sdkInstance = Vtiger_Module::getInstance('SDK');
$sdkInstance->addLink('HEADERSCRIPT', 'DynaFormScript', 'modules/Processes/DynaFormScript.js');

// translations
$trans = array(
	'Settings' => array(
		'it_it' => array(
			'LBL_PMH_RELATED_TO_POPUP'=>'Mostra popup nell\'entità collegata',
		),
		'en_us' => array(
			'LBL_PMH_RELATED_TO_POPUP'=>'Show popup in the related entity',
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
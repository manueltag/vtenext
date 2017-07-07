<?php
// translations
$trans = array(
	'APP_STRINGS' => array(
		'it_it' => array(
			'AddModuleHomeListView'=>'Nuovo tab lista',
			'LBL_DEFAULT_FILTER'=>'Filtro di default',
		),
		'en_us' => array(
			'AddModuleHomeListView'=>'New tab with list',
			'LBL_DEFAULT_FILTER'=>'Default filter',
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

$columns = array_keys($adb->datadict->MetaColumns($table_prefix."_modulehome"));
if (!in_array(strtoupper('cvid'),$columns)) {
	$sql = $adb->datadict->AddColumnSQL($table_prefix."_modulehome",'cvid INT(1)');
	$adb->datadict->ExecuteSQLArray($sql);
}

// create default tabs
require_once('include/utils/ModuleHomeView.php');
$MHW = ModuleHomeView::install();
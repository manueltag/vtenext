<?php
/* crmv@83877 */

$_SESSION['modules_to_update']['Charts'] = 'packages/vte/mandatory/Charts.zip';
$_SESSION['modules_to_update']['Ddt'] = 'packages/vte/mandatory/Ddt.zip';
$_SESSION['modules_to_update']['FieldFormulas'] = 'packages/vte/mandatory/FieldFormulas.zip';
$_SESSION['modules_to_update']['Myfiles'] = 'packages/vte/mandatory/Myfiles.zip';
$_SESSION['modules_to_update']['MyNotes'] = 'packages/vte/mandatory/MyNotes.zip';
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
$_SESSION['modules_to_update']['Sms'] = 'packages/vte/mandatory/Sms.zip';
$_SESSION['modules_to_update']['Webforms'] = 'packages/vte/mandatory/Webforms.zip';


/* crmv@83878 */
global $adb, $table_prefix;

// some functions
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


addColumnToTable($table_prefix.'_import_maps', 'defaults', 'X');
addColumnToTable($table_prefix.'_import_maps', 'formats', 'X');
addColumnToTable($table_prefix.'_import_queue', 'fields_formats', 'X');

// translations

$trans = array(
	'Import' => array(
		'it_it' => array(
			'Skip' => 'Ignora',
			'Overwrite' => 'Sostituisci',
			'Merge' => 'Aggiorna',
			'LBL_IMPORT_FORMAT' => 'Formato',
		),
		'en_us' => array(
			'Skip' => 'Ignore',
			'Overwrite' => 'Replace',
			'Merge' => 'Update',
			'LBL_IMPORT_FORMAT' => 'Format',
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
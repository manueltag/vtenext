<?php

/* crmv@95157 */

if (isModuleInstalled('RecycleBin')) {
	$_SESSION['modules_to_update']['RecycleBin'] = 'packages/vte/optional/RecycleBin.zip';
}

require_once('modules/Documents/storage/StorageBackendUtils.php');

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

SDK::setUitype(212, 'modules/SDK/src/212/212.php', 'modules/SDK/src/212/212.tpl', 'modules/SDK/src/212/212.js', 'picklist');

// aggiungo campo
$fields = array(
	'backend_name_docs'		=> array('module'=>'Documents', 'block'=>'LBL_FILE_INFORMATION',	'name'=>'backend_name',	'label'=>'StorageBackend',		'table'=>"{$table_prefix}_notes", 	'columntype'=>'C(63)',	'typeofdata'=>'V~O', 	'uitype'=>212, 'readonly'=>1, 'masseditable'=>0, 'quickcreate'=>1),
	'backend_name_files'	=> array('module'=>'Myfiles', 'block'=>'LBL_FILE_INFORMATION',	'name'=>'backend_name',	'label'=>'StorageBackend',		'table'=>"{$table_prefix}_myfiles", 	'columntype'=>'C(63)',	'typeofdata'=>'V~O', 	'uitype'=>212, 'readonly'=>1, 'masseditable'=>0, 'quickcreate'=>1),
);

$fieldRet = Update::create_fields($fields);

// aggiungo colonna
addColumnToTable($table_prefix.'_attachments', 'backend_name', 'C(63)');
addColumnToTable($table_prefix.'_attachments', 'backend_key', 'C(1000)');

// add metadata link
$docInstance = Vtiger_Module::getInstance('Documents');
Vtiger_Link::addLink($docInstance->id, 'DETAILVIEWBASIC', 'LBL_SHOW_METADATA', "javascript:showMetadata('\$RECORD\$')", '', 2, 'checkMetadata:modules/Documents/storage/StorageBackendUtils.php');

// convert old attachments
$SBU = StorageBackendUtils::getInstance();
$SBU->convertLegacyBackends();

// translations
$trans = array(
	'Documents' => array(
		'it_it' => array(
			'StorageBackend' => 'Supporto di archiviazione',
			'LBL_STORAGE_BACKEND' => 'Supporto di archiviazione',
			'LBL_STORAGE_BACKEND_FILE' => 'Interno',
			'LBL_STORAGE_BACKEND_ALFRESCO' => 'Alfresco',
			'LBL_INTERNAL' => 'File',
			'LBL_EXTERNAL' => 'URL',
			'Download Type' => 'Tipo di allegato',
			'LBL_SHOW_METADATA' => 'Mostra metadati',
			'LBL_METADATA' => 'Metadati',
		),
		'en_us' => array(
			'StorageBackend' => 'Storage backend',
			'LBL_STORAGE_BACKEND' => 'Storage backend',
			'LBL_STORAGE_BACKEND_FILE' => 'Internal',
			'LBL_STORAGE_BACKEND_ALFRESCO' => 'Alfresco',
			'LBL_INTERNAL' => 'File',
			'LBL_EXTERNAL' => 'URL',
			'Download Type' => 'Attachment type',
			'LBL_SHOW_METADATA' => 'Show metadata',
			'LBL_METADATA' => 'Metadata',
		),
	),
	'Myfiles' => array(
		'it_it' => array(
			'StorageBackend' => 'Supporto di archiviazione',
			'Download Type' => 'Tipo di allegato',
		),
		'en_us' => array(
			'StorageBackend' => 'Storage backend',
			'Download Type' => 'Attachment type',
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

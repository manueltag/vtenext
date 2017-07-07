<?php
global $adb, $table_prefix;

require_once('vtlib/Vtiger/SettingsBlock.php');

// reorder the studio settings block
$sb = Vtiger_SettingsBlock::getInstance('LBL_STUDIO');
if ($sb && $sb->id > 0) {
	$ordered = array(
		'VTLIB_LBL_MODULE_MANAGER',
		'LBL_MODULE_MAKER',
		'LBL_CUSTOM_FIELDS',
		'LBL_PICKLIST_EDITOR',
		'LBL_PICKLIST_EDITOR_MULTI',
		'LBL_EDIT_LINKED_PICKLIST',
		'LBL_EDIT_UITYPE208',
		'LBL_MENU_TABS',
		'LBL_COLORED_LISTVIEW_EDITOR',
		'LBL_LIST_WORKFLOWS',
		'LBL_ST_MANAGER',
		'LBL_COND_MANAGER',
		'LBL_DATA_IMPORTER',
	);
	$seq = 1;
	foreach ($ordered as $setting) {
		$adb->pquery("UPDATE {$table_prefix}_settings_field SET sequence = ? WHERE blockid = ? AND name = ?", array($seq, $sb->id, $setting));
		++$seq;
	}
}


$trans = array(
	'Settings' => array(
		'it_it' => array(
			'LBL_ST_MANAGER' => 'Gestore cambi di stato',
			'EMAILTEMPLATES' => 'Template Email',
		),
		'en_us' => array(
			'LBL_ST_MANAGER' => 'Status change Manager',
			'EMAILTEMPLATES' => 'Email Templates',
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


<?php

/* crmv@102379 */

// change from Processes to Wizards
$adb->pquery("UPDATE {$table_prefix}_modulehome_blocks SET type = ? WHERE type = ?", array('Wizards', 'Processes'));
$adb->pquery("UPDATE {$table_prefix}_modulehome_blocks SET title = ? WHERE type = ? AND title = ?", array('Wizard', 'Wizards', 'Processi'));

// remove wizards from listview
$adb->pquery("DELETE FROM {$table_prefix}_links WHERE linktype = ? AND linklabel IN (?,?)", array('LISTVIEWBASIC', 'WIZARD_NEW_OPPORTUNITY', 'WIZARD_NEW_TICKET'));

// translations
$trans = array(
	'APP_STRINGS' => array(
		'it_it' => array(
			'LBL_WIZARDS'=>'Wizard',
			'LBL_CHOOSE_WIZARDS' => 'Scegli uno o più wizard',
		),
		'en_us' => array(
			'LBL_WIZARDS'=>'Wizards',
			'LBL_CHOOSE_WIZARDS' => 'Choose one or more wizards',
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

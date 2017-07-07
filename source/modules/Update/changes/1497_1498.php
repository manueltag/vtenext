<?php

// crmv@120738

global $adb, $table_prefix;

$adb->query("ALTER TABLE {$table_prefix}_settings_blocks ADD COLUMN image VARCHAR(100) NULL AFTER sequence");
$adb->pquery("UPDATE {$table_prefix}_settings_blocks SET image = ? WHERE label = ?", array('people', 'LBL_USER_MANAGEMENT'));
$adb->pquery("UPDATE {$table_prefix}_settings_blocks SET image = ? WHERE label = ?", array('business', 'LBL_STUDIO'));
$adb->pquery("UPDATE {$table_prefix}_settings_blocks SET image = ? WHERE label = ?", array('public', 'LBL_COMMUNICATION_TEMPLATES'));
$adb->pquery("UPDATE {$table_prefix}_settings_blocks SET image = ? WHERE label = ?", array('build', 'LBL_OTHER_SETTINGS'));

SDK::setUtil('include/utils/ThemeUtils.php');

$trans = array(
	'APP_STRINGS' => array(
		'en_us' => array(
			'LBL_NO_LASTVIEWED' => 'No recents',
			'LBL_NO_FAVORITES' => 'No favorites',
			'LBL_NO_TODOS' => 'No todos',
			'LBL_NO_NOTIFICATIONS' => 'No notifications',
		), 
		'it_it' => array(
			'LBL_NO_LASTVIEWED' => 'Nessun recente',
			'LBL_NO_FAVORITES' => 'Nessun preferito',
			'LBL_NO_TODOS' => 'Nessun compito',
			'LBL_NO_NOTIFICATIONS' => 'Nessuna notifica',
		),
	),
);

$languages = vtlib_getToggleLanguageInfo();
foreach ($trans as $module => $modlang) {
	foreach ($modlang as $lang => $translist) {
		if (array_key_exists($lang, $languages)) {
			foreach ($translist as $label => $translabel) {
				SDK::setLanguageEntry($module, $lang, $label, $translabel);
			}
			if ($module == 'ALERT_ARR') {
				$recalculateJsLanguage[$lang] = $lang;
			}
		}
	}
}
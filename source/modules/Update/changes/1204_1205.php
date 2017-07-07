<?php

// translations
$trans = array(
	'APP_STRINGS' => array(
		'it_it' => array(
			'AddModuleHomeView' => 'Aggiungi configurazione',
			'NewModuleHomeView' => 'Nuova configurazione',
			'LBL_NO_HOME_BLOCKS' => 'Nessun blocco impostato. Clicca "aggiungi configurazione" per crearne uno nuovo',
			'LBL_REMOVE_MODHOME_VIEW' => 'Rimuovi configurazione',
		),
		'en_us' => array(
			'AddModuleHomeView' => 'Add view',
			'NewModuleHomeView' => 'New view',
			'LBL_NO_HOME_BLOCKS' => 'No blocks configured. Click "Add view" to create a new one',
			'LBL_REMOVE_MODHOME_VIEW' => 'Remove view',
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
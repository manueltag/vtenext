<?php

// translations
$trans = array(
	'APP_STRINGS' => array(
		'it_it' => array(
			'QuickFilter' => 'Filtro veloce',
		),
		'en_us' => array(
			'QuickFilter' => 'Quick filter',
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
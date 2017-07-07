<?php

// crmv@92682

$trans = array(
	'Settings' => array(
		'it_it' => array(
			'LBL_MODULENAME_NOT_ALLOWED' => 'Il nome del modulo non è permesso. Scegli un altro nome',
		),
		'en_us' => array(
			'LBL_MODULENAME_NOT_ALLOWED' => 'The module\'s name is not allowed, please choose another name.',
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
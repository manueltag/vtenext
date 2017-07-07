<?php

/* crmv@90287 */

// translations

$trans = array(
	'Settings' => array(
		'it_it' => array(
			'LBL_IMPORT_LINKKEY_KEYFIELD' => 'Devi prima impostare un campo come chiave',
		),
		'en_us' => array(
			'LBL_IMPORT_LINKKEY_KEYFIELD' => 'You have to select a key field first',
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


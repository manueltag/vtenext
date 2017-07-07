<?php

// crmv@111580
$trans = array(
	'Settings' => array(
		'it_it' => array(
			'LBL_RESCAN_FOLDERS' => 'includendo precedenti messaggi non processati',
		),
		'en_us' => array(
			'LBL_RESCAN_FOLDERS' => 'including past skipped messages',
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

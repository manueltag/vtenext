<?php

$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';

// crmv@92218

$trans = array(
	'Import' => array(
		'it_it' => array(
			'LBL_DETECTED_ENCODING' => 'Codifica trovata',
			'LBL_CHANGE_ENCODING' => 'Cambia codifica',
		),
		'en_us' => array(
			'LBL_DETECTED_ENCODING' => 'Detected encoding',
			'LBL_CHANGE_ENCODING' => 'Change encoding',
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
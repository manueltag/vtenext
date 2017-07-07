<?php

// crmv@117880

$trans = array(
	'Settings' => array(
		'it_it' => array(
			'LBL_DIMPORT_FORMAT_NUMBER' => 'Numero',
		),
		'en_us' => array(
			'LBL_DIMPORT_FORMAT_NUMBER' => 'Numeric',
		),
	),
);
$languages = vtlib_getToggleLanguageInfo();
foreach ($trans as $module=>$modlang) {
	foreach ($modlang as $lang=>$translist) {
		if (array_key_exists($lang,$languages)) {
			foreach ($translist as $label=>$translabel) {
				SDK::setLanguageEntry($module, $lang, $label, $translabel);
			}
			if ($module == 'ALERT_ARR') {
				$recalculateJsLanguage[$lang] = $lang;
			}
		}
	}
}
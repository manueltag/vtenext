<?php

// translations
$trans = array(
	'ALERT_ARR' => array(
		'it_it' => array(
			'LBL_DISABLE_MODULE'=>'Disabilitare il modulo %s?',
			'LBL_REPORT_NAME' => 'Nome report',
			'LBL_DESCRIPTION' => 'Descrizione',
		),
		'en_us' => array(
			'LBL_DISABLE_MODULE'=>'Disable the module %s?',
			'LBL_REPORT_NAME' => 'Report name',
			'LBL_DESCRIPTION' => 'Description',
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
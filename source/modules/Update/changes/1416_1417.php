<?php

/* crmv@106900 */

$trans = array(

	'ALERT_ARR' => array(
		'it_it' => array(
			'LBL_NO_NETWORK' => 'Nessuna connessione di rete disponibile.',
		),
		'en_us' => array(
			'LBL_NO_NETWORK' => 'No network connection available.',
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


global $recalculateJsLanguage;
$recalculateJsLanguage['it_it'] = 'it_it';
$recalculateJsLanguage['en_us'] = 'en_us';
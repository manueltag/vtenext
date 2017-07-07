<?php

// fix to recalulcate js labels
global $recalculateJsLanguage;
$recalculateJsLanguage['it_it'] = 'it_it';
$recalculateJsLanguage['en_us'] = 'en_us'; 

// crmv@111926
$trans = array(
	'APP_STRINGS' => array(
		'it_it' => array(
			'LBL_TOO_MANY_INPUT_VARS' => 'Alcune variabili sono state troncate. Aumenta il valore del parametro max_input_vars nel php.ini.',
		),
		'en_us' => array(
			'LBL_TOO_MANY_INPUT_VARS' => 'Some variables were truncated. Please increase the value of max_input_vars in the php.ini.',
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

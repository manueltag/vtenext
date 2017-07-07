<?php
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';

// crmv@42752 Traduzioni

$trans = array(
	'Messages' => array(
		'it_it' => array(
			'LBL_INCLUDE_ATTACH' => 'Includi allegati',
		),
		'en_us' => array(
			'LBL_INCLUDE_ATTACH' => 'Include attachments',
		),
	),
	'APP_STRINGS' => array(
		'it_it' => array(
			'Suggested' => 'Suggeriti',
		),
		'en_us' => array(
			'Suggested' => 'Suggested',
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

?>
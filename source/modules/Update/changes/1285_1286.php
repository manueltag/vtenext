<?php
// translations
$trans = array(
	'APP_STRINGS' => array(
		'it_it' => array(
			'LBL_PM_ELEMENTS_ACTORS'=>'Partecipanti',
		),
		'en_us' => array(
			'LBL_PM_ELEMENTS_ACTORS'=>'Participants',
		),
	),
	'ALERT_ARR' => array(
		'it_it' => array(
			'LBL_PM_ELEMENTS_ACTORS'=>'Partecipanti',
		),
		'en_us' => array(
			'LBL_PM_ELEMENTS_ACTORS'=>'Participants',
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
<?php
// translations
$trans = array(
	'Processes' => array(
		'it_it' => array(
			'LBL_LOG_START_ACTIVITY'=>'ha passato il processo in questa attività dal',
			'LBL_LOG_END_ACTIVITY'=>'ha concluso l\'attività',
		),
		'en_us' => array(
			'LBL_LOG_START_ACTIVITY'=>'passed in this activity from the',
			'LBL_LOG_END_ACTIVITY'=>'end the activity',
		),
	),
	'Settings' => array(
		'it_it' => array(
			'LBL_FIELD_BUTTON'=>'Pulsante',
			'LBL_FIELD_BUTTON_ONCLICK'=>'On click',
			'LBL_FIELD_BUTTON_CODE'=>'Codice',
			'LBL_PM_LIMIT_EXCEEDED'=>'Per usare più di %s processi attivare le funzionalità extra.',
		),
		'en_us' => array(
			'LBL_FIELD_BUTTON'=>'Button',
			'LBL_FIELD_BUTTON_ONCLICK'=>'On click',
			'LBL_FIELD_BUTTON_CODE'=>'Code',
			'LBL_PM_LIMIT_EXCEEDED'=>'You have to enable extra functionalities in order to use more than %s processes.',
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
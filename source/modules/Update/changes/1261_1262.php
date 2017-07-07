<?php
// translations
$trans = array(
	'Settings' => array(
		'it_it' => array(
			'LBL_ONLY_ON_FIRST_SAVE'=>'solo al primo salvataggio',
			'LBL_EVERYTIME_RECORD_SAVED'=>'ogni volta che il record viene salvato',
			'LBL_ON_MODIFY'=>'ogni volta che il record viene modificato',
			'LBL_UNTIL_FIRST_TIME_CONDITION_TRUE'=>'solo la prima volta che la condizione risulti vera',
			'LBL_EVERY_TIME_TIME_CONDITION_TRUE'=>'ogni volta che la condizione risulti vera',
			'LBL_ON_SUBPROCESS'=>'al lancio del sottoprocesso',
			'LBL_PM_LOGS'=>'Log',
		),
		'en_us' => array(
			'LBL_ONLY_ON_FIRST_SAVE'=>'only on the first save',
			'LBL_EVERYTIME_RECORD_SAVED'=>'always when the the record is saved',
			'LBL_ON_MODIFY'=>'always when a record is modified',
			'LBL_UNTIL_FIRST_TIME_CONDITION_TRUE'=>'until the first time the condition is true',
			'LBL_EVERY_TIME_TIME_CONDITION_TRUE'=>'every time the condition is true',
			'LBL_ON_SUBPROCESS'=>'when executed subprocess',
			'LBL_PM_LOGS'=>'Logs',
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
<?php
// translations
$trans = array(
	'Settings' => array(
		'it_it' => array(
			'LBL_PM_DYNAFORM_CONDITIONALS'=>'Campi condizionali su form dinamica',
			'LBL_PM_NO_RULES'=>'Nessuna regola configurata',
			'LBL_PM_RULE'=>'Regola',
			'LBL_PM_ADVANCED_ACTIONS'=>'Avanzate',
		),
		'en_us' => array(
			'LBL_PM_DYNAFORM_CONDITIONALS'=>'Conditionals on dynamic form',
			'LBL_PM_NO_RULES'=>'No rules configured',
			'LBL_PM_RULE'=>'Rule',
			'LBL_PM_ADVANCED_ACTIONS'=>'Advanced',
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
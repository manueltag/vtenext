<?php
SDK::setProcessMakerFieldAction('vte_sum','modules/SDK/src/ProcessMaker/Utils.php','Sum (number1,number2,...)');

// translations
$trans = array(
	'Settings' => array(
		'it_it' => array(
			'LBL_PM_RESOURCE'=>'Partecipante',
			'LBL_FPOFV_CURRENT_VALUE'=>'Valore corrente',
			'LBL_PM_SDK_CUSTOM_FUNCTION'=>'Funzione SDK',
		),
		'en_us' => array(
			'LBL_PM_RESOURCE'=>'Partecipant',
			'LBL_FPOFV_CURRENT_VALUE'=>'Current value',
			'LBL_PM_SDK_CUSTOM_FUNCTION'=>'SDK Function',
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
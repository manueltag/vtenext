<?php

/* crmv@98764 */

// translations
$trans = array(
	'ALERT_ARR' => array(
		'it_it' => array(
			'MISSING_COMPARATOR'=>'Scegli una condizione di confronto',
		),
		'en_us' => array(
			'MISSING_COMPARATOR'=>'Please choose a comparison condition',
		),
	),
	'Reports' => array(
		'it_it' => array(
			'LBL_ORDER_OF_SELECTED_FIELDS'=>'Ordinamento dei campi scelti',
		),
		'en_us' => array(
			'LBL_ORDER_OF_SELECTED_FIELDS'=>'Order of selected fields',
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
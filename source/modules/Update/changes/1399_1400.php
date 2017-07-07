<?php

// crmv@104956

$adb->query("DELETE FROM {$table_prefix}_profile2utility WHERE activityid = 1");


// translations
$trans = array(

	'ALERT_ARR' => array(
		'it_it' => array(
			'LBL_TODAY' => 'Oggi',
			'LBL_CANCEL' => 'Annulla',
		),
		'en_us' => array(
			'LBL_TODAY' => 'Today',
			'LBL_CANCEL' => 'Cancel',
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
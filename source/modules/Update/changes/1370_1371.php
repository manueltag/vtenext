<?php

$ChangeLogModuleInstance = Vtiger_Module::getInstance('ChangeLog');
if ($ChangeLogModuleInstance) {
	$ChangeLogFocus = CRMEntity::getInstance('ChangeLog');
	$ChangeLogFocus->disableRelatedForAll();
}

/* crmv@104914 */

// translations
$trans = array(

	'Reports' => array(
		'it_it' => array(
			'Previous FQ' => 'Trimestre Precedente',
			'Current FQ' => 'Trimestre Attuale',
			'Next FQ' => 'Prossimo Trimestre',
			'LBL_REP_EXTRACT_QUARTER' => 'Trimestre',
		),
	),
	
	'CustomView' => array(
		'it_it' => array(
			'Previous FQ' => 'Trimestre Fiscale Precedente',
			'Current FQ' => 'Trimestre Fiscale Attuale',
			'Next FQ' => 'Prossimo Trimestre Fiscale',
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


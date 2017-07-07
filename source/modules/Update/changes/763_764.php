<?php
// crmv@43117

$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';

$trans = array(
	'Calendar' => array(
		'it_it' => array(
			'LBL_CAL_FILTER' => 'Assegnato a',
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
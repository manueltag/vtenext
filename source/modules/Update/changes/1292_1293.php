<?php
// translations
$trans = array(
	'Settings' => array(
		'it_it' => array(
			'LBL_PM_MODELER'=>'Diagramma',
			'LBL_PM_SAVE_DIAGRAM_ERROR'=>'Errore nel salvataggio',
		),
		'en_us' => array(
			'LBL_PM_MODELER'=>'Diagram',
			'LBL_PM_SAVE_DIAGRAM_ERROR'=>'Diagram save failed',
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
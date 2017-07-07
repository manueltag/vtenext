<?php

$trans = array(
	'PDFMaker' => array(
		'en_us' => array(
			'COPYRIGHT' => '',
			'PDF_MAKER' => '',
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

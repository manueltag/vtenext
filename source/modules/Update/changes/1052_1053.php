<?php
$_SESSION['modules_to_update']['SLA'] = 'packages/vte/mandatory/SLA.zip';
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';

$trans = array(
	'APP_STRINGS' => array(
		'it_it' => array(
			'LBL_RELATED_PRODUCTS' => 'Dettagli Prodotto',
		),
		'en_us' => array(
			'LBL_RELATED_PRODUCTS' => 'Product Details',
		),
		'de_de' => array(
			'LBL_RELATED_PRODUCTS' => 'Artikelliste',
		),
		'nl_nl' => array(
			'LBL_RELATED_PRODUCTS' => 'Productinformatie',
		),
		'pt_br' => array(
			'LBL_RELATED_PRODUCTS' => 'Detalhes do Produto',
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
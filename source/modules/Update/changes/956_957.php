<?php
global $adb, $table_prefix;
$visitreportInstance = Vtiger_Module::getInstance('Visitreport');
$productsInstance = Vtiger_Module::getInstance('Products');
$adb->pquery("UPDATE {$table_prefix}_relatedlists SET name = ? WHERE tabid = ? AND related_tabid = ?",array('get_related_list',$visitreportInstance->id,$productsInstance->id));

$arr = array(
	array('module'=>'APP_STRINGS','label'=>'LBL_NET_PRICE'),
	array('module'=>'Invoice','label'=>'Net Price'),
	array('module'=>'PurchaseOrder','label'=>'Net Price'),
	array('module'=>'Quotes','label'=>'Net Price'),
	array('module'=>'SalesOrder','label'=>'Net Price'),
	array('module'=>'Products','label'=>'Net Price'),
	array('module'=>'Services','label'=>'Net Price'),
);
foreach($arr as $a) {
	SDK::setLanguageEntries($a['module'], $a['label'], array(
		'it_it'=>'Prezzo',
		'en_us'=>'Price',
		'nl_nl'=>'Prijs',
		'de_de'=>'Preis',
		'pt_br'=>'Preço',
	));
}

$arr = array(
	array('module'=>'APP_STRINGS','label'=>'LBL_NET_TOTAL'),
	array('module'=>'APP_STRINGS','label'=>'total net'),
	array('module'=>'Invoice','label'=>'Sub Total'),
	array('module'=>'PurchaseOrder','label'=>'Sub Total'),
	array('module'=>'Quotes','label'=>'Sub Total'),
	array('module'=>'SalesOrder','label'=>'Sub Total'),
	array('module'=>'Ddt','label'=>'Sub Total'),
	array('module'=>'PDFMaker','label'=>'LBL_VARIABLE_SUM'),
);
foreach($arr as $a) {
	SDK::setLanguageEntries($a['module'], $a['label'], array(
		'it_it'=>'Totale',
		'en_us'=>'Total',
		'nl_nl'=>'Totaal',
		'de_de'=>'Summe',
		'pt_br'=>'Total',
	));
}

$arr = array(
	array('module'=>'APP_STRINGS','label'=>'LBL_GRAND_TOTAL'),
	array('module'=>'APP_STRINGS','label'=>'Total'),
	array('module'=>'Reports','label'=>'LBL_GRAND_TOTAL'),
	array('module'=>'PDFMaker','label'=>'LBL_VARIABLE_TOTALSUM'),
	array('module'=>'Invoice','label'=>'Total'),
	array('module'=>'PurchaseOrder','label'=>'Total'),
	array('module'=>'Quotes','label'=>'Total'),
	array('module'=>'SalesOrder','label'=>'Total'),
);
foreach($arr as $a) {
	SDK::setLanguageEntries($a['module'], $a['label'], array(
		'it_it'=>'Totale Documento',
		'en_us'=>'Grand Total',
		'nl_nl'=>'Algemeen Totaal',
		'de_de'=>'Gesamtsumme',
		'pt_br'=>'Total Geral',
	));
}
?>
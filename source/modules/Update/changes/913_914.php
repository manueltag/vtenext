<?php
$_SESSION['modules_to_update']['Visitreport'] = 'packages/vte/mandatory/Visitreport.zip';

global $adb, $table_prefix;
$adb->pquery("UPDATE {$table_prefix}_field SET presence = ? WHERE tabid = ? AND fieldname = ?",array(0,29,'yahoo_id'));

$result = $adb->query("select * from ".$table_prefix."_organizationdetails");
if ($result && $adb->num_rows($result) > 0) {
	$organization_name = $adb->query_result($result,0,'organizationname');
	if ($organization_name == 'Crmvillage') {
		$adb->pquery("update ".$table_prefix."_organizationdetails set
			organizationname = ?,
			address = ?,
			city = ?,
			state = ?,
			country = ?,
			code = ?,
			phone = ?,
			fax = ?,
			website = ?,
			logoname = ?,
			crmv_vat_registration_number = ?
			where organizationname = ?",
		array('Acme','12 W. Coyote','San Antonio','Texas','U.S.A.','78201','(210) 280-0000','(210) 280-0001','www.acme.inc','logo.jpg','1234567890',$organization_name));
	}
}

$package = new Vtiger_Package();
$moduleInstance = Vtiger_Module::getInstance('PDFMaker');
$package->update($moduleInstance,'packages/vte/mandatory/PDFMaker.zip');

require_once('modules/PDFMaker/PDFMaker.php');
$pdfmaker = new PDFMaker();
$pdfmaker->importStandardLayouts();

SDK::setLanguageEntry('Ddt', 'it_it', 'Product Name', 'Nome Prodotto');
SDK::setLanguageEntry('Ddt', 'en_us', 'Product Name', 'Product Name');
SDK::setLanguageEntry('Ddt', 'de_de', 'Product Name', 'Produkt');
SDK::setLanguageEntry('Ddt', 'nl_nl', 'Product Name', 'Productnaam');
SDK::setLanguageEntry('Ddt', 'pt_br', 'Product Name', 'Nome Produto');
SDK::setLanguageEntry('Ddt', 'it_it', 'Service Name', 'Nome Servizio');
SDK::setLanguageEntry('Ddt', 'en_us', 'Service Name', 'Service Name');
SDK::setLanguageEntry('Ddt', 'de_de', 'Service Name', 'Service Name');
SDK::setLanguageEntry('Ddt', 'nl_nl', 'Service Name', 'Service naam');
SDK::setLanguageEntry('Ddt', 'pt_br', 'Service Name', 'Nome Serviço');
SDK::setLanguageEntry('Ddt', 'it_it', 'Quantity', 'Quantità');
SDK::setLanguageEntry('Ddt', 'en_us', 'Quantity', 'Quantity');
SDK::setLanguageEntry('Ddt', 'de_de', 'Quantity', 'Menge');
SDK::setLanguageEntry('Ddt', 'nl_nl', 'Quantity', 'Aantallen');
SDK::setLanguageEntry('Ddt', 'pt_br', 'Quantity', 'Quantidade');
SDK::setLanguageEntry('APP_STRINGS', 'en_us', 'LBL_ORGANIZATION_VAT', 'VAT Registration Number:');
SDK::setLanguageEntry('APP_STRINGS', 'en_us', 'Billing Address', 'Billing Address');
SDK::setLanguageEntry('APP_STRINGS', 'en_us', 'Shipping Address', 'Shipping Address');

$result = $adb->pquery("SELECT relation_id, name FROM {$table_prefix}_relatedlists WHERE tabid = 14 AND related_tabid = 14 AND label = ?",array('Product Bundles'));
if ($result && $adb->num_rows($result) > 0) {
	$relation_id = $adb->query_result($result,0,'relation_id');
	$method = $adb->query_result($result,0,'name');
	SDK::setTurboliftCount($relation_id, $method);
}
$result = $adb->query("SELECT relation_id, name FROM {$table_prefix}_relatedlists WHERE tabid = 14 AND related_tabid = 14 AND label LIKE 'Parent Product%'");
if ($result && $adb->num_rows($result) > 0) {
	$relation_id = $adb->query_result($result,0,'relation_id');
	$method = $adb->query_result($result,0,'name');
	SDK::setTurboliftCount($relation_id, $method);
}

$result = $adb->pquery("SELECT fieldid FROM {$table_prefix}_field WHERE tabid = ? and fieldname = ? and quickcreate not in (?,?)",array(14,'discontinued',0,2));
if ($result && $adb->num_rows($result) > 0) {
	$fieldid = $adb->query_result($result,0,'fieldid');
	$fieldInstance = Vtiger_Field::getInstance($fieldid);
	$adb->pquery("update {$table_prefix}_field set quickcreate = ?, quickcreatesequence = ? where fieldid = ?",array(0,$fieldInstance->__getNextQuickCreateSequence(),$fieldid));
}
?>
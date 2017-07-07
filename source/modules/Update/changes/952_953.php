<?php
$_SESSION['modules_to_update']['Assets'] = 'packages/vte/mandatory/Assets.zip';
$_SESSION['modules_to_update']['Services'] = 'packages/vte/mandatory/Services.zip';
$_SESSION['modules_to_update']['SLA'] = 'packages/vte/mandatory/SLA.zip';
$_SESSION['modules_to_update']['Timecards'] = 'packages/vte/mandatory/Timecards.zip';

global $table_prefix;
if (!Vtiger_Utils::CheckTable($table_prefix.'_import_queue')) {
	Vtiger_Utils::CreateTable(
		$table_prefix.'_import_queue',
		"importid I(11) NOTNULL PRIMARY,
			userid I(11) NOTNULL,
			tabid I(11) NOTNULL,
			field_mapping X,
			default_values X,
			merge_type I(11),
			merge_fields X,
			status I(11) DEFAULT 0",
		true);
}

$translations = array(
	array('module'=>'APP_STRINGS','label'=>'Add Ddt','trans_label'=>'Add Delivery Note'),
	array('module'=>'Ddt','label'=>'Ddt','trans_label'=>'Delivery Notes'),
	array('module'=>'Ddt','label'=>'SINGLE_Ddt','trans_label'=>'Delivery Note'),
	array('module'=>'Ddt','label'=>'LBL_DDT_INFORMATION','trans_label'=>'Delivery Note Information'),
	array('module'=>'Ddt','label'=>'Ddt No','trans_label'=>'Delivery Note No'),
	array('module'=>'Ddt','label'=>'Data Ddt','trans_label'=>'Delivery Note Date'),
	array('module'=>'Ddt','label'=>'Causale','trans_label'=>'Reason'),
	array('module'=>'Ddt','label'=>'Trasporto','trans_label'=>'Carriage'),
	array('module'=>'Ddt','label'=>'Condizione di Consegna','trans_label'=>'Term of Delivery'),
	array('module'=>'Ddt','label'=>'Conto deposito','trans_label'=>'Deposit account'),
	array('module'=>'Ddt','label'=>'Reso da conto deposito','trans_label'=>'Returned goods sent for deposit account'),
	array('module'=>'Ddt','label'=>'Conto lavorazione','trans_label'=>'Manufacturing purposes'),
	array('module'=>'Ddt','label'=>'Reso da conto lavorazione','trans_label'=>'Returned goods sent for manufacturing purposes'),
	array('module'=>'Ddt','label'=>'Prestito d uso','trans_label'=>'Loan for use'),
	array('module'=>'Ddt','label'=>'Reso da prestito d uso','trans_label'=>'Returned goods sent for loan'),
	array('module'=>'Ddt','label'=>'Conto riparazione','trans_label'=>'Returned for repair'),
	array('module'=>'Ddt','label'=>'Reso da conto riparazione','trans_label'=>'Returned goods sent for repair'),
	array('module'=>'Ddt','label'=>'Conto manutenzione','trans_label'=>'Maintenance service'),
	array('module'=>'Ddt','label'=>'Reso da conto manutenzione','trans_label'=>'Returned goods sent for maintenance'),
	array('module'=>'Ddt','label'=>'Conto visione','trans_label'=>'Free evaluation'),
	array('module'=>'Ddt','label'=>'Reso da conto visione','trans_label'=>'Returned goods sent for evaluation'),
	array('module'=>'Ddt','label'=>'Omaggio','trans_label'=>'Free'),
	array('module'=>'Ddt','label'=>'Campionario','trans_label'=>'Sample case'),
	array('module'=>'Ddt','label'=>'Vendita','trans_label'=>'Trade'),
	array('module'=>'Ddt','label'=>'Porto franco','trans_label'=>'Carriage free'),
	array('module'=>'Ddt','label'=>'Porto assegnato','trans_label'=>'Carriage forward'),
	array('module'=>'Ddt','label'=>'Franco magazzino venditore','trans_label'=>'Ex-Works seller\'s warehouse'),
	array('module'=>'Ddt','label'=>'Franco magazzino compratore','trans_label'=>' Ex-Works buyer\'s warehouse'),
);
foreach($translations as $t) {
	SDK::setLanguageEntry($t['module'], 'en_us', $t['label'], $t['trans_label']);
}
?>
<?php

$adb->addColumnToTable($table_prefix.'_contpotentialrel', 'main_contact', 'I(1)', 'DEFAULT 0');
$adb->addColumnToTable($table_prefix.'_contpotentialrel', 'contact_role', 'C(255)');

$schema_table =
'<schema version="0.3">
	<table name="'.$table_prefix.'_accpotentialrel">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="accountid" type="R" size="19">
			<KEY/>
		</field>
		<field name="potentialid" type="R" size="19">
			<KEY/>
		</field>
		<field name="partner_role" type="C" size="255"/>
		<index name="accpotrel_potentialid_idx">
			<col>potentialid</col>
		</index>
	</table>
</schema>';
if(!Vtiger_Utils::CheckTable($table_prefix.'_accpotentialrel')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

$res = $adb->query("select * from {$table_prefix}_relatedlists where tabid = 2 and related_tabid = 6 and name = 'get_related_list'");
if ($res && $adb->num_rows($res) == 0) {
	$acc = Vtiger_Module::getInstance('Accounts');
	$pot = Vtiger_Module::getInstance('Potentials');
	$pot->setRelatedList($acc, 'Accounts', Array('SELECT'), 'get_related_list');
}

// create fields
$fields = array(
	'contact_roles'		=> array('module'=>'Potentials', 'block'=>'LBL_OPPORTUNITY_INFORMATION', 'name'=>'contact_roles', 'label'=>'ContactRoles',   'table'=>"{$table_prefix}_potential",	'columntype'=>'C(255)', 'typeofdata'=>'V~O',    'uitype'=>15, 'picklist'=>array('BusinessUser', 'Manager', 'PurchaseAgent', 'PurchaseManager', 'Examiner', 'SponsorManager', 'Consultant', 'TechPurchaseAgent', 'Other'), 'readonly'=>100, 'presence'=>1),
	'partner_roles'		=> array('module'=>'Potentials', 'block'=>'LBL_OPPORTUNITY_INFORMATION', 'name'=>'partner_roles', 'label'=>'PartnerRoles',   'table'=>"{$table_prefix}_potential",	'columntype'=>'C(255)', 'typeofdata'=>'V~O',    'uitype'=>15, 'picklist'=>array('Advertiser', 'Agency', 'Broker', 'Consultant', 'Dealer', 'Developer', 'Distributor', 'Institution', 'Supplier', 'SystemIntegrator', 'Reseller', 'Other'), 'readonly'=>100, 'presence'=>1),
);

$fieldRet = Update::create_fields($fields);

// new module CreditLines
/*
require_once('vtlib/Vtiger/Package.php');
$package = new Vtiger_Package();
$package->importByManifest('ProductLines');
*/

// -----------

// translations
$trans = array(
	'APP_STRINGS' => array(
		'it_it' => array(
			'Relations' => 'Relazioni',
			'Owners' => 'Assegnatari',
			'Competitors' => 'Concorrenti',
		),
		'en_us' => array(
			'Relations' => 'Relations',
			'Owners' => 'Owners',
			'Competitors' => 'Competitors',
		),
	),
	'Potentials' => array(
		'it_it' => array(
			'ContactRoles' => 'Ruoli Contatto',
			'BusinessUser' => 'Utente business',
			'Manager' => 'Responsabile',
			'PurchaseAgent' => 'Addetto agli acquisti',
			'PurchaseManager' => 'Responsabile acquisti',
			'Examiner' => 'Esaminatore',
			'SponsorManager' => 'Sponsor responsabile',
			'Consultant' => 'Consulente',
			'TechPurchaseAgent' => 'Tecnico addetto agli acquisti',
			'Other' => 'Altro',
			'Advertiser' => 'Inserzionista',
			'Agency' => 'Agenzia',
			'Broker' => 'Broker',
			'Dealer' => 'Concessionario',
			'Developer' => 'Sviluppatore',
			'Distributor' => 'Distributore',
			'Institution' => 'Istituzione',
			'Supplier' => 'Prestatore',
			'SystemIntegrator' => 'Integratore di sistemi',
			'Reseller' => 'Rivenditore',
			'MainAccountContacts' => 'Contatti dell\'azienda principale',
		),
		'en_us' => array(
			'ContactRoles' => 'Contact Roles',
			'BusinessUser' => 'Utente business',
			'Manager' => 'Manager',
			'PurchaseAgent' => 'Purchase agent',
			'PurchaseManager' => 'Purchase manager',
			'Examiner' => 'Examiner',
			'SponsorManager' => 'Sponsor manager',
			'Consultant' => 'Consultant',
			'TechPurchaseAgent' => 'Purchase agent Tech dep.',
			'Other' => 'Other',
			'Advertiser' => 'Advertiser',
			'Agency' => 'Agency',
			'Broker' => 'Broker',
			'Dealer' => 'Dealer',
			'Developer' => 'Developer',
			'Distributor' => 'Distributor',
			'Institution' => 'Institution',
			'Supplier' => 'Supplier',
			'SystemIntegrator' => 'System integrator',
			'Reseller' => 'Reseller',
			'MainAccountContacts' => 'Main account\'s contacts',
		),
	),
	'Products' => array(
		'it_it' => array(
			'ProductLine' => 'Linea di Prodotto',
		),
		'en_us' => array(
			'ProductLine' => 'Product Line',
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
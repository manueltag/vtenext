<?php
$_SESSION['modules_to_update']['Services'] = 'packages/vte/mandatory/Services.zip';
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['Ddt'] = 'packages/vte/mandatory/Ddt.zip';
$_SESSION['modules_to_update']['Visitreport'] = 'packages/vte/mandatory/Visitreport.zip';
$_SESSION['modules_to_update']['Sms'] = 'packages/vte/mandatory/Sms.zip';
$_SESSION['modules_to_update']['Fax'] = 'packages/vte/mandatory/Fax.zip';
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter','Targets'));

SDK::clearSessionValues();

// Fix related lists
$replaceList = array(
	array('from'=>'get_opportunities', 'to'=>'get_dependents_list', 'mods' => array('Campaigns', 'Accounts', 'Contacts')),
	array('from'=>'get_products', 'to'=>'get_dependents_list', 'mods' => array('Vendors')),
	array('from'=>'get_purchase_orders', 'to'=>'get_dependents_list', 'mods' => array('Vendors', 'Products', 'Contacts')),
	array('from'=>'get_products', 'to'=>'get_related_list', 'mods' => array('Potentials', 'Leads', 'Contacts', 'Visitreport')),
	array('from'=>'get_quotes', 'to'=>'get_dependents_list', 'mods' => array('Potentials', 'Accounts', 'Products', 'Contacts')),
	array('from'=>'get_salesorder', 'to'=>'get_dependents_list', 'mods' => array('Potentials', 'Accounts', 'Products', 'Contacts')),
	array('from'=>'get_invoices', 'to'=>'get_dependents_list', 'mods' => array('Accounts', 'Products', 'Contacts')),
	array('from'=>'get_contacts', 'to'=>'get_dependents_list', 'mods' => array('Accounts')),
	array('from'=>'get_leads', 'to'=>'get_related_list', 'mods' => array('Products')),
	array('from'=>'get_accounts', 'to'=>'get_related_list', 'mods' => array('Products')),
	array('from'=>'get_contacts', 'to'=>'get_related_list', 'mods' => array('Products')),
	array('from'=>'get_opportunities', 'to'=>'get_related_list', 'mods' => array('Products')),
	array('from'=>'get_tickets', 'to'=>'get_dependents_list', 'mods' => array('Products', 'Contacts')),
	array('from'=>'get_targets', 'to'=>'get_related_list', 'mods' => array('Targets')),
);

foreach ($replaceList as $rinfo) {
	$adb->pquery("update {$table_prefix}_relatedlists set name = ? where name = ? and tabid in (".generateQuestionMarks($rinfo['mods']).")", array(
		$rinfo['to'],
		$rinfo['from'],
		array_map('getTabid',$rinfo['mods']),
	));
}

$adb->pquery("delete from {$table_prefix}_relatedlists where tabid = 9 and name = ?", array('get_attachments'));


// for the related SMS / FAX, insert the link
$res = $adb->query("select * from {$table_prefix}_relatedlists where name = 'get_sms'");
if ($res) {
	while ($row = $adb->FetchByAssoc($res, -1, false)) {
		$tabid = $row['tabid'];
		$relmod = vtlib_getModuleNameById($tabid);
		$modInstance = Vtiger_Module::getInstance($relmod);
		Vtiger_Link::addLink($modInstance->id, 'DETAILVIEWBASIC', 'TITLE_COMPOSE_SMS', "javascript:fnvshobj(this,'sendsms_cont');sendsms('\$MODULE\$','\$RECORD\$');", '', 1);
	}
}

$res = $adb->query("select * from {$table_prefix}_relatedlists where name = 'get_faxes'");
if ($res) {
	while ($row = $adb->FetchByAssoc($res, -1, false)) {
		$tabid = $row['tabid'];
		$relmod = vtlib_getModuleNameById($tabid);
		$modInstance = Vtiger_Module::getInstance($relmod);
		Vtiger_Link::addLink($modInstance->id, 'DETAILVIEWBASIC', 'TITLE_COMPOSE_FAX', "javascript:fnvshobj(this,'sendfax_cont');sendfax('\$MODULE\$','\$RECORD\$');", '', 1);
	}
}

$trans = array(
	'APP_STRINGS' => array(
		'it_it' => array(
			'TITLE_COMPOSE_SMS' => 'Componi Sms',
			'TITLE_COMPOSE_FAX' => 'Componi Fax',
			'LBL_ADD_SELECTED' => 'Aggiungi selezionati',
		),
		'en_us' => array(
			'TITLE_COMPOSE_SMS' => 'Compose Sms',
			'TITLE_COMPOSE_FAX' => 'Compose Fax',
			'LBL_ADD_SELECTED' => 'Add selected',
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
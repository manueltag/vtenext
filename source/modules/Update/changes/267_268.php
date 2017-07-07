<?php
require_once('vtlib/Vtiger/Package.php');
require_once('vtlib/Vtiger/Language.php');
global $adb;

//crmv@18160
$_SESSION['modules_to_update']['Services'] = 'packages/vte/mandatory/Services.zip';
$_SESSION['modules_to_update']['ServiceContracts'] = 'packages/vte/mandatory/ServiceContracts.zip';
if (!in_array('ServiceContracts',array_keys($_SESSION['modules_to_install']))){
	$modulo = Vtiger_Module::getInstance('ServiceContracts');
	$block = Vtiger_Block::getInstance('LBL_SERVICE_CONTRACT_INFORMATION',$modulo);
	$field = new Vtiger_Field();
	$field->readonly = 1;
	$field->name = 'service_id';
	$field->label= 'Service';
	$field->table = $modulo->basetable;
	//crmv@18160
	$field->columntype = 'I(19)';
	//crmv@18160 end
	$field->typeofdata = 'V~O';
	$field->uitype = 10;
	$block->addField($field);
	$field->setRelatedModules(Array('Services'));
	$service = Vtiger_Module::getInstance('Services');
	$service->setRelatedList($modulo, 'Service Contracts', Array('ADD'), 'get_dependents_list');
	
	$modulo = Vtiger_Module::getInstance('ServiceContracts');
	$block = Vtiger_Block::getInstance('LBL_SERVICE_CONTRACT_INFORMATION',$modulo);
	$field = new Vtiger_Field();
	$field->readonly = 1;
	$field->name = 'sorder_id';
	$field->label= 'Sales Order';
	$field->table = $modulo->basetable;
	$field->columntype = 'I(19)';
	$field->typeofdata = 'V~O';
	$field->uitype = 10;
	$block->addField($field);
	$field->setRelatedModules(Array('SalesOrder'));
	$sorder = Vtiger_Module::getInstance('SalesOrder');
	$sorder->setRelatedList($modulo, 'Service Contracts', '', 'get_dependents_list');
}
$uploadfilename = 'packages/vte/mandatory/Assets.zip';
$package = new Vtiger_Package();
$Vtiger_Utils_Log = true;
$package->change = '267_268';
$package->import($uploadfilename);
//setto il path a false, cosicchè capisco che è stato gia' installato in corso di aggiornamento
$_SESSION['modules_to_install']['Assets'] = false;
$moduloAssets = Vtiger_Module::getInstance('Assets');
$tabid = $moduloAssets->id;
$visible=1;
$query = $adb->pquery("SELECT max(sequence) AS max_tabseq FROM vtiger_customerportal_tabs",array());
$maxtabseq = $adb->query_result($query, 0, 'max_tabseq');
$newTabSeq = ++$maxtabseq;
$moduloAssets = Vtiger_Module::getInstance('Assets');
$tabid = $moduloAssets->id;
$adb->pquery("INSERT INTO vtiger_customerportal_tabs(tabid, visible, sequence) VALUES(?,?,?)", array($tabid,$visible,$newTabSeq));
//crmv@18160 end
$flds = "	
	accountid I(19) NOTNULL PRIMARY,
	sorderid I(19) NOTNULL PRIMARY,
	id I(19) NOTNULL PRIMARY,
	type C(255) DEFAULT NULL
";
$sqlarray = $adb->datadict->CreateTableSQL('crmv_inventorytoacc', $flds);
$adb->datadict->ExecuteSQLArray($sqlarray);
$adb->query("UPDATE vtiger_relatedlists SET name = 'get_services' WHERE tabid = 6 AND label = 'Services'");
?>
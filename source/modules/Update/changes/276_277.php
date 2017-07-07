<?php
require_once('vtlib/Vtiger/Package.php');
require_once('vtlib/Vtiger/Language.php');
global $adb;

//inserimento campo fornitori in modulo Contatti
$modulo = Vtiger_Module::getInstance('Contacts');

$block = Vtiger_Block::getInstance('LBL_CONTACT_INFORMATION',$modulo);
$field = new Vtiger_Field();
$field->readonly = 1;
$field->name = 'vendor_id';
$field->label= 'Vendor Name';
$field->table = $modulo->basetable;
//crmv@18160
$field->columntype = 'I(19)';
//crmv@18160 end
$field->typeofdata = 'I~O';
$field->uitype = 10;
$block->addField($field);
$field->setRelatedModules(Array('Vendors'));

$rel = Vtiger_Module::getInstance('Vendors');
//cancello la related list vecchia
$rel->unsetRelatedList($modulo, 'Contacts', 'get_contacts');
//creo la related list nuova
$rel->setRelatedList($modulo, 'Contacts', Array('ADD'), 'get_dependents_list');

//alter table
$adb->query("UPDATE vtiger_field SET fieldlabel='Social Security number' WHERE fieldid='20'");

//inst: 
//schema

?>
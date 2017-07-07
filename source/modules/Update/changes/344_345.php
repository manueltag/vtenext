<?php
global $adb;
$adb->query("DELETE FROM vtiger_settings_field WHERE name = 'LBL_PDFCONFIGURATOR'");

include_once('vtlib/Vtiger/Module.php');
$modulo = Vtiger_Module::getInstance('ServiceContracts');
$adb->query("UPDATE vtiger_relatedlists SET label='HelpDesk' WHERE tabid = $modulo->id and related_tabid = 13");
$block = Vtiger_Block::getInstance('LBL_SERVICE_CONTRACT_INFORMATION',$modulo);
$field = new Vtiger_Field();
$field->name = 'residual_units';
$field->label= 'Residual Units';
$field->table = $modulo->basetable;
$field->columntype = 'N(5.2)';
$field->typeofdata = 'N~O';
$field->uitype = 7;
$field->displaytype = 1;
$field->masseditable = 0;
$field->readonly = 99;
$block->addField($field);
$_SESSION['modules_to_update']['ServiceContracts'] = 'packages/vte/mandatory/ServiceContracts.zip';
$_SESSION['modules_to_update']['Services'] = 'packages/vte/mandatory/Services.zip';
?>
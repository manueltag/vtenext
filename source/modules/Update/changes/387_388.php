<?php
global $adb;
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
@unlink('modules/Utilities/winMaxOpen.php');

$modulo = Vtiger_Module::getInstance('Users');
$block = Vtiger_Block::getInstance('LBL_USERLOGIN_ROLE',$modulo);
$field = new Vtiger_Field();
$field->name = 'menu_view';
$field->label= 'Menu View';
$field->table = $modulo->basetable;
$field->uitype = 15;
$field->typeofdata = 'V~O';
$field->columntype = 'C(255)';
$block->addField($field);
$field->setPicklistValues(Array('Large Menu','Small Menu'));
$adb->query("UPDATE vtiger_users SET menu_view = 'Large Menu'");

$em = new VTEventsManager($adb);
$em->registerHandler('vtiger.entity.beforesave','modules/Users/MenuViewHandler.php','MenuViewHandler');
?>
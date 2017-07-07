<?php
require_once('vtlib/Vtiger/Module.php');
include_once('vtlib/Vtiger/Menu.php');
$Vtiger_Utils_Log = true;
global $adb;

$tt_module = Vtiger_Module::getInstance('HelpDesk');
$field1 = Vtiger_Field::getInstance('hours',$tt_module);
if (!$field1) {
	$block = Vtiger_Block::getInstance('LBL_TICKET_INFORMATION',$tt_module);
	$field = new Vtiger_Field();
	$field->name = 'hours';
	$field->column = 'hours';
	$field->label= 'Hours';
	$field->table = $modulo->basetable;
	$field->columntype = 'N(5.2)';
	$field->typeofdata = 'N~O';
	$field->uitype = 1;
	$block->addField($field);
	$field1 = Vtiger_Field::getInstance('hours',$tt_module);
}
$field2 = Vtiger_Field::getInstance('days',$tt_module);
if (!$field2) {
	$field = new Vtiger_Field();
	$field->name = 'days';
	$field->column = 'days';
	$field->label= 'Days';
	$field->table = $modulo->basetable;
	$field->columntype = 'N(5.2)';
	$field->typeofdata = 'N~O';
	$field->uitype = 1;
	$block->addField($field);
	$field2 = Vtiger_Field::getInstance('days',$tt_module);
}
$field1->setHelpInfo('LBL_HELPINFO_HOURS');
$field2->setHelpInfo('LBL_HELPINFO_DAYS');

$moduleInstanceP = Vtiger_Module::getInstance('Potentials');
$moduleInstanceH = Vtiger_Module::getInstance('HelpDesk');
$moduleInstanceE = Vtiger_Module::getInstance('Emails');
$moduleInstanceP->setRelatedList($moduleInstanceE, 'Emails', Array(''), 'get_emails');
$moduleInstanceH->setRelatedList($moduleInstanceE, 'Emails', Array(''), 'get_emails');

$menu = Vtiger_Menu::getInstance('Projects');
if ($menu){
	if (Vtiger_Module::getInstance('ProjectMilestone'))
		$menu->removeModule(Vtiger_Module::getInstance('ProjectMilestone'));
	if (Vtiger_Module::getInstance('ProjectTask'))
		$menu->removeModule(Vtiger_Module::getInstance('ProjectTask'));
	if (Vtiger_Module::getInstance('ProjectPlan'))
		$menu->removeModule(Vtiger_Module::getInstance('ProjectPlan'));
	$adb->query("UPDATE vtiger_parenttab SET parenttab_label = 'ProjectPlan' WHERE parenttab_label = 'Projects'");
	$menu = Vtiger_Menu::getInstance('ProjectPlan');
	if (Vtiger_Module::getInstance('ProjectMilestone'))
		$menu->addModule(Vtiger_Module::getInstance('ProjectMilestone'));
	if (Vtiger_Module::getInstance('ProjectTask'))
		$menu->addModule(Vtiger_Module::getInstance('ProjectTask'));
	if (Vtiger_Module::getInstance('ProjectPlan'))
		$menu->addModule(Vtiger_Module::getInstance('ProjectPlan'));
	create_tab_data_file();
}
?>
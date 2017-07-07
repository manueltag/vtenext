<?php
$_SESSION['modules_to_update']['Projects'] = 'packages/vte/mandatory/Projects.zip';

global $adb;
include_once('vtlib/Vtiger/Module.php');
$modulo = Vtiger_Module::getInstance('Events');
$res = $adb->pquery('select block from vtiger_field where tabid = 16 and fieldname = ?',array('date_start'));
$blockid = $adb->query_result($res,0,'block');
$block = Vtiger_Block::getInstance($blockid);
$field = new Vtiger_Field();
$field->name = 'is_all_day_event';
$field->table = 'vtiger_activity';
$field->label= 'All day';
$field->uitype = 56;
$field->typeofdata = 'C~O';
$field->columntype = 'I(1)';
$block->addField($field);
?>
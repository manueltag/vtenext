<?php
include_once('include/utils/utils.php');
global $adb;
$instanceHelpDesk = Vtiger_Module::getInstance('HelpDesk');
$instanceDocuments = Vtiger_Module::getInstance('Documents');
$adb->pquery('update vtiger_entityname set fieldname = ? where tabid = ? and fieldname = ?',array('ticket_title',$instanceHelpDesk->id,'title'));
$adb->pquery('update vtiger_entityname set fieldname = ? where tabid = ? and fieldname = ?',array('notes_title',$instanceDocuments->id,'title'));

$_SESSION['modules_to_update']['Timecards'] = 'packages/vte/mandatory/Timecards.zip';
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
?>
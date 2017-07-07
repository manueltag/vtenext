<?php
// Turn on debugging level
include_once('vtlib/Vtiger/Utils.php');
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

function setTicketStatusPicklistValues($values) {
	//crmv@64964
	include_once('vtlib/Vtiger/Field.php');
	$fieldInstance = Vtiger_Field::getInstance('ticketstatus',Vtiger_Module::getInstance('HelpDesk'));
	$fieldInstance->setPicklistValues($values);
	//crmv@64964e
}
setTicketStatusPicklistValues( Array ('Maintain') );

$moduleHD = Vtiger_Module::getInstance('HelpDesk');
$moduleTC = Vtiger_Module::getInstance('Timecards');
$moduleHD->setRelatedList($moduleTC, 'Timecards', Array('ADD'),'get_timecards');

//$moduleAccounts = Vtiger_Module::getInstance('Accounts');
//$moduleAccounts->setRelatedList($module, 'Timecards', Array('ADD','SELECT'));

//crmv fix ticketstatus
$adb->pquery("update ".$table_prefix."_field set uitype = ? where tablename = ? and fieldname = ?",
				Array(15, $table_prefix.'_timecards', 'ticketstatus'));
?>
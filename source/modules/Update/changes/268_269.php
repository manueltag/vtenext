<?php
require_once('vtlib/Vtiger/Package.php');
require_once('vtlib/Vtiger/Language.php');
global $adb;

if (!in_array('Sms',array_keys($_SESSION['modules_to_install']))){
	$moduleInstance = Vtiger_Module::getInstance('Sms');
	require_once('vtlib/Vtiger/Menu.php');
	$menu = Vtiger_Menu::getInstance('Tools');
	$menu->addModule($moduleInstance);
	Vtiger_Access::setDefaultSharing($moduleInstance);
	
	$moduloSms = Vtiger_Module::getInstance('Sms');
	$tabid = $moduloSms->id;
	$result = $adb->query("SELECT block FROM vtiger_field WHERE tabid = $tabid AND fieldname = 'date_start'");
	$block = $adb->query_result($result,0,'block');
	
	$adb->query("insert into vtiger_entityname (tabid, modulename, tablename, fieldname, entityidfield, entityidcolumn) values($tabid,'Sms','vtiger_crmentity','description','crmid','crmid')");
	$adb->query("UPDATE vtiger_tab SET ownedby='0' WHERE tabid=$tabid");
	$adb->query("UPDATE vtiger_field SET block = $block WHERE tabid = $tabid");
	$adb->query("UPDATE vtiger_field SET fieldlabel = 'Date Sent' WHERE tabid = $tabid AND fieldname = 'date_start'");
	$adb->query("UPDATE vtiger_field SET displaytype = 3 WHERE tabid = $tabid AND fieldname IN ('filename','time_start','subject')");
	$adb->query("UPDATE vtiger_field SET sequence = 3 WHERE tabid = $tabid and fieldname = 'date_start'");
	$adb->query("UPDATE vtiger_field SET sequence = 4 WHERE tabid = $tabid and fieldname = 'parent_type'");
	$adb->query("UPDATE vtiger_field SET sequence = 5 WHERE tabid = $tabid and fieldname = 'activitytype'");
	$adb->query("UPDATE vtiger_field SET sequence = 6 WHERE tabid = $tabid and fieldname = 'assigned_user_id'");
	$adb->query("UPDATE vtiger_field SET sequence = 7 WHERE tabid = $tabid and fieldname = 'modifiedtime'");
	$adb->query("UPDATE vtiger_field SET sequence = 2 WHERE tabid = $tabid and fieldname = 'parent_id'");
	$adb->query("UPDATE vtiger_field SET sequence = 11 WHERE tabid = $tabid and fieldname = 'subject'");
	$adb->query("UPDATE vtiger_field SET sequence = 8 WHERE tabid = $tabid and fieldname = 'createdtime'");
	$adb->query("UPDATE vtiger_field SET sequence = 10 WHERE tabid = $tabid and fieldname = 'filename'");
	$adb->query("UPDATE vtiger_field SET sequence = 9 WHERE tabid = $tabid and fieldname = 'time_start'");
	$adb->query("UPDATE vtiger_field SET sequence = 1 WHERE tabid = $tabid and fieldname = 'description'");
	$adb->query("DELETE FROM vtiger_blocks WHERE tabid = $tabid AND blockid != $block");
	
	require_once('vtlib/Vtiger/Filter.php');
	require_once('vtlib/Vtiger/Field.php');
	$filter = Vtiger_Filter::getInstance('All',$moduleInstance);
	$filter->delete();
	$filter1 = new Vtiger_Filter();
	$filter1->name = 'All';
	$filter1->isdefault = true;
	$moduleInstance->addFilter($filter1);
	$filter1->addField(Vtiger_Field::getInstance('description',$moduleInstance),1)->addField(Vtiger_Field::getInstance('parent_id',$moduleInstance),2)->addField(Vtiger_Field::getInstance('date_start',$moduleInstance),3)->addField(Vtiger_Field::getInstance('assigned_user_id',$moduleInstance),4);
}

//crmv@18160
$_SESSION['modules_to_update']['Sms'] = 'packages/vte/mandatory/Sms.zip';
$_SESSION['modules_to_update']['Mobile'] = 'packages/vte/mandatory/Mobile.zip';
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['Visitreport'] = 'packages/vte/mandatory/Visitreport.zip';
if (!in_array('Visitreport',array_keys($_SESSION['modules_to_install']))){
	$moduloVisitreport = Vtiger_Module::getInstance('Visitreport');
	$visitreport_tabid = $moduloVisitreport->id;
	if ($visitreport_tabid != '') {
		$adb->query("UPDATE vtiger_relatedlists SET name = 'get_dependents_list' WHERE tabid = 6 AND related_tabid = $visitreport_tabid");
		$adb->query("UPDATE vtiger_relatedlists SET name = 'get_products' WHERE tabid = $visitreport_tabid AND related_tabid = 14");
		
		$check = $adb->query("SELECT * FROM vtiger_field WHERE tabid = $visitreport_tabid AND fieldname = 'account_id'");
		if ($check && $adb->num_rows($check)>0) {
			$adb->query("UPDATE vtiger_field SET fieldname = 'accountid' WHERE tabid = $visitreport_tabid AND fieldname = 'account_id'");
			$adb->query("UPDATE vtiger_field SET columnname = 'accountid' WHERE tabid = $visitreport_tabid AND columnname = 'account_id'");
			$sql = $adb->datadict->RenameColumnSQL('vtiger_visitreport','account_id','accountid','account_id I(19)');
			$adb->datadict->ExecuteSQLArray($sql);
		}
	}
}
//crmv@18160 end
?>
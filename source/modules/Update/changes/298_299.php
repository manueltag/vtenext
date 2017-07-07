<?php
$Vtiger_Utils_Log = true;
require_once('vtlib/Vtiger/Package.php');
include_once('vtlib/Vtiger/Module.php');
include_once('vtlib/Vtiger/Field.php');
global $adb;

$moduleInstance = Vtiger_Module::getInstance('Calendar');

//filtro Eventi
$filterInstance = new Vtiger_Filter();
$filterInstance->name = 'Events';
$filterInstance->status = '0';
$moduleInstance->addFilter($filterInstance);
$filterInstance
->addField(Vtiger_Field::getInstance('eventstatus',$moduleInstance),1)
->addField(Vtiger_Field::getInstance('activitytype',$moduleInstance),2)
->addField(Vtiger_Field::getInstance('subject',$moduleInstance),3)
->addField(Vtiger_Field::getInstance('parent_id',$moduleInstance),4)
->addField(Vtiger_Field::getInstance('date_start',$moduleInstance),5)
->addField(Vtiger_Field::getInstance('due_date',$moduleInstance),6)
->addField(Vtiger_Field::getInstance('assigned_user_id',$moduleInstance),7);
$filterInstance->addRule(Vtiger_Field::getInstance('activitytype',$moduleInstance), 'NOT_EQUALS', 'Task');
$adb->pquery("UPDATE vtiger_customview SET status=0 WHERE cvid=?", Array($filterInstance->id));

//filtro Compiti
$filterInstance = new Vtiger_Filter();
$filterInstance->name = 'Tasks';
$filterInstance->status = '0';
$moduleInstance->addFilter($filterInstance);
$filterInstance
->addField(Vtiger_Field::getInstance('taskstatus',$moduleInstance),1)
->addField(Vtiger_Field::getInstance('activitytype',$moduleInstance),2)
->addField(Vtiger_Field::getInstance('subject',$moduleInstance),3)
->addField(Vtiger_Field::getInstance('parent_id',$moduleInstance),4)
->addField(Vtiger_Field::getInstance('date_start',$moduleInstance),5)
->addField(Vtiger_Field::getInstance('due_date',$moduleInstance),6)
->addField(Vtiger_Field::getInstance('assigned_user_id',$moduleInstance),7);
$filterInstance->addRule(Vtiger_Field::getInstance('activitytype',$moduleInstance), 'EQUALS', 'Task');
$adb->pquery("UPDATE vtiger_customview SET status=0 WHERE cvid=?", Array($filterInstance->id));

//related Documenti
$conModuleInstance = Vtiger_Module::getInstance('Documents');
$moduleInstance->setRelatedList($conModuleInstance,'Documents',array('add','select'),'get_attachments');

//related Predecessore e Successori
$moduleInstance->setRelatedList($moduleInstance,'Fathers',array(),'get_fathers');
$moduleInstance->setRelatedList($moduleInstance,'Children',array('add'),'get_children');

//commenti
if (!in_array('ModComments',array_keys($_SESSION['modules_to_install']))){
	require_once('modules/ModComments/ModComments.php');
	ModComments::addWidgetTo(array('Calendar'));
}

//altre modifiche
$adb->query("DELETE FROM vtiger_activity_view WHERE activity_view = 'This Year'");
$adb->query("UPDATE vtiger_activity_view_seq SET id = 3");
$adb->query("UPDATE vtiger_users SET activity_view = 'Today' WHERE activity_view = 'This Year'");
$adb->query("UPDATE vtiger_users SET hour_format = '24'");
$adb->query("UPDATE vtiger_users SET start_hour = ''");
$adb->query("UPDATE vtiger_users SET end_hour = ''");
$adb->query("INSERT INTO vtiger_visibility(visibilityid,visibility,sortorderid,presence) VALUES (3,'Standard',0,1)");
$adb->query("UPDATE vtiger_visibility SET sortorderid = 1 WHERE visibility = 'Private'");
$adb->query("UPDATE vtiger_visibility SET sortorderid = 2 WHERE visibility = 'Public'");
$adb->query("UPDATE vtiger_visibility_seq SET id = 3");
$adb->query("UPDATE vtiger_activity SET visibility = 'Standard' WHERE visibility = 'Private'");

$sqlarray = $adb->datadict->AddColumnSQL('vtiger_invitees','partecipation I(1) DEFAULT 0');
$adb->datadict->ExecuteSQLArray($sqlarray);
$sqlarray = $adb->datadict->AddColumnSQL('vtiger_activity','is_all_day_event I(1) DEFAULT 0');
$adb->datadict->ExecuteSQLArray($sqlarray);

$_SESSION['modules_to_update']['Projects'] = 'packages/vte/mandatory/Projects.zip';
$_SESSION['modules_to_update']['Visitreport'] = 'packages/vte/mandatory/Visitreport.zip';
?>
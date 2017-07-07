<?php
global $adb;

$em = new VTEventsManager($adb);
$em->registerHandler('vtiger.entity.beforesave', 'modules/ProjectTask/ProjectTaskHandler.php', 'ProjectTaskHandler');

SDK::addView('ProjectTask', 'modules/SDK/src/modules/ProjectTask/View.php', 'constrain', 'continue');

$fields = array();
$fields[] = array('module'=>'ProjectTask','block'=>'LBL_CUSTOM_INFORMATION','name'=>'auto_working_days','label'=>'Calculate working days','uitype'=>'56','columntype'=>'I(1) DEFAULT 0','typeofdata'=>'C~O');
$fields[] = array('module'=>'ProjectTask','block'=>'LBL_CUSTOM_INFORMATION','name'=>'working_days','label'=>'Working days','uitype'=>'7','columntype'=>'I(19) DEFAULT 0','typeofdata'=>'I~O');
include('modules/SDK/examples/fieldCreate.php');

SDK::setLanguageEntries('ProjectTask', 'Calculate working days', array('it_it'=>'Calcolo automatico durata prevista','en_us'=>'Auto calculation of expected duration'));
SDK::setLanguageEntries('ProjectTask', 'Working days', array('it_it'=>'Durata prevista','en_us'=>'Expected duration'));

$crmv_utils = CRMVUtils::getInstance();
$adb->query("update {$table_prefix}_projecttask set auto_working_days = 1");
$result = $adb->query("select projecttaskid, startdate, enddate from {$table_prefix}_projecttask where startdate != '' and enddate !=''");
if ($result && $adb->num_rows($result) > 0) {
	while($row=$adb->fetchByASsoc($result)) {
		if (!empty($row['startdate']) && !empty($row['enddate'])) {
			$working_days = $crmv_utils->number_of_working_days($row['startdate'], $row['enddate']);
			$adb->pquery("update {$table_prefix}_projecttask set working_days = ? where projecttaskid = ?", array($working_days, $row['projecttaskid']));
		}
	}
}
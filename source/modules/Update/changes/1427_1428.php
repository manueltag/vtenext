<?php

// crmv@108385
if (isModuleInstalled('Timecards')) {
	$moduleInstance = Vtecrm_Module::getInstance('Timecards');
	if ($moduleInstance) {
		$adb->pquery("UPDATE {$table_prefix}_field SET fieldlabel = ? WHERE tabid = ? AND fieldname = ? and fieldlabel = ?", array('Assigned To', $moduleInstance->id, 'assigned_user_id', 'TCWorker'));
	}
}
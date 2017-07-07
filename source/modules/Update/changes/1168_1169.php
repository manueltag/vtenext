<?php

global $adb, $table_prefix;

/* crmv@92075 */

// fix newsletter bounce cronjob
require_once('include/utils/CronUtils.php');
$CU = CronUtils::getInstance();

$cronname = 'ProcessBounces';
$cj = CronJob::getByName($cronname);
if (empty($cj)) {
	$cj = new CronJob();
	$cj->name = $cronname;
	$cj->active = 0;
	$cj->singleRun = false;
	$cj->fileName = 'cron/modules/Newsletter/ProcessBounces.service.php';
	$cj->timeout = 5400;
	$cj->repeat = 14400;	// run every 4 hours
	$CU->insertCronJob($cj);
} else {
	// check if should be deactivated
	$active = false;
	
	$configPBFile = 'modules/Campaigns/ProcessBounces.config.php';
	if (is_readable($configPBFile)) {
		include($configPBFile);
		$active = (!empty($message_envelope));
	}
	
	if (!$active) {
		// deactivate
		$result = $adb->pquery("UPDATE {$table_prefix}_cronjobs SET active = 0 WHERE cronid = ?", array($cj->getId()));
	}

	// change the repeat (it was every day, so how could run on sunday after 21?)
	$result = $adb->pquery("UPDATE {$table_prefix}_cronjobs SET repeat_sec = ? WHERE cronid = ? AND repeat_sec = ?", array(14400, $cj->getId(), 86400));
}
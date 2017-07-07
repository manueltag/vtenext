<?php
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter' => 'Targets'));

global $adb, $table_prefix;

$result = $adb->pquery("select * from {$table_prefix}_cronjobs where cronname = ?", array('ProcessBounces'));
if ($adb->num_rows($result) == 0) {
	require_once('include/utils/CronUtils.php');
	$CU = CronUtils::getInstance();
	
	$cj = new CronJob();
	$cj->name = 'ProcessBounces';
	$cj->active = 1;
	$cj->singleRun = false;
	$cj->fileName = 'cron/modules/Newsletter/ProcessBounces.service.php';
	$cj->timeout = 5400;
	$cj->repeat = 86400;	// run every day
	$CU->insertCronJob($cj);
}

$configPBFile = 'modules/Campaigns/ProcessBounces.config.php';
$configPB = file_get_contents($configPBFile);
if (empty($configPB)) {
	echo "\nWARNING: Unable to get ProcessBounces.config.php contents, please modify it manually.\n";
} else {
	// backup it (only if it doesn't exist
	$newConfigInc = 'modules/Campaigns/ProcessBounces.config.1023.php';
	if (!file_exists($newConfigInc)) {
		file_put_contents($newConfigInc, $configPB);
	}
	// change value
	$configPB = str_replace("\$message_envelope = 'bounces@yourdomain.com';","\$message_envelope = '';",$configPB);
	$configPB = str_replace("define(\"VERBOSE\", true);","define(\"VERBOSE\", false);",$configPB);
	if (is_writable($configPBFile)) {
		file_put_contents($configPBFile, $configPB);
	} else {
		echo "\nWARNING: Unable to update ProcessBounces.config.php, please modify it manually.\n";
	}
}
?>
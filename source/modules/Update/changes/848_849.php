<?php
/* crmv@47611 - New Cron to rule them all! */

$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter'));
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['SLA'] = 'packages/vte/mandatory/SLA.zip';

require_once('include/utils/CronUtils.php');

$CU = CronUtils::getInstance();

// these are disabled by default
$cronStatus = array(
	'SLA' => 0,
	'MailScanner' => 0,
);

// Add crons

// check if cron was enabled (only for standard linux install)
if (is_readable('/etc/crontab')) {
	$crontab = @file_get_contents('/etc/crontab');
	if ($crontab) {
		if (preg_match("@^\s*[^#]+{$root_directory}[^#]+SLACron.sh@m", $crontab)) {
			$cronStatus['SLA'] = 1;
		}
		if (preg_match("@^\s*[^#]+{$root_directory}[^#]+MailScannerCron.sh@m", $crontab)) {
			$cronStatus['MailScanner'] = 1;
		}
	}
}

// workflow
if (file_exists('cron/modules/com_vtiger_workflow/com_vtiger_workflow.bat')) {
	unlink('cron/modules/com_vtiger_workflow/com_vtiger_workflow.bat');
}
if (file_exists('cron/modules/com_vtiger_workflow/com_vtiger_workflow.vbs')) {
	unlink('cron/modules/com_vtiger_workflow/com_vtiger_workflow.vbs');
}
if (file_exists('cron/modules/com_vtiger_workflow/com_vtiger_workflow.sh')) {
	unlink('cron/modules/com_vtiger_workflow/com_vtiger_workflow.sh');
}
if (file_exists('cron/modules/com_vtiger_workflow/com_vtiger_workflow.service')) {
	// PHP files are only renamed in case of personalizations
	rename('cron/modules/com_vtiger_workflow/com_vtiger_workflow.service', 'cron/modules/com_vtiger_workflow/com_vtiger_workflow.service.old');
}
$cj = CronJob::getByName('Workflow'); // to update if existing
if (empty($cj)) {
	$cj = new CronJob();
	$cj->name = 'Workflow';
	$cj->active = 1;
	$cj->singleRun = false;
	$cj->fileName = 'cron/modules/com_vtiger_workflow/com_vtiger_workflow.service.php';
	$cj->timeout = 300;             // 5min timeout
	$cj->repeat = 300;              // run every 5 min
	$CU->insertCronJob($cj);
}


// modNotifications
if (file_exists('cron/modules/ModNotifications/ModNotificationsCron.bat')) {
	unlink('cron/modules/ModNotifications/ModNotificationsCron.bat');
}
if (file_exists('cron/modules/ModNotifications/ModNotificationsCron.sh')) {
	unlink('cron/modules/ModNotifications/ModNotificationsCron.sh');
}
if (file_exists('cron/modules/ModNotifications/ModNotificationsCron.vbs')) {
	unlink('cron/modules/ModNotifications/ModNotificationsCron.vbs');
}
$cj = CronJob::getByName('ModNotifications'); // to update if existing
if (empty($cj)) {
	$cj = new CronJob();
	$cj->name = 'ModNotifications';
	$cj->active = 1;
	$cj->singleRun = false;
	$cj->fileName = 'cron/modules/ModNotifications/ModNotifications.service.php';
	$cj->timeout = 600;             // 10min timeout
	$cj->repeat = 3600*2;			// run every 2 hours
	$CU->insertCronJob($cj);
}


// import
if (file_exists('cron/modules/Import/ScheduledImportCron.bat')) {
	unlink('cron/modules/Import/ScheduledImportCron.bat');
}
if (file_exists('cron/modules/Import/ScheduledImportCron.sh')) {
	unlink('cron/modules/Import/ScheduledImportCron.sh');
}
if (file_exists('cron/modules/Import/ScheduledImportCron.vbs')) {
	unlink('cron/modules/Import/ScheduledImportCron.vbs');
}

$cj = CronJob::getByName('ScheduledImport'); // to update if existing
if (empty($cj)) {
	$cj = new CronJob();
	$cj->name = 'ScheduledImport';
	$cj->active = 1;
	$cj->singleRun = false;
	$cj->fileName = 'cron/modules/Import/ScheduledImport.service.php';
	$cj->timeout = 1200;			// 20min timeout
	$cj->repeat = 600;			// run every 10 min
	$CU->insertCronJob($cj);
}


// recurring invoice
if (file_exists('cron/modules/SalesOrder/RecurringInvoiceCron.bat')) {
	unlink('cron/modules/SalesOrder/RecurringInvoiceCron.bat');
}
if (file_exists('cron/modules/SalesOrder/RecurringInvoiceCron.sh')) {
	unlink('cron/modules/SalesOrder/RecurringInvoiceCron.sh');
}
if (file_exists('cron/modules/SalesOrder/RecurringInvoiceCron.vbs')) {
	unlink('cron/modules/SalesOrder/RecurringInvoiceCron.vbs');
}
if (file_exists('cron/modules/SalesOrder/RecurringInvoice.service')) {
	rename('cron/modules/SalesOrder/RecurringInvoice.service', 'cron/modules/SalesOrder/RecurringInvoice.service.old');
}
$cj = CronJob::getByName('RecurringInvoice'); // to update if existing
if (empty($cj)) {
	$cj = new CronJob();
	$cj->name = 'RecurringInvoice';
	$cj->active = 1;
	$cj->singleRun = false;
	$cj->fileName = 'cron/modules/SalesOrder/RecurringInvoice.service.php';
	$cj->timeout = 600;				// 10min timeout
	$cj->repeat = 3600*6;			// run every 6 hours
	$CU->insertCronJob($cj);
}


// sla
if (file_exists('cron/modules/SLA/SLACron.bat')) {
	unlink('cron/modules/SLA/SLACron.bat');
}
if (file_exists('cron/modules/SLA/SLACron.sh')) {
	unlink('cron/modules/SLA/SLACron.sh');
}
if (file_exists('cron/modules/SLA/SLA.vbs')) {
	unlink('cron/modules/SLA/SLA.vbs');
}
$cj = CronJob::getByName('SLA'); // to update if existing
if (empty($cj)) {
	$cj = new CronJob();
	$cj->name = 'SLA';
	$cj->active = $cronStatus['SLA'];
	$cj->singleRun = false;
	$cj->fileName = 'cron/modules/SLA/SLA.service.php';
	$cj->timeout = 300;				// 5min timeout
	$cj->repeat = 300;				// run every 5 min
	$CU->insertCronJob($cj);
}
if ($cronStatus['SLA'] == 0) {
	echo "SLA cron is disabled by default now, enable it again if needed<br>\n";
}


// newsletter
if (file_exists('cron/modules/Newsletter/NewsletterCron.bat')) {
	unlink('cron/modules/Newsletter/NewsletterCron.bat');
}
if (file_exists('cron/modules/Newsletter/NewsletterCron.sh')) {
	unlink('cron/modules/Newsletter/NewsletterCron.sh');
}
if (file_exists('cron/modules/Newsletter/NewsletterCron.vbs')) {
	unlink('cron/modules/Newsletter/NewsletterCron.vbs');
}
$cj = CronJob::getByName('Newsletter'); // to update if existing
if (empty($cj)) {
	$focusNL = CRMEntity::getInstance('Newsletter');
	$cj = new CronJob();
	$cj->name = 'Newsletter';
	$cj->active = 1;
	$cj->singleRun = false;
	$cj->fileName = 'cron/modules/Newsletter/Newsletter.service.php';
	$cj->timeout = 600;				// 10min timeout
	// run every 5-20 min
	$cj->repeat = max(300, min($focusNL->getIntervalBetweenBlocksEmailDelivery(), 1200));
	$CU->insertCronJob($cj);
}


// reminder
if (file_exists('SendReminder.php')) {
	rename('SendReminder.php', 'SendReminder.php.old');
}
if (file_exists('cron/sendreminder.bat')) {
	unlink('cron/sendreminder.bat');
}
if (file_exists('cron/sendreminder.sh')) {
	unlink('cron/sendreminder.sh');
}
if (file_exists('cron/sendreminder.vbs')) {
	unlink('cron/sendreminder.vbs');
}

$cj = CronJob::getByName('SendReminder'); // to update if existing
if (empty($cj)) {
	$cj = new CronJob();
	$cj->name = 'SendReminder';
	$cj->active = 1;
	$cj->singleRun = false;
	$cj->fileName = 'cron/modules/Reminder/SendReminder.service.php';
	$cj->timeout = 600;			// 10min timeout
	$cj->repeat = 300;				// run every 5 min
	$CU->insertCronJob($cj);
}


// mailscanner
if (file_exists('cron/MailScannerCron.bat')) {
	unlink('cron/MailScannerCron.bat');
}
if (file_exists('cron/MailScannerCron.sh')) {
	unlink('cron/MailScannerCron.sh');
}
if (file_exists('cron/MailScannerCron.vbs')) {
	unlink('cron/MailScannerCron.vbs');
}
if (file_exists('cron/MailScanner.service')) {
	rename('cron/MailScanner.service', 'cron/MailScanner.service.old');
}
$cj = CronJob::getByName('MailScanner'); // to update if existing
if (empty($cj)) {
	$cj = new CronJob();
	$cj->name = 'MailScanner';
	$cj->active = $cronStatus['MailScanner'];
	$cj->singleRun = false;
	$cj->fileName = 'cron/modules/MailScanner/MailScanner.service.php';
	$cj->timeout = 600;				// 10min timeout
	$cj->repeat = 1200;				// run every 20 min
	$CU->insertCronJob($cj);
}

// support notification (disabled)
if (file_exists('cron/sendsupportnotification.sh')) {
	unlink('cron/sendsupportnotification.sh');
}
if (file_exists('SendSupportNotification.php')) {
	rename('SendSupportNotification.php', 'SendSupportNotification.php.old');
}
$cj = CronJob::getByName('SupportNotification');
if (empty($cj)) {
	$cj = new CronJob();
	$cj->name = 'SupportNotification';
	$cj->active = 0;
	$cj->singleRun = false;
	$cj->fileName = 'cron/modules/Reminder/SendSupportNotification.service.php';
	$cj->timeout = 600;				// 10min timeout
	$cj->repeat = 1800;				// run every 30 min
	$CU->insertCronJob($cj);
}

// other obsolete notification (disabled)
if (file_exists('cron/intimateTaskStatus.bat')) {
	unlink('cron/intimateTaskStatus.bat');
}
if (file_exists('cron/intimateTaskStatus.php')) {
	unlink('cron/intimateTaskStatus.php');
}
if (file_exists('cron/executecron.sh')) {
	unlink('cron/executecron.sh');
}
$cj = CronJob::getByName('TaskStatus');
if (empty($cj)) {
	$cj = new CronJob();
	$cj->name = 'TaskStatus';
	$cj->active = 0;
	$cj->singleRun = false;
	$cj->fileName = 'cron/modules/Reminder/intimateTaskStatus.service.php';
	$cj->timeout = 600;				// 10min timeout
	$cj->repeat = 1800;				// run every 30 min
	$CU->insertCronJob($cj);
}

// remove old vtigercron.php
if (file_exists('vtigercron.php')) {
	rename('vtigercron.php', 'vtigercron.php.old');
}

// message
echo "New CRON subsystem activated, make sure you have only RunCron.sh in your crontab file.<br>\n";

?>
<?php
/* crmv@65455 */

require('config.inc.php');
require_once('modules/Settings/DataImporter/DataImporterCron.php');

$importid = intval($_REQUEST['importid']);
if ($importid <= 0) {
	echo "No valid import ID provided.\n";
	return;
}

$dcron = new DataImporterCron($importid);
$r = $dcron->process();
if (!$r) {
	echo "Error during the automatic import.\n";
}
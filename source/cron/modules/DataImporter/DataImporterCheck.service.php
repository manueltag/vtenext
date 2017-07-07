<?php
/* crmv@65455 */

require('config.inc.php');
require_once('modules/Settings/DataImporter/DataImporterCron.php');

try {
	$dcron = new DataImporterCron();
	$dcron->check();
} catch (Exception $e) {
	echo "Exception: ".$e->getMessage();
}


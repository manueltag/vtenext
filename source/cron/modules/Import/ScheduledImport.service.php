<?php
/* crmv@47611 */

require('config.inc.php');

global $application_unique_key;

$previousBulkSaveMode = $VTIGER_BULK_SAVE_MODE;
$VTIGER_BULK_SAVE_MODE = true;

$_SESSION["app_unique_key"] = $application_unique_key; //for fast notification
require_once 'modules/Import/controllers/Import_Data_Controller.php';

// check table
if (Vtiger_Utils::CheckTable($table_prefix.'_import_queue')) {
	Import_Data_Controller::runScheduledImport();
}

$VTIGER_BULK_SAVE_MODE = $previousBulkSaveMode;

?>
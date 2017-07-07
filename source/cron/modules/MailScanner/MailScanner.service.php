<?php
/* crmv@47611 crmv@56233 */

require('config.inc.php');

require_once('include/utils/utils.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');

require_once('modules/Settings/MailScanner/core/MailScannerInfo.php');
require_once('modules/Settings/MailScanner/core/MailBox.php');
require_once('modules/Settings/MailScanner/core/MailScanner.php');
require_once('modules/Settings/MailScanner/core/MailScannerSpam.php');

//Added as sometimes the php.ini file used for command line php and
//for Apache php is different.
require_once('include/install/language/en_us.lang.php');
if(!function_exists('imap_open')) {
	echo $installationStrings['LBL_NO'].' '.$installationStrings['LBL_IMAP_SUPPORT'];
} elseif(!function_exists('openssl_encrypt')) {
	echo $installationStrings['LBL_NO'].' '.$installationStrings['LBL_OPENSSL_SUPPORT'];
}

// impersonate admin
global $current_user;
if (!$current_user) {
	$current_user = CRMEntity::getInstance('Users');
	$current_user->id = 1;
}

/**
 * Helper function for triggering the scan.
 */
function service_MailScanner_performScanNow($scannerinfo, $debug) {
	/** If the scanner is not enabled, stop. */
	if($scannerinfo->isvalid) {
		echo "Scanning " . $scannerinfo->server . " in progress\n";

		/** Start the scanning. */
		$scanner = new Vtiger_MailScanner($scannerinfo);
		$scanner->debug = $debug;
		$scanner->performScanNow();

		echo "\nScanning " . $scannerinfo->server . " completed\n";

	} else {
		echo "Failed! [{$scannerinfo->scannername}] is not enabled for scanning!";
	}
}

/** Turn-off this if not required. */
$debug = true;

/** Pick up the mail scanner for scanning. */
if(isset($_REQUEST['scannername'])) {

	// Target scannername specified?
	$scannername = vtlib_purify($_REQUEST['scannername']); // crmv@37463
	$scannerinfo = new Vtiger_MailScannerInfo($scannername);
	
	$mailScannerSpam = new Vtecrm_MailScannerSpam();
	$mailScannerSpam->processQueue($scannername);

	service_MailScanner_performScanNow($scannerinfo, $debug);

} else {

	// Scan all the configured mailscanners?
	$mailScannerSpam = new Vtecrm_MailScannerSpam();
	$mailScannerSpam->processQueue();

	$scannerinfos = Vtiger_MailScannerInfo::listAll();
	if(empty($scannerinfos)) {

		echo "No mailbox configured for scanning!";

	} else {
		foreach($scannerinfos as $scannerinfo) {
			service_MailScanner_performScanNow($scannerinfo, $debug);
		}
	}
}

?>
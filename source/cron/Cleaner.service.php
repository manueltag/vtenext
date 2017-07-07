<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/

/* crmv@106069 */
/* Clean old log files and other cache/temporary files in various locations */

require_once('include/utils/utils.php');
require_once('include/utils/CronUtils.php');


// clean/rotate cron logs
CronUtils::cleanLogs();

// clean/rotate general logs
$rotateOpts = array(
	'maxsize' => 5,		// rotate only when they reach 5 MB
);
$logs = glob('logs/*.log');
if ($logs && is_array($logs)) {
	foreach ($logs as $log) {
		LogUtils::rotateLog($log, $rotateOpts);
	}
}

// remove old charts files
purgeDir('cache/charts/', 90, '/^chart_/');

// remove old xls files
purgeDir('cache/images/', 30, '/^merge2/');

// remove temporary pdfmaker files
purgeDir('cache/pdfmaker/', 30, '/\.html$/');

// remove temporary vtlib files
purgeDir('cache/', 30, '/\.zip$/');

// remove old generated pdf
// disabled
//purgeDir('storage/', 90, '/\.pdf$/');

// clean storage/upload_email_* / *
// not implemented, disabled


// ------------- FUNCTIONS ---------------

// remove files in $dir older than $daysOld days
function purgeDir($dir, $daysOld, $match = null) {
	$now = time();
	$tlimit = $now - $daysOld*3600*24;
	if (substr($dir, -1) != '/') $dir .= '/';
	if (is_dir($dir)) {
		if ($handle = opendir($dir)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry !== '.' && $entry !== '..') {
					if ($match == null || preg_match($match, $entry)) {
						$path = $dir.$entry;
						if (is_writable($path) && filemtime($path) < $tlimit) {
							// should be erased
							removeFile($path);
						}
					}
				}
			}
			closedir($handle);
		}
	}
}

function removeFile($filename) {
	$r = @unlink($filename);
	if ($r) {
		echo "Deleted file $filename\n";
	}
}
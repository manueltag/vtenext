<?php

// do not update using packages
$packagedMods = array('CustomerPortal', 'RecycleBin');
if (is_array($_SESSION['modules_to_update'])) {
	foreach ($_SESSION['modules_to_update'] as $mod => $info) {
		if (!in_array($mod, $packagedMods)) unset($_SESSION['modules_to_update'][$mod]);
	}
}

// delete the packages
$packDir = 'packages/vte/mandatory/';
$mandatoryModules = array(
	'SLA', 'ModNotifications', 'Mobile', 'Ddt', 'FieldFormulas',
	'Touch', 'Sms', 'Services', 'Morphsuit', 'Timecards',
	'Assets', 'Charts', 'WSAPP', 'PDFMaker', 'Myfiles',
	'ProjectsStandard',
	'Conditionals', 'M', 'ModComments', 'Webforms',
	'ChangeLog', 'MyNotes', 'PBXManager', 'Visitreport',
	'ServiceContracts', 'Newsletters',
	'Transitions', 'Fax'
);
foreach ($mandatoryModules as $mod) {
	$pack = $packDir . $mod . '.zip';
	if (is_file($pack) && is_writable($pack)) {
		@unlink($pack);
	}
}
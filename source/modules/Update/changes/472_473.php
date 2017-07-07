<?php
global $adb;
$_SESSION['modules_to_update']['ProjectsStandard'] = Array('location'=>'packages/vte/mandatory/ProjectsStandard.zip','modules'=>Array('ProjectPlan'));
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
$_SESSION['modules_to_update']['Conditionals'] = 'packages/vte/mandatory/Conditionals.zip';
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter'));

$adb->query("UPDATE vtiger_home_iframe SET url = 'http://help.vtecrm.com/news/index.php?lang=\$CURRENT_LANGUAGE\$' WHERE hometype = 'CRMVNEWS'");
$adb->query("UPDATE vtiger_home_iframe SET url = 'http://help.vtecrm.com/index.php?lang=\$CURRENT_LANGUAGE\$' WHERE hometype = 'HELPVTE'");

if (file_exists('extract.php')) {
	@rename('extract.php','extract.php.txt');
}

require_once('modules/SDK/InstallTables.php');
?>
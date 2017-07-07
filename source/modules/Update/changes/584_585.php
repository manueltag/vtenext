<?php
global $adb;

SDK::setLanguageEntry('Users','en_us','LBL_NOT_SAFETY_PASSWORD','The password does not satisfy the safety criteria: at least %s characters, no reference to User Name, Name or Last name.');

$_SESSION['modules_to_update']['Assets'] = 'packages/vte/mandatory/Assets.zip';
$_SESSION['modules_to_update']['ChangeLog'] = 'packages/vte/mandatory/ChangeLog.zip';
$_SESSION['modules_to_update']['Conditionals'] = 'packages/vte/mandatory/Conditionals.zip';
$_SESSION['modules_to_update']['Ddt'] = 'packages/vte/mandatory/Ddt.zip';
$_SESSION['modules_to_update']['Fax'] = 'packages/vte/mandatory/Fax.zip';
$_SESSION['modules_to_update']['FieldFormulas'] = 'packages/vte/mandatory/FieldFormulas.zip';
$_SESSION['modules_to_update']['M'] = 'packages/vte/mandatory/M.zip';
$_SESSION['modules_to_update']['Mobile'] = 'packages/vte/mandatory/Mobile.zip';
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['Morphsuit'] = 'packages/vte/mandatory/Morphsuit.zip';
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter', 'Targets'));
$_SESSION['modules_to_update']['PBXManager'] = 'packages/vte/mandatory/PBXManager.zip';
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';
$_SESSION['modules_to_update']['ProjectsStandard'] = Array('location'=>'packages/vte/mandatory/ProjectsStandard.zip','modules'=>Array('ProjectPlan','ProjectMilestone','ProjectTask'));
$_SESSION['modules_to_update']['ServiceContracts'] = 'packages/vte/mandatory/ServiceContracts.zip';
$_SESSION['modules_to_update']['Services'] = 'packages/vte/mandatory/Services.zip';
$_SESSION['modules_to_update']['SLA'] = 'packages/vte/mandatory/SLA.zip';
$_SESSION['modules_to_update']['Sms'] = 'packages/vte/mandatory/Sms.zip';
$_SESSION['modules_to_update']['Timecards'] = 'packages/vte/mandatory/Timecards.zip';
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';
$_SESSION['modules_to_update']['Transitions'] = 'packages/vte/mandatory/Transitions.zip';
$_SESSION['modules_to_update']['Visitreport'] = 'packages/vte/mandatory/Visitreport.zip';
$_SESSION['modules_to_update']['Webforms'] = 'packages/vte/mandatory/Webforms.zip';
$_SESSION['modules_to_update']['WSAPP'] = 'packages/vte/mandatory/WSAPP.zip';

//aggiungo $table_prefix al config.inc.php - i
global $table_prefix;
$table_prefix = 'vtiger';

$file = 'config.inc.php';
$handle_file = fopen($file, "r");
while(!feof($handle_file)) {
	$buffer = fread($handle_file, 552000);
}

$bk_file = 'config.inc.vte4.2.php';
$handle_bk_file = fopen($bk_file, "w");
fputs($handle_bk_file, $buffer);
fclose($handle_bk_file);

$buffer = str_replace("\$host_name = \$dbconfig['db_hostname'];","// table prefix (ex. vte_account)\n\$table_prefix = '$table_prefix';\n\n\$host_name = \$dbconfig['db_hostname'];",$buffer);
fclose($handle_file);
$handle = fopen($file, "w");
fputs($handle, $buffer);
fclose($handle);
//end

$res = $adb->query("SELECT tabid FROM {$table_prefix}_tab WHERE name = 'CustomerPortal'");
if ($res && $adb->num_rows($res)>0) $_SESSION['modules_to_update']['CustomerPortal'] = 'packages/vte/optional/CustomerPortal.zip';
$res = $adb->query("SELECT tabid FROM {$table_prefix}_tab WHERE name = 'Projects'");
if ($res && $adb->num_rows($res)>0) $_SESSION['modules_to_update']['Projects'] = 'packages/vte/optional/Projects.zip';
$res = $adb->query("SELECT tabid FROM {$table_prefix}_tab WHERE name = 'RecycleBin'");
if ($res && $adb->num_rows($res)>0) $_SESSION['modules_to_update']['RecycleBin'] = 'packages/vte/optional/RecycleBin.zip';

//cancello vecchi file in themes
$themes = array('softed','ztv');
$files_to_remove = array('footer.php','header.php','layout_utils.php');
foreach($themes as $theme) {
	foreach ($files_to_remove as $file) {
		if(is_file("themes/$theme/$file"))	{
			unlink("themes/$theme/$file");
		}
	}
}
?>
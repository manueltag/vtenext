<?php
global $adb;
$res = $adb->query("SELECT tabid FROM vtiger_tab WHERE name = 'Ddt'");
if ($res && $adb->num_rows($res)>0)
	$_SESSION['modules_to_update']['Ddt'] = 'packages/vte/mandatory/Ddt.zip';
$res = $adb->query("SELECT tabid FROM vtiger_tab WHERE name = 'RecycleBin'");
if ($res && $adb->num_rows($res)>0)
	$_SESSION['modules_to_update']['RecycleBin'] = 'packages/vte/optional/RecycleBin.zip';

$_SESSION['modules_to_update']['Assets'] = 'packages/vte/mandatory/Assets.zip';
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['PBXManager'] = 'packages/vte/mandatory/PBXManager.zip';
$_SESSION['modules_to_update']['ProjectsStandard'] = Array('location'=>'packages/vte/mandatory/ProjectsStandard.zip','modules'=>Array('ProjectPlan','ProjectMilestone','ProjectTask'));
$_SESSION['modules_to_update']['Projects'] = 'packages/vte/mandatory/Projects.zip';
$_SESSION['modules_to_update']['ServiceContracts'] = 'packages/vte/mandatory/ServiceContracts.zip';
$_SESSION['modules_to_update']['Services'] = 'packages/vte/mandatory/Services.zip';
$_SESSION['modules_to_update']['Sms'] = 'packages/vte/mandatory/Sms.zip';
$_SESSION['modules_to_update']['Timecards'] = 'packages/vte/mandatory/Timecards.zip';
$_SESSION['modules_to_update']['Visitreport'] = 'packages/vte/mandatory/Visitreport.zip';

$moduloVisitreport = Vtiger_Module::getInstance('Visitreport');
if ($moduloVisitreport) {
	$adb->query("UPDATE vtiger_links SET linkicon = 'themes/softed/images/AddEvent.gif' WHERE tabid = $moduloVisitreport->id AND linklabel = 'LBL_ADD_EVENT'");
	$adb->query("UPDATE vtiger_links SET linkicon = 'themes/softed/images/AddToDo.gif' WHERE tabid = $moduloVisitreport->id AND linklabel = 'LBL_ADD_TASK'");
}
$moduloDdt = Vtiger_Module::getInstance('Ddt');
if ($moduloDdt)
	$adb->query("UPDATE vtiger_links SET linkicon = 'themes/softed/images/actionGenerateInvoice.gif' WHERE tabid = $moduloDdt->id AND linklabel = 'Add Invoice'");
$moduloSalesOrder = Vtiger_Module::getInstance('SalesOrder');
$adb->query("UPDATE vtiger_links SET linkicon = 'themes/softed/images/actionGenerateQuote.gif' WHERE tabid = $moduloSalesOrder->id AND linklabel = 'Add Ddt'");
$adb->query("UPDATE vtiger_settings_field SET iconpath = 'Transitions.gif' WHERE name = 'LBL_ST_MANAGER'");
$adb->query("UPDATE vtiger_settings_field SET iconpath = 'menuSettings.gif' WHERE name = 'LBL_MENU_TABS'");
$res = $adb->query("SELECT blockid FROM vtiger_settings_blocks WHERE label = 'LBL_STUDIO'");
$studio_block_id = $adb->query_result($res,0,'blockid');
$res = $adb->query("SELECT MAX(sequence) AS sequence FROM vtiger_settings_field WHERE blockid = $studio_block_id GROUP BY blockid");
$sequence = $adb->query_result($res,0,'sequence')+1;
$adb->query("UPDATE vtiger_settings_field SET blockid = $studio_block_id, sequence = $sequence WHERE name = 'LBL_LIST_WORKFLOWS'");

if ($adb->table_exist('tbl_s_menu') == 0) {
	$flds = "type C(255) DEFAULT NULL";
	$sqlarray = $adb->datadict->CreateTableSQL('tbl_s_menu', $flds);
	$adb->datadict->ExecuteSQLArray($sqlarray);
	$adb->pquery('insert into tbl_s_menu (type) values (?)',array('modules'));
}
if ($adb->table_exist('tbl_s_menu_modules') == 0) {
	$flds = "tabid I(19) NOTNULL PRIMARY,
			fast I(1) DEFAULT 0,
			sequence I(19)";
	$sqlarray = $adb->datadict->CreateTableSQL('tbl_s_menu_modules', $flds);
	$adb->datadict->ExecuteSQLArray($sqlarray);
	
	$fast_modules = array('Home','Calendar','Webmails','Leads','Accounts','Contacts','Campaigns');
	$i = 0;
	foreach($fast_modules as $module) {
		if(vtlib_isModuleActive($module)) {
			$moduleInstance = Vtiger_Module::getInstance($module);
			$params = array($moduleInstance->id,1,$i);
			$adb->pquery('insert into tbl_s_menu_modules (tabid,fast,sequence) values (?,?,?)',$params);
			$i++;
		}
	}
	$res = $adb->query('SELECT vtiger_tab.tabid,vtiger_tab.name
						FROM vtiger_tab
						INNER JOIN (SELECT DISTINCT tabid FROM vtiger_parenttabrel) parenttabrel ON parenttabrel.tabid = vtiger_tab.tabid
						WHERE vtiger_tab.presence = 0');
	$i = 0;
	while($row=$adb->fetchByAssoc($res)) {
		if(vtlib_isModuleActive($row['name']) && !in_array($row['name'],$fast_modules)) {
			$params = array($row['tabid'],0,$i);
			$adb->pquery('insert into tbl_s_menu_modules (tabid,fast,sequence) values (?,?,?)',$params);
			$i++;
		}
	}
}

//cancello vecchi temi
function rmdirr($dir) {
	if($objs = @glob($dir."/*")) {
		foreach($objs as $obj) {
			@is_dir($obj)? rmdirr($obj) : @unlink($obj);
		}
 	}
	@rmdir($dir);
}
$old_themes = array('alphagrey','bluelagoon','enterprise','woodspice');
foreach($old_themes as $old_theme) {
	rmdirr("themes/$old_theme");
}

//setto default tema softed
include_once('config.inc.php');
global $default_theme,$theme;
$file = 'config.inc.php';
$handle_file = fopen($file, "r");
while(!feof($handle_file)) {
	$buffer = fread($handle_file, 552000);
}
$bk_file = 'config.inc.vte3.0.php';
$handle_bk_file = fopen($bk_file, "w");
fputs($handle_bk_file, $buffer);
fclose($handle_bk_file);
$buffer = str_replace($default_theme,'softed',$buffer);
fclose($handle_file);
$handle = fopen($file, "w");
fputs($handle, $buffer);
fclose($handle);
$default_theme = $theme = $_SESSION['vtiger_authenticated_user_theme'] = 'softed';

include('vteversion.php');
$adb->query("update vtiger_version set old_version = '5.1.0'");
$adb->pquery("update vtiger_version set current_version = ?",array($vtiger_current_version));
$_SESSION['VTIGER_DB_VERSION'] = $vtiger_current_version;
?>
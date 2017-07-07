<?php
global $adb;

$_SESSION['modules_to_update']['ProjectsStandard'] = Array('location'=>'packages/vte/mandatory/ProjectsStandard.zip','modules'=>Array('ProjectPlan'));
$_SESSION['modules_to_update']['Projects'] = 'packages/vte/mandatory/Projects.zip';

if (is_dir($dir)) folderDetete('studio');
if(!function_exists('folderDetete')){
	function folderDetete($dir) {
		$handle = opendir($dir);
		while (false !== ($file = readdir($handle))) {
			if (in_array($file,array('.','..'))) continue;
			elseif(is_file($dir.'/'.$file))	unlink($dir.'/'.$file);
			elseif (is_dir($dir.'/'.$file)) folderDetete($dir.'/'.$file);
		}
		closedir($handle);
		rmdir($dir);
	}
}

$operationid = $adb->getUniqueID("vtiger_ws_operation");
$adb->query("INSERT INTO vtiger_ws_operation (operationid,name,handler_path,handler_method,type,prelogin) VALUES ($operationid,'updateRecord','include/Webservices/Update2.php','vtws_update2','POST',0)");
$adb->query("INSERT INTO vtiger_ws_operation_parameters (operationid,name,type,sequence) VALUES ($operationid,'id','string',1)");
$adb->query("INSERT INTO vtiger_ws_operation_parameters (operationid,name,type,sequence) VALUES ($operationid,'columns','encoded',2)");

$operationid = $adb->getUniqueID("vtiger_ws_operation");
$adb->query("INSERT INTO vtiger_ws_operation (operationid,name,handler_path,handler_method,type,prelogin) VALUES ($operationid,'retrieveInventory','include/Webservices/RetrieveInventory.php','vtws_retrieve_inventory','POST',0)");
$adb->query("INSERT INTO vtiger_ws_operation_parameters (operationid,name,type,sequence) VALUES ($operationid,'id','string',1)");

$moduleInstance = Vtiger_Module::getInstance('SDK');
Vtiger_Link::deleteLink($moduleInstance->id,'HEADERSCRIPT','SDKScript','modules/SDK/LoadJsLang.js');

$em = new VTEventsManager($adb);
$em->registerHandler('vtiger.entity.beforesave','modules/Calendar/CalendarHandler.php','CalendarHandler');

SDK::setLanguageEntry('Calendar', 'it_it', 'LBL_ANSWER', 'ha risposto');
SDK::setLanguageEntry('Calendar', 'en_us', 'LBL_ANSWER', 'answered');
SDK::setLanguageEntry('Calendar', 'it_it', 'LBL_TO_INVITATION', 'all\'invito');
SDK::setLanguageEntry('Calendar', 'en_us', 'LBL_TO_INVITATION', 'to invitation');
SDK::setLanguageEntry('Calendar', 'it_it', 'LBL_MAIL_INVITATION_3', 'per visitare l\'evento');
SDK::setLanguageEntry('Calendar', 'en_us', 'LBL_MAIL_INVITATION_3', 'to visit the activity');

SDK::clearSessionValues();
?>
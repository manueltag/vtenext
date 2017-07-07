<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

global $current_user; // crmv@37463
$module_export = $_REQUEST['module_export'];

//crmv@25233
if(is_admin($current_user) && isset($_SESSION["authenticated_user_id"]) && (isset($_SESSION["app_unique_key"]) && $_SESSION["app_unique_key"] == $application_unique_key)) { // crmv@37463
	$modules = vtlib_getToggleModuleInfo();
	if ($modules[$module_export]['customized'] != '1') {
		exit;
	}
} else {
	exit;
}
//crmv@25233e

require_once("vtlib/Vtecrm/Package.php");
require_once("vtlib/Vtecrm/Module.php");

$package = new Vtiger_Package();
$package->export(Vtiger_Module::getInstance($module_export),'',"$module_export.zip",true);
exit;
?>
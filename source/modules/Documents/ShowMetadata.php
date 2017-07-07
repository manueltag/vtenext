<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ************************************************************************************/
/* crmv@95157 */

global $adb, $table_prefix;
global $app_strings, $mod_strings, $current_language, $currentModule, $theme;

require_once('Smarty_setup.php');
require_once('modules/Documents/storage/StorageBackendUtils.php');

$crmid = intval($_REQUEST['record']);

$smarty = new vtigerCRM_Smarty();

$action = $_REQUEST['ajxaction'];


$SBU = StorageBackendUtils::getInstance();
$check = $SBU->checkMetadata($currentModule, $crmid);

if (!$check) {
	$smarty->assign('ERROR', 'Metadata not supported for this backend');
} else {

	if ($action == 'save') {
		$props = Zend_Json::decode($_REQUEST['properties']);
		if ($props) {
			$ok = $SBU->updateMetadata($currentModule, $crmid, $props);
			if (!$ok) {
				$smarty->assign('ERROR', 'Unable to save metadata');
			}
		}
	}

	$metadata = $SBU->readMetadata($currentModule, $crmid);
	$smarty->assign('METADATA', $metadata);
	$smarty->assign('META_EDITABLE', true);
}

$smarty->assign('MOD', $mod_strings);
$smarty->assign('APP', $app_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('ID', $crmid);
$smarty->assign("THEME", $theme);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");

// display
$smarty->display('modules/Documents/ShowMetadata.tpl');

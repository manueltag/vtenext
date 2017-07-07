<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
require_once('include/utils/ModuleHomeView.php');

/* crmv@115445 */

global $adb, $table_prefix;

$cvid = intval($_REQUEST["record"]);
$module = vtlib_purify($_REQUEST["dmodule"]);
$smodule = vtlib_purify($_REQUEST["smodule"]);
$parenttab = getParentTab();
(!empty($_REQUEST['return_action'])) ? $return_action = vtlib_purify($_REQUEST['return_action']) : $return_action = 'ListView';

if ($cvid > 0) {

	$deletesql = "delete from ".$table_prefix."_customview where cvid =?";
	$deleteresult = $adb->pquery($deletesql, array($cvid));
	unsetLVS($module,"viewname");

	// update the modhome blocks
	$MHW = ModuleHomeView::getInstance($module);
	$MHW->handleRemoveFilter($cvid);

	// crmv@49398
	global $metaLogs;
	if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_DELFILTER, $cvid, array('module'=>$module));
	// crmv@49398e
}

if(isset($smodule) && $smodule != '') {
	$smodule_url = "&smodule=".$smodule;
}

header("Location: index.php?action=$return_action&parenttab=$parenttab&module=$module".$smodule_url);

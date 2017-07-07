<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

/* crmv@91571 */

require_once('include/utils/MassEditUtils.php');

global $current_user, $currentModule;

$MUtils = MassEditUtils::getInstance();

//crmv@27096
$idlist = getListViewCheck($currentModule);
$use_worklow = ($_REQUEST['use_workflow'] == 'true');
$enqueue = ($_REQUEST['enqueue'] == 'true');
//crmv@27096e

$return_module = vtlib_purify($_REQUEST['massedit_module']);
($currentModule == 'Documents' || $currentModule == 'Calendar') ? $return_action = 'ListView' : $return_action = 'index';	//crmv@56444 //crmv@60708

global $rstart;
if(isset($_REQUEST['start']) && $_REQUEST['start']!=''){
	$rstart = "&start=".vtlib_purify($_REQUEST['start']);
}

$savedRecords = array();
if(is_array($idlist) && count($idlist) > 0) {

	$massValues = $MUtils->extractValuesFromRequest($currentModule, $_REQUEST);
	
	$r = true;
	if ($enqueue) {
		$r = $MUtils->enqueue($current_user->id, $currentModule, $massValues, $idlist, $use_worklow);
	} else {
		// code for immediate massedit
		foreach ($idlist as $recordid) {
			if($recordid == '') continue;
			$r &= $MUtils->saveRecord($currentModule, $recordid, $massValues, $use_worklow);
		}
	}
	
}

$parenttab = getParentTab();
header("Location: index.php?module=$return_module&action=$return_action&parenttab=$parenttab$rstart");

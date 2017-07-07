<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
// crmv@38798
require_once('include/database/PearDatabase.php');
@include_once('user_privileges/default_module_view.php');

global $adb, $singlepane_view, $currentModule, $table_prefix;
$idlist = vtlib_purify($_REQUEST['idlist']);
$dest_mod = vtlib_purify($_REQUEST['destination_module']);
$parenttab = getParentTab();

$forCRMRecord = vtlib_purify($_REQUEST['parentid']);
$mode = $_REQUEST['mode'];

if($singlepane_view == 'true')
	$action = "DetailView";
else
	$action = "CallRelatedList";

$focus = CRMEntity::getInstance($currentModule);

if($mode == 'delete') {

	// Split the string of ids
	$ids = array_filter(explode (";",$idlist));
	if (!empty($ids)) {
		$focus->delete_related_module($currentModule, $forCRMRecord, $dest_mod, $ids);
	}

} else {

	$ids = array();
	if (!empty($idlist)) {
		// Split the string of ids
		$ids = array_filter(explode(";", trim($idlist,";")));
	} elseif (!empty($_REQUEST['entityid'])){
		$ids = array(intval($_REQUEST['entityid']));
	}

	if (count($ids) > 0) {
		$focus->save_related_module($currentModule, $forCRMRecord, $dest_mod, $ids);
	}
}

// crmv@37004
if ($_REQUEST['no_redirect'] == true) {

} else {
	header("Location: index.php?module=$currentModule&record=$forCRMRecord&action=$action&parenttab=$parenttab");
}
// crmv@37004e
?>
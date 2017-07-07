<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * Contributor(s): mmbrich
 ********************************************************************************/

require_once('modules/CustomView/CustomView.php');
require_once('user_privileges/default_module_view.php');

global $singlepane_view, $adb,$current_user;
global $table_prefix;
$queryGenerator = QueryGenerator::getInstance(vtlib_purify($_REQUEST["list_type"]), $current_user);
$queryGenerator->initForCustomViewById(vtlib_purify($_REQUEST["cvid"]));
$list_query = $queryGenerator->getQuery();
$list_query = replaceSelectQuery($list_query,$table_prefix.'_crmentity.crmid');
$rs = $adb->query($list_query);

if($_REQUEST["list_type"] == "Leads"){
	$reltable = $table_prefix."_campaignleadrel";
	$relid = "leadid";
}
elseif($_REQUEST["list_type"] == "Contacts"){
	$reltable = $table_prefix."_campaigncontrel";
	$relid = "contactid";
}
elseif($_REQUEST["list_type"] == "Accounts"){
	$reltable = $table_prefix."_campaignaccountrel";
	$relid = "accountid";
}

while($row=$adb->fetch_array($rs)) {
	$sql = "SELECT $relid FROM $reltable WHERE $relid = ? AND campaignid = ?";
	$result = $adb->pquery($sql, array($row['crmid'], $_REQUEST['return_id']));
	if ($adb->num_rows($result) > 0) continue;
	$adb->pquery("INSERT INTO $reltable(campaignid,$relid,campaignrelstatusid) VALUES(?,?,1)", array($_REQUEST["return_id"], $row["crmid"]));
}

header("Location: index.php?module=Campaigns&action=CampaignsAjax&file=CallRelatedList&ajax=true&".
"record=".vtlib_purify($_REQUEST['return_id']));
?>
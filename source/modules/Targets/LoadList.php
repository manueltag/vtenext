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

global $php_max_execution_time;
set_time_limit($php_max_execution_time);

require_once('modules/CustomView/CustomView.php');
require_once('user_privileges/default_module_view.php');
global $table_prefix;
global $singlepane_view,$adb,$current_user,$currentModule;
$queryGenerator = QueryGenerator::getInstance(vtlib_purify($_REQUEST["list_type"]), $current_user);
$queryGenerator->initForCustomViewById(vtlib_purify($_REQUEST["cvid"]));
$list_query = $queryGenerator->getQuery();
$list_query = replaceSelectQuery($list_query,$table_prefix.'_crmentity.crmid');
$res = $adb->query($list_query);
if ($res && $adb->num_rows($res)>0) {
	$ids = array();
	$focus = CRMEntity::getInstance($currentModule);
	while($row=$adb->fetchByAssoc($res)) {
		$ids[] = $row['crmid'];
	}
	$focus->save_related_module($currentModule, $_REQUEST['return_id'], $_REQUEST["list_type"], $ids);
}

header("Location: index.php?module=Targets&action=TargetsAjax&file=CallRelatedList&ajax=true&".
"record=".vtlib_purify($_REQUEST['return_id']));
?>
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
require_once('include/utils/crmv_utils.php');
global $adb;
$shareid =  $_REQUEST['shareid'];
$id = $_REQUEST['record'];
deleteAdvSharingRulePerm($shareid,$id);
if (isset($_REQUEST['recalculate']) && $_REQUEST['recalculate']=='true' ){
	require_once('modules/Users/CreateUserPrivilegeFile.php');
	createUserSharingPrivilegesfile($id);
	header("Location: index.php?module=".$_REQUEST['return_module']."&action=DetailView&parenttab=Settings&record=".$_REQUEST['record']."&adv_sharing=true");
}
elseif (isset($_REQUEST['return_module']) && isset($_REQUEST['record'])) header("Location: index.php?module=".$_REQUEST['return_module']."&action=DetailView&parenttab=Settings&record=".$_REQUEST['record']);
else header("Location: index.php?module=Settings&action=AdvRuleDetailView&parenttab=Settings");
?>

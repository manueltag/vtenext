<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/
require_once 'modules/VteCore/EditView.php';	//crmv@30447

//adding support for uitype 10
if(!empty($_REQUEST['contact_id'])){
	$focus->column_fields['related_to'] = $_REQUEST['contact_id'];
}elseif(!empty($_REQUEST['account_id'])){
	$focus->column_fields['related_to'] = $_REQUEST['account_id'];
}

// crmv@104568
$smarty->assign("BLOCKS",getBlocks($currentModule,$disp_view,$mode,$focus->column_fields));

if($disp_view != 'edit_view') {
	//merge check - start
	$smarty->assign("MERGE_USER_FIELDS",implode(',',get_merge_user_fields($currentModule))); //crmv_utils
	//ends
}
// crmv@104568e

//needed when creating a new opportunity with a default vtiger_account value passed in
if (isset($_REQUEST['accountname']) && is_null($focus->accountname)) {
	$focus->accountname = $_REQUEST['accountname'];
}
if (isset($_REQUEST['accountid']) && is_null($focus->related_to)) {
	$focus->related_to = $_REQUEST['accountid'];
}
if (isset($_REQUEST['contactid']) && is_null($focus->related_to)) {
	$focus->related_to = $_REQUEST['contactid'];
}

$smarty->display('salesEditView.tpl');

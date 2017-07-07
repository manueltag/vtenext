<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ********************************************************************************/

$block = 'Accounts';

(file_exists("$block/header.html")) ? $header = "$block/header.html" : $header = 'VteCore/header.html';
include($header);

global $result;

if ($customerid == '') {
	$customerid = $_SESSION['customer_id'];
}

if($_REQUEST['id'] == '') {
	$params = Array('id'=>$customerid);
	$id = $client->call('get_check_account_id', $params, $Server_Path, $Server_Path);
} else {
	$id = $_REQUEST['id'];
}

if (!empty($id)) {
	(file_exists("$block/Detail.php")) ? $detail = "$block/Detail.php" : $detail = 'VteCore/Detail.php';
	include($detail);
}else{
	$smarty->assign('ERR_MESSAGE','LBL_NOT_AVAILABLE');
	$smarty->assign('MODULE',$block);
	$smarty->assign('TITLE',getTranslatedString($block));
	$smarty->display('List.tpl');
}

(file_exists("$block/footer.html")) ? $footer = "$block/footer.html" : $footer = 'VteCore/footer.html';
include($footer);
?>
<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
*
 ********************************************************************************/
global $result;
global $client;
global $Server_Path;

$customerid = $_SESSION['customer_id'];
$sessionid = $_SESSION['customer_sessionid'];
if($id != '')
{
	//Get the Basic Information
	$params = array('id' => "$id", 'block'=>"$block", 'contactid'=>"$customerid",'sessionid'=>"$sessionid");
 	if (empty($detailview_function)) $detailview_function = 'get_details';
	$result = $client->call($detailview_function, $params, $Server_Path, $Server_Path);

	$smarty = new VTECRM_Smarty();
	
	// Check for Authorization
	if (count($result) == 1 && $result[0] == "#NOT AUTHORIZED#") {
		$smarty->display('NotAuthorized.tpl');
		die();
	} else {
		$info = $result[0][$block];
		$smarty->assign('FIELDLIST',$info);
		$smarty->display('Detail.tpl');
	}
}
?>
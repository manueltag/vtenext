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
/* crmv@57342 */
/*
if ($_REQUEST['profile'] != 'yes') {
	$detail = 'VteCore/Detail.php';
	$detail = 'Contacts/ContactsList.php';
	include($detail);
	return;
}*/

global $result;
global $client;
global $Server_Path;

$smarty = new VTECRM_Smarty();

$customerid = $_SESSION['customer_id'];
$sessionid = $_SESSION['customer_sessionid'];

$smarty->assign('CUSTOMERID',$customerid);

if($id != '')
{
	// Modalità dettaglio Contatto loggato o no
	$profile = $_REQUEST['profile'];
	if($profile != 'yes'){
		$profile = '';
	}
	if($id != $customerid){
		$profile = '';
	}
	$smarty->assign('CONTACTPROFILE',$profile);
	
	//Get the Basic Information
	$params = array('id' => "$id", 'block'=>"$block", 'contactid'=>"$customerid",'sessionid'=>"$sessionid",'language'=>getPortalCurrentLanguage());	//crmv@slowear

	if (empty($detailview_function)) $detailview_function = 'get_details';
	$result = $client->call($detailview_function, $params, $Server_Path, $Server_Path);

	// Check for Authorization
	if (count($result) == 1 && $result[0] == "#NOT AUTHORIZED#") {
		$smarty->display('NotAuthorized.tpl');
		die();
	} else {
		$info = $result[0][$block];

		include('Contacts/config.php');
		foreach ($permittedFields as $fieldname) {
			$data[]=$info[$fieldname];
		}

		$smarty->assign('FIELDLIST',getblock_fieldlist($data));
		
		if ($_REQUEST['update'] == 'yes' && $id == $customerid) {			
			$smarty->assign('SELECTFIELDS',$selectFields);
			$smarty->display('EditProfile.tpl');
			die();
		}
		
		$smarty->display('DetailProfile.tpl');
		
	}
}else{
	$detail = 'VteCore/Detail.php';
	$detail = 'Contacts/ContactsList.php';
	include($detail);
	return;
}
?>
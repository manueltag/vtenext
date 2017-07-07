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
$block = 'Contacts';

global $result;
global $Server_Path;

$customerid = $_SESSION ['customer_id'];
$sessionid = $_SESSION ['customer_sessionid'];
$id = portal_purify($_REQUEST['id']);

if($id != '')
{
	//Get the Basic Information
	$params = array('id' => "$id", 'block'=>"$block", 'contactid'=>"$customerid",'sessionid'=>"$sessionid",'language'=>getPortalCurrentLanguage());	//crmv@slowear
	
	$result = $client->call('unsubscribe_contact', $params, $Server_Path, $Server_Path);
	
	$smarty->assign('CUSTOMERID',$customerid);
	$smarty->assign('UNSUBSCRIBE',$result);
	$smarty->display('unsubscribe.tpl');

}

?>
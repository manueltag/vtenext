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
$only_mine = (isset ( $_REQUEST ['only_mine'] )) ? " checked " : "";

@include ("../PortalConfig.php");
if (! isset ( $_SESSION ['customer_id'] ) || $_SESSION ['customer_id'] == '') {
	@header ( "Location: $Authenticate_Path/login.php" );
	exit ();
}

global $result;

$customerid = $_SESSION ['customer_id'];
$sessionid = $_SESSION ['customer_sessionid'];
$id = portal_purify($_REQUEST['id']);
if (empty($id)) {
	include ("ContactsList.php");
} elseif($id != '' && isset($_REQUEST['fun']) && $_REQUEST['fun'] == 'unsubscribe') {
	if($id != $_SESSION ['customer_id']){
		$smarty->display('NotAuthorized.tpl');
		die();
	}else{	
		include('unsubscribe.php');
	}
} else {
	(file_exists("$block/Detail.php")) ? $detail = "$block/Detail.php" : $detail = 'VteCore/Detail.php';
	include($detail);
}
?>
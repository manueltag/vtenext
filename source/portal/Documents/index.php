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

/* crmv@90004 */

$block = 'Documents';

(file_exists ( "$block/header.html" )) ? $header = "$block/header.html" : $header = 'VteCore/header.html';
include ($header);

$only_mine = (isset ( $_REQUEST ['only_mine'] ) ? " checked " : "");

@include ("../PortalConfig.php");
if (! isset ( $_SESSION ['customer_id'] ) || $_SESSION ['customer_id'] == '') {
	@header ( "Location: $Authenticate_Path/login.php" );
	exit ();
}

global $result;
$sessionid = $_SESSION ['customer_sessionid'];
$customerid = $_SESSION ['customer_id'];
$id = portal_purify($_REQUEST['id']);

$folderid = portal_purify($_REQUEST['folderid']); 

if (empty($id) && empty($folderid)) {
	include ("DocumentsListFolder.php");
} else if(!empty($folderid)) {
	include ("DocumentList.php");
} else {
	(file_exists("$block/Detail.php")) ? $detail = "$block/Detail.php" : $detail = 'VteCore/Detail.php';
	include($detail);
}

// if ($_REQUEST['folderid'] != '')
// 	include ("DownloadFile.php");

(file_exists ( "$block/footer.html" )) ? $footer = "$block/footer.html" : $footer = 'VteCore/footer.html';
include ($footer);
?>
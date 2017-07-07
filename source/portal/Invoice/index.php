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
require_once ("include/utils/utils.php");

$block = "Invoice";

(file_exists ( "$block/header.html" )) ? $header = "$block/header.html" : $header = 'VteCore/header.html';
include ($header);

@include ("../PortalConfig.php");
if (! isset ( $_SESSION ['customer_id'] ) || $_SESSION ['customer_id'] == '') {
	@header ( "Location: $Authenticate_Path/login.php" );
	exit ();
}

global $result;
$customerid = $_SESSION ['customer_id'];
$sessionid = $_SESSION ['customer_sessionid'];
$id = portal_purify($_REQUEST['id']);

if (!empty($id)) {
	$status = $_REQUEST ['status'];
	if ($status != true) {
		$params = array (
				'id' => "$id",
				'block' => "$block",
				'contactid' => "$customerid",
				'sessionid' => "$sessionid" 
		);
		$filecontent = $client->call ( 'get_pdf', $params, $Server_Path, $Server_Path );
		if ($filecontent != 'failure') {
			$filename = "$Server_Path/test/product/" . portal_purify ( $id ) . "_Invoice.pdf";
			header ( "Content-type: text/pdf" );
			header ( "Cache-Control: private" );
			header ( "Content-Disposition: attachment; filename=$filename" );
			header ( "Content-Description: PHP Generated Data" );
			echo base64_decode ( $filecontent );
			exit ();
		} else {
			echo getTranslatedString ( 'LBL_PDF_CANNOT_GENERATE' ); // We have to show the error message like "PDF output cannot be generated. Please contact admin"
		}
	} else {
		$detailview_function = 'get_invoice_detail';
		(file_exists("$block/Detail.php")) ? $detail = "$block/Detail.php" : $detail = 'VteCore/Detail.php';
		include($detail);
	}
} else {
	include ("InvoiceList.php");
}

(file_exists ( "$block/footer.html" )) ? $footer = "$block/footer.html" : $footer = 'VteCore/footer.html';
include ($footer);
?>	
<?php
global $table_prefix;
$accountid = $_REQUEST['accountid'];
if( $accountid != '' && $accountid != 'undefined'){
	$query .= " and ".$table_prefix."_salesorder.accountid = '".$accountid."' ";
}
?>
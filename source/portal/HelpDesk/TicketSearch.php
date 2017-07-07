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

function getTicketSearchQuery() {
	global $table_prefix;
	
	if(trim($_REQUEST['search_ticketid']) != '')
	{
		$where .= $table_prefix."_troubletickets.ticketid = '".addslashes($_REQUEST['search_ticketid'])."'&&&";
	}
	if(trim($_REQUEST['search_title']) != '')
	{
		//$where .= $table_prefix."_troubletickets.title = '".$_REQUEST['search_title']."'&&&";
		$where .= $table_prefix."_troubletickets.title like '%".addslashes(trim($_REQUEST['search_title']))."%'&&&";
	}
	
	if(trim($_REQUEST['search_ticketstatus']) != '')
	{
		$where .= $table_prefix."_troubletickets.status = '".$_REQUEST['search_ticketstatus']."'&&&";
	}
	if(trim($_REQUEST['search_ticketpriority']) != '')
	{
		$where .= $table_prefix."_troubletickets.priority = '".$_REQUEST['search_ticketpriority']."'&&&";
	}
	if(trim($_REQUEST['search_ticketcategory']) != '')
	{
		$where .= $table_prefix."_troubletickets.category = '".$_REQUEST['search_ticketcategory']."'&&&";
	}
	$where = trim($where,'&&&');
	return $where;
}

?>

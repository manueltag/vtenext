<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ************************************************************************************/

$smarty = new VTECRM_Smarty();

	$status_array = getPicklist('ticketstatus');
	$smarty->assign('SEARCH_TICKETSTATUS',getComboList('search_ticketstatus',$status_array,' '));
	//echo getComboList('search_ticketstatus',$status_array,' ');

	$priority_array = getPicklist('ticketpriorities');
	$smarty->assign('SEARCH_TICKETPRIORITY',getComboList('search_ticketpriority',$priority_array,' '));
	//echo getComboList('search_ticketpriority',$priority_array,' ');

	$category_array = getPicklist('ticketcategories');
	$smarty->assign('SEARCH_TICKETCATEGORY',getComboList('search_ticketcategory',$category_array,' '));
	//echo getComboList('search_ticketcategory',$category_array,' ');

//$smarty->display('TicketDetail.tpl');
?>
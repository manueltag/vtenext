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

$smarty = new VTECRM_Smarty();

$showstatus = $_REQUEST['showstatus'];

global $result;

include('SearchForm.php');

if (!empty($result)) {

	$header = $result[0]['head'][0];
	$nooffields = count($header);
	$data = $result[1]['data'];
	$rowcount = count($data);
	
	$header_arr = array();
	for($i=0; $i<$nooffields; $i++)
	{
		$header_value = $header[$i]['fielddata'];
		$header_arr[] = $header_value;
	}
	$smarty->assign('HEADER',$header_arr);

	$entries_arr = array();
	$links_arr = array();
	for($i=0;$i<count($data);$i++)
	{
		$row = array();
		$ticket_link = '';
		$ticket_status = '';
		
		for($j=0; $j<$nooffields; $j++) {
			$fielddata = $data[$i][$j]['fielddata'];
			if ($header[$j]['fielddata'] == 'Status') {
				$ticket_status = $fielddata;
			}
			if ($header[$j]['fielddata'] == 'Subject') {
				preg_match('/<a href="(.+)">/', $fielddata, $match);
				$ticket_link = $match[1];
			}
			$fielddata = strip_tags($fielddata);
			$row[] = $fielddata;
		}
		
		if ($showstatus != '' && $rowcount >= 1) {
			if ($ticket_status == $showstatus) {
				$entries_arr[] = $row;
				$links_arr[] = $ticket_link;
			}
			// crmv@104101
			if ($showstatus == 'Open') {
				$smarty->assign('TICKETOPEN', 'selected=""');
			} elseif ($showstatus == 'Closed') {
				$smarty->assign('TICKETCLOSE', 'selected=""');
			}
			// crmv@104101e
		} else {
			$entries_arr[] = $row;
			$links_arr[] = $ticket_link;
		}
	}
	$smarty->assign('ENTRIES',$entries_arr);
	$smarty->assign('LINKS',$links_arr);
	$smarty->assign('MODULE','HelpDesk');
	$smarty->assign('MINE_SELECTED',$mine_selected);
	$smarty->assign('ALL_SELECTED',$all_selected);
}
$smarty->assign('MODULE',$_REQUEST['module']);
$smarty->display('TicketList.tpl');
?>
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
global $result;
global $client;
global $Server_Path;

$smarty = new VTECRM_Smarty();

$customerid = $_SESSION['customer_id'];
$sessionid = $_SESSION['customer_sessionid'];

if($projectid != '') {
	$params = array('id' => "$projectid", 'block'=>"$block",'contactid'=>$customerid,'sessionid'=>"$sessionid");
	$result = $client->call('get_details', $params, $Server_Path, $Server_Path);
	
	// Check for Authorization
	if (count($result) == 1 && $result[0] == "#NOT AUTHORIZED#") {
		$smarty->display('NotAuthorized.tpl');
		die();
	}
	
	$smarty = new VTECRM_Smarty();
	
	// Check for Authorization
	if (count($result) == 1 && $result[0] == "#NOT AUTHORIZED#") {
		$smarty->display('NotAuthorized.tpl');
		die();
	} else {
		$info = $result[0][$block];
		
		$smarty->assign('FIELDLIST',$info);
// 		$smarty->display('Detail.tpl');
	}
	
	$other_blocks = array();
	
	$projecttaskblock = 'ProjectTask';
	$params = array('id' => "$projectid", 'block'=>"$projecttaskblock",'contactid'=>$customerid,'sessionid'=>"$sessionid");
	$result = $client->call('get_project_components', $params, $Server_Path, $Server_Path);

	$other_blocks[getTranslatedString('LBL_PROJECT_TASKS')] = getblock_fieldlistview($result,"$projecttaskblock");
	
	$other_blocks_entries = array();
	$other_blocks_link = array();
	foreach($other_blocks as $other_blocks_laber=>$other_blocks_list){
		$element = count($other_blocks_list['HEADER']);
	}
	$other_blocks[getTranslatedString('LBL_PROJECT_TASKS')]['LAYOUT'] = $element;
	
	foreach($other_blocks as $other_blocks_laber=>$other_blocks_list){
		$other_blocks_header = $other_blocks_list['HEADER'];
		$other_blocks_entries[] = $other_blocks_list['ENTRIES'][0];
		$other_blocks_link[] = $other_blocks_list['LINK'];
	}
	
	$projectmilestoneblock = 'ProjectMilestone';
	$params = array('id' => "$projectid", 'block'=>"$projectmilestoneblock",'contactid'=>$customerid,'sessionid'=>"$sessionid");
	$result = $client->call('get_project_components', $params, $Server_Path, $Server_Path);	
	$other_blocks[getTranslatedString('LBL_PROJECT_MILESTONES')] = getblock_fieldlistview($result,"$projectmilestoneblock");
	
	foreach($other_blocks as $other_blocks_laber=>$other_blocks_list){
		$element = count($other_blocks_list['HEADER']);
	}
	$other_blocks[getTranslatedString('LBL_PROJECT_MILESTONES')]['LAYOUT'] = $element;
	
	
	$projectticketsblock = 'HelpDesk';
	$params = array('id' => "$projectid", 'block'=>"$projectticketsblock",'contactid'=>$customerid,'sessionid'=>"$sessionid");
	$result = $client->call('get_project_tickets', $params, $Server_Path, $Server_Path);
	
	$other_blocks[getTranslatedString('LBL_PROJECT_TICKETS')] = getblock_fieldlistview($result,"$projectticketsblock");
	
	
	
	foreach($other_blocks as $other_blocks_laber=>$other_blocks_list){
		$element = count($other_blocks_list['HEADER']);
	}
	$other_blocks[getTranslatedString('LBL_PROJECT_TICKETS')] = getblock_fieldlistview($result,"$projectticketsblock");
	
	
	$projectdocumentsblock = 'Documents';
	$params = array('id' => "$projectid", 'block'=>"$projectdocumentsblock",'contactid'=>$customerid,'sessionid'=>"$sessionid");
	$result = $client->call('get_documents', $params, $Server_Path, $Server_Path);
	$other_blocks[getTranslatedString('LBL_PROJECT_DOCUMENTS')] = getblock_fieldlistview($result,"$projectdocumentsblock");
	
	foreach($other_blocks as $other_blocks_laber=>$other_blocks_list){
		$element = count($other_blocks_list['HEADER']);
	}
	$other_blocks[getTranslatedString('LBL_PROJECT_DOCUMENTS')] = getblock_fieldlistview($result,"$projectdocumentsblock");

	
	$smarty->assign('OTHERBLOCKS',$other_blocks);
	
	$smarty->display('Detail.tpl');
}
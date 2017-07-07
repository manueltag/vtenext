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

global $result;
global $client;

$smarty = new VTECRM_Smarty();
$smarty->assign('TITLE',getTranslatedString("LBL_INVOICE_INFORMATION"));

$onlymine=$_REQUEST['onlymine'];
if($onlymine == 'true') {
    $mine_selected = 'selected';
    $all_selected = '';
} else {
    $mine_selected = '';
    $all_selected = 'selected';
}
$params = array();
	
$allow_all = $client->call('show_all',array('module'=>'Invoice'),$Server_Path, $Server_Path);
if($allow_all == 'true'){
  	$smarty->assign('ALLOW_ALL',$allow_all);
   	$smarty->assign('MINE_SELECTED',$mine_selected);
   	$smarty->assign('ALL_SELECTED',$all_selected);
}	    	
	    					
if ($customerid != '' )
{
	$block = "Invoice";
	$smarty->assign('MODULE',$module);
	$params = array('id' => "$customerid", 'block'=>"$block",'sessionid'=>$sessionid,'onlymine'=>$onlymine);
	$result = $client->call('get_list_values', $params, $Server_Path, $Server_Path);
	$smarty->assign('FIELDLISTVIEW',getblock_fieldlistview($result,$block));
}

$smarty->display('List.tpl');
?>
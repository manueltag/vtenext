<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
*
********************************************************************************/
global $result;
$customerid = $_SESSION['customer_id'];
$username = $_SESSION['customer_name'];
$sessionid = $_SESSION['customer_sessionid'];

$smarty = new VTECRM_Smarty();
$smarty->assign('TITLE',getTranslatedString("LBL_PRODUCT_INFORMATION"));

$onlymine=$_REQUEST['onlymine'];
if($onlymine == 'true') {
    $mine_selected = 'selected';
    $all_selected = '';
} else {
    $mine_selected = '';
    $all_selected = 'selected';
}

$block = 'Products';	

		$params = Array('id'=>$customerid,'block'=>$block,'sessionid'=>$sessionid,'onlymine'=>$onlymine);
		$result = $client->call('get_product_list_values', $params, $Server_Path, $Server_Path);
 
		$allow_all = $client->call('show_all',array('module'=>'Products'),$Server_Path, $Server_Path);
	    if($allow_all == 'true'){
	    	$smarty->assign('ALLOW_ALL',$allow_all);
	    	$smarty->assign('MINE_SELECTED',$mine_selected);
	    	$smarty->assign('ALL_SELECTED',$all_selected);
	    	}
	      		
$smarty->assign('MODULE',$block);
$smarty->assign('FIELDLISTVIEW',getblock_fieldlistview_product($result,$block));

$smarty->display('List.tpl');
?>
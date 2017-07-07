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
$smarty->assign('TITLE',getTranslatedString("LBL_ASSET_INFORMATION"));

if ($customerid != '' ) {
	$params = array('id' => "$customerid", 'block'=>"$block",'sessionid'=>$sessionid);
	$result = $client->call('get_list_values', $params, $Server_Path, $Server_Path);
	$smarty->assign('FIELDLISTVIEW',getblock_fieldlistview($result,$block));
}

$smarty->display('List.tpl');
?>
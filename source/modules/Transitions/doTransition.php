<?php 
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/
require_once('include/utils/utils.php');
$field = $_REQUEST['field'];
$module = $_REQUEST['module_name'];
$obj = CRMEntity::getInstance('Transitions');
$obj->Initialize($module);
$ret_res['success'] = $obj->saveField($field);
if (!$ret_res['success'])
	$ret_res['msg'] = GetTranslatedString('LBL_CANT_SET_FIELD','Transitions');
echo Zend_Json::encode($ret_res);
die();
?>
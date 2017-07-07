<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/
include_once('include/utils/utils.php');
$module = $_REQUEST['module_name'];
$field = $_REQUEST['field'];
$obj = CRMEntity::getInstance('Transitions');
$obj->Initialize($module,"",$field);
$ret_res['all_fields'] = $obj->all_status_field;
$ret_res['is_managed'] = $obj->is_managed;
$ret_res['module_is_managed'] = $obj->module_is_managed;
$ret_res['picklist_fields'] = $obj->getFieldPicklist();
echo Zend_Json::encode($ret_res);
exit();
?>
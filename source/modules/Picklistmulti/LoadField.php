<?php 
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  crmvillage.biz
 * The Initial Developer of the Original Code is crmvillage.biz.
 * Portions created by vtiger are Copyright (C) crmvillage.biz.
 * All Rights Reserved.
 *
 ********************************************************************************/
require_once('modules/Picklistmulti/Picklistmulti_class.php');
include_once('include/Zend/Json.php');
$module = $_REQUEST['module_name'];
$obj=new Picklistmulti(false,$module);
$ret_res = $obj->field_list;
echo Zend_Json::encode($ret_res);
die();
?>
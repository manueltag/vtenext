<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
//crmv@26897
require_once('../../config.inc.php');
chdir($root_directory);
require_once('data/CRMEntity.php');
//crmv@26897e

function vtSortFieldsJson($request){
	$moduleName = $request['module_name'];
	$focus = CRMEntity::getInstance($moduleName); // crmv@26897
	echo Zend_Json::encode($focus->sortby_fields);
}
vtSortFieldsJson($_REQUEST);
?>
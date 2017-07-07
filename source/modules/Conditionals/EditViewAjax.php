<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is crmvillage.biz.
 * Portions created by crmvillage.biz are Copyright (C) crmvillage.biz.
 * All Rights Reserved.
 *******************************************************************************/
require_once('Smarty_setup.php');
require_once('include/utils/utils.php');

$smarty = new vtigerCRM_Smarty;
global $app_strings,$mod_strings,$current_language;
$smarty->assign("APP", $app_strings);
$smarty->assign("UMOD", $mod_strings);

if($_REQUEST["chk_module"] != "") {
		
	$to_mod_strings = return_module_language($current_language,$_REQUEST["chk_module"]);
	$smarty->assign("TOMOD", $to_mod_strings);		

	$conditionals_obj = CRMEntity::getInstance('Conditionals');	//crmv@36505
	$FpofvData = $conditionals_obj->wui_getFpofvData('',$_REQUEST["chk_module"]);	//crmv@36505
	$FpofvDataRemapped = Array();
	
	for($i=0;$i<count($FpofvData);$i++) {
		//@todo - optimize
		$FpofvDataRemapped[$i][TaskField] 			= $FpofvData[$i]["FpofvChkFieldName"];
		$FpofvDataRemapped[$i][TaskFieldLabel] 		= $FpofvData[$i]["FpofvChkFieldLabel"];
		$FpofvDataRemapped[$i][TaskType]			= "FieldChange";
		$FpofvDataRemapped[$i][FpovManaged] 	= $FpofvData[$i]["FpovManaged"];
		$FpofvDataRemapped[$i][FpovReadPermission] 	= $FpofvData[$i]["FpovReadPermission"];
		$FpofvDataRemapped[$i][FpovWritePermission]	= $FpofvData[$i]["FpovWritePermission"];
		$FpofvDataRemapped[$i][FpovMandatoryPermission]	= $FpofvData[$i]["FpovMandatoryPermission"];
		$FpofvDataRemapped[$i][FpofvSequence] = $FpofvData[$i]["FpofvSequence"];
		$FpofvDataRemapped[$i][FpofvBlockLabel] = $FpofvData[$i]["FpofvBlockLabel"];
		//crmv@115268
		$FpofvDataRemapped[$i][HideFpovValue] = $FpofvData[$i]["HideFpovValue"];
		$FpofvDataRemapped[$i][HideFpovManaged] = $FpofvData[$i]["HideFpovManaged"];
		$FpofvDataRemapped[$i][HideFpovReadPermission] = $FpofvData[$i]["HideFpovReadPermission"];
		$FpofvDataRemapped[$i][HideFpovWritePermission] = $FpofvData[$i]["HideFpovWritePermission"];
		$FpofvDataRemapped[$i][HideFpovMandatoryPermission] = $FpofvData[$i]["HideFpovMandatoryPermission"];
		//crmv@115268e
	}
}

$smarty->assign("CHECK_MODULE",$_REQUEST["chk_module"]);

$smarty->assign("MODE", "create");
$smarty->assign("FPOFV_PIECE_DATA", $FpofvDataRemapped);
$smarty->display('modules/Conditionals/FieldTable.tpl');
?>
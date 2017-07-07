<?php
/*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/
//crmv@62414
global $mod_strings, $app_strings, $currentModule, $current_user, $theme;
include_once('include/utils/utils.php');

$requestedfile = vtlib_purify($_REQUEST['requestedfile']);

require_once('Smarty_setup.php');
$smarty = new vtigerCRM_Smarty();
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('REQUESTED_FILE', $requestedfile);

$extension = substr(strrchr($requestedfile, "."), 1);
if(strtolower($extension) == 'pdf'){
	$smarty->display('modules/Messages/ViewerJSPDF.tpl');
}
else{
	$smarty->display('modules/Messages/ViewerJS.tpl');
}
//crmv@62414 e
?>
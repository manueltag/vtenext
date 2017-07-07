<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@125351 */

require_once "Smarty_setup.php";
$qc_modules = getQuickCreateModules();

$smarty = new vtigerCRM_Smarty();
$smarty->assign("QCMODULE", $qc_modules);
$smarty->display("FastQuickCreate.tpl");
?>
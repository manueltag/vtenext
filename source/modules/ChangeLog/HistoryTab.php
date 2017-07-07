<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@104566 */
require_once('Smarty_setup.php');
global $currentModule, $current_user;
$module = vtlib_purify($_REQUEST['pmodule']);
$record = vtlib_purify($_REQUEST['record']);
$smarty = new vtigerCRM_Smarty();

$focus = CRMEntity::getInstance($currentModule);
$query_result = $focus->get_history_query($module, $record);
$history = $focus->get_history_log($module, $record, $query_result);

$smarty->assign('HISTORY',$history);
$smarty->display('modules/ChangeLog/HistoryTab.tpl');
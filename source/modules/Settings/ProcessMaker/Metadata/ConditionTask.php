<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@92272 */

global $adb, $table_prefix;

if ($PMUtils->isStartTask($id,$elementid)) {
	$smarty->assign("moduleNames", $PMUtils->getModuleList('picklist',$vte_metadata_arr['moduleName']));
	$smarty->assign('IS_START_TASK',true);
} else {
	$modules = $PMUtils->getRecordsInvolvedOptions($id, $vte_metadata_arr['moduleName']);
	//crmv@96450
	require_once('modules/Settings/ProcessMaker/ProcessDynaForm.php');
	$processDynaFormObj = ProcessDynaForm::getInstance();
	$dynaforms = $processDynaFormObj->getOptions($id, $vte_metadata_arr['moduleName']);
	if (!empty($dynaforms)) $modules = array_merge($modules,$dynaforms);
	//crmv@96450e
	$smarty->assign("moduleNames", $modules);
}

$smarty->assign('SDK_CUSTOM_FUNCTIONS',SDK::getFormattedProcessMakerTaskConditions());

if ($PMUtils->showRunProcessesButton('Settings')) $smarty->assign('ENABLE_MANUAL_MODE',true);	//crmv@100495
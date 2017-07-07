<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
require_once 'modules/VteCore/EditView.php';	//crmv@30447

// crmv@64542

global $currentModule;

$templates = array(
	'inventory' => array(
		'create' => 'Inventory/InventoryEditView.tpl',
		'edit' => 'Inventory/InventoryEditView.tpl',
	),
	'standard' => array(
		'create' => 'salesEditView.tpl',
		'edit' => 'salesEditView.tpl',
	)
);

$templateMode = isInventoryModule($currentModule) ? 'inventory' : 'standard';

//crmv@99316
if ($focus->mode == 'edit')
	$template = $templates[$templateMode]['edit'];
else
	$template = $templates[$templateMode]['create'];
	
$smarty->assign('TEMPLATE', $template);

// crmv@105933
// remove some tools for the module
if ($smarty && is_array($smarty->get_template_vars('CHECK'))) {
	$tool_buttons = $smarty->get_template_vars('CHECK');
	unset($tool_buttons['EditView']);
	unset($tool_buttons['Import']);
	unset($tool_buttons['Merge']);
	unset($tool_buttons['DuplicatesHandling']);
	$smarty->assign('CHECK', $tool_buttons);
}
// crmv@105933e

require_once('modules/Settings/ProcessMaker/ProcessDynaForm.php');
$processDynaFormObj = ProcessDynaForm::getInstance();
$enable = $processDynaFormObj->existsConditionalPermissions($focus);
$smarty->assign('ENABLE_DFCONDITIONALS', $enable);
if ($enable) {
	$dynaFormFields = $processDynaFormObj->getFields($focus);
	$smarty->assign('DFFIELDS', Zend_Json::encode($dynaFormFields));
}

//crmv@93990
if($_REQUEST['ajxaction'] == 'DYNAFORMPOPUP') {
	
	require_once('modules/Settings/ProcessMaker/ProcessMakerUtils.php');
	$PMUtils = ProcessMakerUtils::getInstance();
	$processesid = $PMUtils->getProcessRelatedTo($focus->column_fields['related_to'],'processesid');
	if ($focus->id == $processesid) {
		$related_to_popup_opt = $PMUtils->getProcessRelatedTo($focus->column_fields['related_to'],'related_to_popup_opt');
		if ($related_to_popup_opt == 'once') {
			$dynaformmetaid = $PMUtils->getProcessRelatedTo($focus->column_fields['related_to'],'dynaformmetaid');
			$adb->pquery("UPDATE {$table_prefix}_process_dynaform SET done = ? WHERE running_process = ? AND metaid = ?", array(2,$focus->column_fields['running_process'],$dynaformmetaid));
		}
	}
	
	$smarty->assign('PROCESS_NAME', $focus->column_fields['process_name']);
	$smarty->assign('REQUESTED_ACTION', $focus->column_fields['requested_action']);
	
	$blocks = $smarty->_tpl_vars['BLOCKS'];
	$headers = array_keys($blocks);
	$blockstatus = array();
	foreach($headers as $header) $blockstatus[$header] = 0;
	
	$dyna_blocks = $processDynaFormObj->getCurrentDynaForm($focus);
	//crmv@99316 crmv@110419
	foreach($dyna_blocks as $dyna_block) {
		$label = getTranslatedString($dyna_block['label']);
		if (isset($blockVisibility[$dyna_block['label']])) {
			$blockstatus[$label] = $blockVisibility[$label];
		} else {
			$blockstatus[$label] = 1;
		}
	}
	//crmv@99316e crmv@110419e
	$smarty->assign('BLOCKVISIBILITY', $blockstatus);
}
//crmv@93990e

$smarty->display('modules/Processes/EditView.tpl');
//crmv@99316e
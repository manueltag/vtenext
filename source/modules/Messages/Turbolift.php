<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/

global $currentModule;
$record = vtlib_purify($_REQUEST['record']);

if (isPermitted($currentModule,'DetailView',$record) == 'no') {
	die($app_strings['LBL_PERMISSION']);
}

require_once('Smarty_setup.php');
$smarty = new vtigerCRM_Smarty();

$smarty->assign('ID',$record);

$focus = CRMEntity::getInstance($currentModule);
$focus->retrieve_entity_info($record,$currentModule);
$layout = $focus->getLayoutSettings();	//crmv@90628
$rm = RelationManager::getInstance();
$excludeMods = array('ModComments', 'Campaigns');

$record_ids = array();
if ($layout['thread'] == '1') {	//crmv@90628
	$father = $focus->getFather($record,$focus->column_fields['folder']);
	if ($father) {
		$children = $focus->getChildren($father,$focus->column_fields['folder']);
		if (!empty($children)) {
			$record_ids = $children;
		}
	}
}	//crmv@90628
if (empty($record_ids)) {
	$record_ids[] = $record;
}

$links = array();
foreach($record_ids as $record_id) {
	$ids = $rm->getRelatedIds($currentModule, $record_id, null, $excludeMods);
	//$ids = array_slice($ids, 0, 5);	// limit to 5
	// TODO: filder by ? importance...
	if (is_array($ids)) {
		foreach ($ids as $id) {
			//crmv@38592
			$l = $focus->getEntityPreview($id);
			if ($l) $links[$id] = $l;
			//crmv@38592e
		}
	}
}

$smarty->assign('MODULE',$currentModule);
$smarty->assign('LINKS',$links);
$smarty->display('TurboliftButtons.tpl');
?>
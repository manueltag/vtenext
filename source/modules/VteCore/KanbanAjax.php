<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/

/* crmv@OPER6288 */

require_once('include/utils/KanbanView.php');

global $adb, $table_prefix, $currentModule, $current_user, $app_strings;
$modObj = CRMEntity::getInstance($currentModule);

$ajxaction = $_REQUEST['ajxaction'];
if ($ajxaction == 'SAVE') {
	$viewid = vtlib_purify($_REQUEST['viewid']);
	$column = vtlib_purify($_REQUEST['column']);
	$record = vtlib_purify($_REQUEST['record']);
	$ajax_result = false;
	$ajax_result_message = getTranslatedString('ERROR');
	
	$kanbanView = KanbanView::getInstance($viewid);
	$actions = $kanbanView->getActions($column);
	if (empty($actions['conditions'])) {
		$ajax_result_message = getTranslatedString('LBL_KANBAN_DRAG_DISABLED');
	} else {
		$isPermitted = (isPermitted($currentModule, 'DetailViewAjax', $record) == 'yes');
		if ($isPermitted) {
			foreach($actions['conditions'] as $action) {
				$permField = getFieldVisibilityPermission($currentModule, $current_user->id, $action['fieldname']);
				if ($permField != 0) {
					$isPermitted = false;
					break;
				}
			}
		}
		if ($isPermitted) {
			$modObj->retrieve_entity_info_no_html($record, $currentModule);
			foreach($actions['conditions'] as $action) {
				$modObj->column_fields[$action['fieldname']] = $action['value'];
			}
			$modObj->id = $record;
			$modObj->mode = 'edit';
			$modObj->save($currentModule);
			if($modObj->id != '') {
				$ajax_result = true;
				$ajax_result_message = '';
			}
		} else {
			$ajax_result_message = getTranslatedString('LBL_PERMISSION');
		}
	}
	if ($ajax_result) {
		echo ":#:SUCCESS:$ajax_result_message";
	} else {
		echo ":#:FAILURE:$ajax_result_message";
	}
} elseif ($ajxaction == 'LOADCOLUMN') {
	$viewid = vtlib_purify($_REQUEST['viewid']);
	$column = vtlib_purify($_REQUEST['column']);
	$page = vtlib_purify($_REQUEST['page']);
	
	$kanbanView = KanbanView::getInstance($viewid);
	$column = $kanbanView->getList($column,$_SESSION['lv_user_id_'.$currentModule],$page); // crmv@107328
	
	require_once('Smarty_setup.php');
	$smarty = new vtigerCRM_Smarty();
	$smarty->assign('APP',$app_strings);
	$smarty->assign('MODULE',$currentModule);
	$smarty->assign('KANBAN_COL',$column);
	$smarty->display('KanbanColumn.tpl');
}
<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@92272 crmv@102879 crmv@106857 */

require_once('modules/Settings/ProcessMaker/ProcessDynaForm.php');
$PDynaForm = ProcessDynaForm::getInstance();

$actionTypes = $PMUtils->getActionTypes();
$smarty->assign("actionTypes", $actionTypes);

if ($actionTypes['Cycle']) {
	// check if there are table fields
	$tableFields = $PMUtils->getAllTableFields($id);
	$dFtableFields = $PDynaForm->getAllTableFields($id);
	$tableFields = array_merge($tableFields, $dFtableFields);
	if (is_array($tableFields) && count($tableFields) > 0) {
		$smarty->assign("tableFields", $tableFields);
		$cycleActionTypes = array();
		foreach($actionTypes['Cycle']['actions'] as $a) {
			$cycleActionTypes[$a] = $actionTypes[$a];
		}
		$smarty->assign("cycleActionTypes", $cycleActionTypes);
	} else {
		unset($actionTypes['Cycle']);
	}
}

$_REQUEST['enable_editoptions'] = 'yes';
$_REQUEST['editoptionsfieldnames'] = implode('|',array('process_name','description'));	//crmv@109685
$_REQUEST['assigned_user_id'] = $helper_arr['assigned_user_id'];
if (isset($helper_arr['sdk_params_assigned_user_id'])) $_REQUEST['sdk_params_assigned_user_id'] = $helper_arr['sdk_params_assigned_user_id'];	//crmv@113527

//if ($PMUtils->isStartTask($id,$elementid)) {
//	$smarty->assign("PMH_RELATEDTO_LIST", $PMUtils->getRecordsInvolvedOptions($id, $helper_arr['related_to'], true));
//	$smarty->assign("PMH_OTHER_ASSIGNED_TO", $PMUtils->getOwnerFieldOptions($id, $helper_arr['assigned_user_id'], true));
//} else {
	$smarty->assign("PMH_RELATEDTO_LIST", $PMUtils->getRecordsInvolvedOptions($id, $helper_arr['related_to']));
	//$smarty->assign("PMH_OTHER_ASSIGNED_TO", $PMUtils->getOwnerFieldOptions($id, $helper_arr['assigned_user_id'], false, true));
//}
$smarty->assign('PMH_ASSIGNEDTO', getOutputHtml(53, 'assigned_user_id', 'LBL_ASSIGNED_TO', 100, array('assigned_user_id'=>$helper_arr['assigned_user_id']),1,'Settings','',1,'I~M'));
//crmv@103450
if (empty($helper_arr['process_status'])) {
	$helper_arr['process_status'] = $PMUtils->getProcessHelperDefault($id,$elementid,$type);
}
$smarty->assign('PMH_STATUS', getOutputHtml(15, 'process_status', 'Status', 100, array('process_status'=>$helper_arr['process_status']),1,'Processes','',1,'V~O'));
//crmv@103450e

$tmp_helper = $helper_arr;
unset($tmp_helper['dynaform']);
//crmv@109685
$tmp_helper = addslashes(Zend_Json::encode($tmp_helper));
$smarty->assign('JSON_HELPER_ARR',$tmp_helper);
//crmv@109685e

$smarty->assign('SDK_CUSTOM_FUNCTIONS',SDK::getFormattedProcessMakerFieldActions());

$involvedRecords = $PMUtils->getRecordsInvolved($id,true);
$smarty->assign('JSON_INVOLVED_RECORDS',Zend_Json::encode($involvedRecords));

require_once('modules/Settings/ProcessMaker/ProcessDynaForm.php');
$processDynaFormObj = ProcessDynaForm::getInstance();
$dynaFormOptions = $processDynaFormObj->getFieldsOptions($id,true);
$smarty->assign('JSON_DYNAFORM_OPTIONS',Zend_Json::encode($dynaFormOptions));

//crmv@100591
$elementsActors = $PMUtils->getElementsActors($id);
$smarty->assign('JSON_ELEMENTS_ACTORS',Zend_Json::encode($elementsActors));
//crmv@100591e

//crmv@106856
$PMUtils->unsetReloadAdvancedFieldAssignment();
$PMUtils->setAdvancedFieldAssignment('assigned_user_id',$helper_arr['advanced_field_assignment']['assigned_user_id']);
//crmv@106856e